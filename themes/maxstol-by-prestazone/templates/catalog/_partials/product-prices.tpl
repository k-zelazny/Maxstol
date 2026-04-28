{if $product.show_price}
  <div class="product__prices js-product-prices">
    {block name='product_price'}
      <div class="prices__wrapper d-flex flex-column gap-2">
        {if $product.has_discount}
          <div class="product__discount">
            {hook h='displayProductPriceBlock' product=$product type="old_price"}

            <span class="product__price-regular">{$product.regular_price}</span>

            {if $product.discount_type === 'percentage'}
              <span class="product__discount-percentage">
                ({l s='Save %percentage%' d='Shop.Theme.Catalog' sprintf=['%percentage%' => $product.discount_percentage_absolute]})
              </span>
            {else}
              <span class="product__discount-amount">
                ({l s='Save %amount%' d='Shop.Theme.Catalog' sprintf=['%amount%' => $product.discount_to_display]})
              </span>
            {/if}
          </div>
        {/if}
        
        <div class="d-flex align-items-center gap-2">
          <div class="product__current-price">
            {capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='product_sheet'}{/capture}
            {if '' !== $smarty.capture.custom_price}
              {$smarty.capture.custom_price nofilter}
            {else}
              {$product.price}
            {/if}
          </div>

          {block name='product_unit_price'}
            {if $displayUnitPrice}
              <div class="product__unit-price">
                {l s='(%unit_price%)' d='Shop.Theme.Catalog' sprintf=['%unit_price%' => $product.unit_price_full]}
              </div>
            {/if}
          {/block}
        </div>

        {block name='product_pack_price'}
          {if $displayPackPrice}
            <div class="product__pack-price">
              {l s='Instead of %price%' d='Shop.Theme.Catalog' sprintf=['%price%' => $noPackPrice]}
            </div>
          {/if}
        {/block}

        {block name='product_without_taxes'}
          {if $priceDisplay == 2}
            <p class="product__price-taxless">{l s='%price% tax excl.' d='Shop.Theme.Catalog' sprintf=['%price%' => $product.price_tax_exc]}</p>
          {/if}
        {/block}
      </div>
    {/block}

    {hook h='displayProductPriceBlock' product=$product type="weight" hook_origin='product_sheet'}
  </div>
{/if}
