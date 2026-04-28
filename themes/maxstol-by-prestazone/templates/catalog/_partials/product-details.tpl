
<div
  class="info js-product-details accordion-item"
  id="product-details"
  data-product="{$product.embedded_attributes|json_encode}"
>
  {block name='product_features'}
    {if $product.grouped_features}
      <div class="info accordion-item" id="product-features">
        <h2 class="info__title accordion-header" id="product-features-heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#product-features-collapse" aria-expanded="false"
            aria-controls="product-features-collapse">
            {l s='Technical specifications' d='Shop.Theme.Catalog'}
          </button>
        </h2>
        <div id="product-features-collapse" class="info__content accordion-collapse collapse" data-bs-parent="#product-features-heading" aria-labelledby="product-features-heading">
          <div class="accordion-body">
            <ul class="product__features">
              {foreach from=$product.grouped_features item=feature}
                <li class="detail">
                  <div class="detail__left">
                    <span class="detail__title">{$feature.name}</span>
                  </div>

                  <div class="detail__right">
                    <span>{$feature.value|escape:'htmlall'|nl2br nofilter}</span>
                  </div>
                </li>
              {/foreach}
            </ul>
          </div>
        </div>
      </div>
    {/if}
  {/block}
</div>
