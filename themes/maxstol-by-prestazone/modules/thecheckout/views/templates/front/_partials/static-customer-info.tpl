<div id="static-customer-info-container">
  {if !$s_customer.is_guest && $s_customer.is_logged}
  <div class="form-group email">
    <label class="has-float-label required">
      <input class="form-control orig-field" type="text" value="{$s_customer.email|escape:'htmlall':'UTF-8'}" placeholder="" disabled>
      <span class="field-label">{l s='Email' d='Shop.Theme.Label'}</span>
    </label>
  </div>
  {/if}
</div>