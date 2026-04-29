{*
* PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
*
* @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
* @copyright 2010-2025
* @license   This program is not free software and you can't resell and redistribute it
*
* CONTACT WITH DEVELOPER http://mypresta.eu
* support@mypresta.eu
*}

{if !$logged}
    <div class="glogin customBtnTopColumn">
        <div id="customBtnTopColumn" class="customGPlusSignIn" onclick="glogin_mypresta();">
            <span class="buttonText">{if Configuration::get('glogin_button_style') == 1}{l s='Sign in with Google' mod='glogin'}{else}{l s='Log in' mod='glogin'}{/if}</span>
        </div>
    </div>
{/if}