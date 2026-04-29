<?php

/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2026
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class glogin extends Module
{
    public $mkey;
    public $mypresta_link;
    public $modulehooks;

    public function __construct()
    {
        $this->name = 'glogin';
        $this->tab = 'front_office_features';
        $this->version = '1.7.6';
        $this->author = 'MyPresta.eu';
        $this->bootstrap = true;
        $this->mypresta_link = 'https://mypresta.eu/modules/social-networks/google-login-connect.html';
        $this->module_key = 'c07f29413466a4d22d5621252f0c87db';
        parent::__construct();
        $this->displayName = $this->l('Google Login');
        $this->description = $this->l('With this module you can allow your customers to register & login with their google account.');

        $this->modulehooks['top']['ps14'] = 1;
        $this->modulehooks['top']['ps15'] = 1;
        $this->modulehooks['top']['ps16'] = 1;
        $this->modulehooks['top']['ps17'] = 1;
        $this->modulehooks['top']['ps80'] = 1;
        $this->modulehooks['footer']['ps14'] = 1;
        $this->modulehooks['footer']['ps15'] = 1;
        $this->modulehooks['footer']['ps16'] = 1;
        $this->modulehooks['footer']['ps17'] = 1;
        $this->modulehooks['footer']['ps80'] = 1;
        $this->modulehooks['displaytopColumn']['ps14'] = 0;
        $this->modulehooks['displaytopColumn']['ps15'] = 0;
        $this->modulehooks['displaytopColumn']['ps16'] = 1;
        $this->modulehooks['displaytopColumn']['ps17'] = 0;
        $this->modulehooks['displaytopColumn']['ps80'] = 0;
        $this->modulehooks['displayNav']['ps14'] = 0;
        $this->modulehooks['displayNav']['ps15'] = 0;
        $this->modulehooks['displayNav']['ps16'] = 1;
        $this->modulehooks['displayNav']['ps17'] = 0;
        $this->modulehooks['displayNav']['ps80'] = 0;
        $this->modulehooks['displayNav1']['ps14'] = 0;
        $this->modulehooks['displayNav1']['ps15'] = 0;
        $this->modulehooks['displayNav1']['ps16'] = 0;
        $this->modulehooks['displayNav1']['ps17'] = 1;
        $this->modulehooks['displayNav1']['ps80'] = 1;
        $this->modulehooks['displayNav2']['ps14'] = 0;
        $this->modulehooks['displayNav2']['ps15'] = 0;
        $this->modulehooks['displayNav2']['ps16'] = 0;
        $this->modulehooks['displayNav2']['ps17'] = 1;
        $this->modulehooks['displayNav2']['ps80'] = 1;
        $this->modulehooks['shoppingCart']['ps14'] = 1;
        $this->modulehooks['shoppingCart']['ps15'] = 1;
        $this->modulehooks['shoppingCart']['ps16'] = 1;
        $this->modulehooks['shoppingCart']['ps17'] = 1;
        $this->modulehooks['shoppingCart']['ps80'] = 1;
        $this->modulehooks['glogin']['ps14'] = 1;
        $this->modulehooks['glogin']['ps15'] = 1;
        $this->modulehooks['glogin']['ps16'] = 1;
        $this->modulehooks['glogin']['ps17'] = 1;
        $this->modulehooks['glogin']['ps80'] = 1;
        $this->modulehooks['leftcolumn']['ps14'] = 1;
        $this->modulehooks['leftcolumn']['ps15'] = 1;
        $this->modulehooks['leftcolumn']['ps16'] = 1;
        $this->modulehooks['leftcolumn']['ps17'] = 1;
        $this->modulehooks['leftcolumn']['ps80'] = 1;
        $this->modulehooks['rightcolumn']['ps14'] = 1;
        $this->modulehooks['rightcolumn']['ps15'] = 1;
        $this->modulehooks['rightcolumn']['ps16'] = 1;
        $this->modulehooks['rightcolumn']['ps17'] = 1;
        $this->modulehooks['rightcolumn']['ps80'] = 1;
        $this->modulehooks['home']['ps14'] = 1;
        $this->modulehooks['home']['ps15'] = 1;
        $this->modulehooks['home']['ps16'] = 1;
        $this->modulehooks['home']['ps17'] = 1;
        $this->modulehooks['home']['ps80'] = 1;
        $this->modulehooks['displayCustomerLoginFormAfter']['ps14'] = 0;
        $this->modulehooks['displayCustomerLoginFormAfter']['ps15'] = 0;
        $this->modulehooks['displayCustomerLoginFormAfter']['ps16'] = 0;
        $this->modulehooks['displayCustomerLoginFormAfter']['ps17'] = 1;
        $this->modulehooks['displayCustomerLoginFormAfter']['ps80'] = 1;
        $this->modulehooks['shoppingCartExtra']['ps14'] = 1;
        $this->modulehooks['shoppingCartExtra']['ps15'] = 1;
        $this->modulehooks['shoppingCartExtra']['ps16'] = 1;
        $this->modulehooks['shoppingCartExtra']['ps17'] = 1;
        $this->modulehooks['shoppingCartExtra']['ps80'] = 1;
        $this->modulehooks['createAccountForm']['ps14'] = 1;
        $this->modulehooks['createAccountForm']['ps15'] = 1;
        $this->modulehooks['createAccountForm']['ps16'] = 1;
        $this->modulehooks['createAccountForm']['ps17'] = 1;
        $this->modulehooks['createAccountForm']['ps80'] = 1;
        $this->modulehooks['createAccountTop']['ps14'] = 1;
        $this->modulehooks['createAccountTop']['ps15'] = 1;
        $this->modulehooks['createAccountTop']['ps16'] = 1;
        $this->modulehooks['createAccountTop']['ps17'] = 1;
        $this->modulehooks['createAccountTop']['ps80'] = 1;
        $this->modulehooks['popuplogin']['ps14'] = 0;
        $this->modulehooks['popuplogin']['ps15'] = 0;
        $this->modulehooks['popuplogin']['ps16'] = 1;
        $this->modulehooks['popuplogin']['ps17'] = 1;
        $this->modulehooks['popuplogin']['ps80'] = 1;
        $this->modulehooks['displayPersonalInformationTop']['ps14'] = 0;
        $this->modulehooks['displayPersonalInformationTop']['ps15'] = 0;
        $this->modulehooks['displayPersonalInformationTop']['ps16'] = 0;
        $this->modulehooks['displayPersonalInformationTop']['ps17'] = 1;
        $this->modulehooks['displayPersonalInformationTop']['ps80'] = 1;
        $this->checkforupdates();
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 16 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 || $this->psversion(0) >= 8 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = gloginUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (gloginUpdate::version($this->version) < gloginUpdate::version(Configuration::get('updatev_' . $this->name)) && Tools::getValue('ajax', 'false') == 'false') {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                        $this->warning = $this->context->controller->warnings[0];
                    }
                } else {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = gloginUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                }
                if ($display_msg == 1) {
                    if (gloginUpdate::version($this->version) < gloginUpdate::version(gloginUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp = $explode = explode(".", $version);
        if ($part == 0) {
            return $exp[0];
        }
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    public function install()
    {
        $prefix = _DB_PREFIX_;
        $sql = array();
        $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'glogin` (
                  `id_fblogin` int(10) unsigned NOT NULL AUTO_INCREMENT,
                  `id_social` VARCHAR(20) NOT NULL,
                  `type` VARCHAR(10) NOT NULL,
                  `hash` VARCHAR(100) NOT NULL,
                  `id_customer` INT(10) UNSIGNED NOT NULL,
                  PRIMARY KEY (`id_fblogin`),
                  UNIQUE  `id_fblogin_unique` (`id_fblogin`)
                 ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

        if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('dashboardZoneOne') || !$this->runSql($sql) || !$this->installModuleHooks() || !Configuration::updateValue('glogin_groupid', Configuration::get('PS_CUSTOMER_GROUP'))) {
            return false;
        }
        return true;
    }

    public function installModuleHooks()
    {
        foreach ($this->modulehooks AS $modulehook => $value) {
            if (($this->psversion() == 4 && $value['ps14'] == 1) || ($this->psversion() == 5 && $value['ps15'] == 1) || ($this->psversion() == 6 && $value['ps16'] == 1) || ($this->psversion() == 7 && $value['ps17'] == 1) || ($this->psversion(0) >= 8 && $value['ps80'] == 1)) {
                if ($this->registerHook($modulehook) == false) {
                    return false;
                }
            }
        }
        return true;
    }

    public function runSql($sql)
    {
        foreach ($sql as $s) {
            if (!Db::getInstance()->Execute($s)) {
                //return FALSE;
            }
        }
        return true;
    }

    public function verifycustomer($post)
    {

        if (isset($post['email'])) {
            if ($this->check_core($post['email']) == false) {
                if ($this->check_db($post['id']) == false) {
                    $this->loginprocess($this->registeraccount_db($post, $this->registeraccount_core($post), ""));
                    $this->update_glogin_db($post, $this->check_core($post['email']));
                } else {
                    $this->loginprocess($this->registeraccount_core($post));
                    $this->update_glogin_db($post, $this->check_core($post['email']));
                }
            } else {
                if ($this->check_db($post['id']) == false) {
                    $this->loginprocess($this->registeraccount_db($post, $this->check_core($post['email']), ""));
                    $this->update_glogin_db($post, $this->check_core($post['email']));
                } else {
                    $this->loginprocess($this->check_core($post['email']));
                    $this->update_glogin_db($post, $this->check_core($post['email']));
                }
            }
        } elseif (isset($post['id'])) {
            $post['email'] = $post['id'] . "@plus.google.com";
            //echo "alert('no email address');";
            if ($this->check_core($post['email']) == false) {
                if ($this->check_db($post['id']) == false) {
                    $this->loginprocess($this->registeraccount_db($post, $this->registeraccount_core($post), ""));
                } else {
                    $this->loginprocess($this->registeraccount_core($post));
                }
            } else {
                if ($this->check_db($post['id']) == false) {
                    $this->loginprocess($this->registeraccount_db($post, $this->check_core($post['email']), ""));
                } else {
                    $this->loginprocess($this->check_core($post['email']));
                }
            }
        }
    }

    public function check_core($email)
    {
        $customer = new Customer();
        $cust = $customer->getByEmail($email);
        if ($cust == false) {
            return false;
        } else {
            return $cust->id;
        }
    }

    public function check_db($fbid)
    {
        return Db::getInstance()->getRow('SELECT id_customer FROM `' . _DB_PREFIX_ . 'glogin` WHERE id_social="' . $fbid . '"');
    }

    public function registeraccount_db($post, $id_customer, $passwd)
    {
        Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'glogin` (`id_social`,`type`,`hash`,`id_customer`) VALUES ("' . $post['id'] . '","gg","' . $passwd . '","' . $id_customer . '")');
        return $id_customer;
    }

    public function update_glogin_db($post, $id_customer)
    {
        Db::getInstance()->Execute('UPDATE `' . _DB_PREFIX_ . 'glogin` SET `id_customer`=' . $id_customer . ' WHERE `id_social`=' . $post['id']);
    }

    public function registeraccount_core($post)
    {
        $passwd = md5(Tools::passwdGen(8));
        $customer = new Customer();
        $customer->passwd = $passwd;
        $customer->email = $post['email'];
        $customer->firstname = $post['given_name'];
        $customer->lastname = $post['family_name'];
        $customer->active = 1;
        $customer->newsletter = 0;
        $customer->optin = 0;

        if (isset($post['gender'])) {
            if ($post['gender'] == "male") {
                $customer->id_gender = 1;
            } elseif ($post['gender'] == "female") {
                $customer->id_gender = 2;
            }
        }

        if ($customer->add()) {
            $_POST['email'] = $post['email'];
            if ($this->psversion() != 4 && $this->psversion() != 3) {
                Hook::Exec('actionCustomerAccountAdd', array(
                    'newCustomer' => $customer,
                    'glogin' => true
                ));
            } elseif ($this->psversion() == 5) {
                Module::hookExec('actionCustomerAccountAdd', array(
                    'newCustomer' => $customer,
                    'glogin' => true
                ));
            } else {
                Hook::Exec('actionCustomerAccountAdd', array(
                    '_POST' => $_POST,
                    'glogin' => true,
                    'newCustomer' => $customer
                ));
            }
        }
        $customer->cleanGroups();
        $customer->addGroups(array((int)Configuration::get('glogin_groupid')));

        if (Configuration::get('PS_CUSTOMER_CREATION_EMAIL') && Configuration::get('glogin_semail') == 1) {
            Mail::Send($this->context->language->id, 'account', Mail::l('Welcome!'), array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => $passwd
            ), $customer->email, $customer->firstname . ' ' . $customer->lastname);
        }

        return $customer->id;
    }

    public function loginprocess($id_customer)
    {
        $customer = new Customer($id_customer);
        if ($this->psversion() != 7 && $this->psversion(0) != 8 && $this->psversion(0) != 9) {
            $this->context->cookie->id_compare = isset($this->context->cookie->id_compare) ? $this->context->cookie->id_compare : CompareProduct::getIdCompareByIdCustomer($customer->id);
        }

        $this->context->cookie->id_customer = (int)($customer->id);
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->logged = 1;
        $customer->logged = 1;
        $this->context->cookie->is_guest = $customer->isGuest();
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->email = $customer->email;

        // Add customer to the context
        $this->context->customer = $customer;

        if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int)Cart::lastNoneOrderedCart($this->context->customer->id)) {
            $this->context->cart = new Cart($id_cart);
        } else {
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = Address::getFirstCustomerAddressId((int)($customer->id));
            $this->context->cart->id_address_invoice = Address::getFirstCustomerAddressId((int)($customer->id));
        }
        $this->context->cart->id_customer = (int)$customer->id;
        $this->context->cart->secure_key = $customer->secure_key;
        $this->context->cart->save();
        $this->context->cookie->id_cart = (int)$this->context->cart->id;
        $this->context->cookie->write();
        $this->context->cart->autosetProductAddress();

        if ($this->psversion() == 7 || $this->psversion(0) >= 8) {
            $this->context->updateCustomer($customer);
        }
        Hook::exec('actionAuthentication');


        // Login information have changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);
        if ($this->psversion() == 5 || $this->psversion() == 6 || $this->psversion() == 7 || $this->psversion(0) >= 8) {
            if (Tools::getValue('back')) {
                if (Tools::getValue('back') != 0 && Tools::getValue('back') != '') {
                    if (Validate::isUrl(Tools::getValue('back'))) {
                        echo "top.location.href='" . (Tools::getValue('back')) . "'";
                    } else {
                        echo "top.location.href='" . 'index.php?controller=' . (Tools::getValue('back')) . "'";
                    }
                } else {
                    echo 'top.location.reload();';
                }
            } else {
                echo 'top.location.reload();';
            }
        } else {
            echo 'top.location.reload();';
        }
    }

    public function getContent()
    {
        $output = "";
        if (Tools::isSubmit('hooks_settings')) {
            Configuration::updateValue('ggl_popuplogin', Tools::getValue('ggl_popuplogin'));
        }

        if (Tools::isSubmit('fblapp_settings')) {
            Configuration::updateValue('ggl_appid', Tools::getValue('ggl_appid'));
            Configuration::updateValue('glogin_semail', Tools::getValue('glogin_semail'));
            Configuration::updateValue('glogin_identity', Tools::getValue('glogin_identity'));
            Configuration::updateValue('glogin_button_style', Tools::getValue('glogin_button_style'));
        }

        if (Tools::isSubmit('register_settings')) {
            Configuration::updateValue('glogin_groupid', Tools::getValue('glogin_groupid'));
        }


        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        if (Hook::getIdByName(preg_replace("/[^\da-z]/i", '', trim('popuplogin'))) == true) {
            $popuplogin = '';
        } else {
            $popuplogin = '<tr>
                        <td style="width:150px">' . $this->l('popup login module is not installed, hook doesnt exist') . '</td>
        				<td>' . $this->l('PopUp login') . '</td>
                    </tr>';
        }

        $checkbox_options = '';
        foreach ($this->modulehooks AS $modulehook => $value) {
            if (Tools::getValue('hooks_settings', 'false') != 'false') {
                if (Tools::getValue('ggl_' . $modulehook, 'false') != 'false') {
                    if (Tools::getValue('ggl_' . $modulehook) == 1) {
                        Configuration::updateValue('ggl_' . $modulehook, 1);
                    } else {
                        Configuration::updateValue('ggl_' . $modulehook, 0);
                    }
                } else {
                    Configuration::updateValue('ggl_' . $modulehook, 0);
                }
            }
            if (($this->psversion() == 4 && $value['ps14'] == 1) || ($this->psversion() == 5 && $value['ps15'] == 1) || ($this->psversion() == 6 && $value['ps16'] == 1) || ($this->psversion() == 7 && $value['ps17'] == 1) || ($this->psversion(0) >= 8 && $value['ps80'] == 1)) {
                $checkbox_options .= '<tr><td style="width:150px">' . "<input type=\"checkbox\" value=\"1\" name=\"ggl_$modulehook\" " . (Configuration::get('ggl_' . $modulehook) == 1 ? 'checked' : '') . "> </td><td>" . $modulehook . "</td></tr>";
            }
        }

        return '
                    <div class="panel">
                        <form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post">
                            <div class="panel-heading"><i class="icon-wrench"></i>' . $this->l('Google Client ID') . '</div>

                            <label>' . $this->l('Google Client ID') . '</label>
                    		<div class="margin-form">
                    		    <input type="text" name="ggl_appid" value="' . Configuration::get('ggl_appid') . '"/>
                    		</div>
                    		<br/>
                    		<div class="alert alert-info">
                                ' . $this->l('Define the Google API client ID. You can find the guide here:') . '
                                <a target="_blank" href="http://mypresta.eu/en/art/basic-tutorials/google-client-id.html"><strong>' . $this->l('how to create Google Client ID') . '</strong></a>
                            </div>
                    		<br/><br/>
                           <div style="clear:both; margin-bottom:20px;">
                                    <label>' . $this->l('Send email with account register confirmation:') . '</label>
                    			    <div class="margin-form">
                                    <select name="glogin_semail">
                                        <option>' . $this->l('-- SELECT --') . '</option>
                                        <option value="1" ' . (Configuration::get('glogin_semail') == 1 ? 'selected' : '') . '>' . $this->l('Yes') . '</option>
                                        <option value="0" ' . (Configuration::get('glogin_semail') != 1 ? 'selected' : '') . '>' . $this->l('No') . '</option>
                                    </select>
                    				</div>
                                </div>
                                    <div class="bootstrap clearfix">
                                        <div class="alert alert-info" style="text-align:left">
                                            ' . $this->l('Google login module creates an account in shop and user after register can receive email with account details (see option above)') . ' <br />
                                            ' . $this->l('Customer will receive email and password information. And it will be possible to log in to shop with google login as well as with email and password') . ' <br />
                                        </div>
                                    </div><br/>
                                <div style="clear:both; margin-bottom:20px;">
                                    <label>' . $this->l('Do not allow to alter password') . '</label>
                    			    <div class="margin-form">
                                    <select name="glogin_identity">
                                        <option>' . $this->l('-- SELECT --') . '</option>
                                        <option value="1" ' . (Configuration::get('glogin_identity') == 1 ? 'selected' : '') . '>' . $this->l('Yes') . '</option>
                                        <option value="0" ' . (Configuration::get('glogin_identity') != 1 ? 'selected' : '') . '>' . $this->l('No') . '</option>
                                    </select><br/>
                                    <div class="alert alert-info" style="text-align:left">
                                    ' . $this->l('This option - when enabled will hide password change feature in customer account details section (my personal information)') . ' <br />
                                    ' . $this->l('Please note that this option can be active only if previous option is set to "NO".') . ' <br />
                                    </div>
                    				</div>
                                </div>
                                <div style="clear:both; margin-bottom:20px;">
                                    <label>' . $this->l('Button style') . '</label>
                    			    <div class="margin-form">
                                    <select name="glogin_button_style">
                                        <option>' . $this->l('-- SELECT --') . '</option>
                                        <option value="1" ' . (Configuration::get('glogin_button_style') == 1 ? 'selected' : '') . '>' . $this->l('Large') . '</option>
                                        <option value="0" ' . (Configuration::get('glogin_button_style') != 1 ? 'selected' : '') . '>' . $this->l('Small') . '</option>
                                    </select><br/>
                                    <div class="alert alert-info" style="text-align:left">
                                    ' . $this->l('This option gives possibility to decide about button style that module will use. Below you can find examples of button design. If you will send app for google verification purposes - they will requiure "large" design during the app verification process.') . ' <br />
                                    <table stylw="width:100%" class="table table-bordered">
                                    <tr >
                                        <th style="text-align:center;">'.$this->l('small').'</th>
                                        <th style="text-align:center;">'.$this->l('large').'</th>
                                    </tr>
                                    <tr>
                                        <td><img src="'.Context::getContext()->shop->getBaseURL().'/modules/glogin/img/button-small.jpg"/></td>
                                        <td><img src="'.Context::getContext()->shop->getBaseURL().'/modules/glogin/img/button-large.jpg"/></td>
                                    </tr>
                                    </table>
                                    </div>
                    				</div>
                                </div>
                            <div class="panel-footer">
                                <button class="btn btn-default pull-right" type="submit" name="fblapp_settings" value="1" class="button" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '</button>
                            </div>
                        </form>
                    </div>


                    <div class="panel">
                        <form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post">
                                <div class="panel-heading"><i class="icon-wrench"></i>' . $this->l('Display login button in:') . '</div>
                                <div class="alert alert-info">
                                ' . $this->l('Select position where the module will display button to Log in+') . '
                                </div>
                                <table class="table table-bordered">
                                <tr>
                                <th>' . $this->l('Enabled') . '</th>
                                <th>' . $this->l('Position') . '</th>
                                </tr>
                                ' . $checkbox_options . $popuplogin . '
                                </table>
                				<div class="panel-footer">
                				    <button type="submit" name="hooks_settings" value="1" class="btn btn-default pull-right" />
                				    <i class="process-icon-save"></i>
                				    ' . $this->l('Save') . '
                				    </button>
                				</div>
                        </form>
                    </div>

                    <div class="panel">
                        <form action="' . Tools::safeOutput($_SERVER['REQUEST_URI']) . '" method="post">
                        <div class="panel-heading"><i class="icon-wrench"></i>' . $this->l('Register settings:') . '</div>
                        <label>' . $this->l('Associate customer with group:') . '</label>
                    	<div class="margin-form">
                            <select name="glogin_groupid">
                            <option>' . $this->l('-- SELECT --') . '</option>
                            ' . $this->customerGroup(Configuration::get('glogin_groupid')) . '
                            </select>
                        </div>
                        <div class="panel-footer">
                            <button type="submit" name="register_settings" value="1" class="btn btn-default pull-right" />
                            <i class="process-icon-save"></i>
                            ' . $this->l('Save') . '
                            </button>
                		</div>
                        </form>
                    </div>

                    <div class="panel">
                        <h3>' . $this->l('Accounts created with Google Login:') . '</h3>
                        <table class="table table-bordered" style="width:100%;">
                        <tr>
                            <th>' . $this->l('Name') . '</th>
                            <th>' . $this->l('Email') . '</th>
                            <th>' . $this->l('Register date') . '</th>
                        </tr>
                        ' . $this->displayListOfAccounts() . '
                        </table>
                   </div>' . $this->checkforupdates(0, 1);
    }

    public function customerGroup($group_id)
    {
        global $cookie;
        $return = '';
        foreach (Group::getGroups($cookie->id_lang, $id_shop = false) as $key => $value) {
            $return .= '<option ' . ($group_id == $value['id_group'] ? 'selected="yes"' : '') . ' value="' . $value['id_group'] . '">' . $value['name'] . '</option>';
        }
        return $return;
    }

    public function check_db_id_customer($id)
    {
        $entry = Db::getInstance()->getRow('SELECT id_customer FROM `' . _DB_PREFIX_ . 'glogin` WHERE id_customer="' . $id . '"');
        if (isset($entry['id_customer'])) {
            return true;
        } else {
            return false;
        }
    }

    public function hookdisplayHeader($params)
    {
        if (Tools::getValue('controller', 'false') != 'false') {

            if (Tools::getValue('controller') == "identity" && Configuration::get('glogin_identity') == 1 && Configuration::get('glogin_semail') != 1) {
                if (isset($this->context->cookie)) {
                    if (isset($this->context->cookie->id_customer)) {
                        if ($this->check_db_id_customer($this->context->cookie->id_customer)) {
                            $passwd = Tools::passwdGen(16, Tools::PASSWORDGEN_FLAG_RANDOM);
                            $customer = new Customer($this->context->cookie->id_customer);
                            $customer->passwd = Tools::encrypt($passwd);
                            $this->context->cookie->passwd = Tools::encrypt($passwd);
                            $this->context->cookie->write();
                            $customer->update();
                            if ($this->psversion() == 7 || $this->psversion(0) >= 8) {
                                $this->context->updateCustomer($customer);
                            }
                            $this->context->smarty->assign('passwd', $passwd);
                        }
                    }
                }
            }
        }

        $logged = $this->context->customer->isLogged();
        $this->context->smarty->assign('logged', $logged);
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        $this->context->smarty->assign('ggl_loginloader', $this->context->link->getModuleLink('glogin', 'loginloader', array('ajax' => 1)));
        $this->context->smarty->assign('ps_version', $this->psversion());
        $this->context->smarty->assign('ps_version80', $this->psversion(0));

        if ($this->psversion() == 5 || $this->psversion() == 6) {
            $this->context->controller->addJS(($this->_path) . 'views/js/glogin.js', 'all');
            $this->context->controller->addCSS(($this->_path) . 'views/css/glogin.css', 'all');
        } else {
            if (Configuration::get('glogin_button_style') == 1) {
                $this->context->controller->addJS(($this->_path) . 'views/js/glogin.js', 'all');
                $this->context->controller->addCSS(($this->_path) . 'views/css/glogin17-new.css', 'all');
            } else {
                $this->context->controller->addJS(($this->_path) . 'views/js/glogin.js', 'all');
                $this->context->controller->addCSS(($this->_path) . 'views/css/glogin17.css', 'all');
            }
        }
        return $this->display(__FILE__, 'views/templates/header.tpl');
    }

    public function hookdisplayFooter($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        $this->context->smarty->assign('fb_psver', $this->psversion());
        $this->context->smarty->assign('fb_psver80', $this->psversion(0));
        return $this->display(__FILE__, 'views/templates/footer.tpl');
    }

    public function hookdisplayPersonalInformationTop($params)
    {
        if (Configuration::get('ggl_displayPersonalInformationTop') == 1) {
            $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
            return $this->display(__FILE__, 'views/templates/top.tpl');
        }
    }

    public function hookFooter($params)
    {
        return $this->hookdisplayFooter($params);
    }

    public function hookdisplayTop($params)
    {
        return $this->hookTop($params);
    }

    public function hookTop($params)
    {
        if (Configuration::get('ggl_top') == 1) {
            $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
            return $this->display(__FILE__, 'views/templates/top.tpl');
        }
    }

    public function hookdisplaytopColumn($params)
    {
        if (Configuration::get('ggl_top') == 1) {
            $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
            return $this->display(__FILE__, 'views/templates/topColumn.tpl');
        }
    }

    public function hookdisplayNav($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_displayNav') == 1) {
            return $this->display(__FILE__, 'views/templates/displayNav.tpl');
        }
    }

    public function hookdisplayNav1($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_displayNav1') == 1) {
            return $this->display(__FILE__, 'views/templates/displayNav1.tpl');
        }
    }

    public function hookdisplayNav2($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_displayNav2') == 1) {
            return $this->display(__FILE__, 'views/templates/displayNav2.tpl');
        }
    }

    public function hookshoppingCart($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_shoppingCart') == 1) {
            return $this->display(__FILE__, 'views/templates/shoppingCart.tpl');
        }
    }

    public function hookglogin($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_glogin') == 1) {
            return $this->display(__FILE__, 'views/templates/glogin.tpl');
        }
    }

    public function hookleftcolumn($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_leftcolumn') == 1) {
            return $this->display(__FILE__, 'views/templates/leftcolumn.tpl');
        }
    }

    public function hookrightcolumn($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_rightoclumn') == 1) {
            return $this->display(__FILE__, 'views/templates/rightoclumn.tpl');
        }
    }

    public function hookhome($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_home') == 1) {
            return $this->display(__FILE__, 'views/templates/home.tpl');
        }
    }

    public function hookdisplayCustomerLoginFormAfter($params)
    {

        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_displayCustomerLoginFormAfter') == 1) {
            return $this->display(__FILE__, 'views/templates/displayCustomerLoginFormAfter.tpl');
        }
    }

    public function hookshoppingCartExtra($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_shoppingCartExtra') == 1) {
            return $this->display(__FILE__, 'views/templates/shoppingCartExtra.tpl');
        }
    }

    public function hookcreateAccountForm($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_createAccountForm') == 1) {
            return $this->display(__FILE__, 'views/templates/createAccountForm.tpl');
        }
    }

    public function hookCreateAccountTop($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        if (Configuration::get('ggl_createAccountTop') == 1) {
            return $this->display(__FILE__, 'views/templates/createAccountTop.tpl');
        }
    }

    public function hookpopuplogin($params)
    {
        $this->context->smarty->assign('ggl_appid', Configuration::get('ggl_appid'));
        $this->context->smarty->assign('ggl_loginloader', $this->context->link->getModuleLink('glogin', 'loginloader', array('ajax' => 1)));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/displayPopUpLogin.tpl');

    }

    public function hookdashboardZoneOne($params)
    {
        $this->context->smarty->assign('last_accounts', $this->displayListOfAccountsLast(10));
        $this->context->smarty->assign('nb_of_fb_accounts', $this->nbOfAccounts());
        $this->checkforupdates(0, 0);
        $this->context->smarty->assign('update_availablility', (isset($this->warning) ? $this->warning : false));
        return $this->display(__FILE__, 'views/templates/dashboardZoneOne.tpl');
    }

    public function nbOfAccounts()
    {
        $accounts = Db::getInstance()->ExecuteS('SELECT count(*) as counter FROM `' . _DB_PREFIX_ . 'glogin`');
        return (isset($accounts[0]['counter']) ? $accounts[0]['counter'] : 0);
    }

    public function displayListOfAccountsLast($nb)
    {
        $accounts = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'glogin` ORDER BY id_social DESC LIMIT ' . $nb);
        $return = '';
        foreach ($accounts AS $account => $value) {
            $customer = new Customer($value['id_customer']);
            if (isset($customer->email)) {
                $return .= "
                <tr>
                    <td>" . $customer->firstname . ' ' . $customer->lastname . "</td>
                </tr>";
            }
        }
        return $return;
    }

    public function displayListOfAccounts()
    {
        $accounts = Db::getInstance()->ExecuteS('SELECT * FROM `' . _DB_PREFIX_ . 'glogin` ORDER BY id_social DESC');
        $return = '';
        foreach ($accounts AS $account => $value) {
            $customer = new Customer($value['id_customer']);
            if (isset($customer->email)) {
                $return .= "
            <tr>
                <td>" . $customer->firstname . ' ' . $customer->lastname . "</td>
                <td>" . $customer->email . "</td>
                <td>" . $customer->date_add . "</td>
            </tr>";
            }
        }
        return $return;
    }

    public function inconsistency($ret)
    {
        return;
    }

}

class gloginUpdate extends glogin
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }
        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);
        return $actual_version;
    }
}
