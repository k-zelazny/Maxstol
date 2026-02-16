<div id="_desktop_search" class="order-2 ms-auto col-auto d-none d-md-flex align-items-center">
  <div id="search_widget" class="search-widgets js-search-widget" data-search-controller-url="{$search_controller_url}">
    <form method="get" action="{$search_controller_url}">
      <input type="hidden" name="controller" value="search">
      <input class="js-search-input" type="search" name="s" value="{$search_string}" placeholder="{l s='Search our catalog' d='Shop.Theme.Catalog'}" aria-label="{l s='Search' d='Shop.Theme.Catalog'}">
      <i class="material-icons clear" aria-hidden="true">clear</i>
    </form>
    
    <div class="search-widgets__dropdown js-search-dropdown d-none">
      <ul class="search-widgets__results js-search-results">
      </ul>
    </div>
  </div>
</div>

<template id="search-products" class="js-search-template">
  <li class="search-result">
    <a class="search-result__link" href="">
      <img src="" alt="" class="search-result__image">
      <p class="search-result__name"></p>
    </a>
  </li>
</template>