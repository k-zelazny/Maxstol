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

use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Pz_Featuredproducts extends Module implements WidgetInterface
{
  public function __construct()
  {
    $this->name = 'pz_featuredproducts';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'PrestaZone';
    $this->need_instance = 0;
    $this->bootstrap = true;
    
    parent::__construct();
    
    $this->displayName = $this->trans('Featured Products', [], 'Modules.Pzfeaturedproducts.Admin');
    $this->description = $this->trans('...', [], 'Modules.Pzfeaturedproducts.Admin');
    
    $this->ps_versions_compliancy = [ 'min' => '9.0', 'max' => _PS_VERSION_ ];
  }

  public function isUsingNewTranslationSystem()
  {
    return true;
  }

  public function install()
  {
    return parent::install()
      && $this->registerHook('displayHome')
      && $this->registerHook('displayPZFeaturedProducts');
  }

  public function uninstall()
  {
    return parent::uninstall();
  }

  public function renderWidget($hookName, array $configuration)
  {
    $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
    
    return $this->fetch('module:' . $this->name . '/views/templates/hook/widget.tpl');
  }

  public function getWidgetVariables($hookName, array $configuration)
  {
    return [
      'products' => $this->getTemporaryProducts(),
    ];
  }

  protected function getTemporaryProductIds()
  {
    return [ 1, 2, 3, 4 ];
  }

  protected function getTemporaryProducts()
  {
    $ids = array_values(array_unique(array_map('intval', $this->getTemporaryProductIds())));
    $ids = array_filter($ids, function ($id) {
      return $id > 0;
    });

    if (empty($ids)) {
      return [];
    }

    $rawProducts = [];
    foreach ($ids as $idProduct) {
      $rawProducts[] = ['id_product' => $idProduct];
    }

    $assembler = new ProductAssembler($this->context);
    $presenterFactory = new ProductPresenterFactory($this->context);
    $presentationSettings = $presenterFactory->getPresentationSettings();

    if (version_compare(_PS_VERSION_, '1.7.5', '>=')) {
      $presenter = new \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter(
        new ImageRetriever($this->context->link),
        $this->context->link,
        new PriceFormatter(),
        new ProductColorsRetriever(),
        $this->context->getTranslator()
      );
    } else {
      $presenter = new \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter(
        new ImageRetriever($this->context->link),
        $this->context->link,
        new PriceFormatter(),
        new ProductColorsRetriever(),
        $this->context->getTranslator()
      );
    }

    $productsForTemplate = [];
    $assembleInBulk = method_exists($assembler, 'assembleProducts');

    if ($assembleInBulk) {
      $rawProducts = $assembler->assembleProducts($rawProducts);
    }

    if (is_array($rawProducts)) {
      foreach ($rawProducts as $rawProduct) {
        $preparedProduct = $assembleInBulk ? $rawProduct : $assembler->assembleProduct($rawProduct);
        if (!is_array($preparedProduct) || empty($preparedProduct['id_product'])) {
          continue;
        }

        $productsForTemplate[] = $presenter->present(
          $presentationSettings,
          $preparedProduct,
          $this->context->language
        );
      }
    }

    return $productsForTemplate;
  }
}
