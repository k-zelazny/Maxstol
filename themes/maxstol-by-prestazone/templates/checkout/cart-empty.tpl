{extends file='checkout/cart.tpl'}

{block name='cart_actions'}
  <div class="checkout text-sm-center card-block">
    <button type="button" class="btn btn-primary w-100 disabled" disabled>{l s='Checkout' d='Shop.Theme.Actions'}</button>
  </div>
{/block}

{block name='continue_shopping'}{/block}
{block name='cart_voucher'}{/block}
{block name='display_reassurance'}{/block}
