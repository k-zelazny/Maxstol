<section class="ps-featuredproducts {if $page.page_name === 'cart'}container{/if}">
  <div class="ps-featuredproducts__thead">
    {if $page.page_name === 'cart'}
      <h2 class="ps-featuredproducts__thead-title h3">{l s='Maybe you forgot about...' d='Maxstol.Theme.Cart'}</h2>
      <p class="ps-featuredproducts__thead-desc body-small">{l s='Our offer includes beds tailored to different needs:' d='Maxstol.Theme.Cart'}</p>
    {else}
      <h2 class="ps-featuredproducts__thead-title h3">{l s='Popular products' d='Maxstol.Theme.Product'}</h2>
      <p class="ps-featuredproducts__thead-desc body-small">{l s='Our offer includes beds tailored to different needs:' d='Maxstol.Theme.Product'}</p>
    {/if}
  </div>
  <div class="swiper">
    <div class="swiper-wrapper">
      {foreach from=$products item="product"}
        <div class="swiper-slide">
          {include file="catalog/_partials/miniatures/product.tpl" product=$product}
        </div>
      {/foreach}
    </div>
  </div>
</section>
