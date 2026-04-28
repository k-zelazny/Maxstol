<?php

class Pz_ProductconfiguratorCustomizationModuleFrontController extends ModuleFrontController
{
  /** @var bool */
  public $ssl = true;

  public function initContent()
  {
    parent::initContent();

    $this->processCustomizationSync();
  }

  private function processCustomizationSync()
  {
    $requestToken = trim((string) Tools::getValue('token', ''));
    $expectedToken = (string) Tools::getToken(false);
    if ($requestToken === '' || $expectedToken === '' || !hash_equals($expectedToken, $requestToken)) {
      $this->renderJson([
        'success' => false,
        'error' => 'invalid_token',
        'message' => $this->trans('Session expired. Refresh the page and try again.', [], 'Modules.Pzproductconfigurator.Shop'),
      ]);

      return;
    }

    $idProduct = (int) Tools::getValue('id_product');
    $idProductAttribute = (int) Tools::getValue('id_product_attribute', Tools::getValue('ipa'));

    if ($idProduct <= 0) {
      $this->renderJson([
        'success' => false,
        'error' => 'missing_product',
        'message' => $this->trans('Product could not be resolved.', [], 'Modules.Pzproductconfigurator.Shop'),
      ]);

      return;
    }

    $payload = $this->decodePayload(Tools::getValue('pz_productconfigurator_payload', ''));
    $validated = $this->module->getValidatedCustomizationForCart($idProduct, $idProductAttribute, $payload);

    if (empty($validated['is_valid'])) {
      $errorCode = isset($validated['error']) ? (string) $validated['error'] : 'invalid_payload';
      $message = $this->resolveValidationMessage($validated);

      $this->renderJson([
        'success' => false,
        'error' => $errorCode,
        'message' => $message,
      ]);

      return;
    }

    if (!empty($validated['is_empty'])) {
      $this->renderJson([
        'success' => true,
        'id_customization' => 0,
        'total_price_impact' => 0,
      ]);

      return;
    }

    $cart = $this->ensureCart();
    if (!Validate::isLoadedObject($cart)) {
      $this->renderJson([
        'success' => false,
        'error' => 'missing_cart',
        'message' => $this->trans('Cart could not be initialized.', [], 'Modules.Pzproductconfigurator.Shop'),
      ]);

      return;
    }

    $summary = trim((string) ($validated['summary'] ?? ''));
    if ($summary === '') {
      $summary = $this->trans('Product configuration', [], 'Modules.Pzproductconfigurator.Shop');
    }

    $total = isset($validated['total']) ? (float) $validated['total'] : 0.0;

    try {
      $idCustomization = $this->findExistingCustomizationId((int) $cart->id, $idProduct, $idProductAttribute, $summary, $total);
      if ($idCustomization <= 0) {
        $idCustomization = $this->createCustomization((int) $cart->id, $idProduct, $idProductAttribute);
        if ($idCustomization <= 0) {
          throw new \RuntimeException('Could not create customization.');
        }

        if (!$this->insertCustomizationData($idCustomization, $summary, $total)) {
          throw new \RuntimeException('Could not save customization payload.');
        }
      }

      if (method_exists($this->module, 'saveCartCustomizationSnapshot')) {
        $this->module->saveCartCustomizationSnapshot(
          (int) $cart->id,
          $idProduct,
          $idProductAttribute,
          (int) $idCustomization,
          $validated
        );
      }

      $this->renderJson([
        'success' => true,
        'id_customization' => (int) $idCustomization,
        'total_price_impact' => (float) $total,
      ]);
    } catch (\Throwable $e) {
      if (class_exists('PrestaShopLogger')) {
        \PrestaShopLogger::addLog(
          sprintf('PZ Product Configurator customization sync failed for product %d: %s', (int) $idProduct, $e->getMessage()),
          3,
          null,
          'Cart',
          (int) $cart->id
        );
      }

      $this->renderJson([
        'success' => false,
        'error' => 'sync_failed',
        'message' => $this->trans('Could not save configurator selection.', [], 'Modules.Pzproductconfigurator.Shop'),
      ]);
    }
  }

  private function decodePayload($rawPayload)
  {
    if (is_array($rawPayload)) {
      return $rawPayload;
    }

    $rawPayload = trim((string) $rawPayload);
    if ($rawPayload === '') {
      return [];
    }

    try {
      $decoded = json_decode($rawPayload, true, 512, JSON_THROW_ON_ERROR);

      return is_array($decoded) ? $decoded : [];
    } catch (\Throwable $e) {
      return [];
    }
  }

