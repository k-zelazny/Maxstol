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
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

use PrestaZone\CategoryTree\Service\CategoryTreeLegacyProvider;
use PrestaZone\CategoryTree\Service\CategoryTreeProvider;

class Pz_Categorytree extends Module implements WidgetInterface
{
  /** @var CategoryTreeProvider|null */
  private $categoryTreeProvider;

  /** @var CategoryTreeLegacyProvider|null */
  private $legacyCategoryTreeProvider;

  public function __construct()
  {
    $this->name = 'pz_categorytree';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'PrestaZone';
    $this->need_instance = 0;
    $this->bootstrap = true;
    
    parent::__construct();
    
    $this->displayName = $this->trans('Category Tree', [], 'Modules.Pzcategorytree.Admin');
    $this->description = $this->trans('Displays main categories as expandable sections with subcategory product counters.', [], 'Modules.Pzcategorytree.Admin');
    
    $this->ps_versions_compliancy = ['min' => '9.0.0', 'max' => _PS_VERSION_];
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
    
    return parent::install()
      && $this->registerHook('displayLeftColumn')
      && $this->registerHook('displayPZCategoryTree')
      && $this->registerHook('header');
  }

  public function uninstall()
  {
    return parent::uninstall();
  }

  public function hookHeader()
  {
    if (!isset($this->context->controller)) {
      return;
    }
    
    $this->context->controller->registerStylesheet('module-' . $this->name . '-widget', 'modules/' . $this->name . '/views/css/widget.css', [
      'media' => 'all',
      'priority' => 200,
    ]);
  }

  public function renderWidget($hookName, array $configuration)
  {
    $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
    
    return $this->fetch('module:pz_categorytree/views/templates/hook/widget.tpl');
  }

  public function getWidgetVariables($hookName, array $configuration)
  {
    return [
      'mainCategories' => $this->getCategoryTreeData(),
    ];
  }

  /**
   * @return array<int, array<string, mixed>>
   */
  private function getCategoryTreeData()
  {
    try {
      return $this->getCategoryTreeProvider()->getMainCategoriesWithSubcategories($this->context);
    } catch (\Throwable $e) {
      try {
        return $this->getLegacyCategoryTreeProvider()->getMainCategoriesWithSubcategories($this->context);
      } catch (\Throwable $e) {
        return [];
      }
    }
  }

  /**
   * @return CategoryTreeProvider
   */
  private function getCategoryTreeProvider()
  {
    if (null === $this->categoryTreeProvider) {
      try {
        $this->categoryTreeProvider = $this->get(CategoryTreeProvider::class);
      } catch (\Throwable $e) {
        try {
          $container = SymfonyContainer::getInstance();
          if (null !== $container) {
            $this->categoryTreeProvider = $container->get(CategoryTreeProvider::class);
          }
        } catch (\Throwable $e) {
        }
      }
    }
    
    if (null === $this->categoryTreeProvider) {
      throw new RuntimeException('CategoryTreeProvider service is not available.');
    }
    
    return $this->categoryTreeProvider;
  }

  /**
   * @return CategoryTreeLegacyProvider
   */
  private function getLegacyCategoryTreeProvider()
  {
    if (null === $this->legacyCategoryTreeProvider) {
      $this->legacyCategoryTreeProvider = new CategoryTreeLegacyProvider();
    }
    
    return $this->legacyCategoryTreeProvider;
  }
}