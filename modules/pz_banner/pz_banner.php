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

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Pz_Banner extends Module implements WidgetInterface
{
  public function __construct()
  {
    $this->name = 'pz_banner';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'PrestaZone';
    $this->need_instance = 0;
    $this->bootstrap = true;
    
    parent::__construct();
    
    $this->displayName = $this->trans('Banner', [], 'Modules.Pzbanner.Admin');
    $this->description = $this->trans('...', [], 'Modules.Pzbanner.Admin');
    
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
      && $this->registerHook('displayPZBanner');
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
    return [];
  }
}
