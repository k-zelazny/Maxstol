{$componentName = 'search-filters'}

{if $displayedFacets|count}
  <div id="search-filters" class="{$componentName} d-flex flex-direction-column flex-wrap w-100">
    <div class="accordion w-100 order-1 order-md-2">
      {foreach from=$displayedFacets item="facet" name="facets"}
        <section class="facet accordion-item">
          {assign var=_expand_id value=10|mt_rand:100000}
          {assign var=_collapse value=true}
          {foreach from=$facet.filters item="filter"}
            {if $filter.active}{assign var=_collapse value=false}{/if}
          {/foreach}

          <span class="{$componentName}-subtitle facet-title">
            <button class="accordion-button fw-bold px-0{if $_collapse} collapsed{/if}" type="button" data-bs-target="#facet_{$_expand_id}" data-bs-toggle="collapse"{if !$_collapse} aria-expanded="true"{/if}>
              <div>
                <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M7.33141 12C7.18816 12 7.04878 11.9545 6.93417 11.8703L4.27135 9.92432C4.18912 9.8639 4.12238 9.78556 4.07642 9.69549C4.03045 9.60542 4.00652 9.5061 4.00652 9.4054V7.02486L0.259893 2.06919C0.114011 1.87612 0.0257058 1.64717 0.00481566 1.40783C-0.0160745 1.16849 0.0312713 0.928172 0.141578 0.713644C0.251884 0.499116 0.420819 0.318806 0.62956 0.192804C0.838302 0.0668009 1.07865 5.42111e-05 1.32383 0H10.6762C10.9213 5.42111e-05 11.1617 0.0668009 11.3704 0.192804C11.5792 0.318806 11.7481 0.499116 11.8584 0.713644C11.9687 0.928172 12.0161 1.16849 11.9952 1.40783C11.9743 1.64717 11.886 1.87612 11.7401 2.06919L7.99348 7.02486V11.3514C7.99348 11.5234 7.92373 11.6884 7.79956 11.81C7.6754 11.9317 7.507 12 7.33141 12V12ZM5.33065 9.08108L6.66935 10.0586V6.81081C6.66935 6.67046 6.71581 6.5339 6.80176 6.42162L10.6762 1.2973H1.32383L5.20089 6.42162C5.28684 6.5339 5.3333 6.67046 5.3333 6.81081L5.33065 9.08108Z" fill="#1F2A37"/>
                </svg>
                {$facet.label}
              </div>
              <i class="material-icons">expand_more</i>
            </button>
          </span>
          <div id="facet_{$_expand_id}" class="accordion-collapse collapse">
            {if in_array($facet.widgetType, ['radio', 'checkbox'])}
              {block name='facet_item_other'}
                <ul  class="accordion-body px-0 mb-0 pb-1 pt-0">
                  {foreach from=$facet.filters key=filter_key item="filter"}
                    {$isColorOrTexture = isset($filter.properties.color) || isset($filter.properties.texture)}
                    {if !$filter.displayed}
                      {continue}
                    {/if}

                    <li>
                      <div class="{$componentName}-label facet-label{if $filter.active} active {/if}">
                        {if $facet.multipleSelectionAllowed}
                          <div class="form-check{if $isColorOrTexture} ps-0{/if}">
                            <input 
                              class="form-check-input{if $isColorOrTexture} d-none{/if}" 
                              id="facet_input_{$_expand_id}_{$filter_key}"
                              data-search-url="{$filter.nextEncodedFacetsURL}"
                              type="checkbox"
                              {if $filter.active }checked{/if}
                          >
                            <label class="form-check-label align-middle" for="facet_input_{$_expand_id}_{$filter_key}">
                              {if isset($filter.properties.color)}
                                <span class="color color-sm me-1 align-middle{if $filter.active } active{/if}" style="background-color:{$filter.properties.color}"></span>
                                <span class="align-middle">
                                  {$filter.label}
                                  {if $filter.magnitude and $show_quantities}
                                    ({$filter.magnitude})
                                  {/if}
                                </span>
                              {elseif isset($filter.properties.texture)}
                                <span class="color color-sm me-1 texture align-middle{if $filter.active } active{/if}" style="background-image:url({$filter.properties.texture})"></span>
                                <span class="align-middle">
                                  {$filter.label}
                                  {if $filter.magnitude and $show_quantities}
                                    ({$filter.magnitude})
                                  {/if}
                                </span>
                              {else}
                                <a
                                  href="{$filter.nextEncodedFacetsURL}"
                                  class="{$componentName}-link _gray-darker search-link js-search-link"
                                  rel="nofollow"
                              >
                                  {$filter.label}
                                  {if $filter.magnitude and $show_quantities}
                                    <span class="magnitude">({$filter.magnitude})</span>
                                  {/if}
                                </a>
                              {/if}
                            </label>
                          </div>
                        {else}
                          <div class="form-check">
                            <input
                              class="form-check-input"
                              id="facet_input_{$_expand_id}_{$filter_key}"
                              data-search-url="{$filter.nextEncodedFacetsURL}"
                              type="radio"
                              name="filter {$facet.label}"
                              {if $filter.active }checked{/if}
                          >
                            <label class="form-check-label" for="facet_input_{$_expand_id}_{$filter_key}">
                              <a
                                href="{$filter.nextEncodedFacetsURL}"
                                class="{$componentName}-link _gray-darker search-link js-search-link"
                                rel="nofollow"
                            >
                                {$filter.label}
                                {if $filter.magnitude and $show_quantities}
                                  <span class="magnitude">({$filter.magnitude})</span>
                                {/if}
                              </a>
                            </label>
                          </div>
                        {/if}
                      </div>
                    </li>
                  {/foreach}
                </ul>
              {/block}

            {elseif $facet.widgetType == 'dropdown'}
              {block name='facet_item_dropdown'}
                <ul class="accordion-body">
                  <li>
                    <div class="facet-dropdown dropdown">
                      <a class="select-title" rel="nofollow" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {$active_found = false}
                        <span>
                          {foreach from=$facet.filters item="filter"}
                            {if $filter.active}
                              {$filter.label}
                              {if $filter.magnitude and $show_quantities}
                                ({$filter.magnitude})
                              {/if}
                              {$active_found = true}
                            {/if}
                          {/foreach}
                          {if !$active_found}
                            {l s='(no filter)' d='Shop.Theme.Global'}
                          {/if}
                        </span>
                        <i class="material-icons float-end">&#xE5C5;</i>
                      </a>
                      <div class="dropdown-menu dropdown-menu-start">
                        {foreach from=$facet.filters item="filter"}
                          {if !$filter.active}
                            <a
                              rel="nofollow"
                              href="{$filter.nextEncodedFacetsURL}"
                              class="dropdown-item select-list js-search-link"
                           >
                              {$filter.label}
                              {if $filter.magnitude and $show_quantities}
                                ({$filter.magnitude})
                              {/if}
                            </a>
                          {/if}
                        {/foreach}
                      </div>
                    </div>
                  </li>
                </ul>
              {/block}

            {elseif $facet.widgetType == 'slider'}
              {block name='facet_item_slider'}
                {foreach from=$facet.filters item="filter"}
                  <div class="accordion-body faceted-filter px-0 js-faceted-filter-slider">
                    <div
                      class="faceted-slider js-faceted-slider-container"
                      data-slider-min="{$facet.properties.min}"
                      data-slider-max="{$facet.properties.max}"
                      data-slider-id="{$_expand_id}"
                      data-slider-values="{$filter.value|@json_encode}"
                      data-slider-unit="{$facet.properties.unit}"
                      data-slider-label="{$facet.label}"
                      data-slider-specifications="{$facet.properties.specifications|@json_encode}"
                      data-slider-encoded-url="{$filter.nextEncodedFacetsURL}"
                      data-slider-direction="{$language.is_rtl}"
                  >
                    </div>
                    <div class="js-faceted-values"></div>  
                  <input 
                    type="hidden"
                    class="form-range-start js-faceted-slider js-faceted-slider-start"
                    id="slider-range_{$_expand_id}-start"
                  >
                  <input 
                    type="hidden"
                    class="form-range-start js-faceted-slider js-faceted-slider-end"
                    id="slider-range_{$_expand_id}-end"
                  >
                </div>
                {/foreach}
              {/block}
            {/if}
          </div>
        </section>
      {/foreach}
    </div>
  </div>
{/if}
