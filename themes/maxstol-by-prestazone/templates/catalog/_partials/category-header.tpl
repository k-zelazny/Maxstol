<div id="js-product-list-header">
  <div class="block-category">
    {if !empty($category.cover.large.url)}
      <div class="category-cover">
        <img
          src="{$urls.img_cat_url}{$category.id}.png"
          onerror="this.onerror=null;this.src='{$urls.img_cat_url}{$category.id}.jpg';"
          alt="{if !empty($category.cover.legend)}{$category.cover.legend}{else}{$category.name}{/if}"
          width="1320"
          height="385"
          fetchpriority="high"
          loading="eager"
          class="img-fluid"
        >
      </div>
    {/if}

    {if $category.description}
      <div id="category-description" class="rich-text mb-4">{$category.description nofilter}</div>
    {/if}
  </div>
</div>
