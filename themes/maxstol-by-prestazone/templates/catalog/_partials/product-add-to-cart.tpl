<div class="product__add-to-cart product-add-to-cart js-product-add-to-cart">
  {if !$configuration.is_catalog}
    </div>

    {block name='product_quantity'}
      <div class="product-actions__buttons">
        <div class="product-actions__button add">
          <button
            class="btn btn-primary btn-with-icon add-to-cart"
            data-button-action="add-to-cart"
            type="submit"
            {if !$product.add_to_cart_url}
              disabled
            {/if}
          >
            {l s='To cart' d='Shop.Theme.Actions'}
          </button>
        </div>

        <div class="product-actions__button buy-now">
          <button
            class="btn btn-outline-primary btn-with-icon js-buy-now"
            data-button-action="add-to-cart"
            data-checkout-url="{$urls.pages.order}"
            type="submit"
            {if !$product.add_to_cart_url}
              disabled
            {/if}
          >
            {l s='Buy now' d='Shop.Theme.Actions'}
          </button>
        </div>

        {hook h='displayProductActions' product=$product}
      </div>
    {/block}

    {block name='product_minimal_quantity'}
      <p class="product__minimal-quantity product-minimal-quantity js-product-minimal-quantity d-flex align-items-center mt-3 mt-md-0">
        {if $product.minimal_quantity> 1}
          <i class="material-icons me-2" aria-hidden="true">&#xE88F;</i>
          {l
            s='The minimum purchase order quantity for the product is %quantity%.'
            d='Shop.Theme.Checkout'
            sprintf=['%quantity%' => $product.minimal_quantity]
          }
        {/if}
      </p>
    {/block}
  {/if}
</div>
