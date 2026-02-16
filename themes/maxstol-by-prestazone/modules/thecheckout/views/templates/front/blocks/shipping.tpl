{**
* NOTICE OF LICENSE
*
* This source file is subject to the Software License Agreement
* that is bundled with this package in the file LICENSE.txt.
*
*  @author    Peter Sliacky (Prestasmart)
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{block name='shipping_options'}
  <div class="block-header shipping-method-header">{l s='Shipping methods' d='Maxstol.Theme.Checkout'}</div>
  <div class="inner-wrapper">
    <div class="error-msg">{l s='Please select a shipping method' mod='thecheckout'}</div>
    {if isset($shipping_payment_blocks_wait_for_selection) && $shipping_payment_blocks_wait_for_selection}
      <div class="dummy-block-container disallowed force-country">
        <span>{l s='Please choose delivery country to see shipping options' mod='thecheckout'}</span></div>
    {elseif isset($shipping_block_wait_for_address) && $shipping_block_wait_for_address|count}
      <div class="dummy-block-container disallowed">
        <span>{l s='First, please enter your: ' mod='thecheckout'}
          <ul>
            {foreach $shipping_block_wait_for_address as $field_name}
              <li>{$field_name|escape:'htmlall':'UTF-8'}</li>
            {/foreach}
          </ul>
        </span>
      </div>
    {elseif isset($force_email_wait_for_enter) && $force_email_wait_for_enter}
      <div class="dummy-block-container disallowed">
        <span>{l s='Please enter your email to see shipping options' mod='thecheckout'}</span></div>
    {elseif isset($wait_for_account) && $wait_for_account}
      <div class="dummy-block-container disallowed">
        <span>{l s='Please Save your Personal Info to see shipping options' mod='thecheckout'}</span></div>
    {else}
      {if isset($shippingAddressNotice) && $shippingAddressNotice}
        <div class="shipping-address-notice">{l s='Shipping Address' d='Shop.Theme.Checkout'}: <span
                  class="country-name">{$shippingAddressNotice|escape:'htmlall':'UTF-8'}</span></div>
      {/if}
      <div id="hook-display-before-carrier">
        {$hookDisplayBeforeCarrier nofilter}
      </div>
      <div class="delivery-options-list">
        {if $delivery_options|count}
          <form
                  id="js-delivery"
                  data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectDeliveryOption']}"
                  method="post"
          >
            <div class="form-fields">
              {block name='delivery_options'}
                <div class="delivery-options">
                  {foreach from=$delivery_options item=carrier key=carrier_id}
                    <div class="delivery-options__item">
                      <div class="delivery-option-row delivery-option{if isset($carrier.external_module_name) && "" != $carrier.external_module_name} {$carrier.external_module_name|escape:'javascript':'UTF-8'}{/if}{if (isset($customerSelectedDeliveryOption) && $carrier_id == $customerSelectedDeliveryOption)} user-selected{/if} carrier-ref-{$carrier.id_reference|escape:'javascript':'UTF-8'}">
                        <div class="shipping-radio">
                            <span class="custom-radio float-xs-left">
                              <input type="radio" name="delivery_option[{$id_address|escape:'javascript':'UTF-8'}]" id="delivery_option_{$carrier.id|escape:'javascript':'UTF-8'}"
                                    value="{$carrier_id|escape:'javascript':'UTF-8'}"{if $delivery_option == $carrier_id && (!$forceToChooseCarrier || (isset($customerSelectedDeliveryOption) && $carrier_id == $customerSelectedDeliveryOption))} checked{/if}>
                              <span></span>
                            </span>
                        </div>
                        <label for="delivery_option_{$carrier.id|escape:'javascript':'UTF-8'}" class="delivery-option-label delivery-option-2 {if $carrier.logo|escape:'javascript':'UTF-8'}has-logo{else}no-logo{/if}">
                          <div class="delivery-option-detail">
                            <div class="delivery-option-logo">
                              {if $carrier.id === 5}
                                <svg width="62" height="42" viewBox="0 0 62 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M55 0.625C58.1066 0.625002 60.625 3.1434 60.625 6.25V35C60.625 38.1066 58.1066 40.625 55 40.625H6.25C3.1434 40.625 0.625002 38.1066 0.625 35V6.25C0.625 3.1434 3.1434 0.625 6.25 0.625H55Z" fill="white" stroke="#D3D3D3" stroke-width="1.25"/>
                                  <path d="M28.6345 14.25L25.1372 20.022L27.7429 24.3075H25.8819L24.2381 21.5053L21.3634 26.25H57.25V14.25H28.6345Z" fill="#CE2B2D"/>
                                  <path d="M34.0524 20.6601C33.849 20.3015 33.5674 20.0184 33.2081 19.8113C32.8481 19.6046 32.2933 19.4042 31.5428 19.2104C30.792 19.017 30.3195 18.8307 30.125 18.6522C29.972 18.5116 29.8957 18.3429 29.8957 18.1452C29.8957 17.9287 29.977 17.7561 30.1404 17.6269C30.394 17.4257 30.7451 17.3251 31.1935 17.3251C31.642 17.3251 31.9537 17.419 32.1707 17.6069C32.3878 17.7948 32.5293 18.1037 32.5956 18.5328L34.1384 18.4588C34.114 17.6915 33.8594 17.078 33.3747 16.6188C32.89 16.1592 32.1682 15.9294 31.2089 15.9294C30.6218 15.9294 30.1203 16.0261 29.7052 16.2199C29.29 16.4137 28.9719 16.6956 28.7516 17.0659C28.5306 17.4362 28.4206 17.834 28.4206 18.2591C28.4206 18.92 28.6549 19.4802 29.1241 19.9398C29.4576 20.2662 30.0379 20.5415 30.8651 20.7658C31.5077 20.9404 31.9196 21.0617 32.1005 21.1302C32.3645 21.2328 32.5494 21.3534 32.6554 21.492C32.7614 21.6305 32.8145 21.7985 32.8145 21.9962C32.8145 22.3035 32.6884 22.5724 32.4365 22.8022C32.1847 23.032 31.81 23.1467 31.3132 23.1467C30.8443 23.1467 30.4714 23.0176 30.1952 22.7592C29.919 22.5012 29.7356 22.0968 29.6453 21.546L28.1441 21.7053C28.2451 22.6398 28.5542 23.3507 29.0718 23.8389C29.5898 24.3267 30.3317 24.5709 31.2978 24.5709C31.9612 24.5709 32.5157 24.4692 32.9602 24.266C33.4051 24.0628 33.749 23.7524 33.9923 23.3347C34.2355 22.9169 34.3573 22.4687 34.3573 21.9903C34.3573 21.4626 34.2555 21.0191 34.0524 20.6601Z" fill="white"/>
                                  <path d="M35.1077 17.4844H37.3752V24.4226H38.9181V17.484H41.1802V16.0716H35.1077V17.4844Z" fill="white"/>
                                  <path d="M48.0683 17.0745C47.3923 16.3111 46.4914 15.9294 45.3655 15.9294C44.7437 15.9294 44.1945 16.0394 43.7185 16.2599C43.3606 16.4231 43.0314 16.6736 42.7309 17.0119C42.4303 17.3497 42.1932 17.7314 42.0195 18.1566C41.7866 18.734 41.6702 19.4481 41.6702 20.2983C41.6702 21.6278 42.0055 22.6711 42.6761 23.4286C43.3467 24.1861 44.2501 24.5651 45.3863 24.5651C46.5226 24.5651 47.4052 24.1846 48.0761 23.4227C48.7467 22.6617 49.082 21.6067 49.082 20.2588C49.082 18.9109 48.7439 17.8379 48.0683 17.0745ZM46.8955 22.3975C46.5011 22.8817 45.9964 23.124 45.3813 23.124C44.7663 23.124 44.2587 22.8798 43.8593 22.392C43.4595 21.9042 43.26 21.187 43.26 20.2416C43.26 19.2962 43.4545 18.5621 43.8435 18.0857C44.2329 17.6089 44.7455 17.3705 45.3813 17.3705C46.0171 17.3705 46.5272 17.6061 46.9109 18.0771C47.2949 18.548 47.4869 19.2617 47.4869 20.2189C47.4869 21.176 47.2899 21.9136 46.8955 22.3975Z" fill="white"/>
                                  <path d="M51.8707 23.0156V16.1401H50.3279V24.4226H55.7068V23.0156H51.8707Z" fill="white"/>
                                  <path d="M24.1446 18.5085L26.7248 14.25H3.25V26.25H19.4538L21.3294 23.1542L23.2498 19.9499L20.8834 15.9568H22.687L24.1446 18.5085Z" fill="#1D1D1B"/>
                                  <path d="M9.8594 16.0747L8.48852 21.7711L7.10223 16.0747H4.79318V24.4253H6.2264V17.852L7.73806 24.4253H9.22357L10.7402 17.852V24.4253H12.1738V16.0747H9.8594Z" fill="white"/>
                                  <path d="M17.5374 16.0747H15.9057L12.9297 24.4253H14.5663L15.1968 22.5286H18.2513L18.9186 24.4253H20.5969L17.5374 16.0747ZM15.6711 21.1216L16.7031 18.0227L17.7562 21.1216H15.6711Z" fill="white"/>
                                  <path d="M25.1372 20.022L27.7429 24.3075H25.8819L24.2381 21.5053L21.3634 26.25H19.4538L21.3294 23.1542L23.2498 19.9499L20.8834 15.9568H22.687L24.1446 18.5085L26.7248 14.25H28.6345L25.1372 20.022Z" fill="white"/>
                                </svg>
                              {else}
                                {if $carrier.logo}
                                  <img src="{$carrier.logo|escape:'javascript':'UTF-8'}" alt="{$carrier.name|escape:'javascript':'UTF-8'}"/>
                                {/if}
                              {/if}
                            </div>
                            <div class="name-and-delay">
                              <div class="delivery-option-name">
                                <span class="h6 carrier-name">{$carrier.name|escape:'htmlall':'UTF-8'}</span>
                              </div>
                              {* Yes, this is repeated from below, so that we can control price display easily with CSS *}
                              <div class="delivery-option-price{if $carrier.price_with_tax == 0} free{/if}">
                                <span class="carrier-price">{$carrier.price|escape:'htmlall':'UTF-8'}</span>
                                <span class="carrier-price-with-tax-formatted">{$carrier.price_with_tax_formatted|escape:'htmlall':'UTF-8'}</span>
                                <span class="carrier-price-without-tax-formatted">{$carrier.price_without_tax_formatted|escape:'htmlall':'UTF-8'}</span>
                              </div>
                            </div>
                          </div>
                        </label>
                      </div>
                      {*Some themes have CSS definition: .carrier-extra-content:not(:empty) { margin-bottom: 2rem; } - so we need to keep no extra spaces here in .carrier-extra-content, if it shall be empty *}
                      <div
                              class="carrier-extra-content{if isset($carrier.external_module_name) && "" != $carrier.external_module_name} {$carrier.external_module_name|escape:'javascript':'UTF-8'}{/if}"{if $delivery_option != $carrier_id} style="display:none;"{/if}>{$carrier.extraContent nofilter}</div>
                    </div>
                  {/foreach}
                </div>
              {/block}
              {if $delivery_options|count > 1}
                <div id="expand_other_shipping_options">
                  <div class="btn btn-secondary"><span>{l s='Other shipping options' mod='thecheckout'}</span></div>
                </div>
              {/if}
              <div class="order-options">
                {if $recyclablePackAllowed}
                  <span class="custom-checkbox">
                    <input type="checkbox" id="input_recyclable" name="recyclable"
                           value="1" {if $recyclable} checked {/if}>
                    <span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
                    <label
                            for="input_recyclable">{l s='I would like to receive my order in recycled packaging.' d='Shop.Theme.Checkout'}</label>
                  </span>
                {/if}

                {if $gift.allowed}
                  <span class="custom-checkbox">
                    <input class="js-gift-checkbox" id="input_gift" name="gift" type="checkbox" value="1"
                           {if $gift.isGift}checked="checked"{/if}>
                    <span><i class="material-icons rtl-no-flip checkbox-checked check-icon">&#xE5CA;</i></span>
                    <label for="input_gift">{$gift.label|escape:'htmlall':'UTF-8'}</label>
                  </span>
                  <div id="gift" class="collapse{if $gift.isGift} in show{/if}">
                    <label
                            for="gift_message">{l s='If you\'d like, you can add a note to the gift:' d='Shop.Theme.Checkout'}</label>
                    <textarea rows="2" id="gift_message" name="gift_message">{$gift.message|escape:'htmlall':'UTF-8'}</textarea>
                  </div>
                {/if}

              </div>
            </div>
            {*<button type="submit" class="continue btn btn-primary float-xs-right" name="confirmDeliveryOption" value="1">*}
            {*{l s='Continue' d='Shop.Theme.Actions'}*}
            {*</button> *}
          </form>
        {else}
          <p
                  class="alert alert-danger">{l s='Unfortunately, there are no carriers available for your delivery address.' d='Shop.Theme.Checkout'}</p>
        {/if}
      </div>
      <div id="hook-display-after-carrier">
        {$hookDisplayAfterCarrier nofilter}
      </div>
      <div id="extra_carrier"></div>
      <input type="hidden" name="confirmDeliveryOption" />
    {/if}
  </div>
{/block}
