{extends file=$layout}

{block name='head_microdata_special'}
  {include file='_partials/microdata/product-list-jsonld.tpl' listing=$listing}
{/block}

{block name='content'}
    {hook h='displayHeaderCategory'}
    
    <section id="products">
      {include file='catalog/_partials/category-header.tpl'}
      
      <div class="category-head">
        <h1>{$category.name}</h1>
        
        <div class="category-head__filters">
          {if $listing.products|count}
            {hook h='displayLeftColumn'}
            
            {block name='product_list_top'}
              {include file='catalog/_partials/products-top.tpl' listing=$listing}
            {/block}
          {/if}
        </div>
      </div>
      
      <div class="category-head__filters-active">
        {block name='product_list_active_filters'}
          {$listing.rendered_active_filters nofilter}
        {/block}
      </div>
      
      {if $listing.products|count}
        <div class="category-grid">
          {hook h='displayPZCategoryTree'}
          
          {block name='product_list'}
            {if isset($page.body_classes['layout-full-width'])}
              {assign var="classes" value="col-12 col-xs-6 col-lg-4 col-xl-3"}
            {elseif isset($page.body_classes['layout-left-column']) || isset($page.body_classes['layout-right-column'])}
              {assign var="classes" value="col-12 col-xs-6 col-xl-4"}
            {elseif isset($page.body_classes['layout-both-columns'])}
              {assign var="classes" value="col-12 col-xs-6 col-md-12 col-lg-6"}
            {else}
              {assign var="classes" value="col-12 col-xs-6 col-lg-4 col-xl-3"}
            {/if}
            {include file='catalog/_partials/products.tpl' listing=$listing productClass=$classes}
          {/block}

          {block name='product_list_bottom'}
            {include file='catalog/_partials/products-bottom.tpl' listing=$listing}
          {/block}
        </div>
      {else}
        <div id="js-product-list-top"></div>

        <div id="js-product-list">
          {capture assign="errorContent"}
            <p class="h4">{l s='No products available yet' d='Shop.Theme.Catalog'}</p>
            <p>{l s='Stay tuned! More products will be shown here as they are added.' d='Shop.Theme.Catalog'}</p>
          {/capture}

          {include file='errors/not-found.tpl' errorContent=$errorContent}
        <div>

        <div id="js-product-list-bottom"></div>
      {/if}
    </section>


    {block name='product_list_footer'}{/block}


    {hook h='displayFooterCategory'}
{/block}
