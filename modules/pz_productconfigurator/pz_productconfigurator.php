<?php
/**
 * 2026 PrestaZone
 *
 * NOTICE OF LICENSE
 *
 * This code is proprietary and paid. Distribution, copying, or modification
 * of the code without prior written consent from PrestaZone is strictly prohibited.
 *
 * LICENSE
 *
 * This module is licensed for use on a single domain only. Using the module on
 * more than one domain requires purchasing additional licenses. Details are
 * available in the module documentation or by contacting PrestaZone.
 *
 * DISCLAIMER
 *
 * PrestaZone is not liable for any damages resulting from the improper use
 * of this module.
 *
 * @author    PrestaZone <modules@prestazone.pl>
 * @copyright 2026 PrestaZone
 * @license   Paid license for a single domain, contact: <modules@prestazone.pl>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
  require_once __DIR__ . '/vendor/autoload.php';
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaZone\ProductConfigurator\Repository\ProductConfiguratorRepository;

class Pz_Productconfigurator extends Module
{
  /** @var int */
  const CART_CUSTOMIZATION_TEXT_INDEX = 910000001;
  /** @var string */
  const DEPENDENCY_NONE_OPTION = '__none__';
  /** @var bool Temporary safety switch: keep module tables on uninstall. */
  const PRESERVE_DATA_ON_UNINSTALL = true;

  /** @var ProductConfiguratorRepository|null */
  private $repository;

  /** @var bool */
  private $frontConfiguratorRendered = false;

  /** @var bool */
  private static $frontAssetsRegistered = false;

  /** @var bool */
  private static $frontAssetsFallbackPrinted = false;

  /** @var bool */
  private static $repositorySchemaEnsured = false;

  public function __construct()
  {
    $this->name = 'pz_productconfigurator';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'PrestaZone';
    $this->need_instance = 0;
    $this->bootstrap = true;

    parent::__construct();

    $this->displayName = $this->trans('Product Configurator', [], 'Modules.Pzproductconfigurator.Admin');
    $this->description = $this->trans(
      'Adds a hybrid product configurator based on rules and option groups instead of generating every possible combination.',
      [],
      'Modules.Pzproductconfigurator.Admin'
    );

    $this->ps_versions_compliancy = [
      'min' => '9.0.0',
      'max' => _PS_VERSION_,
    ];

    if ((int) $this->id > 0) {
      $this->ensureRuntimeHookRegistration();
      $this->ensureRepositorySchema();
    }
  }

  public function isUsingNewTranslationSystem()
  {
    return true;
  }

  public function install()
  {
    if (Shop::isFeatureActive()) {
      Shop::setContext(Shop::CONTEXT_ALL);
    }

    if (!$this->createCustomHook('displayPZProductConfigurator', 'PZ Product Configurator', 'Displays module-driven configuration groups near native variants.')) {
      return false;
    }

    if (!parent::install()) {
      return false;
    }

    if (!$this->createTables()) {
      $this->uninstall();

      return false;
    }

    $requiredHooks = [
      'displayPZProductConfigurator',
      'displayProductAdditionalInfo',
      'displayFooterProduct',
      'displayCustomization',
      'actionFrontControllerSetMedia',
      'displayHeader',
      'header',
      'actionProductFormBuilderModifier',
      'actionCreateProductFormBuilderModifier',
      'actionBeforeUpdateProductFormHandler',
      'actionAfterCreateProductFormHandler',
      'actionAfterUpdateProductFormHandler',
      'actionBeforeUpdateCreateProductFormHandler',
      'actionAfterCreateCreateProductFormHandler',
      'actionAfterUpdateCreateProductFormHandler',
      'actionObjectProductAddAfter',
      'actionObjectProductUpdateAfter',
      'actionAdminControllerSetMedia',
      'actionValidateOrder',
    ];

    foreach ($requiredHooks as $hookName) {
      if (!$this->registerHookIfAvailable($hookName)) {
        $this->uninstall();

        return false;
      }
    }

    return true;
  }

  public function uninstall()
  {
    if (!self::PRESERVE_DATA_ON_UNINSTALL) {
      if (!$this->dropTables()) {
        return false;
      }
    }

    return parent::uninstall();
  }

  public function getContent()
  {
    return $this->displayInformation(
      $this->trans(
        'Configure the schema directly on each product card. Native combinations should be limited to dimensions or stock-driven attributes; the remaining options can live in this module.',
        [],
        'Modules.Pzproductconfigurator.Admin'
      )
    );
  }

  public function hookHeader()
  {
    $this->ensureRuntimeHookRegistration();
    $this->registerFrontAssets();
  }

  public function hookDisplayHeader()
  {
    $this->ensureRuntimeHookRegistration();
    $this->registerFrontAssets();
  }

  public function hookActionFrontControllerSetMedia()
  {
    $this->registerFrontAssets();
  }

  private function registerFrontAssets()
  {
    static $assetsRegistered = false;

    $this->ensureRuntimeHookRegistration();

    if (!$this->isProductPageContext()) {
      return;
    }

    if ($assetsRegistered) {
      return;
    }

    if (!isset($this->context->controller)) {
      return;
    }

    if (
      !method_exists($this->context->controller, 'registerStylesheet')
      || !method_exists($this->context->controller, 'registerJavascript')
    ) {
      return;
    }

    $cssPath = _PS_MODULE_DIR_ . $this->name . '/views/css/front.css';
    $jsPath = _PS_MODULE_DIR_ . $this->name . '/views/js/front.js';

    $this->context->controller->registerStylesheet(
      'module-' . $this->name . '-front',
      'modules/' . $this->name . '/views/css/front.css?v=' . rawurlencode(is_file($cssPath) ? (string) filemtime($cssPath) : '1'),
      [
        'media' => 'all',
        'priority' => 210,
      ]
    );

    $this->context->controller->registerJavascript(
      'module-' . $this->name . '-front',
      'modules/' . $this->name . '/views/js/front.js?v=' . rawurlencode(is_file($jsPath) ? (string) filemtime($jsPath) : '1'),
      [
        'position' => 'bottom',
        'priority' => 210,
      ]
    );

    self::$frontAssetsRegistered = true;
    $assetsRegistered = true;
  }

  private function getFrontCssUri()
  {
    $cssPath = _PS_MODULE_DIR_ . $this->name . '/views/css/front.css';

    return $this->_path . 'views/css/front.css?v=' . rawurlencode(is_file($cssPath) ? (string) filemtime($cssPath) : '1');
  }

  private function getFrontJsUri()
  {
    $jsPath = _PS_MODULE_DIR_ . $this->name . '/views/js/front.js';

    return $this->_path . 'views/js/front.js?v=' . rawurlencode(is_file($jsPath) ? (string) filemtime($jsPath) : '1');
  }

  public function hookActionAdminControllerSetMedia()
  {
    $this->ensureRuntimeHookRegistration();
    $this->addAdminProductFormAssets();
  }

  public function hookActionProductFormBuilderModifier(array $params)
  {
    $this->ensureRuntimeHookRegistration();
    $this->addAdminProductFormAssets();
    $this->modifyProductFormBuilder($params);
  }

  public function hookActionCreateProductFormBuilderModifier(array $params)
  {
    $this->ensureRuntimeHookRegistration();
    $this->addAdminProductFormAssets();
    $this->modifyProductFormBuilder($params);
  }

  public function hookActionObjectProductAddAfter(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionObjectProductUpdateAfter(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionAfterCreateProductFormHandler(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionBeforeUpdateProductFormHandler(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionAfterUpdateProductFormHandler(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionBeforeUpdateCreateProductFormHandler(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionAfterCreateCreateProductFormHandler(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionAfterUpdateCreateProductFormHandler(array $params)
  {
    $this->saveProductFormConfiguration($params);
  }

  public function hookActionValidateOrder(array $params)
  {
    $this->persistOrderSnapshotsFromHook($params);
  }

  public function hookDisplayPZProductConfigurator(array $params = [])
  {
    $this->ensureRuntimeHookRegistration();
    $this->registerFrontAssets();

    return $this->renderFrontConfigurator($params, 'displayPZProductConfigurator');
  }

  public function hookDisplayProductAdditionalInfo(array $params = [])
  {
    $this->ensureRuntimeHookRegistration();
    $this->registerFrontAssets();

    return $this->renderFrontConfigurator($params, 'displayProductAdditionalInfo');
  }

  public function hookDisplayFooterProduct(array $params = [])
  {
    $this->ensureRuntimeHookRegistration();
    $this->registerFrontAssets();

    return $this->renderFrontConfigurator($params, 'displayFooterProduct');
  }

  public function hookDisplayCustomization(array $params = [])
  {
    $customization = isset($params['customization']) && is_array($params['customization'])
      ? $params['customization']
      : [];

    $value = isset($customization['value']) ? (string) $customization['value'] : '';
    $value = trim(strip_tags($value));
    if ($value === '') {
      return '';
    }

    $idProduct = isset($customization['id_product']) ? (int) $customization['id_product'] : 0;
    if ($idProduct <= 0) {
      return $value;
    }

    return $this->appendMissingOptionPricesToSummary($idProduct, $value);
  }

  public function saveCartCustomizationSnapshot($idCart, $idProduct, $idProductAttribute, $idCustomization, array $validated)
  {
    if (empty($validated['is_valid'])) {
      return false;
    }

    $hash = trim((string) ($validated['hash'] ?? ''));
    $payloadJson = trim((string) ($validated['payload_json'] ?? ''));
    $total = isset($validated['total']) ? (float) $validated['total'] : 0.0;

    if ($hash === '' || $payloadJson === '') {
      return false;
    }

    try {
      return (bool) $this->getRepository()->saveCartSnapshot(
        (int) $idCart,
        (int) $idProduct,
        (int) $idProductAttribute,
        (int) $idCustomization,
        $hash,
        $payloadJson,
        $total
      );
    } catch (\Throwable $e) {
      if (class_exists('PrestaShopLogger')) {
        \PrestaShopLogger::addLog(
          sprintf('PZ Product Configurator cart snapshot save failed for cart %d: %s', (int) $idCart, $e->getMessage()),
          3,
          null,
          'Cart',
          (int) $idCart
        );
      }

      return false;
    }
  }

  private function persistOrderSnapshotsFromHook(array $params)
  {
    $order = isset($params['order']) && $params['order'] instanceof Order ? $params['order'] : null;
    $cart = isset($params['cart']) && $params['cart'] instanceof Cart ? $params['cart'] : null;

    if (!Validate::isLoadedObject($order) || !Validate::isLoadedObject($cart)) {
      return;
    }

    try {
      $snapshots = $this->getRepository()->getCartSnapshotsByCartId((int) $cart->id);
    } catch (\Throwable $e) {
      if (class_exists('PrestaShopLogger')) {
        \PrestaShopLogger::addLog(
          sprintf('PZ Product Configurator order snapshot load failed for cart %d: %s', (int) $cart->id, $e->getMessage()),
          3,
          null,
          'Order',
          (int) $order->id
        );
      }

      return;
    }

    if (empty($snapshots)) {
      return;
    }

    $orderDetails = $this->extractOrderDetailsForSnapshot((int) $order->id, $order);
    $detailsByCustomization = [];
    $detailsByProduct = [];

    foreach ($orderDetails as $orderDetail) {
      $idOrderDetail = (int) ($orderDetail['id_order_detail'] ?? 0);
      $idProduct = (int) ($orderDetail['id_product'] ?? 0);
      $idProductAttribute = (int) ($orderDetail['id_product_attribute'] ?? 0);
      $idCustomization = (int) ($orderDetail['id_customization'] ?? 0);

      if ($idOrderDetail <= 0 || $idProduct <= 0) {
        continue;
      }

      if ($idCustomization > 0) {
        if (!isset($detailsByCustomization[$idCustomization])) {
          $detailsByCustomization[$idCustomization] = [];
        }

        $detailsByCustomization[$idCustomization][] = $idOrderDetail;
      }

      $productKey = $idProduct . '_' . $idProductAttribute;
      if (!isset($detailsByProduct[$productKey])) {
        $detailsByProduct[$productKey] = [];
      }

      $detailsByProduct[$productKey][] = $idOrderDetail;
    }

    $usedOrderDetailIds = [];

    foreach ($snapshots as $snapshot) {
      $idProduct = (int) ($snapshot['id_product'] ?? 0);
      $idProductAttribute = (int) ($snapshot['id_product_attribute'] ?? 0);
      $idCustomization = (int) ($snapshot['id_customization'] ?? 0);
      $hash = trim((string) ($snapshot['configuration_hash'] ?? ''));

      if ($idProduct <= 0 || $hash === '') {
        continue;
      }

      $idOrderDetail = 0;
      if ($idCustomization > 0 && isset($detailsByCustomization[$idCustomization])) {
        $idOrderDetail = $this->popFirstUnusedOrderDetailId($detailsByCustomization[$idCustomization], $usedOrderDetailIds);
      }

      if ($idOrderDetail <= 0) {
        $productKey = $idProduct . '_' . $idProductAttribute;
        if (isset($detailsByProduct[$productKey])) {
          $idOrderDetail = $this->popFirstUnusedOrderDetailId($detailsByProduct[$productKey], $usedOrderDetailIds);
        }
      }

      try {
        $this->getRepository()->saveOrderSnapshot(
          (int) $order->id,
          $idOrderDetail,
          $idProduct,
          $idProductAttribute,
          $hash,
          (string) ($snapshot['configuration_json'] ?? ''),
          (float) ($snapshot['price_impact_tax_excl'] ?? 0.0)
        );
      } catch (\Throwable $e) {
        if (class_exists('PrestaShopLogger')) {
          \PrestaShopLogger::addLog(
            sprintf('PZ Product Configurator order snapshot save failed for order %d: %s', (int) $order->id, $e->getMessage()),
            3,
            null,
            'Order',
            (int) $order->id
          );
        }
      }
    }
  }

  private function extractOrderDetailsForSnapshot($idOrder, $order)
  {
    $idOrder = (int) $idOrder;
    if ($idOrder <= 0) {
      return [];
    }

    $rawProducts = [];

    if ($order instanceof Order && method_exists($order, 'getProducts')) {
      try {
        $products = $order->getProducts();
        if (is_array($products)) {
          $rawProducts = $products;
        }
      } catch (\Throwable $e) {
        $rawProducts = [];
      }
    }

    if (empty($rawProducts) && class_exists('OrderDetail') && method_exists('OrderDetail', 'getList')) {
      try {
        $products = OrderDetail::getList($idOrder);
        if (is_array($products)) {
          $rawProducts = $products;
        }
      } catch (\Throwable $e) {
        $rawProducts = [];
      }
    }

    $normalized = [];

    foreach ($rawProducts as $row) {
      if (!is_array($row)) {
        continue;
      }

      $normalized[] = [
        'id_order_detail' => (int) ($row['id_order_detail'] ?? 0),
        'id_product' => (int) ($row['id_product'] ?? ($row['product_id'] ?? 0)),
        'id_product_attribute' => (int) ($row['id_product_attribute'] ?? ($row['product_attribute_id'] ?? 0)),
        'id_customization' => (int) ($row['id_customization'] ?? 0),
      ];
    }

    return $normalized;
  }

  private function popFirstUnusedOrderDetailId(array &$orderDetailIds, array &$usedOrderDetailIds)
  {
    while (!empty($orderDetailIds)) {
      $candidate = (int) array_shift($orderDetailIds);
      if ($candidate <= 0 || isset($usedOrderDetailIds[$candidate])) {
        continue;
      }

      $usedOrderDetailIds[$candidate] = true;

      return $candidate;
    }

    return 0;
  }

  private function appendMissingOptionPricesToSummary($idProduct, $summary)
  {
    $idProduct = (int) $idProduct;
    $summary = trim((string) $summary);

    if ($idProduct <= 0 || $summary === '') {
      return $summary;
    }

    $configuration = $this->resolveNormalizedConfiguration($idProduct);
    $groups = isset($configuration['groups']) && is_array($configuration['groups']) ? $configuration['groups'] : [];
    if (empty($groups)) {
      return $summary;
    }

    $priceMap = [];
    foreach ($groups as $group) {
      if (!is_array($group) || ($group['type'] ?? '') === 'native_combination') {
        continue;
      }

      $groupLabel = trim((string) ($group['label'] ?? ''));
      if ($groupLabel === '') {
        continue;
      }

      $groupKey = Tools::strtolower($groupLabel);
      if (!isset($priceMap[$groupKey])) {
        $priceMap[$groupKey] = [];
      }

      foreach (($group['options'] ?? []) as $option) {
        if (!is_array($option) || empty($option['enabled'])) {
          continue;
        }

        $optionLabel = trim((string) ($option['label'] ?? ''));
        $optionPrice = is_numeric($option['price'] ?? null) ? (float) $option['price'] : 0.0;
        if ($optionLabel === '' || abs($optionPrice) < 0.000001) {
          continue;
        }

        $priceMap[$groupKey][Tools::strtolower($optionLabel)] = $optionPrice;
      }
    }

    if (empty($priceMap)) {
      return $summary;
    }

    $parts = explode('|', $summary);
    $updatedParts = [];

    foreach ($parts as $part) {
      $part = trim((string) $part);
      if ($part === '') {
        continue;
      }

      if (preg_match('/\s-\s[+-]?\d/u', $part) === 1) {
        $updatedParts[] = $part;
        continue;
      }

      if (preg_match('/^\s*([^:]+):\s*(.+?)\s*$/u', $part, $matches) !== 1) {
        $updatedParts[] = $part;
        continue;
      }

      $groupLabel = trim((string) $matches[1]);
      $optionLabel = trim((string) $matches[2]);
      $groupKey = Tools::strtolower($groupLabel);
      $optionKey = Tools::strtolower($optionLabel);

      if (
        !isset($priceMap[$groupKey])
        || !isset($priceMap[$groupKey][$optionKey])
      ) {
        $updatedParts[] = $part;
        continue;
      }

      $formattedPrice = $this->formatPriceImpact((float) $priceMap[$groupKey][$optionKey]);
      $formattedPrice = $formattedPrice !== '' ? ltrim($formattedPrice, '+') : '';

      if ($formattedPrice === '') {
        $updatedParts[] = $part;
        continue;
      }

      $updatedParts[] = $groupLabel . ': ' . $optionLabel . ' - ' . $formattedPrice;
    }

    if (empty($updatedParts)) {
      return $summary;
    }

    return implode(' | ', $updatedParts);
  }

  private function renderFrontConfigurator(array $params, $hookName)
  {
    if ($this->frontConfiguratorRendered) {
      return '';
    }

    $debugMode = $this->isFrontDebugEnabled();
    $idProduct = $this->extractProductId($params);
    if ($idProduct <= 0) {
      return $this->renderFrontDebugPanel($hookName, [
        'status' => 'missing_product_id',
        'id_product' => 0,
      ], $debugMode);
    }

    $configurationRow = $this->loadConfigurationRow($idProduct);
    if (empty($configurationRow)) {
      return $this->renderFrontDebugPanel($hookName, [
        'status' => 'missing_configuration_row',
        'id_product' => (int) $idProduct,
        'shop_ids' => implode(', ', $this->getContextShopIds()),
      ], $debugMode);
    }

    if (empty($configurationRow['active']) || empty($configurationRow['is_valid'])) {
      return $this->renderFrontDebugPanel($hookName, [
        'status' => 'inactive_or_invalid',
        'id_product' => (int) $idProduct,
        'active' => !empty($configurationRow['active']) ? '1' : '0',
        'is_valid' => !empty($configurationRow['is_valid']) ? '1' : '0',
        'last_error' => isset($configurationRow['last_error']) ? (string) $configurationRow['last_error'] : '',
      ], $debugMode);
    }

    $normalizedJson = isset($configurationRow['normalized_json']) ? trim((string) $configurationRow['normalized_json']) : '';
    if ($normalizedJson === '') {
      return $this->renderFrontDebugPanel($hookName, [
        'status' => 'empty_normalized_json',
        'id_product' => (int) $idProduct,
      ], $debugMode);
    }

    $decodedConfiguration = json_decode($normalizedJson, true);
    if (!is_array($decodedConfiguration)) {
      return $this->renderFrontDebugPanel($hookName, [
        'status' => 'normalized_json_decode_failed',
        'id_product' => (int) $idProduct,
      ], $debugMode);
    }

    $frontPayload = $this->buildFrontPayload($decodedConfiguration, $idProduct);
    if (empty($frontPayload['groups']) && $frontPayload['impact_map_json'] === '{}') {
      return $this->renderFrontDebugPanel($hookName, [
        'status' => 'empty_front_payload',
        'id_product' => (int) $idProduct,
        'group_count' => isset($decodedConfiguration['groups']) && is_array($decodedConfiguration['groups']) ? count($decodedConfiguration['groups']) : 0,
      ], $debugMode);
    }

    $includeAssetsFallback = !self::$frontAssetsRegistered && !self::$frontAssetsFallbackPrinted;
    if ($includeAssetsFallback) {
      self::$frontAssetsFallbackPrinted = true;
    }

    $this->smarty->assign(array_merge($frontPayload, [
      'include_assets_fallback' => $includeAssetsFallback,
      'front_css_uri' => $this->getFrontCssUri(),
      'front_js_uri' => $this->getFrontJsUri(),
    ]));

    $content = $this->fetch('module:' . $this->name . '/views/templates/hook/configurator.tpl');

    if (is_string($content) && trim($content) !== '') {
      $this->frontConfiguratorRendered = true;
    }

    if (!is_string($content) || trim($content) === '') {
      return $this->renderFrontDebugPanel($hookName, [
        'status' => 'empty_template_output',
        'id_product' => (int) $idProduct,
      ], $debugMode);
    }

    return $content;
  }

  private function createTables()
  {
    try {
      return (bool) $this->getRepository()->createTables();
    } catch (\Throwable $e) {
      $this->_errors[] = $this->trans('Could not create module tables.', [], 'Modules.Pzproductconfigurator.Admin');
      if ($e->getMessage() !== '') {
        $this->_errors[] = $e->getMessage();
      }

      return false;
    }
  }

  private function dropTables()
  {
    try {
      return (bool) $this->getRepository()->dropTables();
    } catch (\Throwable $e) {
      $this->_errors[] = $this->trans('Could not remove module tables.', [], 'Modules.Pzproductconfigurator.Admin');
      if ($e->getMessage() !== '') {
        $this->_errors[] = $e->getMessage();
      }

      return false;
    }
  }

  private function createCustomHook($hookName, $title, $description)
  {
    $hookName = trim((string) $hookName);
    if ($hookName === '') {
      return false;
    }

    if ((int) Hook::getIdByName($hookName) > 0) {
      return true;
    }

    $hook = new Hook();
    $hook->name = $hookName;
    $hook->title = (string) $title;
    $hook->description = (string) $description;
    $hook->position = true;
    $hook->live_edit = false;

    return (bool) $hook->add();
  }

  private function ensureRuntimeHookRegistration()
  {
    static $hooksChecked = false;

    if ($hooksChecked) {
      return;
    }

    $hooksChecked = true;

    $this->createCustomHook(
      'displayPZProductConfigurator',
      'PZ Product Configurator',
      'Displays module-driven configuration groups near native variants.'
    );

    $hooksToEnsure = [
      'displayPZProductConfigurator',
      'displayProductAdditionalInfo',
      'displayFooterProduct',
      'displayCustomization',
      'actionFrontControllerSetMedia',
      'displayHeader',
      'header',
      'actionProductFormBuilderModifier',
      'actionCreateProductFormBuilderModifier',
      'actionBeforeUpdateProductFormHandler',
      'actionAfterCreateProductFormHandler',
      'actionAfterUpdateProductFormHandler',
      'actionBeforeUpdateCreateProductFormHandler',
      'actionAfterCreateCreateProductFormHandler',
      'actionAfterUpdateCreateProductFormHandler',
      'actionObjectProductAddAfter',
      'actionObjectProductUpdateAfter',
      'actionAdminControllerSetMedia',
      'actionValidateOrder',
    ];

    foreach ($hooksToEnsure as $hookName) {
      $this->registerHookIfAvailable($hookName);
    }
  }

  private function ensureRepositorySchema()
  {
    if (self::$repositorySchemaEnsured) {
      return;
    }

    self::$repositorySchemaEnsured = true;

    try {
      $this->getRepository()->ensureSchema();
    } catch (\Throwable $e) {
      if (class_exists('PrestaShopLogger')) {
        \PrestaShopLogger::addLog(
          sprintf('PZ Product Configurator schema ensure failed: %s', $e->getMessage()),
          3
        );
      }
    }
  }

  private function registerHookIfAvailable($hookName)
  {
    $hookName = trim((string) $hookName);
    if ($hookName === '') {
      return true;
    }

    $hookId = (int) Hook::getIdByName($hookName);
    if ($hookId <= 0) {
      return true;
    }

    if ($this->isHookAlreadyRegistered($hookId, $hookName)) {
      return true;
    }

    try {
      return (bool) $this->registerHook($hookName);
    } catch (\Throwable $e) {
      if ($this->isDuplicateHookRegistrationException($e)) {
        return true;
      }

      throw $e;
    }
  }

  private function isHookAlreadyRegistered($hookId, $hookName)
  {
    $hookId = (int) $hookId;
    if ($hookId <= 0 || (int) $this->id <= 0) {
      return false;
    }

    if (method_exists($this, 'isRegisteredInHook') && $this->isRegisteredInHook($hookName)) {
      return true;
    }

    $shopIds = $this->getContextShopIds();
    if (empty($shopIds)) {
      $shopIds = [(int) $this->context->shop->id];
    }

    $shopIds = array_values(array_filter(array_map('intval', $shopIds)));
    if (empty($shopIds)) {
      return false;
    }

    $count = (int) Db::getInstance()->getValue(
      'SELECT COUNT(*)
      FROM `' . _DB_PREFIX_ . 'hook_module`
      WHERE id_module = ' . (int) $this->id . '
        AND id_hook = ' . $hookId . '
        AND id_shop IN (' . implode(', ', $shopIds) . ')'
    );

    return $count > 0;
  }

  private function isDuplicateHookRegistrationException(\Throwable $e)
  {
    $message = strtolower(trim((string) $e->getMessage()));
    if ($message === '') {
      return false;
    }

    return strpos($message, 'duplicate entry') !== false
      || strpos($message, 'integrity constraint violation') !== false;
  }

  private function addAdminProductFormAssets()
  {
    if (!isset($this->context->controller) || !method_exists($this->context->controller, 'addJS')) {
      return;
    }

    $controller = (string) Tools::getValue('controller');
    $route = (string) Tools::getValue('route');
    $isProductController = $controller === 'AdminProducts' || $controller === 'AdminProductsLite' || strpos($route, 'admin_products') !== false;

    if (!$isProductController) {
      return;
    }

    static $assetsLoaded = false;
    if ($assetsLoaded) {
      return;
    }

    $assetFilePath = _PS_MODULE_DIR_ . $this->name . '/views/js/admin-product-form.js';
    $assetCssPath = _PS_MODULE_DIR_ . $this->name . '/views/css/admin-product-form.css';
    $assetVersion = is_file($assetFilePath) ? (string) filemtime($assetFilePath) : (string) time();
    $assetCssVersion = is_file($assetCssPath) ? (string) filemtime($assetCssPath) : (string) time();

    $this->context->controller->addJS(
      $this->_path . 'views/js/admin-product-form.js?v=' . rawurlencode($assetVersion)
    );

    if (method_exists($this->context->controller, 'addCSS')) {
      $this->context->controller->addCSS(
        $this->_path . 'views/css/admin-product-form.css?v=' . rawurlencode($assetCssVersion)
      );
    }

    $assetsLoaded = true;
  }

  private function modifyProductFormBuilder(array $params)
  {
    if (!isset($params['form_builder']) || !$params['form_builder'] instanceof \Symfony\Component\Form\FormBuilderInterface) {
      return;
    }

    $formBuilder = $params['form_builder'];

    if ($formBuilder->has('pz_productconfigurator')) {
      return;
    }

    $idProduct = $this->extractProductIdFromFormHook($params);
    $shopIds = $this->getContextShopIds();
    $idShop = (int) reset($shopIds);

    $configurationRow = $idProduct > 0 ? $this->getRepository()->getProductConfiguration($idProduct, $idShop) : [];
    $active = !empty($configurationRow['active']);
    $configJson = isset($configurationRow['config_json']) ? (string) $configurationRow['config_json'] : '';
    if (trim($configJson) === '') {
      $configJson = $this->getConfigurationExampleJson();
    }
    $lastError = isset($configurationRow['last_error']) ? trim((string) $configurationRow['last_error']) : '';

    $formBuilder->add('pz_productconfigurator', \Symfony\Component\Form\Extension\Core\Type\FormType::class, [
      'label' => $this->displayName,
      'required' => false,
    ]);

    $configuratorBuilder = $formBuilder->get('pz_productconfigurator');

    $configuratorBuilder->add('active', \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class, [
      'required' => false,
      'label' => $this->trans('Enable configurator for this product', [], 'Modules.Pzproductconfigurator.Admin'),
      'data' => (bool) $active,
    ]);

    $helpLines = [
      $this->trans('Use the visual editor below to add groups like colors, barriers, drawers, slats or mattress options.', [], 'Modules.Pzproductconfigurator.Admin'),
      $this->trans('The raw JSON stays available only as an advanced fallback. Native combinations should still be limited to stock-driven options like size.', [], 'Modules.Pzproductconfigurator.Admin'),
    ];

    if ($lastError !== '') {
      $helpLines[] = '<span class="text-danger">' . htmlspecialchars($this->trans('Last validation issue:', [], 'Modules.Pzproductconfigurator.Admin') . ' ' . $lastError, ENT_QUOTES, 'UTF-8') . '</span>';
    }

    $configuratorBuilder->add('config_json', \Symfony\Component\Form\Extension\Core\Type\TextareaType::class, [
      'required' => false,
      'label' => false,
      'data' => $configJson,
      'empty_data' => '',
      'help' => implode('<br>', $helpLines),
      'help_html' => true,
      'attr' => [
        'rows' => 24,
        'class' => 'font-monospace js-pz-productconfigurator-json',
        'spellcheck' => 'false',
        'data-label-add-group' => $this->trans('Add group', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-load-sample' => $this->trans('Load sample data', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-clear-configuration' => $this->trans('Clear configuration', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-move-group-up' => $this->trans('Move group up', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-move-group-down' => $this->trans('Move group down', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-remove-group' => $this->trans('Remove group', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-move-option-up' => $this->trans('Move option up', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-move-option-down' => $this->trans('Move option down', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-remove-option' => $this->trans('Remove option', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-add-option' => $this->trans('Add option', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-empty-groups' => $this->trans('No groups have been added yet. Start with "%s".', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-name' => $this->trans('Option name', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-short-description' => $this->trans('Short description (shown on info icon hover)', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-surcharge' => $this->trans('Surcharge', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-color-hex' => $this->trans('HEX color', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-texture-url' => $this->trans('Texture URL (optional)', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-image-url' => $this->trans('Image URL', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-default-selected' => $this->trans('Selected by default', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-name' => $this->trans('Group name', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-presentation' => $this->trans('Front presentation', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-presentation-text' => $this->trans('Text tiles', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-presentation-swatch' => $this->trans('Color swatches', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-presentation-image' => $this->trans('Image tiles', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-image-columns' => $this->trans('Tiles per row (image groups)', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-required-selection' => $this->trans('Selection required', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-none-option-label' => $this->trans('No-option label', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-dependency-source' => $this->trans('Show this group only when (source group)', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-dependency-option' => $this->trans('Condition option', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-dependency-any-option' => $this->trans('Any selected option in source group', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-dependency-none-option' => $this->trans('No option selected in source group', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-dependency-disabled' => $this->trans('Always visible (no dependency)', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-options-title' => $this->trans('Group options', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-warning-configurator-disabled' => $this->trans('Configurator has saved groups but is disabled. Enable it before saving the product.', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-enable-configurator' => $this->trans('Enable configurator', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-warning-invalid-json' => $this->trans('Could not parse previous JSON. You can rebuild from this editor, but saving will overwrite old technical data.', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-show-advanced-json' => $this->trans('Show technical JSON data', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-group-template' => $this->trans('Group %d', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-option-template' => $this->trans('Option %d', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-none-option-default' => $this->trans('No extra option', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-upload-image' => $this->trans('Upload image', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-change-image' => $this->trans('Change image', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-remove-image' => $this->trans('Remove image', [], 'Modules.Pzproductconfigurator.Admin'),
        'data-label-image-preview-alt' => $this->trans('Selected image preview', [], 'Modules.Pzproductconfigurator.Admin'),
      ],
    ]);
  }

  private function saveProductFormConfiguration(array $params)
  {
    $idProduct = $this->extractProductIdFromFormHook($params);
    if ($idProduct <= 0) {
      return;
    }

    $payload = $this->extractConfiguratorPayload($params);
    if ($payload === null) {
      return;
    }

    $active = !empty($payload['active']);
    $rawConfigJson = isset($payload['config_json']) ? trim((string) $payload['config_json']) : '';
    $normalized = $this->normalizeConfigurationJson($rawConfigJson);
    $shopIds = $this->getContextShopIds();

    try {
      $this->getRepository()->saveProductConfiguration(
        $idProduct,
        $shopIds,
        $active,
        $rawConfigJson,
        (bool) $normalized['is_valid'],
        $normalized['normalized_json'],
        $normalized['last_error']
      );
    } catch (\Throwable $e) {
      if (class_exists('PrestaShopLogger')) {
        \PrestaShopLogger::addLog(
          sprintf('PZ Product Configurator save failed for product %d: %s', (int) $idProduct, $e->getMessage()),
          3,
          null,
          'Product',
          (int) $idProduct
        );
      }
    }
  }

  private function extractConfiguratorPayload(array $params)
  {
    $formData = isset($params['form_data']) && is_array($params['form_data']) ? $params['form_data'] : [];
    $fromFormData = $this->findConfiguratorPayloadRecursively($formData);
    if (is_array($fromFormData)) {
      return $fromFormData;
    }

    try {
      $request = SymfonyContainer::getInstance()->get('request_stack')->getCurrentRequest();
      $requestPayload = $request !== null ? $request->request->all() : [];
      if (is_array($requestPayload)) {
        $fromRequest = $this->findConfiguratorPayloadRecursively($requestPayload);
        if (is_array($fromRequest)) {
          return $fromRequest;
        }
      }
    } catch (\Throwable $e) {
      // Fall back to POST below.
    }

    if (isset($_POST) && is_array($_POST)) {
      $fromPost = $this->findConfiguratorPayloadRecursively($_POST);
      if (is_array($fromPost)) {
        return $fromPost;
      }
    }

    return null;
  }

  private function findConfiguratorPayloadRecursively(array $payload)
  {
    if (isset($payload['pz_productconfigurator']) && is_array($payload['pz_productconfigurator'])) {
      return $payload['pz_productconfigurator'];
    }

    foreach ($payload as $value) {
      if (!is_array($value)) {
        continue;
      }

      $found = $this->findConfiguratorPayloadRecursively($value);
      if (is_array($found)) {
        return $found;
      }
    }

    return null;
  }

  private function normalizeConfigurationJson($rawJson)
  {
    $rawJson = trim((string) $rawJson);
    if ($rawJson === '') {
      return [
        'is_valid' => true,
        'normalized_json' => null,
        'last_error' => null,
      ];
    }

    try {
      $decoded = json_decode($rawJson, true, 512, JSON_THROW_ON_ERROR);
    } catch (\Throwable $e) {
      return [
        'is_valid' => false,
        'normalized_json' => null,
        'last_error' => $this->trans('Invalid JSON syntax.', [], 'Modules.Pzproductconfigurator.Admin'),
      ];
    }

    if (!is_array($decoded)) {
      return [
        'is_valid' => false,
        'normalized_json' => null,
        'last_error' => $this->trans('Configurator schema must decode to a JSON object.', [], 'Modules.Pzproductconfigurator.Admin'),
      ];
    }

    $normalized = [
      'version' => max(1, (int) ($decoded['version'] ?? 1)),
      'native_attribute_impacts' => $this->normalizeImpactMap(isset($decoded['native_attribute_impacts']) && is_array($decoded['native_attribute_impacts']) ? $decoded['native_attribute_impacts'] : []),
      'groups' => [],
      'rules' => [],
    ];

    $groups = isset($decoded['groups']) && is_array($decoded['groups']) ? array_values($decoded['groups']) : [];
    foreach ($groups as $groupIndex => $group) {
      if (!is_array($group)) {
        continue;
      }

      $normalizedGroup = $this->normalizeGroup($group, $groupIndex);
      if ($normalizedGroup === null) {
        continue;
      }

      if ($normalizedGroup['type'] === 'native_combination') {
        foreach ($normalizedGroup['options'] as $option) {
          if (!empty($option['attribute_id'])) {
            $normalized['native_attribute_impacts'][(string) $option['attribute_id']] = (float) $option['price'];
          }
        }
      }

      $normalized['groups'][] = $normalizedGroup;
    }

    $rules = isset($decoded['rules']) && is_array($decoded['rules']) ? array_values($decoded['rules']) : [];
    foreach ($rules as $rule) {
      if (is_array($rule) && !empty($rule)) {
        $normalized['rules'][] = $rule;
      }
    }

    try {
      return [
        'is_valid' => true,
        'normalized_json' => json_encode($normalized, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR),
        'last_error' => null,
      ];
    } catch (\Throwable $e) {
      return [
        'is_valid' => false,
        'normalized_json' => null,
        'last_error' => $this->trans('Normalized schema could not be encoded back to JSON.', [], 'Modules.Pzproductconfigurator.Admin'),
      ];
    }
  }

  private function normalizeImpactMap(array $impactMap)
  {
    $result = [];

    foreach ($impactMap as $attributeId => $impactValue) {
      $normalizedAttributeId = (int) $attributeId;
      if ($normalizedAttributeId <= 0 || !is_numeric($impactValue)) {
        continue;
      }

      $result[(string) $normalizedAttributeId] = (float) $impactValue;
    }

    ksort($result);

    return $result;
  }

  private function normalizeGroup(array $group, $groupIndex)
  {
    $label = trim((string) ($group['label'] ?? $group['name'] ?? ''));
    $code = $this->sanitizeCode(isset($group['code']) ? $group['code'] : $label, 'group_' . ((int) $groupIndex + 1));
    $type = trim((string) ($group['type'] ?? 'virtual_option'));
    $selection = trim((string) ($group['selection'] ?? 'single'));
    $presentation = trim((string) ($group['presentation'] ?? 'text'));

    $allowedTypes = ['virtual_option', 'addon_product', 'native_combination'];
    $allowedSelections = ['single', 'multiple'];
    $allowedPresentations = ['text', 'swatch', 'image'];

    if (!in_array($type, $allowedTypes, true)) {
      $type = 'virtual_option';
    }

    if (!in_array($selection, $allowedSelections, true)) {
      $selection = 'single';
    }

    if (!in_array($presentation, $allowedPresentations, true)) {
      $presentation = 'text';
    }

    $imageColumns = (int) ($group['image_columns'] ?? 0);
    if ($imageColumns < 1 || $imageColumns > 8) {
      $imageColumns = 0;
    }

    $options = isset($group['options']) && is_array($group['options']) ? array_values($group['options']) : [];
    $normalizedOptions = [];

    foreach ($options as $optionIndex => $option) {
      if (!is_array($option)) {
        continue;
      }

      $normalizedOption = $this->normalizeOption($option, $optionIndex);
      if ($normalizedOption === null) {
        continue;
      }

      $normalizedOptions[] = $normalizedOption;
    }

    if (empty($normalizedOptions) && $type !== 'native_combination') {
      return null;
    }

    usort($normalizedOptions, static function (array $left, array $right) {
      if ($left['position'] === $right['position']) {
        return strcmp((string) $left['label'], (string) $right['label']);
      }

      return $left['position'] <=> $right['position'];
    });

    $defaultFound = false;
    foreach ($normalizedOptions as &$normalizedOption) {
      if (!$normalizedOption['default']) {
        continue;
      }

      if ($selection === 'multiple') {
        $defaultFound = true;
        continue;
      }

      if ($defaultFound) {
        $normalizedOption['default'] = false;
        continue;
      }

      $defaultFound = true;
    }
    unset($normalizedOption);

    $noneLabel = trim((string) ($group['none_label'] ?? ''));
    if ($noneLabel === '') {
      $noneLabel = $this->trans('No extra option', [], 'Modules.Pzproductconfigurator.Shop');
    }

    $dependsOnGroup = trim((string) ($group['depends_on_group'] ?? ''));
    $dependsOnGroup = $dependsOnGroup !== '' ? $this->sanitizeCode($dependsOnGroup, '') : '';
    if ($dependsOnGroup === $code) {
      $dependsOnGroup = '';
    }

    $dependsOnOptionRaw = trim((string) ($group['depends_on_option'] ?? ''));
    $dependsOnOption = '';
    if ($dependsOnGroup !== '') {
      if ($dependsOnOptionRaw === '') {
        $dependsOnOption = self::DEPENDENCY_NONE_OPTION;
      } elseif ($dependsOnOptionRaw === self::DEPENDENCY_NONE_OPTION) {
        $dependsOnOption = self::DEPENDENCY_NONE_OPTION;
      } else {
        $dependsOnOption = $this->sanitizeCode($dependsOnOptionRaw, '');
      }
    }

    return [
      'code' => $code,
      'label' => $label !== '' ? $label : ucfirst(str_replace('_', ' ', $code)),
      'type' => $type,
      'selection' => $selection,
      'presentation' => $presentation,
      'image_columns' => $presentation === 'image' ? $imageColumns : 0,
      'required' => !empty($group['required']),
      'position' => max(0, (int) ($group['position'] ?? $groupIndex)),
      'none_label' => $noneLabel,
      'depends_on_group' => $dependsOnGroup,
      'depends_on_option' => $dependsOnOption,
      'options' => $normalizedOptions,
    ];
  }

  private function normalizeOption(array $option, $optionIndex)
  {
    $label = trim((string) ($option['label'] ?? $option['name'] ?? ''));
    if ($label === '') {
      return null;
    }

    $code = $this->sanitizeCode(isset($option['code']) ? $option['code'] : $label, 'option_' . ((int) $optionIndex + 1));

    return [
      'code' => $code,
      'label' => $label,
      'description' => trim((string) ($option['description'] ?? '')),
      'price' => is_numeric($option['price'] ?? null) ? (float) $option['price'] : 0.0,
      'attribute_id' => !empty($option['attribute_id']) ? (int) $option['attribute_id'] : null,
      'product_id' => !empty($option['product_id']) ? (int) $option['product_id'] : null,
      'color' => trim((string) ($option['color'] ?? '')),
      'texture' => trim((string) ($option['texture'] ?? '')),
      'image' => trim((string) ($option['image'] ?? '')),
      'default' => !empty($option['default']),
      'enabled' => !array_key_exists('enabled', $option) || !empty($option['enabled']),
      'position' => max(0, (int) ($option['position'] ?? $optionIndex)),
    ];
  }

  private function sanitizeCode($value, $fallback)
  {
    $value = Tools::str2url((string) $value);
    if ($value === '') {
      $value = Tools::str2url((string) $fallback);
    }
    if ($value === '') {
      $value = 'item';
    }

    return (string) $value;
  }

  private function loadConfigurationRow($idProduct)
  {
    $shopCandidates = $this->getContextShopIds();

    foreach ($shopCandidates as $shopId) {
      $row = $this->getRepository()->getProductConfiguration((int) $idProduct, (int) $shopId);
      if (!empty($row)) {
        return $row;
      }
    }

    $allShopIds = Shop::getShops(true, null, true);
    if (is_array($allShopIds)) {
      foreach ($allShopIds as $shopId) {
        $row = $this->getRepository()->getProductConfiguration((int) $idProduct, (int) $shopId);
        if (!empty($row)) {
          return $row;
        }
      }
    }

    return [];
  }

  private function buildFrontPayload(array $configuration, $idProduct)
  {
    $groups = [];

    foreach (($configuration['groups'] ?? []) as $group) {
      if (!is_array($group) || ($group['type'] ?? '') === 'native_combination') {
        continue;
      }

      $selection = ($group['selection'] ?? 'single') === 'multiple' ? 'multiple' : 'single';
      $presentation = (string) ($group['presentation'] ?? 'text');
      $required = !empty($group['required']);
      $imageColumns = (int) ($group['image_columns'] ?? 0);
      if ($imageColumns < 1 || $imageColumns > 8) {
        $imageColumns = 0;
      }
      $inputName = 'pz_productconfigurator[' . $group['code'] . ']';
      if ($selection === 'multiple') {
        $inputName .= '[]';
      }

      $frontOptions = [];
      $defaultLabels = [];

      foreach (($group['options'] ?? []) as $option) {
        if (!is_array($option) || empty($option['enabled'])) {
          continue;
        }

        $isDefault = !empty($option['default']);
        if ($isDefault) {
          $defaultLabels[] = (string) $option['label'];
        }

        $previewKind = 'none';
        if ($presentation === 'image' && !empty($option['image'])) {
          $previewKind = 'image';
        } elseif ($presentation === 'swatch' && !empty($option['texture'])) {
          $previewKind = 'texture';
        } elseif ($presentation === 'swatch' && !empty($option['color'])) {
          $previewKind = 'color';
        }

        $frontOptions[] = [
          'code' => (string) $option['code'],
          'label' => (string) $option['label'],
          'description' => (string) ($option['description'] ?? ''),
          'input_type' => $selection === 'multiple' ? 'checkbox' : 'radio',
          'input_name' => $inputName,
          'price' => (float) ($option['price'] ?? 0),
          'formatted_price' => $this->formatPriceImpact((float) ($option['price'] ?? 0)),
          'is_default' => $isDefault,
          'preview_kind' => $previewKind,
          'preview_image' => !empty($option['image']) ? (string) $option['image'] : (!empty($option['texture']) ? (string) $option['texture'] : ''),
          'preview_color' => !empty($option['color']) ? (string) $option['color'] : '',
          'product_id' => !empty($option['product_id']) ? (int) $option['product_id'] : 0,
          'attribute_id' => !empty($option['attribute_id']) ? (int) $option['attribute_id'] : 0,
        ];
      }

      if (empty($frontOptions)) {
        continue;
      }

      $selectedSummary = implode(', ', $defaultLabels);
      if ($selectedSummary === '' && !$required && $selection === 'single') {
        $selectedSummary = (string) ($group['none_label'] ?? $this->trans('No extra option', [], 'Modules.Pzproductconfigurator.Shop'));
      }
      if ($selectedSummary === '') {
        $selectedSummary = $this->trans('Choose option', [], 'Modules.Pzproductconfigurator.Shop');
      }

      $groups[] = [
        'code' => (string) $group['code'],
        'label' => (string) $group['label'],
        'selection' => $selection,
        'presentation' => $presentation,
        'image_columns' => $presentation === 'image' ? $imageColumns : 0,
        'required' => $required,
        'depends_on_group' => (string) ($group['depends_on_group'] ?? ''),
        'depends_on_option' => (string) ($group['depends_on_option'] ?? ''),
        'selected_summary' => $selectedSummary,
        'none_label' => (string) ($group['none_label'] ?? $this->trans('No extra option', [], 'Modules.Pzproductconfigurator.Shop')),
        'show_none_option' => !$required && $selection === 'single',
        'options' => $frontOptions,
      ];
    }

    return [
      'id_product' => (int) $idProduct,
      'groups' => $groups,
      'schema_json' => json_encode($configuration, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
      'impact_map_json' => json_encode($configuration['native_attribute_impacts'] ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
      'payload_input_name' => 'pz_productconfigurator_payload',
      'payload_total_name' => 'pz_productconfigurator_total',
      'customization_sync_url' => $this->getCustomizationSyncUrl(),
      'cart_token' => (string) Tools::getToken(false),
      'total_label' => $this->trans('Configurator surcharge', [], 'Modules.Pzproductconfigurator.Shop'),
      'reset_label' => $this->trans('Reset selection', [], 'Modules.Pzproductconfigurator.Shop'),
      'sync_error_message' => $this->trans('Could not save configurator selection. Try again.', [], 'Modules.Pzproductconfigurator.Shop'),
      'choose_option_label' => $this->trans('Choose option', [], 'Modules.Pzproductconfigurator.Shop'),
      'option_info_label' => $this->trans('More information', [], 'Modules.Pzproductconfigurator.Shop'),
    ];
  }

  private function getCustomizationSyncUrl()
  {
    if (!isset($this->context->link)) {
      return '';
    }

    try {
      return (string) $this->context->link->getModuleLink($this->name, 'customization', [], true);
    } catch (\Throwable $e) {
      return '';
    }
  }

  public function resolveNormalizedConfiguration($idProduct)
  {
    $idProduct = (int) $idProduct;
    if ($idProduct <= 0) {
      return [];
    }

    $configurationRow = $this->loadConfigurationRow($idProduct);
    if (empty($configurationRow) || empty($configurationRow['active']) || empty($configurationRow['is_valid'])) {
      return [];
    }

    $normalizedJson = isset($configurationRow['normalized_json']) ? trim((string) $configurationRow['normalized_json']) : '';
    if ($normalizedJson === '') {
      return [];
    }

    $decoded = json_decode($normalizedJson, true);

    return is_array($decoded) ? $decoded : [];
  }

  public function getValidatedCustomizationForCart($idProduct, $idProductAttribute, array $rawPayload)
  {
    $idProduct = (int) $idProduct;
    $idProductAttribute = (int) $idProductAttribute;

    if ($idProduct <= 0) {
      return [
        'is_valid' => false,
        'is_empty' => true,
        'error' => 'missing_product',
      ];
    }

    $configuration = $this->resolveNormalizedConfiguration($idProduct);
    if (empty($configuration)) {
      return [
        'is_valid' => false,
        'is_empty' => true,
        'error' => 'missing_configuration',
      ];
    }

    $selectionPayload = isset($rawPayload['selections']) && is_array($rawPayload['selections']) ? $rawPayload['selections'] : [];
    $ruleDefinitions = isset($configuration['rules']) && is_array($configuration['rules']) ? $configuration['rules'] : [];
    $normalizedSelections = [];
    $items = [];
    $total = 0.0;

    $groupsIndex = [];
    $candidateSelections = [];

    foreach (($configuration['groups'] ?? []) as $group) {
      if (!is_array($group) || empty($group['code']) || ($group['type'] ?? '') === 'native_combination') {
        continue;
      }

      $groupCode = (string) $group['code'];
      $groupLabel = (string) ($group['label'] ?? $groupCode);
      $selectionMode = ($group['selection'] ?? 'single') === 'multiple' ? 'multiple' : 'single';
      $rawSelection = array_key_exists($groupCode, $selectionPayload) ? $selectionPayload[$groupCode] : ($selectionMode === 'multiple' ? [] : '');
      $candidateSelections[$groupCode] = $this->collectSelectionCandidates($rawSelection, $selectionMode);

      $optionsByCode = [];
      foreach (($group['options'] ?? []) as $option) {
        if (!is_array($option) || empty($option['code']) || empty($option['enabled'])) {
          continue;
        }

        $optionsByCode[(string) $option['code']] = $option;
      }

      $groupsIndex[$groupCode] = [
        'label' => $groupLabel,
        'selection' => $selectionMode,
        'required' => !empty($group['required']),
        'depends_on_group' => trim((string) ($group['depends_on_group'] ?? '')),
        'depends_on_option' => trim((string) ($group['depends_on_option'] ?? '')),
        'options_by_code' => $optionsByCode,
      ];
    }

    $groupActiveMap = [];
    foreach ($groupsIndex as $groupCode => $groupData) {
      $groupActiveMap[$groupCode] = true;
    }

    $maxIterations = max(2, count($groupsIndex) + 1);
    for ($iteration = 0; $iteration < $maxIterations; $iteration++) {
      $changed = false;
      $effectiveSelections = $candidateSelections;

      foreach ($groupsIndex as $groupCode => $groupData) {
        if (!empty($groupActiveMap[$groupCode])) {
          continue;
        }

        $effectiveSelections[$groupCode] = [];
      }

      foreach ($groupsIndex as $groupCode => $groupData) {
        $isActive = $this->isGroupDependencyMatched($groupData, $effectiveSelections);
        if ((bool) $groupActiveMap[$groupCode] === (bool) $isActive) {
          continue;
        }

        $groupActiveMap[$groupCode] = (bool) $isActive;
        $changed = true;
      }

      $candidateSelections = $effectiveSelections;

      if (!$changed) {
        break;
      }
    }

    foreach ($groupActiveMap as $groupCode => $isActive) {
      if (!empty($isActive)) {
        continue;
      }

      $candidateSelections[$groupCode] = [];
    }

    foreach ($groupsIndex as $groupCode => $groupData) {
      $groupLabel = (string) $groupData['label'];
      $selectionMode = (string) $groupData['selection'];
      $groupIsActive = !empty($groupActiveMap[$groupCode]);
      if (!$groupIsActive) {
        $normalizedSelections[$groupCode] = $selectionMode === 'multiple' ? [] : '';
        continue;
      }

      $candidateCodes = isset($candidateSelections[$groupCode]) ? $candidateSelections[$groupCode] : [];
      $optionsByCode = isset($groupData['options_by_code']) && is_array($groupData['options_by_code']) ? $groupData['options_by_code'] : [];
      $allowedCodes = array_keys($optionsByCode);
      $allowedCodes = $this->applyRulesToGroupSelection($groupCode, $allowedCodes, $candidateSelections, $ruleDefinitions);

      $validCodes = [];
      foreach ($candidateCodes as $optionCode) {
        if (!isset($optionsByCode[$optionCode]) || !in_array($optionCode, $allowedCodes, true)) {
          continue;
        }

        $option = $optionsByCode[$optionCode];
        $price = is_numeric($option['price'] ?? null) ? (float) $option['price'] : 0.0;
        $validCodes[] = $optionCode;
        $total += $price;

        $items[] = [
          'group' => $groupCode,
          'group_label' => $groupLabel,
          'option' => (string) $optionCode,
          'option_label' => (string) ($option['label'] ?? $optionCode),
          'price' => $price,
          'product_id' => !empty($option['product_id']) ? (int) $option['product_id'] : 0,
          'attribute_id' => !empty($option['attribute_id']) ? (int) $option['attribute_id'] : 0,
        ];
      }

      if (!empty($groupData['required']) && empty($validCodes)) {
        return [
          'is_valid' => false,
          'is_empty' => false,
          'error' => 'missing_required_group',
          'group_label' => $groupLabel,
        ];
      }

      $normalizedSelections[$groupCode] = $selectionMode === 'multiple'
        ? array_values(array_unique($validCodes))
        : (!empty($validCodes) ? (string) $validCodes[0] : '');
    }

    $nativeImpactTotal = $this->calculateNativeAttributeImpact(
      $idProductAttribute,
      isset($configuration['native_attribute_impacts']) && is_array($configuration['native_attribute_impacts'])
        ? $configuration['native_attribute_impacts']
        : []
    );

    $total += $nativeImpactTotal;
    $total = round($total, 6);

    $canonicalPayload = [
      'selections' => $normalizedSelections,
      'items' => $items,
      'native_attribute_impact' => round($nativeImpactTotal, 6),
      'total_price_impact' => $total,
    ];

    $payloadJson = json_encode($canonicalPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if (!is_string($payloadJson)) {
      $payloadJson = '{}';
    }

    $summaryParts = [];
    foreach ($items as $item) {
      $groupLabel = trim((string) ($item['group_label'] ?? ''));
      $optionLabel = trim((string) ($item['option_label'] ?? ''));
      if ($groupLabel === '' || $optionLabel === '') {
        continue;
      }

      $optionPrice = is_numeric($item['price'] ?? null) ? (float) $item['price'] : 0.0;
      if (abs($optionPrice) > 0.000001) {
        $formattedPrice = $this->formatPriceImpact($optionPrice);
        $formattedPrice = $formattedPrice !== '' ? ltrim($formattedPrice, '+') : '';

        if ($formattedPrice !== '') {
          $summaryParts[] = $groupLabel . ': ' . $optionLabel . ' - ' . $formattedPrice;
          continue;
        }
      }

      $summaryParts[] = $groupLabel . ': ' . $optionLabel;
    }

    $summary = implode(' | ', $summaryParts);
    $summary = trim(Tools::substr($summary, 0, 1024));

    return [
      'is_valid' => true,
      'is_empty' => empty($items) && abs($total) < 0.000001 && !$this->hasMeaningfulSelection($normalizedSelections),
      'error' => null,
      'total' => $total,
      'hash' => sha1($payloadJson),
      'summary' => $summary,
      'payload_json' => $payloadJson,
      'payload' => $canonicalPayload,
    ];
  }

  private function collectSelectionCandidates($rawSelection, $selectionMode)
  {
    $selectionMode = $selectionMode === 'multiple' ? 'multiple' : 'single';
    $candidateCodes = [];

    if ($selectionMode === 'multiple') {
      if (is_array($rawSelection)) {
        foreach ($rawSelection as $value) {
          $value = trim((string) $value);
          if ($value !== '') {
            $candidateCodes[] = $value;
          }
        }
      } elseif ($rawSelection !== null) {
        $value = trim((string) $rawSelection);
        if ($value !== '') {
          $candidateCodes[] = $value;
        }
      }
    } else {
      if (is_array($rawSelection)) {
        foreach ($rawSelection as $value) {
          $value = trim((string) $value);
          if ($value !== '') {
            $candidateCodes[] = $value;
          }
        }
      } else {
        $value = trim((string) $rawSelection);
        if ($value !== '') {
          $candidateCodes[] = $value;
        }
      }
    }

    $candidateCodes = array_values(array_unique($candidateCodes));
    if ($selectionMode === 'single' && count($candidateCodes) > 1) {
      $candidateCodes = [$candidateCodes[0]];
    }

    return $candidateCodes;
  }

  private function isGroupDependencyMatched(array $groupData, array $candidateSelections)
  {
    $dependsOnGroup = trim((string) ($groupData['depends_on_group'] ?? ''));
    if ($dependsOnGroup === '') {
      return true;
    }

    $dependsOnOption = trim((string) ($groupData['depends_on_option'] ?? ''));
    $selectedInSource = isset($candidateSelections[$dependsOnGroup]) && is_array($candidateSelections[$dependsOnGroup])
      ? array_values(array_filter(array_map('trim', array_map('strval', $candidateSelections[$dependsOnGroup]))))
      : [];

    if ($dependsOnOption === '') {
      return !empty($selectedInSource);
    }

    if ($dependsOnOption === self::DEPENDENCY_NONE_OPTION) {
      return empty($selectedInSource);
    }

    return in_array($dependsOnOption, $selectedInSource, true);
  }

  private function applyRulesToGroupSelection($groupCode, array $allowedCodes, array $candidateSelections, array $rules)
  {
    $groupCode = trim((string) $groupCode);
    if ($groupCode === '' || empty($allowedCodes) || empty($rules)) {
      return array_values(array_unique($allowedCodes));
    }

    $resolvedAllowed = array_values(array_unique($allowedCodes));

    foreach ($rules as $rule) {
      if (!is_array($rule)) {
        continue;
      }

      if (!$this->isRuleConditionMatched($rule, $candidateSelections)) {
        continue;
      }

      $allowScopes = $this->extractRuleScopes($rule, 'allow');
      foreach ($allowScopes as $scope) {
        if ($scope['group'] !== $groupCode || empty($scope['options'])) {
          continue;
        }

        $resolvedAllowed = array_values(array_intersect($resolvedAllowed, $scope['options']));
      }

      $denyScopes = $this->extractRuleScopes($rule, 'deny');
      foreach ($denyScopes as $scope) {
        if ($scope['group'] !== $groupCode || empty($scope['options'])) {
          continue;
        }

        $resolvedAllowed = array_values(array_diff($resolvedAllowed, $scope['options']));
      }
    }

    return array_values(array_unique($resolvedAllowed));
  }

  private function isRuleConditionMatched(array $rule, array $candidateSelections)
  {
    $hasCondition = array_key_exists('when', $rule) || array_key_exists('if', $rule);
    if (!$hasCondition) {
      return true;
    }

    $rawConditions = array_key_exists('when', $rule) ? $rule['when'] : $rule['if'];
    $conditions = $this->normalizeRuleScopes($rawConditions);
    if (empty($conditions)) {
      return false;
    }

    foreach ($conditions as $condition) {
      $groupCode = $condition['group'];
      $expectedOptions = $condition['options'];
      $selectedOptions = isset($candidateSelections[$groupCode]) && is_array($candidateSelections[$groupCode])
        ? $candidateSelections[$groupCode]
        : [];

      if (empty(array_intersect($selectedOptions, $expectedOptions))) {
        return false;
      }
    }

    return true;
  }

  private function extractRuleScopes(array $rule, $mode)
  {
    $mode = $mode === 'deny' ? 'deny' : 'allow';

    if (array_key_exists($mode, $rule)) {
      return $this->normalizeRuleScopes($rule[$mode]);
    }

    $type = Tools::strtolower(trim((string) ($rule['type'] ?? '')));
    if ($type !== $mode) {
      return [];
    }

    $group = trim((string) ($rule['group'] ?? ''));
    $rawOptions = array_key_exists('options', $rule) ? $rule['options'] : ($rule['option'] ?? null);

    return $this->normalizeRuleScopes([
      [
        'group' => $group,
        'options' => $rawOptions,
      ],
    ]);
  }

  private function normalizeRuleScopes($rawScopes)
  {
    if (!is_array($rawScopes)) {
      return [];
    }

    $scopes = [];
    $isAssoc = array_keys($rawScopes) !== range(0, count($rawScopes) - 1);
    $scopeList = $isAssoc ? [$rawScopes] : $rawScopes;

    foreach ($scopeList as $scope) {
      if (!is_array($scope)) {
        continue;
      }

      $groupCode = trim((string) ($scope['group'] ?? ''));
      if ($groupCode === '') {
        continue;
      }

      $options = [];

      if (array_key_exists('option', $scope)) {
        $optionValue = trim((string) $scope['option']);
        if ($optionValue !== '') {
          $options[] = $optionValue;
        }
      }

      if (array_key_exists('options', $scope)) {
        $rawOptions = $scope['options'];
        if (is_array($rawOptions)) {
          foreach ($rawOptions as $rawOption) {
            $optionValue = trim((string) $rawOption);
            if ($optionValue !== '') {
              $options[] = $optionValue;
            }
          }
        } elseif ($rawOptions !== null) {
          $optionValue = trim((string) $rawOptions);
          if ($optionValue !== '') {
            $options[] = $optionValue;
          }
        }
      }

      $options = array_values(array_unique($options));
      if (empty($options)) {
        continue;
      }

      $scopes[] = [
        'group' => $groupCode,
        'options' => $options,
      ];
    }

    return $scopes;
  }

  private function calculateNativeAttributeImpact($idProductAttribute, array $impactMap)
  {
    $idProductAttribute = (int) $idProductAttribute;
    if ($idProductAttribute <= 0 || empty($impactMap)) {
      return 0.0;
    }

    $normalizedImpactMap = $this->normalizeImpactMap($impactMap);
    if (empty($normalizedImpactMap)) {
      return 0.0;
    }

    if (isset($normalizedImpactMap[(string) $idProductAttribute])) {
      return (float) $normalizedImpactMap[(string) $idProductAttribute];
    }

    $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
      'SELECT pac.`id_attribute`
      FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
      WHERE pac.`id_product_attribute` = ' . (int) $idProductAttribute
    );

    if (!is_array($rows) || empty($rows)) {
      return 0.0;
    }

    $total = 0.0;
    foreach ($rows as $row) {
      $idAttribute = (int) ($row['id_attribute'] ?? 0);
      if ($idAttribute <= 0) {
        continue;
      }

      $key = (string) $idAttribute;
      if (!isset($normalizedImpactMap[$key])) {
        continue;
      }

      $total += (float) $normalizedImpactMap[$key];
    }

    return round($total, 6);
  }

  private function hasMeaningfulSelection(array $selections)
  {
    foreach ($selections as $value) {
      if (is_array($value)) {
        foreach ($value as $item) {
          if (trim((string) $item) !== '') {
            return true;
          }
        }

        continue;
      }

      if (trim((string) $value) !== '') {
        return true;
      }
    }

    return false;
  }

  private function formatPriceImpact($price)
  {
    $price = (float) $price;
    if (abs($price) < 0.000001) {
      return '';
    }

    $absolutePrice = abs($price);
    $formatted = '';
    $currencyIsoCode = '';

    if (isset($this->context->currency) && !empty($this->context->currency->iso_code)) {
      $currencyIsoCode = (string) $this->context->currency->iso_code;
    }

    if ($currencyIsoCode !== '' && method_exists($this->context, 'getCurrentLocale')) {
      try {
        $formatted = (string) $this->context->getCurrentLocale()->formatPrice($absolutePrice, $currencyIsoCode);
      } catch (\Throwable $e) {
        $formatted = '';
      }
    }

    if ($formatted === '' && is_callable(['Tools', 'convertAndFormatPrice'])) {
      try {
        $formatted = (string) Tools::convertAndFormatPrice($absolutePrice);
      } catch (\Throwable $e) {
        $formatted = '';
      }
    }

    if ($formatted === '') {
      $formatted = number_format($absolutePrice, floor($absolutePrice) === $absolutePrice ? 0 : 2, ',', ' ');
      if ($currencyIsoCode !== '') {
        $formatted .= ' ' . $currencyIsoCode;
      }
    }

    return ($price > 0 ? '+' : '-') . $formatted;
  }

  private function getConfigurationExampleJson()
  {
    return json_encode([
      'version' => 1,
      'native_attribute_impacts' => new \stdClass(),
      'groups' => [
        [
          'code' => 'group-1',
          'label' => 'Rozmiar łóżka',
          'type' => 'virtual_option',
          'selection' => 'single',
          'presentation' => 'text',
          'required' => true,
          'none_label' => 'Nie chce tej opcji',
          'options' => [
            [
              'code' => 'option-1-1',
              'label' => '70x140',
              'price' => 1120,
              'enabled' => true,
            ],
            [
              'code' => 'option-1-2',
              'label' => '70x150',
              'price' => 1120,
              'enabled' => true,
            ],
            [
              'code' => 'option-1-3',
              'label' => '70x160',
              'price' => 1120,
              'enabled' => true,
            ],
            [
              'code' => 'option-1-4',
              'label' => '80x150',
              'price' => 1130,
              'enabled' => true,
            ],
            [
              'code' => 'option-1-5',
              'label' => '80x160',
              'price' => 1130,
              'enabled' => true,
            ],
            [
              'code' => 'option-1-6',
              'label' => '80x170',
              'price' => 1130,
              'enabled' => true,
            ],
          ],
        ],
        [
          'code' => 'group-2',
          'label' => 'Materac',
          'type' => 'virtual_option',
          'selection' => 'single',
          'presentation' => 'text',
          'required' => false,
          'none_label' => 'Nie chcę materaca',
          'options' => [
            [
              'code' => 'option-2-1',
              'label' => '70x140',
              'price' => 1120,
              'enabled' => true,
            ],
            [
              'code' => 'option-2-2',
              'label' => '70x150',
              'price' => 1120,
              'enabled' => true,
            ],
            [
              'code' => 'option-2-3',
              'label' => '70x160',
              'price' => 1120,
              'enabled' => true,
            ],
            [
              'code' => 'option-2-4',
              'label' => '80x150',
              'price' => 1130,
              'enabled' => true,
            ],
            [
              'code' => 'option-2-5',
              'label' => '80x160',
              'price' => 1130,
              'enabled' => true,
            ],
            [
              'code' => 'option-2-6',
              'label' => '80x170',
              'price' => 1130,
              'enabled' => true,
            ],
          ],
        ],
        [
          'code' => 'group-6',
          'label' => 'Kolory',
          'type' => 'virtual_option',
          'selection' => 'single',
          'presentation' => 'image',
          'image_columns' => 6,
          'required' => true,
          'none_label' => 'Nie chce tej opcji',
          'options' => [
            [
              'code' => 'option-6-1',
              'label' => 'Orzech 22-62',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-2',
              'label' => 'Orzech 22-74',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-3',
              'label' => 'Orzech 22-68',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-4',
              'label' => 'Oliwka 27-50',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-5',
              'label' => 'Orzech 22-66',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-6',
              'label' => 'Oranż 23-05',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-7',
              'label' => 'Brąz 22-45',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-8',
              'label' => 'Teak 23-49',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-9',
              'label' => 'Brąz 22-40',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-10',
              'label' => 'Oranż 23-50',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-11',
              'label' => 'Wenge',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-12',
              'label' => 'Oranż 23-25',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-13',
              'label' => 'Palisander 26-32',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-14',
              'label' => 'Granat 28-60',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-15',
              'label' => 'Olcha/Olcha miodowa',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-16',
              'label' => 'Biel transparentna',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-17',
              'label' => 'Dąb bielony',
              'price' => 0,
              'enabled' => true,
            ],
          ],
        ],
        [
          'code' => 'group-3',
          'label' => 'Barierka ochronna',
          'type' => 'virtual_option',
          'selection' => 'single',
          'presentation' => 'text',
          'required' => false,
          'none_label' => 'Nie chcę barierki ochronnej',
          'options' => [
            [
              'code' => 'option-3-1',
              'label' => 'Barierka A',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-3-2',
              'label' => 'Barierka B',
              'price' => 0,
              'enabled' => true,
            ],
          ],
        ],
        [
          'code' => 'group-4',
          'label' => 'Szuflady',
          'type' => 'virtual_option',
          'selection' => 'single',
          'presentation' => 'text',
          'required' => false,
          'none_label' => 'Nie chce szuflad',
          'options' => [
            [
              'code' => 'option-4-1',
              'label' => 'Szuflada jednoczęściowa',
              'price' => 420,
              'enabled' => true,
            ],
            [
              'code' => 'option-4-2',
              'label' => 'Szuflada dwuczęściowa',
              'price' => 420,
              'enabled' => true,
            ],
          ],
        ],
        [
          'code' => 'group-5',
          'label' => 'Stelaż',
          'type' => 'virtual_option',
          'selection' => 'single',
          'presentation' => 'text',
          'required' => false,
          'none_label' => 'Nie chcę stelaża',
          'options' => [
            [
              'code' => 'option-5-1',
              'label' => 'Stelaż Standardowy A',
              'price' => 0,
              'enabled' => true,
            ],
            [
              'code' => 'option-5-2',
              'label' => 'Stelaż B z ręczną regulacją przy głowie (6 stopni  podnoszenia)',
              'price' => 200,
              'enabled' => true,
            ],
          ],
        ],
      ],
      'rules' => [],
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
  }

  private function extractProductId(array $params)
  {
    if (isset($params['product'])) {
      $productId = $this->extractIntegerFromMixedValue($params['product'], ['id_product', 'id']);
      if ($productId > 0) {
        return $productId;
      }
    }

    if (!empty($params['id_product'])) {
      return (int) $params['id_product'];
    }

    return (int) Tools::getValue('id_product');
  }

  private function extractIntegerFromMixedValue($value, array $keys)
  {
    foreach ($keys as $key) {
      $candidate = $this->readMixedValue($value, $key);
      if ((int) $candidate > 0) {
        return (int) $candidate;
      }
    }

    return 0;
  }

  private function readMixedValue($value, $key)
  {
    if (is_array($value) && array_key_exists($key, $value)) {
      return $value[$key];
    }

    if ($value instanceof \ArrayAccess && $value->offsetExists($key)) {
      return $value[$key];
    }

    if (is_object($value) && isset($value->{$key})) {
      return $value->{$key};
    }

    return null;
  }

  private function extractProductIdFromFormHook(array $params)
  {
    if (isset($params['id']) && (int) $params['id'] > 0) {
      return (int) $params['id'];
    }

    if (isset($params['product_id']) && (int) $params['product_id'] > 0) {
      return (int) $params['product_id'];
    }

    if (isset($params['product']) && is_object($params['product']) && !empty($params['product']->id)) {
      return (int) $params['product']->id;
    }

    if (isset($params['form_data']) && is_array($params['form_data'])) {
      if (!empty($params['form_data']['id'])) {
        return (int) $params['form_data']['id'];
      }
      if (!empty($params['form_data']['header']['id'])) {
        return (int) $params['form_data']['header']['id'];
      }
    }

    return (int) Tools::getValue('id_product');
  }

  private function getContextShopIds()
  {
    $shopIds = Shop::getContextListShopID();

    if (!is_array($shopIds) || empty($shopIds)) {
      $shopIds = [(int) $this->context->shop->id];
    }

    return array_values(array_unique(array_map('intval', $shopIds)));
  }

  private function isProductPageContext()
  {
    if (!isset($this->context->controller)) {
      return false;
    }

    if (property_exists($this->context->controller, 'php_self') && $this->context->controller->php_self === 'product') {
      return true;
    }

    return (string) Dispatcher::getInstance()->getController() === 'product';
  }

  private function isFrontDebugEnabled()
  {
    return (string) Tools::getValue('pzpc_debug') === '1';
  }

  private function renderFrontDebugPanel($hookName, array $details, $debugMode)
  {
    if (!$debugMode) {
      return '';
    }

    $this->frontConfiguratorRendered = true;

    $items = [];
    $items[] = '<strong>PZ Configurator debug</strong>';
    $items[] = 'hook: ' . htmlspecialchars((string) $hookName, ENT_QUOTES, 'UTF-8');

    foreach ($details as $label => $value) {
      $items[] = htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . ': ' . htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    return '<div class="alert alert-warning pz-productconfigurator-debug" style="margin:12px 0;padding:12px 14px;border:1px solid #f3d38a;border-radius:8px;background:#fff8e5;color:#5d4200;font-size:14px;line-height:1.5;">'
      . implode('<br>', $items)
      . '</div>';
  }

  private function getRepository()
  {
    if (null === $this->repository) {
      $resolvedRepository = null;

      try {
        $candidate = $this->get(ProductConfiguratorRepository::class);
        if ($candidate instanceof ProductConfiguratorRepository) {
          $resolvedRepository = $candidate;
        }
      } catch (\Throwable $e) {
        // Keep fallback resolution below.
      }

      if (null === $resolvedRepository) {
        $connection = null;

        try {
          $container = SymfonyContainer::getInstance();
          if (null !== $container) {
            $connection = $container->get('doctrine.dbal.default_connection');
          }
        } catch (\Throwable $e) {
          // Keep DBAL direct connection fallback below.
        }

        if (null === $connection) {
          try {
            $dbHost = (string) _DB_SERVER_;
            $dbPort = null;

            if (preg_match('/^([^:]+):(\d+)$/', $dbHost, $matches) === 1) {
              $dbHost = (string) $matches[1];
              $dbPort = (int) $matches[2];
            }

            $params = [
              'driver' => 'pdo_mysql',
              'host' => $dbHost,
              'dbname' => (string) _DB_NAME_,
              'user' => (string) _DB_USER_,
              'password' => (string) _DB_PASSWD_,
              'charset' => 'utf8mb4',
            ];

            if ($dbPort !== null && $dbPort > 0) {
              $params['port'] = $dbPort;
            }

            $connection = \Doctrine\DBAL\DriverManager::getConnection($params);
          } catch (\Throwable $e) {
            throw new \RuntimeException($e->getMessage(), 0, $e);
          }
        }

        $resolvedRepository = new ProductConfiguratorRepository($connection, _DB_PREFIX_);
      }

      try {
        $resolvedRepository->ensureSchema();
      } catch (\Throwable $e) {
        // Keep lazy compatibility for old installs where schema update cannot run yet.
      }

      $this->repository = $resolvedRepository;
    }

    return $this->repository;
  }
}
