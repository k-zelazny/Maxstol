{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{extends file=$layout}

{block name='content'}
  <div class="cart-grid-outer">
    {include file='checkout/_partials/progress.tpl'}
    
    <h1 class="h4">{l s='Your cart' d='Maxstol.Theme.Cart'}</h1>
    
    <div class="cart-grid">
      <!-- Left Block: cart product informations & shpping -->
      <div class="cart-grid__body">
        <!-- cart products detailed -->
        <div class="cart-container">
          <div class="js-cart-update-alert" data-alert="{l s='has been removed from the cart.' d='Shop.Theme.Actions' js=1}"></div>
          
          {block name='cart_overview'}
            {include file='checkout/_partials/cart-detailed.tpl' cart=$cart}
          {/block}

          <!-- shipping informations -->
          {block name='hook_shopping_cart_footer'}
            {hook h='displayShoppingCartFooter'}
          {/block}
        </div>
      </div>

      <!-- Right Block: cart subtotal & cart total -->
      <div class="cart-grid__right">
        {block name='cart_summary'}
          <div class="card cart-summary">
            {block name='hook_shopping_cart'}
              {hook h='displayShoppingCart'}
            {/block}

            {block name='cart_totals'}
              {include file='checkout/_partials/cart-detailed-totals.tpl' cart=$cart}
            {/block}

            {include file='checkout/_partials/cart-detailed-actions.tpl' cart=$cart}
            
            <div class="pz-loginrequired">
              {l s='One or more items in your cart require an account.' d='Maxstol.Theme.Cart'}
              <a href="#">{l s='Please log in or create an account now.' d='Maxstol.Theme.Cart'}</a>
            </div>
          </div>
        {/block}
      </div>
    </div>
  </div>
  
  {hook h='displayCrossSellingShoppingCart'}
{/block}
