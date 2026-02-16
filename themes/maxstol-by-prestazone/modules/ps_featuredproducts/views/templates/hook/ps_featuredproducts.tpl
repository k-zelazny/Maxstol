<section class="ps-featuredproducts container">
  <div class="ps-featuredproducts__thead">
    <h2 class="ps-featuredproducts__thead-title h3">{l s='Maybe you forgot about...' d='Maxstol.Theme.Cart'}</h2>
    <p class="ps-featuredproducts__thead-desc body-small">{l s='Our offer includes beds tailored to different needs:' d='Maxstol.Theme.Cart'}</p>
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
