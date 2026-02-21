<section class="pz-categorytree" data-module="pz-categorytree">
  {if !empty($mainCategories)}
    <ul class="pz-categorytree__main-list">
      {foreach from=$mainCategories item=mainCategory}
        <li class="pz-categorytree__main-item">
          <details class="pz-categorytree__details" open>
            <summary class="pz-categorytree__summary">
              {$mainCategory.name|escape:'html':'UTF-8'}
            </summary>

            {if !empty($mainCategory.subcategories)}
              <ul class="pz-categorytree__sub-list">
                {foreach from=$mainCategory.subcategories item=subCategory}
                  <li class="pz-categorytree__sub-item">
                    <a class="pz-categorytree__sub-link" href="{$subCategory.link|escape:'htmlall':'UTF-8'}">
                      <span class="pz-categorytree__sub-name">{$subCategory.name|escape:'html':'UTF-8'}</span>
                      <span class="pz-categorytree__count">({$subCategory.product_count|intval})</span>
                    </a>
                  </li>
                {/foreach}
              </ul>
            {else}
              <p class="pz-categorytree__empty">
                {l s='No subcategories found.' d='Modules.Pzcategorytree.Widget'}
              </p>
            {/if}
          </details>
        </li>
      {/foreach}
    </ul>
  {else}
    <p class="pz-categorytree__empty">
      {l s='No categories found.' d='Modules.Pzcategorytree.Widget'}
    </p>
  {/if}
</section>