  private function ensureCart()
  {
    if (!isset($this->context->cart) || !Validate::isLoadedObject($this->context->cart)) {
      $this->context->cart = new Cart();
      $this->context->cart->id_currency = (int) $this->context->currency->id;
      $this->context->cart->id_lang = (int) $this->context->language->id;
      $this->context->cart->id_shop_group = (int) $this->context->shop->id_shop_group;
      $this->context->cart->id_shop = (int) $this->context->shop->id;

      if ($this->context->customer && Validate::isLoadedObject($this->context->customer)) {
        $this->context->cart->id_customer = (int) $this->context->customer->id;
        $this->context->cart->secure_key = (string) $this->context->customer->secure_key;
      }

      $this->context->cart->add();
      $this->context->cookie->id_cart = (int) $this->context->cart->id;
    }

    return $this->context->cart;
  }

  private function findExistingCustomizationId($idCart, $idProduct, $idProductAttribute, $summary, $total)
  {
    $idCart = (int) $idCart;
    $idProduct = (int) $idProduct;
    $idProductAttribute = (int) $idProductAttribute;
    $summary = trim((string) $summary);
    $total = round((float) $total, 6);

    if ($idCart <= 0 || $idProduct <= 0 || $summary === '') {
      return 0;
    }

    $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
      'SELECT cu.`id_customization`
      FROM `' . _DB_PREFIX_ . 'customization` cu
      INNER JOIN `' . _DB_PREFIX_ . 'customized_data` cd
        ON (cd.`id_customization` = cu.`id_customization`)
      WHERE cu.`id_cart` = ' . (int) $idCart . '
        AND cu.`id_product` = ' . (int) $idProduct . '
        AND cu.`id_product_attribute` = ' . (int) $idProductAttribute . '
        AND cu.`in_cart` IN (0, 1)
        AND cd.`id_module` = ' . (int) $this->module->id . '
        AND cd.`type` = ' . (int) Product::CUSTOMIZE_TEXTFIELD . '
        AND cd.`index` = ' . (int) Pz_Productconfigurator::CART_CUSTOMIZATION_TEXT_INDEX . '
        AND cd.`value` = \'' . pSQL($summary) . '\'
        AND ABS(cd.`price` - ' . (float) $total . ') < 0.000001
      ORDER BY cu.`id_customization` ASC'
    );

    $idCustomization = (int) $value;
    if ($idCustomization > 0) {
      Db::getInstance()->update(
        'customization',
        ['in_cart' => 1],
        'id_customization = ' . (int) $idCustomization
      );
    }

    return $idCustomization;
  }

  private function createCustomization($idCart, $idProduct, $idProductAttribute)
  {
    $idCart = (int) $idCart;
    $idProduct = (int) $idProduct;
    $idProductAttribute = (int) $idProductAttribute;

    if ($idCart <= 0 || $idProduct <= 0) {
      return 0;
    }

    $inserted = Db::getInstance()->insert('customization', [
      'id_cart' => $idCart,
      'id_product' => $idProduct,
      'id_product_attribute' => $idProductAttribute,
      'id_address_delivery' => 0,
      'quantity' => 0,
      'quantity_refunded' => 0,
      'quantity_returned' => 0,
      'in_cart' => 1,
    ]);

    if (!$inserted) {
      return 0;
    }

    return (int) Db::getInstance()->Insert_ID();
  }

  private function insertCustomizationData($idCustomization, $summary, $total)
  {
    $idCustomization = (int) $idCustomization;
    $summary = trim((string) $summary);
    $total = round((float) $total, 6);

    if ($idCustomization <= 0) {
      return false;
    }

    return (bool) Db::getInstance()->insert('customized_data', [
      'id_customization' => $idCustomization,
      'type' => (int) Product::CUSTOMIZE_TEXTFIELD,
      'index' => (int) Pz_Productconfigurator::CART_CUSTOMIZATION_TEXT_INDEX,
      'value' => $summary,
      'id_module' => (int) $this->module->id,
      'price' => $total,
      'weight' => 0,
    ]);
  }

  private function resolveValidationMessage(array $validated)
  {
    if (isset($validated['error']) && (string) $validated['error'] === 'missing_required_group' && !empty($validated['group_label'])) {
      return sprintf(
        $this->trans('Choose an option in the required group: %s', [], 'Modules.Pzproductconfigurator.Shop'),
        (string) $validated['group_label']
      );
    }

    if (isset($validated['error']) && (string) $validated['error'] === 'missing_configuration') {
      return $this->trans('Configurator for this product is unavailable.', [], 'Modules.Pzproductconfigurator.Shop');
    }

    return $this->trans('Configurator selection is invalid. Refresh the page and try again.', [], 'Modules.Pzproductconfigurator.Shop');
  }

  private function renderJson(array $payload)
  {
    while (ob_get_level() > 0) {
      ob_end_clean();
    }

    header('Content-Type: application/json');
    exit(json_encode($payload));
  }
}
