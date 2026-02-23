{extends file='layouts/layout-both-columns.tpl'}

{block name="content_columns"}
  <div class="{block name="container_class"}container{/block}">
    {if $page.page_name == 'category' && !empty($category.cover.large.url)}
      <div class="category-cover">
        <picture>
          <img width="1320" height="385" src="{$urls.img_cat_url}{$category.id}.jpg" alt="{if !empty($category.cover.legend)}{$category.cover.legend}{else}{$category.name}{/if}" fetchpriority="high">
        </picture>
      </div>
      
      {block name='product_list_top'}
        {include file='catalog/_partials/products-top.tpl' listing=$listing}
      {/block}
    {/if}
    
    <div class="row">
      {block name="left_column"}
        <div id="left-column" class="wrapper__left-column col-md-4 col-lg-3">
          {if $page.page_name == 'product'}
            {hook h='displayLeftColumnProduct'}
          {else}
            {hook h='displayLeftColumn'}
          {/if}
        </div>
      {/block}

      {block name="content_wrapper"}
        <div id="content-wrapper" class="wrapper__content col-md-8 col-lg-9">
          {hook h='displayContentWrapperTop'}
          {block name="content"}
            <p>Hello world! This is HTML5 Boilerplate.</p>
          {/block}
          {hook h='displayContentWrapperBottom'}
        </div>
      {/block}

      {block name='right_column'}{/block}
    </div>
  </div>
{/block}
