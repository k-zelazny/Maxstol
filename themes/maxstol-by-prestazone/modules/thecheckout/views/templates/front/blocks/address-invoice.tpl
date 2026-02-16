{**
* NOTICE OF LICENSE
*
* This source file is subject to the Software License Agreement
* that is bundled with this package in the file LICENSE.txt.
*
*  @author    Peter Sliacky (Prestasmart)
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{if $z_tc_config->show_i_am_business}
    <style>
        {if $hideBusinessFields}
          {literal}
            #thecheckout-address-invoice .form-group.business-field:not(.need-dni) {
              display: none;
            }
          {/literal}
        {else}
          {literal}
            #thecheckout-address-invoice .form-group.business-disabled-field {
              display: none;
            }
            #thecheckout-address-invoice .business-private-checkboxes {
              display: none;
            }
          {/literal}
        {/if}
    </style>
{/if}
{if $z_tc_config->show_i_am_private}
  <style>
    {if $hidePrivateFields}
      {literal}
        #thecheckout-address-invoice .form-group.private-field:not(.need-dni):not(.business-field) {
          display: none;
        }
      {/literal}
    {else}
      {literal}
        #thecheckout-address-invoice .private-fields-separator {
          display: block;
        }
      {/literal}
    {/if}
  </style>
{/if}

<div class="inner-wrapper" data-address-id="{$address_invoice_id|escape:'javascript':'UTF-8'}" data-address-is-used="{if $z_tc_config->restrict_editing_used_address}{$address_invoice_is_used|escape:'javascript':'UTF-8'}{else}0{/if}">
  {if $z_tc_config->show_i_am_business || $z_tc_config->show_i_am_private}
    <div class="business-private-checkboxes form-group">
      {if $z_tc_config->show_i_am_business}
          <div class="business-customer">
          <span class="custom-checkbox">
            <input id="i_am_business" type="checkbox" data-link-action="x-i-am-business" {if !$hideBusinessFields}checked="checked"{/if} disabled="disabled">
            <span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
            <label for="i_am_business" class="btn-tertiary">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11.9777 6.37768H7.62212V2.02212C7.62212 1.8571 7.55657 1.69884 7.43988 1.58215C7.32319 1.46546 7.16493 1.3999 6.9999 1.3999C6.83488 1.3999 6.67661 1.46546 6.55992 1.58215C6.44324 1.69884 6.37768 1.8571 6.37768 2.02212V6.37768H2.02212C1.8571 6.37768 1.69884 6.44324 1.58215 6.55992C1.46546 6.67661 1.3999 6.83488 1.3999 6.9999C1.3999 7.16493 1.46546 7.32319 1.58215 7.43988C1.69884 7.55657 1.8571 7.62212 2.02212 7.62212H6.37768V11.9777C6.37768 12.1427 6.44324 12.301 6.55992 12.4177C6.67661 12.5343 6.83488 12.5999 6.9999 12.5999C7.16493 12.5999 7.32319 12.5343 7.43988 12.4177C7.55657 12.301 7.62212 12.1427 7.62212 11.9777V7.62212H11.9777C12.1427 7.62212 12.301 7.55657 12.4177 7.43988C12.5343 7.32319 12.5999 7.16493 12.5999 6.9999C12.5999 6.83488 12.5343 6.67661 12.4177 6.55992C12.301 6.44324 12.1427 6.37768 11.9777 6.37768Z" fill="#111928"/>
              </svg>
              {l s='I want a VAT invoice' d='Maxstol.Theme.Checkout'}
            </label>
          </span>
          </div>
      {/if}
      {if $z_tc_config->show_i_am_private}
          <div class="private-customer">
          <span class="custom-checkbox">
            <input id="i_am_private" type="checkbox" data-link-action="x-i-am-private" {if !$hidePrivateFields}checked="checked"{/if} disabled="disabled">
            <span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
            <label for="i_am_private">{l s='I am a private customer' mod='thecheckout'}</label>
          </span>
          </div>
      {/if}
    </div>
  {/if}
  
  <form class="address-fields" data-address-type="invoice" id="invoice-address">    
    {block name="address_invoice_form_fields"}
        <section class="form-fields">
            {block name='form_fields'}
                {if $z_tc_config->show_i_am_business}
                    <div class="business-fields-container">
                      <div class="block-header address-invoice-header form-group business-field">{l s='Invoice details' d='Maxstol.Theme.Checkout'}</div>
                    </div>
                {/if}
                {if $z_tc_config->show_i_am_private}
                  <div class="private-fields-container"></div>
                {/if}
                {foreach from=$formFieldsInvoice item="field"}
                    {block name='form_field'}
                        {include file='module:thecheckout/views/templates/front/_partials/checkout-form-fields.tpl' checkoutSection='invoice'}
                    {/block}
                {/foreach}
            {/block}
        </section>
    {/block}
    <div class="address-is-used"><span class="asterisk">*</span>{l s='Address used in past orders' mod='thecheckout'} - <a href="{url entity=address id=$address_invoice_id}">{l s='Edit' d='Shop.Theme.Actions'}</a></div>
  </form>
  {if $isInvoiceAddressPrimary}
      <div class="second-address">
      <span class="custom-checkbox">
      <input type="checkbox" data-link-action="x-ship-to-different-address" id="ship-to-different-address"{if $showShipToDifferentAddress} checked{/if}>
      <span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
      <label for="ship-to-different-address">{l s='Ship to a different address' mod='thecheckout'}</label>
      </span>
      </div>
  {/if}
</div>
