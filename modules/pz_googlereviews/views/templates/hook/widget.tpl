<section class="pz-googlereviews container">
  <div class="pz-googlereviews__thead">
    <h2 class="h3">
      Opinie Klientów
    </h2>
  </div>
  
  <div class="pz-googlereviews__blocks">
    <div class="pz-googlereviews__block">
      <p>
        Gorąco polecam. Zamówiłam łóżeczko. Bardzo dobra jakość wykonania. Bardzo dobry kontakt ze sprzedającym. Pan miły i konkretny.
      </p>
      
      <div class="pz-googlereviews__meta">
        <picture>
          <img width="40" height="40" src="{$urls.base_url}img/cms/pz-googlereviews.jpg" alt="Google Reviews">
        </picture>
        <span>
          John Doe
        </span>
      </div>
    </div>
    
    <div class="pz-googlereviews__block">
      <p>
        Gorąco polecam. Zamówiłam łóżeczko. Bardzo dobra jakość wykonania. Bardzo dobry kontakt ze sprzedającym. Pan miły i konkretny.
      </p>
      
      <div class="pz-googlereviews__meta">
        <picture>
          <img width="40" height="40" src="{$urls.base_url}img/cms/pz-googlereviews.jpg" alt="Google Reviews">
        </picture>
        <span>
          John Doe
        </span>
      </div>
    </div>
    
    <div class="pz-googlereviews__block">
      <p>
        Gorąco polecam. Zamówiłam łóżeczko. Bardzo dobra jakość wykonania. Bardzo dobry kontakt ze sprzedającym. Pan miły i konkretny.
      </p>
      
      <div class="pz-googlereviews__meta">
        <picture>
          <img width="40" height="40" src="{$urls.base_url}img/cms/pz-googlereviews.jpg" alt="Google Reviews">
        </picture>
        <span>
          John Doe
        </span>
      </div>
    </div>
  </div>
</section>

<style>
  .pz-googlereviews {
    display: flex;
    flex-direction: column;
    gap: 70px;
    padding-block: 80px;
  }

  .pz-googlereviews .pz-googlereviews__thead {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    text-align: center;
  }

  .pz-googlereviews .pz-googlereviews__thead .body-small {
    color: var(--color-body);
  }

  .pz-googlereviews .pz-googlereviews__blocks {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }
  
  .pz-googlereviews .pz-googlereviews__block {
    border: 1px solid var(--color-gray-300);
    padding: 16px 24px;
    display: flex;
    flex-direction: column;
    border-radius: 6px;
    gap: 20px;
  }
  
  .pz-googlereviews .pz-googlereviews__block p {
    font-size: 0.875rem;
    font-weight: 400;
    line-height: 150%;
    color: var(--color-gray-500);
    margin: 0;
  }
  
  .pz-googlereviews .pz-googlereviews__block .pz-googlereviews__meta {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  
  .pz-googlereviews .pz-googlereviews__block .pz-googlereviews__meta picture {
    width: 40px;
    height: 40px;
    border-radius: 100%;
    overflow: hidden;
  }
  
  .pz-googlereviews .pz-googlereviews__block .pz-googlereviews__meta span {
    font-size: 1rem;
    font-weight: 600;
    line-height: 120%;
    color: var(--color-gray-900);
  }

  @media (max-width: 1023.98px) {
    .pz-googlereviews .pz-googlereviews__blocks {
      gap: 12px;
    }
  }
  
  @media (max-width: 767.98px) {
    .pz-googlereviews .pz-googlereviews__blocks {
      grid-template-columns: 1fr;
    }
  }
</style>