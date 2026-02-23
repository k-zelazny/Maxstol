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