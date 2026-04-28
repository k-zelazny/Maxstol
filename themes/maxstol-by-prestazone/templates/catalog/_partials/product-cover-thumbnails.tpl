<div class="product__images js-images-container">
  <div id="product-images">
    {block name='product_images'}
      {foreach from=$product.images item=image key=key}
        {assign var='typed_image_url' value=''}
        {if !empty($image.bySize.product_main_2x.url)}
          {assign var='typed_image_url' value=$image.bySize.product_main_2x.url}
        {elseif !empty($image.bySize.product_main.url)}
          {assign var='typed_image_url' value=$image.bySize.product_main.url}
        {elseif !empty($image.large.url)}
          {assign var='typed_image_url' value=$image.large.url}
        {elseif !empty($image.medium.url)}
          {assign var='typed_image_url' value=$image.medium.url}
        {/if}

        {assign var='original_image_url' value=$typed_image_url|regex_replace:'/-(?:[a-z0-9_]+)(?=\\/[^\\/]+$)/i':''}
        {if empty($original_image_url)}
          {assign var='original_image_url' value=$typed_image_url}
        {/if}

        {if empty($original_image_url)}
          {assign var='original_image_url' value=$link->getImageLink($product.link_rewrite, $image.id_image, null)}
        {/if}
        <div class="product-image">
          <picture>
            <img
              class="img-fluid js-thumb{if $image.id_image == $product.default_image.id_image} js-thumb-selected{/if}"
              src="{$original_image_url}"
              loading="lazy"
              alt="{$image.legend}"
              title="{$image.legend}"
            >
          </picture>
        </div>
      {/foreach}
    {/block}
  </div>
    
  {hook h='displayAfterProductThumbs' product=$product}
</div>

{block name='product_images_modal'}
  {include file='catalog/_partials/product-images-modal.tpl'}
{/block}
