<?php
/**
 * 2025 PrestaZone
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
 * @copyright 2025 PrestaZone
 * @license   Paid license for a single domain, contact: <modules@prestazone.pl>
 */

if (!defined('_PS_VERSION_')) {
  exit;
}

class Pz_Orderconfirmation extends Module
{
  public function __construct()
  {
    $this->name = 'pz_orderconfirmation';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'PrestaZone';
    $this->need_instance = 0;
    $this->bootstrap = true;
    
    parent::__construct();
    
    $this->displayName = $this->trans('Order confirmation', [], 'Modules.Pzorderconfirmation.Admin');
    $this->description = $this->trans('Allows you to replace the standard order confirmation page with a custom view with extended order information.', [], 'Modules.Pzorderconfirmation.Admin');
    
    $this->ps_versions_compliancy = [ 'min' => '8.0', 'max' => _PS_VERSION_ ];
  }

  public function install()
  {
    return parent::install()
      && $this->registerHook('displayOrderConfirmation')
      && $this->registerHook('actionFrontControllerSetMedia');
  }

  public function uninstall()
  {
    return parent::uninstall();
  }

  public function isUsingNewTranslationSystem()
  {
    return true;
  }

  /**
   * Renders the custom order confirmation block.
   *
   * This hook is executed on the order confirmation page. It receives the
   * Order object in $params['order'], prepares the related data (customer,
   * addresses, carrier, products, etc.) and assigns them to Smarty so they
   * can be displayed in the module’s front template.
   *
   * @param array $params Hook parameters, must contain an 'order' instance
   *
   * @return string Rendered HTML for the order confirmation section
   */
  public function hookDisplayOrderConfirmation($params)
  {
    if (empty($params['order']) || !$params['order'] instanceof Order) {
      return '';
    }
    
    $order = $params['order'];
    
    $idLang = (int) $this->context->language->id;
    $idShop  = (int) $this->context->shop->id;
    
    $locale = $this->context->getCurrentLocale();
    $currency = $this->context->currency;
    
    // Details
    $deliveryAddress = new Address((int) $order->id_address_delivery);
    $invoiceAddress  = new Address((int) $order->id_address_invoice);
    $customer = new Customer((int) $order->id_customer);
    $carrier = new Carrier((int) $order->id_carrier, $idLang);
    
    // Products
    $products = [];
    foreach ($order->getProducts() as $row) {
      $product = new Product($row['id_product'], false, $idLang, $idShop);
      if (!Validate::isLoadedObject($product)) {
        continue;
      }
      
      $cover = Product::getCover($product->id);
      
      $products[] = [
        'id_product' => (int) $product->id,
        'name' => $product->name,
        'reference' => $product->reference,
        'quantity' => (int) $row['product_quantity'],
        'price' => $locale->formatPrice($product->price, $currency->iso_code),
        'product_url' => $this->context->link->getProductLink($product),
        'image_url' => $this->context->link->getImageLink($product->link_rewrite, (int) $cover['id_image'], 'cart_default'),
      ];
    }
    
    $this->context->smarty->assign([
      'order' => $order,
      'products' => $products,
      'deliveryAddress' => $deliveryAddress,
      'invoiceAddress' => $invoiceAddress,
      'customerDetails' => $customer,
      'carrier' => $carrier,
      'summary' => [
        'products' => $locale->formatPrice($order->total_products_wt, $currency->iso_code),
        'discount' => $locale->formatPrice($order->total_discounts_tax_incl, $currency->iso_code),
        'shipping' => $locale->formatPrice($order->total_shipping_tax_incl, $currency->iso_code),
        'tax' => $locale->formatPrice(($order->total_paid_tax_incl - $order->total_paid_tax_excl), $currency->iso_code),
        'total' => $locale->formatPrice($order->total_paid_tax_incl, $currency->iso_code),
      ]
    ]);
    
    return $this->display(__FILE__, 'views/templates/hook/front.tpl');
  }

  /**
   * Registers front-office CSS and JS for the order confirmation page.
   *
   * This hook is executed before rendering a front controller. It is used
   * to conditionally load the module’s assets (CSS/JS) only on the order
   * confirmation page, in order to avoid unnecessary assets on other pages.
   *
   * @param array $params Hook parameters provided by the front controller
   *
   * @return void
   */
  public function hookActionFrontControllerSetMedia($params)
  {
    if ($this->context->controller && $this->context->controller->php_self === 'order-confirmation') {
      $this->context->controller->registerStylesheet('front', 'modules/' . $this->name . '/views/css/front.css', [
        'media' => 'all', 
        'priority' => 150
      ]);
      
      $this->context->controller->registerJavascript('front', 'modules/' . $this->name . '/views/js/front.js', [
        'position' => 'bottom', 
        'priority' => 150
      ]);
    }
  }
}