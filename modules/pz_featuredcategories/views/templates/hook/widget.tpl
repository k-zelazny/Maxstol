<section class="pz-featuredcategories container-sm">
  <div class="pz-featuredcategories__thead">
    <h2 class="h3">
      Kategorie
      {* {l s='Categories' d='Modules.Pzfeaturedcategories.Widget'} *}
    </h2>
    <p class="body-small">
      Nasza oferta obejmuje łóżka dostosowane do różnych potrzeb:
      {* {l s='Our offer includes beds tailored to different needs:' d='Modules.Pzfeaturedcategories.Widget'} *}
    </p>
  </div>
  
  <div class="pz-featuredcategories__blocks">
    <article class="pz-featuredcategories__block">
      <a href="#">
        {* <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Łózka dla seniorów">
        </picture> *}
        <span class="pz-featuredcategories__block-title body-small">Łóżka dla seniorów</span>
      </a>
    </article>
    
    <article class="pz-featuredcategories__block">
      <a href="#">
        {* <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Łózka dla seniorów">
        </picture> *}
        <span class="pz-featuredcategories__block-title body-small">Łóżka dla seniorów</span>
      </a>
    </article>
    
    <article class="pz-featuredcategories__block">
      <a href="#">
        {* <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Osoby wysokie">
        </picture> *}
        <span class="pz-featuredcategories__block-title body-small">Osoby wysokie</span>
      </a>
    </article>
    
    <article class="pz-featuredcategories__block">
      <a href="#">
        {* <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Meble ogrodowe">
        </picture> *}
        <span class="pz-featuredcategories__block-title body-small">Meble ogrodowe</span>
      </a>
    </article>
  </div>
</section>

<style>
.pz-featuredcategories {
  display: flex;
  flex-direction: column;
  gap: 70px;
  padding-block: 80px;
}

.pz-featuredcategories .pz-featuredcategories__thead {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  text-align: center;
}

.pz-featuredcategories .pz-featuredcategories__thead .body-small {
  color: var(--color-body);
}

.pz-featuredcategories .pz-featuredcategories__blocks {
  display: flex;
  align-items: center;
  column-gap: 24px;
}

.pz-featuredcategories .pz-featuredcategories__block {
  break-inside: avoid;
  margin: 0 0 24px;
  width: 100%;
}

.pz-featuredcategories .pz-featuredcategories__block a {
  padding-block: 24px;
  border-radius: 4px;
  overflow: hidden;
  position: relative;
  background: #858282;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  text-align: center;
  min-height: 192px;
}

.pz-featuredcategories .pz-featuredcategories__block:nth-child(1) a, 
.pz-featuredcategories .pz-featuredcategories__block:nth-child(3) a {
  min-height: 254px;
}

.pz-featuredcategories .pz-featuredcategories__block .pz-featuredcategories__block-thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
  position: absolute;
  left: 0;
  top: 0;
}

.pz-featuredcategories .pz-featuredcategories__block .pz-featuredcategories__block-title {
  padding: 10px;
  border-radius: 4px;
  background: var(--color-white);
  color: var(--color-black);
}

@media (max-width: 1023.98px) {
  .pz-featuredcategories .pz-featuredcategories__blocks {
    gap: 12px;
  }
}

@media (max-width: 991.98px) {
  .pz-featuredcategories {
    gap: 24px;
    padding-block: 32px;
  }
}

@media (max-width: 767.98px) {
  .pz-featuredcategories .pz-featuredcategories__blocks {
    display: block;
    column-count: 2;
    column-gap: 12px;
  }

  .pz-featuredcategories .pz-featuredcategories__block {
    margin: 0 0 12px;
  }

  .pz-featuredcategories .pz-featuredcategories__block:nth-child(1) a, 
  .pz-featuredcategories .pz-featuredcategories__block:nth-child(4) a {
    min-height: 254px;
  }

  .pz-featuredcategories .pz-featuredcategories__block:nth-child(2) a, 
  .pz-featuredcategories .pz-featuredcategories__block:nth-child(3) a {
    min-height: 192px;
  }

  .pz-featuredcategories .pz-featuredcategories__block a {
    padding-inline: 12px;
  }
}
</style>