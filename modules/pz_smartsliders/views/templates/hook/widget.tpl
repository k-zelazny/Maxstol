<section class="pz-smartsliders container">
  <div class="pz-smartsliders__thead">
    <h2 class="h3">
      Polecane dla seniorów
    </h2>
    <p class="body-small">
      Oferujemy wyjątkowe łóżka dla seniorów, które łączą komfort z nowoczesnym stylem. Nasze modele są dostępne w różnych kolorach i wzorach, co pozwala na idealne dopasowanie do każdego wnętrza. Wybierz łóżko z funkcją regulacji, aby zapewnić sobie wygodę i wsparcie w codziennym użytkowaniu. Zainwestuj w zdrowy sen i stylowy wystrój swojego pokoju!
    </p>
  </div>
  
  <div class="pz-smartsliders__blocks">
    {foreach from=$products item=product}
      {include file='catalog/_partials/miniatures/product.tpl' product=$product}
    {/foreach}
  </div>
</section>

<section class="pz-smartsliders container">
  <div class="pz-smartsliders__thead">
    <h2 class="h3">
      Polecane dla dzieci i młodzieży
    </h2>
    <p class="body-small">
      Oferujemy szeroki wybór łóżek idealnych dla dzieci i młodzieży, które łączą funkcjonalność z modnym designem. Nasze łóżka są dostępne w różnych stylach i kolorach, co pozwala na dopasowanie ich do każdego wnętrza. Wybierz łóżko z dodatkową przestrzenią do przechowywania, aby maksymalnie wykorzystać przestrzeń w pokoju. 
    </p>
  </div>
  
  <div class="pz-smartsliders__blocks">
    {foreach from=$products item=product}
      {include file='catalog/_partials/miniatures/product.tpl' product=$product}
    {/foreach}
  </div>
</section>

<style>
  .pz-smartsliders {
    display: flex;
    flex-direction: column;
    gap: 70px;
    padding-block: 80px;
  }

  .pz-smartsliders .pz-smartsliders__thead {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-align: center;
    max-width: 1016px;
    margin: 0 auto;
  }

  .pz-smartsliders .pz-smartsliders__thead .body-small {
    color: var(--color-body);
  }

  .pz-smartsliders .pz-smartsliders__blocks {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    align-items: center;
    column-gap: 24px;
  }

  @media (max-width: 1023.98px) {
    .pz-smartsliders .pz-smartsliders__blocks {
      gap: 12px;
    }
  }

  @media (max-width: 991.98px) {
    .pz-smartsliders {
      gap: 36px;
    }
  }

  @media (max-width: 767.98px) {
    .pz-smartsliders .pz-smartsliders__blocks {
      grid-template-columns: repeat(2, 1fr);
    }
  }
</style>