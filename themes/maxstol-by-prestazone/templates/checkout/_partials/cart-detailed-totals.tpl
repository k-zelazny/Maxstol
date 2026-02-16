{block name='cart_detailed_totals'}
  <div class="cart-detailed__totals card-block js-cart-detailed-totals">
    <div class="cart-detailed__subtotals js-cart-detailed-subtotals">
      <div class="cart-summary__line">
        <span class="cart-summary__label js-subtotal">
          {l s='Summary' d='Maxstol.Theme.Cart'}
        </span>
        
        <div class="cart-summary__line__value">
          <span class="cart-summary__value">
            {if $cart.subtotals.products.amount > 0}
              {$cart.subtotals.products.value}
            {else}
              {Context::getContext()->getCurrentLocale()->formatPrice(0, $currency.iso_code)}
            {/if}
          </span>
        </div>
      </div>
      
      <div class="cart-summary__line discounts">
        <span class="cart-summary__label js-subtotal">
          {l s='Discounts' d='Maxstol.Theme.Cart'}
        </span>
        
        <div class="cart-summary__line__value">
          <span class="cart-summary__value">
            {if $cart.subtotals.discounts}
              {$cart.subtotals.discounts.value}
            {else}
              {Context::getContext()->getCurrentLocale()->formatPrice(0, $currency.iso_code)}
            {/if}
          </span>
        </div>
      </div>
      
      <div class="cart-summary__line">
        <span class="cart-summary__label js-subtotal">
          {l s='Shipping' d='Maxstol.Theme.Cart'}
        </span>
        
        <div class="cart-summary__line__value">
          <span class="cart-summary__value">
            {if $cart.subtotals.shipping.amount > 0}
              {$cart.subtotals.shipping.value}
            {else}
              {Context::getContext()->getCurrentLocale()->formatPrice($cart.subtotals.shipping.amount, $currency.iso_code)}
            {/if}
          </span>
        </div>
      </div>
      
      <div class="cart-summary__line">
        <span class="cart-summary__label js-subtotal">
          {l s='VAT' d='Maxstol.Theme.Cart'}
        </span>
        
        <div class="cart-summary__line__value">
          <span class="cart-summary__value">
            {if $cart.totals.total_including_tax.amount && $cart.totals.total_excluding_tax.amount}
              {Context::getContext()->getCurrentLocale()->formatPrice($cart.totals.total_including_tax.amount - $cart.totals.total_excluding_tax.amount, $currency.iso_code)}
            {else}
              {Context::getContext()->getCurrentLocale()->formatPrice(0, $currency.iso_code)}
            {/if}
          </span>
        </div>
      </div>
    </div>

    {block name='cart_summary_totals'}
      {include file='checkout/_partials/cart-summary-totals.tpl' cart=$cart}
    {/block}
  </div>
{/block}
