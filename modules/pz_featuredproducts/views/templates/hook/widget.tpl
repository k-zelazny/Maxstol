<section class="pz-featuredproducts container">
  <div class="pz-featuredproducts__thead">
    <h2 class="h3">
      Popularne produkty
    </h2>
    <p class="body-small">
      Nasze popularne produkty to łóżka, które zaspokajają różnorodne potrzeby klientów:
    </p>
  </div>
  
  <div class="pz-featuredproducts__blocks">
    {foreach from=$products item=product}
      {include file='catalog/_partials/miniatures/product.tpl' product=$product}
    {/foreach}
  </div>
</section>

<style>
  .pz-featuredproducts {
    display: flex;
    flex-direction: column;
    gap: 70px;
    padding-block: 80px;
  }

  .pz-featuredproducts .pz-featuredproducts__thead {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-align: center;
    max-width: 1016px;
    margin: 0 auto;
  }

  .pz-featuredproducts .pz-featuredproducts__thead .body-small {
    color: var(--color-body);
  }

  .pz-featuredproducts .pz-featuredproducts__blocks {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;
    column-gap: 24px;
  }

  @media (max-width: 1023.98px) {
    .pz-featuredproducts .pz-featuredproducts__blocks {
      gap: 12px;
    }
  }

  @media (max-width: 991.98px) {
    .pz-featuredproducts {
      gap: 36px;
    }
  }
  
  @media (max-width: 767.98px) {
    .pz-featuredproducts .pz-featuredproducts__blocks {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>