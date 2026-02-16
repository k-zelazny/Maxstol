<div class="product-line">
  <div class="product-line__image">
    {if $product.default_image}
      <picture>
        {if isset($product.default_image.bySize.default_xs.sources.avif)}
          <source 
            srcset="
              {$product.default_image.bySize.default_xs.sources.avif},
              {$product.default_image.bySize.default_m.sources.avif} 2x"
            type="image/avif"
          >
        {/if}

        {if isset($product.default_image.bySize.default_xs.sources.webp)}
          <source 
            srcset="
              {$product.default_image.bySize.default_xs.sources.webp},
              {$product.default_image.bySize.default_m.sources.webp} 2x"
            type="image/webp"
          >
        {/if}

        <img
          class="img-fluid"
          srcset="
            {$product.default_image.bySize.default_xs.url},
            {$product.default_image.bySize.default_m.url} 2x"
          width="{$product.default_image.bySize.default_xs.width}"
          height="{$product.default_image.bySize.default_xs.height}"
          loading="lazy"
          alt="{$product.name|escape:'quotes'}"
          title="{$product.name|escape:'quotes'}"
        >
      </picture>
    {else}
      <picture>
        {if isset($urls.no_picture_image.bySize.default_xs.sources.avif)}
          <source 
            srcset="
              {$urls.no_picture_image.bySize.default_xs.sources.avif},
              {$urls.no_picture_image.bySize.default_m.sources.avif} 2x"
            type="image/avif"
          >
        {/if}

        {if isset($urls.no_picture_image.bySize.default_xs.sources.webp)}
          <source 
            srcset="
              {$urls.no_picture_image.bySize.default_xs.sources.webp},
              {$urls.no_picture_image.bySize.default_m.sources.webp} 2x"
            type="image/webp"
          >
        {/if}

        <img
          class="img-fluid"
          srcset="
            {$urls.no_picture_image.bySize.default_xs.url},
            {$urls.no_picture_image.bySize.default_m.url} 2x"
          width="{$urls.no_picture_image.bySize.default_xs.width}"
          height="{$urls.no_picture_image.bySize.default_xs.height}"
          loading="lazy"
        >
      </picture>
    {/if}
  </div>
  
  <div class="product-line__content">
    {assign var=product_line_alert_id value=10|mt_rand:100000}
    <div id="js-product-line-alert--{$product_line_alert_id}"></div>
    
    <div class="product-line__content-name">{$product.name}</div>
    
    <div class="product-line__content-actions">
      <div class="quantity-button js-quantity-button">
        {if !empty($product.is_gift)}
          <span class="gift-quantity">{$product.quantity}</span>
        {else}
          {include file='components/qty-input.tpl'
            attributes=[
              "class"=>"js-cart-line-product-quantity form-control",
              "name"=>"product-quantity-spin",
              "data-update-url"=>"{$product.update_quantity_url}",
              "data-product-id"=>"{$product.id_product}",
              "data-alert-id"=>"{$product_line_alert_id}",
              "value"=>"{$product.quantity}",
              "min"=>"{$product.minimal_quantity}"
            ]
          }
        {/if}
      </div>
      
      <div class="product-line__content">
        {if empty($product.is_gift)}
          <a class="remove-from-cart" rel="nofollow" href="{$product.remove_from_cart_url}"
            data-link-action="delete-from-cart" data-id-product="{$product.id_product|escape:'javascript'}"
            data-id-product-attribute="{$product.id_product_attribute|escape:'javascript'}"
            data-id-customization="{$product.id_customization|escape:'javascript'}"
            data-product-url="{$product.url|escape:'javascript'}"
            data-product-name="{$product.name|escape:'htmlall':'UTF-8'}"
            >
            {l s='Remove' d='Shop.Theme.Checkout'}
          </a>
        {/if}

        {block name='hook_cart_extra_product_actions'}
          {hook h='displayCartExtraProductActions' product=$product}
        {/block}
      </div>
    </div>
    
    {if $product.delivery_in_stock}
      <div class="product-line__content-availability">
        <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M11.94 5.17778C11.94 5.17206 11.94 5.16571 11.9364 5.16064L10.7364 2.62095C10.6866 2.5155 10.6101 2.42681 10.5153 2.3648C10.4206 2.30278 10.3114 2.2699 10.2 2.26984H7.8C7.8 1.93306 7.67357 1.61007 7.44853 1.37193C7.22348 1.13379 6.91826 1 6.6 1H1.2C0.88174 1 0.576515 1.13379 0.351472 1.37193C0.126428 1.61007 0 1.93306 0 2.26984V7.98413C0 8.15252 0.0632141 8.31401 0.175736 8.43308C0.288258 8.55215 0.44087 8.61905 0.6 8.61905H0.78C0.761884 8.72385 0.751652 8.83 0.7494 8.93651C0.7494 9.48378 0.954846 10.0086 1.32054 10.3956C1.68624 10.7826 2.18223 11 2.6994 11C3.21657 11 3.71256 10.7826 4.07826 10.3956C4.44395 10.0086 4.6494 9.48378 4.6494 8.93651C4.64754 8.83003 4.63771 8.72388 4.62 8.61905H7.38C7.36188 8.72385 7.35165 8.83 7.3494 8.93651C7.3494 9.48378 7.55485 10.0086 7.92054 10.3956C8.28624 10.7826 8.78223 11 9.2994 11C9.81657 11 10.3126 10.7826 10.6783 10.3956C11.044 10.0086 11.2494 9.48378 11.2494 8.93651C11.2475 8.83003 11.2377 8.72388 11.22 8.61905H11.4C11.5591 8.61905 11.7117 8.55215 11.8243 8.43308C11.9368 8.31401 12 8.15252 12 7.98413V5.44444C11.9988 5.35197 11.9783 5.26091 11.94 5.17778ZM9.3 6.87302C8.84991 6.87526 8.41468 7.04368 8.0694 7.34921H7.8V6.07937H10.8V7.34921H10.5306C10.1853 7.04368 9.75009 6.87526 9.3 6.87302ZM9.8292 3.53968L10.4292 4.80952H7.8V3.53968H9.8292ZM1.2 2.26984H6.6V7.34921H3.9306C3.5865 7.04199 3.15081 6.87347 2.7006 6.87347C2.25039 6.87347 1.8147 7.04199 1.4706 7.34921H1.2V2.26984ZM2.7 9.73016C2.55166 9.73016 2.40666 9.68361 2.28332 9.59641C2.15999 9.5092 2.06386 9.38525 2.00709 9.24022C1.95032 9.0952 1.93547 8.93563 1.96441 8.78167C1.99335 8.62772 2.06478 8.48631 2.16967 8.37531C2.27456 8.26432 2.4082 8.18873 2.55368 8.15811C2.69917 8.12748 2.84997 8.1432 2.98701 8.20327C3.12406 8.26334 3.24119 8.36506 3.3236 8.49558C3.40601 8.62609 3.45 8.77954 3.45 8.93651C3.44968 9.14689 3.37056 9.34857 3.22998 9.49733C3.0894 9.6461 2.89881 9.72982 2.7 9.73016ZM9.3 9.73016C9.15166 9.73016 9.00666 9.68361 8.88332 9.59641C8.75998 9.5092 8.66386 9.38525 8.60709 9.24022C8.55032 9.0952 8.53547 8.93563 8.56441 8.78167C8.59335 8.62772 8.66478 8.48631 8.76967 8.37531C8.87456 8.26432 9.0082 8.18873 9.15368 8.15811C9.29917 8.12748 9.44997 8.1432 9.58701 8.20327C9.72406 8.26334 9.84119 8.36506 9.9236 8.49558C10.006 8.62609 10.05 8.77954 10.05 8.93651C10.0497 9.14689 9.97056 9.34857 9.82998 9.49733C9.6894 9.6461 9.49882 9.72982 9.3 9.73016Z" fill="#6B7280"/>
        </svg>
        {$product.delivery_in_stock}
      </div>
    {/if}
    
    {if $product.attributes}
      <div class="product-line__content-variants">
        {l s='Selected variants:' d='Maxstol.Theme.Cart'}
        <div class="product-line__variants">
          {foreach from=$product.attributes key="attribute" item="value"}
            <div class="product-line__info product-line__item {$attribute|lower}">
              <span class="label">{$attribute}:</span>
              <span class="value">{$value}</span>
            </div>
          {/foreach}
        </div>
      </div>
    {/if}
    
    <div class="product-line__content-price">
      {if !empty($product.is_gift)}
        <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
      {else}
        {$product.total}
      {/if}
    </div>
  </div>
  
  <div class="product-line__price">
    {if $product.has_discount}
      <div class="product-line__discount">
        <div class="price">
          <span class="product-line__price">
            <strong>
              {if !empty($product.is_gift)}
                <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
              {else}
                {$product.total}
              {/if}
            </strong>
          </span>
        </div>
      </div>
    {else}
      {if !empty($product.is_gift)}
        <span class="gift">{l s='Gift' d='Shop.Theme.Checkout'}</span>
      {else}
        {$product.total}
      {/if}
    {/if}
    
    {hook h='displayProductPriceBlock' product=$product type="unit_price"}
  </div>
</div>
