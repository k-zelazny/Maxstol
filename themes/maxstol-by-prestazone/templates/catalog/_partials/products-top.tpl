<div id="js-product-list-top" class="products-selection">
  <div class="products-selections-filters">
    <div class="d-flex align-items-center justify-content-between sort-by-row">
      <div class="category-filters">
        {block name='sort_by'}
          {include file='catalog/_partials/sort-orders.tpl' sort_orders=$listing.sort_orders}
        {/block}
        
        {if !empty($listing.rendered_facets)}
          <div class="d-block d-md-none filter-button">
            <button id="search_filter_toggler" class="btn btn-outline-primary btn-with-icon js-search-toggler" data-bs-toggle="offcanvas" data-bs-target="#offcanvas-faceted">
              <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M7.33141 12C7.18816 12 7.04878 11.9545 6.93417 11.8703L4.27135 9.92432C4.18912 9.8639 4.12238 9.78556 4.07642 9.69549C4.03045 9.60542 4.00652 9.5061 4.00652 9.4054V7.02486L0.259893 2.06919C0.114011 1.87612 0.0257058 1.64717 0.00481566 1.40783C-0.0160745 1.16849 0.0312713 0.928172 0.141578 0.713644C0.251884 0.499116 0.420819 0.318806 0.62956 0.192804C0.838302 0.0668009 1.07865 5.42111e-05 1.32383 0H10.6762C10.9213 5.42111e-05 11.1617 0.0668009 11.3704 0.192804C11.5792 0.318806 11.7481 0.499116 11.8584 0.713644C11.9687 0.928172 12.0161 1.16849 11.9952 1.40783C11.9743 1.64717 11.886 1.87612 11.7401 2.06919L7.99348 7.02486V11.3514C7.99348 11.5234 7.92373 11.6884 7.79956 11.81C7.6754 11.9317 7.507 12 7.33141 12V12ZM5.33065 9.08108L6.66935 10.0586V6.81081C6.66935 6.67046 6.71581 6.5339 6.80176 6.42162L10.6762 1.2973H1.32383L5.20089 6.42162C5.28684 6.5339 5.3333 6.67046 5.3333 6.81081L5.33065 9.08108Z" fill="#1F2A37"/>
              </svg>
              {l s='Filter' d='Shop.Theme.Actions'}
            </button>
          </div>
        {/if}
      </div>
    </div>
  </div>
</div>
