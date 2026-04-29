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
{literal}
    <script>
        var gglogin_appid = '{/literal}{$ggl_appid}{literal}';
        var ggl_loginloader = '{/literal}{$ggl_loginloader nofilter}{literal}';
        {/literal}
        {if Tools::getValue('back','false') != 'false'}
        var back = '{Tools::getValue('back','false')}';
        {/if}
        {literal}
        {/literal}
        {if $ps_version==7 || $ps_version80>=8}
        var baseDir = prestashop.urls.base_url;
        var baseUri = prestashop.urls.base_url;
        {/if}
        {literal}
    </script>
{/literal}
{/if}
{if isset(Context::getContext()->controller->php_self) && isset($passwd)}
    {if Context::getContext()->controller->php_self=="identity"}
    {literal}
        <script>
            document.addEventListener("DOMContentLoaded", function (event) {
                $("#old_passwd,#passwd,#confirmation, input[name='new_password'], input[name='password']").parent().parent().parent().hide();
                $("#old_passwd, input[name='new_password']").val('{/literal}{$passwd}{literal}');
                $("#passwd, input[name='password']").val('{/literal}{$passwd}{literal}');
                $("#confirmation").val('{/literal}{$passwd}{literal}');
            });
        </script>
    {/literal}
    {/if}
{/if}