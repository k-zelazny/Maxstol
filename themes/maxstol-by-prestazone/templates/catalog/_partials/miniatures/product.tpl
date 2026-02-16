{**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *}
{$componentName = 'product-miniature'}

{block name='product_miniature_item'}
  <article
    class="{$componentName} js-{$componentName}{if !empty($productClasses)} {$productClasses}{/if}"
    data-id-product="{$product.id_product}"
    data-id-product-attribute="{$product.id_product_attribute}"
  >
    <div class="card">
      <a href="{$product.url}" class="{$componentName}__link">
        {block name='product_miniature_image'}
          <div class="{$componentName}__image-container thumbnail-container">
            {if $product.cover}
              <picture>
                {if isset($product.cover.bySize.default_md.sources.avif)}
                  <source
                    srcset="
                      {$product.cover.bySize.default_xs.sources.avif} 120w,
                      {$product.cover.bySize.default_m.sources.avif} 200w,
                      {$product.cover.bySize.default_md.sources.avif} 320w,
                      {$product.cover.bySize.product_main.sources.avif} 720w"
                    sizes="(min-width: 1300px) 720px, (min-width: 768px) 50vw, 50vw"
                    type="image/avif"
                  >
                {/if}

                {if isset($product.cover.bySize.default_md.sources.webp)}
                  <source
                    srcset="
                      {$product.cover.bySize.default_xs.sources.webp} 120w,
                      {$product.cover.bySize.default_m.sources.webp} 200w,
                      {$product.cover.bySize.default_md.sources.webp} 320w,
                      {$product.cover.bySize.product_main.sources.webp} 720w"
                    sizes="(min-width: 1300px) 320px, (min-width: 768px) 120px, 50vw"
                    type="image/webp"
                  >
                {/if}

                <img
                  class="{$componentName}__image card-img-top"
                  srcset="
                    {$product.cover.bySize.default_xs.url} 120w,
                    {$product.cover.bySize.default_m.url} 200w,
                    {$product.cover.bySize.default_md.url} 320w,
                    {$product.cover.bySize.product_main.url} 720w"
                  sizes="(min-width: 1300px) 320px, (min-width: 768px) 120px, 50vw"
                  src="{$product.cover.bySize.default_md.url}"
                  width="{$product.cover.bySize.default_md.width}"
                  height="{$product.cover.bySize.default_md.height}"
                  loading="lazy"
                  alt="{$product.cover.legend}"
                  title="{$product.cover.legend}"
                  data-full-size-image-url="{$product.cover.bySize.home_default.url}"
                >
              </picture>
            {else}
              <picture>
                {if isset($urls.no_picture_image.bySize.default_md.sources.avif)}
                  <source
                    srcset="
                      {$urls.no_picture_image.bySize.default_xs.sources.avif} 120w,
                      {$urls.no_picture_image.bySize.default_m.sources.avif} 200w,
                      {$urls.no_picture_image.bySize.default_md.sources.avif} 320w,
                      {$urls.no_picture_image.bySize.product_main.sources.avif} 720w"
                    sizes="(min-width: 1300px) 720px, (min-width: 768px) 50vw, 50vw"
                    type="image/avif"
                  >
                {/if}

                {if isset($urls.no_picture_image.bySize.default_md.sources.webp)}
                  <source
                    srcset="
                      {$urls.no_picture_image.bySize.default_xs.sources.webp} 120w,
                      {$urls.no_picture_image.bySize.default_m.sources.webp} 200w,
                      {$urls.no_picture_image.bySize.default_md.sources.webp} 320w,
                      {$urls.no_picture_image.bySize.product_main.sources.webp} 720w"
                    sizes="(min-width: 1300px) 320px, (min-width: 768px) 120px, 50vw"
                    type="image/webp"
                  >
                {/if}

                <img
                  class="{$componentName}__image card-img-top"
                  srcset="
                    {$urls.no_picture_image.bySize.default_xs.url} 120w,
                    {$urls.no_picture_image.bySize.default_m.url} 200w,
                    {$urls.no_picture_image.bySize.default_md.url} 320w,
                    {$urls.no_picture_image.bySize.product_main.url} 720w"
                  sizes="(min-width: 1300px) 320px, (min-width: 768px) 120px, 50vw"
                  width="{$urls.no_picture_image.bySize.default_md.width}"
                  height="{$urls.no_picture_image.bySize.default_md.height}"
                  src="{$urls.no_picture_image.bySize.default_md.url}"
                  loading="lazy"
                  alt="{l s='No image available' d='Shop.Theme.Catalog'}"
                  title="{l s='No image available' d='Shop.Theme.Catalog'}"
                  data-full-size-image-url="{$urls.no_picture_image.bySize.home_default.url}"
                >
              </picture>
            {/if}
          </div>
        {/block}
      </a>

      {block name='product_miniature_bottom'}
        <div class="{$componentName}__infos card-body">
          <div class="{$componentName}__infos__top">
            {block name='product_name'}
              <a href="{$product.url}"><p class="{$componentName}__title body">{$product.name}</p></a>
            {/block}
            
            <div class="{$componentName}__prices">
              {l s='From' d='Maxstol.Theme.Miniature'}
              {block name='product_price'}
                {if $product.show_price}
                  {hook h='displayProductPriceBlock' product=$product type="before_price"}

                  <span class="{$componentName}__price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
                    {capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='products_list'}{/capture}
                    {if '' !== $smarty.capture.custom_price}
                      {$smarty.capture.custom_price nofilter}
                    {else}
                      {$product.price}
                    {/if}
                  </span>

                  {hook h='displayProductPriceBlock' product=$product type='unit_price'}

                  {hook h='displayProductPriceBlock' product=$product type='weight'}
                {/if}
              {/block}
            </div>
          </div>

          <div class="{$componentName}__infos__bottom">
            <a href="{$product.url}" class="btn btn-secondary">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4.09835 19.9017C5.56282 21.3661 7.93719 21.3661 9.40165 19.9017L15.8033 13.5M6.75 21C4.67893 21 3 19.3211 3 17.25V4.125C3 3.50368 3.50368 3 4.125 3H9.375C9.99632 3 10.5 3.50368 10.5 4.125V8.1967M6.75 21C8.82107 21 10.5 19.3211 10.5 17.25V8.1967M6.75 21H19.875C20.4963 21 21 20.4963 21 19.875V14.625C21 14.0037 20.4963 13.5 19.875 13.5H15.8033M10.5 8.1967L13.3791 5.31757C13.8185 4.87823 14.5308 4.87823 14.9701 5.31757L18.6824 9.02988C19.1218 9.46922 19.1218 10.1815 18.6824 10.6209L15.8033 13.5M6.75 17.25H6.7575V17.2575H6.75V17.25Z" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              {l s='Customize' d='Maxstol.Theme.Miniature'}
            </a>
          </div>
        </div>
      {/block}
    </div>
  </article>
{/block}
