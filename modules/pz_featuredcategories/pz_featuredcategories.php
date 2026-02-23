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

use PrestaZone\FeaturedCategories\Repository\FeaturedCategoryRepository;

class Pz_Featuredcategories extends Module implements WidgetInterface
{
  private $repository;

  public function __construct()
  {
    $this->name = 'pz_featuredcategories';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'PrestaZone';
    $this->need_instance = 0;
    $this->bootstrap = true;
    
    parent::__construct();
    
    $this->displayName = $this->trans('Featured Categories', [], 'Modules.Pzfeaturedcategories.Admin');
    $this->description = $this->trans('Manage featured category cards with image/video media.', [], 'Modules.Pzfeaturedcategories.Admin');
    
    $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
  }

  public function install()
  {
    if (Shop::isFeatureActive()) {
      Shop::setContext(Shop::CONTEXT_ALL);
    }
  
    if (!parent::install()) {
      return false;
    }

    if (!$this->createTables()) {
      $this->uninstall();

      return false;
    }

    if (!$this->installAdminTab()) {
      $this->uninstall();

      return false;
    }

    $hooksRegistered = $this->registerHook('displayHome')
      && $this->registerHook('displayPZFeaturedCategories')
      && $this->registerHook('actionObjectCategoryAddAfter')
      && $this->registerHook('actionObjectCategoryUpdateAfter')
      && $this->registerHook('actionObjectCategoryDeleteAfter');

    if (!$hooksRegistered) {
      $this->uninstall();

      return false;
    }

    return true;
  }

  public function uninstall()
  {
    if (!$this->uninstallAdminTab()) {
      return false;
    }

    if (!$this->dropTables()) {
      return false;
    }

    return parent::uninstall();
  }

  public function isUsingNewTranslationSystem()
  {
    return true;
  }

  public function getContent()
  {
    $router = SymfonyContainer::getInstance()->get('router');

    Tools::redirectAdmin(
      $router->generate('admin_pz_featured_categories_index')
    );
  }

  public function renderWidget($hookName, array $configuration)
  {
    $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
    
    return $this->fetch('module:' . $this->name . '/views/templates/hook/widget.tpl');
  }

  public function getWidgetVariables($hookName, array $configuration)
  {
    return [];
  }

  private function createTables()
  {
    try {
      return (bool) $this->getRepository()->createTables();
    } catch (\Throwable $e) {
      $this->_errors[] = $this->trans('Could not create module tables.', [], 'Modules.Pzfeaturedcategories.Admin');

      return false;
    }
  }

  private function dropTables()
  {
    try {
      return (bool) $this->getRepository()->dropTables();
    } catch (\Throwable $e) {
      $this->_errors[] = $this->trans('Could not remove module tables.', [], 'Modules.Pzfeaturedcategories.Admin');

      return false;
    }
  }

  private function getRepository()
  {
    if (null === $this->repository) {
      try {
        $this->repository = $this->get(FeaturedCategoryRepository::class);
      } catch (\Throwable $e) {
        $this->repository = SymfonyContainer::getInstance()->get(FeaturedCategoryRepository::class);
      }
    }

    return $this->repository;
  }

  private function installAdminTab()
  {
    $className = 'AdminPzFeaturedCategories';
    $tabId = (int) Tab::getIdFromClassName($className);
    $tab = new Tab($tabId ?: null);

    $tab->active = true;
    $tab->class_name = $className;
    $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentCatalog');
    $tab->route_name = 'admin_pz_featured_categories_index';
    $tab->module = $this->name;
    $tab->wording = 'Featured Categories';
    $tab->wording_domain = 'Modules.Pzfeaturedcategories.Admin';

    foreach (Language::getLanguages() as $lang) {
      $locale = isset($lang['locale']) ? $lang['locale'] : null;
      $tab->name[(int) $lang['id_lang']] = $this->trans(
        'Featured Categories',
        [],
        'Modules.Pzfeaturedcategories.Admin',
        $locale
      );
    }

    return (bool) $tab->save();
  }

  private function uninstallAdminTab()
  {
    $tabId = (int) Tab::getIdFromClassName('AdminPzFeaturedCategories');
    if ($tabId <= 0) {
      return true;
    }

    $tab = new Tab($tabId);

    return (bool) $tab->delete();
  }
}
