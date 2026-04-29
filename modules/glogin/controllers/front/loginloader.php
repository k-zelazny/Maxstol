<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2025
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

include_once(dirname(__FILE__) . '../../../glogin.php');

class gloginloginloaderModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        $thismodule = new glogin();
        $thismodule = new glogin();
        $thismodule->verifycustomer(Tools::getValue('resp'));
        die();
    }
}