{if $homeslider.slides}
  <section class="ps-imageslider container">
    <div id="home-slider" class="ratio ratio-homeSlider">
      <div class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
          {assign var="count" value=0}
          {if $count > 1}
            {foreach from=$homeslider.slides item=slide name='homeslider'}
              <button type="button" data-bs-target="#home-slider .carousel" data-bs-slide-to="{$count}" aria-label="{$slide.title}"
              {if $smarty.foreach.homeslider.first} class="active" aria-current="true" {/if}></button>
              {$count = $count + 1}
            {/foreach}
          {/if}
        </div>
        <div class="carousel-inner" role="listbox" aria-label="{l s='Carousel container' d='Shop.Theme.Global'}">
          {foreach from=$homeslider.slides item=slide name='homeslider'}
            <li class="carousel-item{if $smarty.foreach.homeslider.first} active{/if}" role="option"
              aria-hidden="{if $smarty.foreach.homeslider.first}false{else}true{/if}">
              <figure class="carousel-content">
                <img src="{$slide.image_url}" alt="{$slide.legend|escape}" {if $slide@iteration == 1}loading="eager"{else}loading="lazy"{/if} {$slide.size|replace: '"':''}>
                {if $slide.description}
                  <figcaption class="carousel-caption caption">
                    <div class="caption-description">{$slide.description nofilter}</div>
                  </figcaption>
                {/if}
              </figure>
            </li>
          {/foreach}
        </div>
      </div>
    </div>
  </section>
{/if}

<section class="pz-featuredcategories container-sm">
  <div class="pz-featuredcategories__thead">
    <h2 class="h3">{l s='Categories' d='Maxstol.Theme.Categories'}</h2>
    <p class="body-small">{l s='Our offer includes beds tailored to different needs:' d='Maxstol.Theme.Categories'}</p>
  </div>
  
  <div class="pz-featuredcategories__blocks">
    <article class="pz-featuredcategories__block">
      <a href="#">
        <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Łózka dla seniorów">
        </picture>
        <span class="pz-featuredcategories__block-title body-small">Łóżka dla seniorów</span>
      </a>
    </article>
    
    <article class="pz-featuredcategories__block">
      <a href="#">
        <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Łózka dla seniorów">
        </picture>
        <span class="pz-featuredcategories__block-title body-small">Łóżka dla seniorów</span>
      </a>
    </article>
    
    <article class="pz-featuredcategories__block">
      <a href="#">
        <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Osoby wysokie">
        </picture>
        <span class="pz-featuredcategories__block-title body-small">Osoby wysokie</span>
      </a>
    </article>
    
    <article class="pz-featuredcategories__block">
      <a href="#">
        <picture class="pz-featuredcategories__block-thumb">
          <img width="200" height="254" src="" alt="Meble ogrodowe">
        </picture>
        <span class="pz-featuredcategories__block-title body-small">Meble ogrodowe</span>
      </a>
    </article>
  </div>
</section>

<div class="container">
  <section class="pz-banner">    
    <img width="" height="" src="{$urls.base_url}img/cms/pz-banner.jpg" alt="">
    
    <div class="pz-banner__meta">
      <h3 class="pz-banner__meta-title h3">Posiadamy<br> własną stolarnię</h3>
      <div class="pz-banner__meta-desc">
        <p><strong>Jakość, nad którą mamy pełną kontrolę.</strong></p>
        <p>Dzięki własnej stolarni mamy pełną kontrolę nad każdym etapem produkcji – od wyboru drewna po finalne wykończenie. Oznacza to, że nasze łóżka powstają z dbałością o każdy detal, zapewniając trwałość i estetykę, której możesz zaufać.</p>
      </div>
      <a href="#" class="pz-banner__meta-link btn-primary">Sprawdź nasze możliwości</a>
    </div>
  </section>
</div>

<section class="pz-googlereviews container">
  <div class="pz-googlereviews__thead">
    <h2 class="h3">{l s='Customer reviews' d='Maxstol.Theme.Reviews'}</h2>
  </div>
</section>