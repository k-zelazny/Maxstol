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

<div class="panel" id="hookDashboardZoneOne_glogin">
    <div class="panel-heading">
        <i class="icon-puzzle-piece"></i> {l s='Google Login' mod='glogin'}
    </div>
    {if $update_availablility != NULL}
        <div class="alert alert-danger">
            {$update_availablility|replace:'http://MyPresta.eu':'<a href="https://mypresta.eu" target="blank">MyPresta.eu</a>' nofilter}
        </div>
    {/if}
    <ul class="data_list_large">
        <li>
            <span class="data_label size_l">
                {l s='Customer accounts' mod='glogin'}
                <small class="text-muted"><br>
                    {l s='Created with google login module' mod='glogin'}
                </small>
			</span>
            <span class="data_value size_xxl">
			{$nb_of_fb_accounts}
			</span>
        </li>
    </ul>
    <span class="data_label size_md">
        {l s='Last 10 accounts' mod='glogin'}
    </span>
    <table class="table table-bordered clearfix">
        {$last_accounts nofilter}
    </table>
</div>