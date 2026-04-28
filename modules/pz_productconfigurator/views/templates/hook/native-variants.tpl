{**
 * Custom product configurator variants.
 * Keeps native PrestaShop variant inputs (`group[id]`) so core product refresh still works.
 *}
{if empty($smarty.capture.pzpc_front_assets_included)}
  {capture name='pzpc_front_assets_included'}1{/capture}
  <link rel="stylesheet" href="{$urls.base_url|escape:'htmlall':'UTF-8'}modules/pz_productconfigurator/views/css/front.css">
  <script type="text/javascript" src="{$urls.base_url|escape:'htmlall':'UTF-8'}modules/pz_productconfigurator/views/js/front.js"></script>
{/if}

{capture name='native_variant_groups'}
  {foreach from=$groups key=id_attribute_group item=group}
    {if !empty($group.attributes)}
      {assign var=selected_name value=''}
      {foreach from=$group.attributes key=id_attribute item=group_attribute}
        {if $group_attribute.selected}
          {assign var=selected_name value=$group_attribute.name}
        {/if}
      {/foreach}

      <details
        class="config-group config-group--{$group.group_type|escape:'htmlall':'UTF-8'}"
        data-group-id="{$id_attribute_group|intval}"
        open
      >
        <summary class="config-group__summary">
          <span class="config-group__title">{$group.name}</span>
          <span class="config-group__value js-config-selected">{$selected_name|escape:'htmlall':'UTF-8'}</span>
        </summary>

        <div class="config-group__options {if $group.group_type == 'color'}config-group__options--color{else}config-group__options--text{/if}">
          {foreach from=$group.attributes key=id_attribute item=group_attribute}
            {assign var=impact_value value=$group_attribute.price_impact_formatted|default:$group_attribute.price_impact|default:$group_attribute.price|default:''}

            <label class="config-option {if $group.group_type == 'color'}config-option--color{else}config-option--text{/if}{if $group_attribute.selected} is-selected{/if}">
              <input
                class="{if $group.group_type == 'color'}input-color{else}input-radio{/if}"
                type="radio"
                data-product-attribute="{$id_attribute_group}"
                name="group[{$id_attribute_group}]"
                value="{$id_attribute}"
                title="{$group_attribute.name}"
                {if $group_attribute.selected}checked="checked"{/if}
              >

              <span
                class="config-option__card"
                data-raw-name="{$group_attribute.name|escape:'htmlall':'UTF-8'}"
                data-impact="{$impact_value|escape:'htmlall':'UTF-8'}"
              >
                {if $group.group_type == 'color'}
                  <span class="config-option__preview">
                    {if $group_attribute.texture}
                      <span class="config-option__swatch config-option__swatch--texture" style="background-image: url('{$group_attribute.texture}')"></span>
                    {elseif $group_attribute.html_color_code}
                      <span class="config-option__swatch" style="background-color: {$group_attribute.html_color_code}"></span>
                    {/if}
                  </span>
                {/if}

                <span class="config-option__meta">
                  <span class="config-option__name js-option-name">{$group_attribute.name}</span>
                  <span class="config-option__impact js-option-impact"></span>
                </span>
              </span>
            </label>
          {/foreach}
        </div>
      </details>
    {/if}
  {/foreach}
{/capture}

{if $smarty.capture.native_variant_groups|trim neq ''}
  <div class="product__configurator js-product-variants">
    {$smarty.capture.native_variant_groups nofilter}
  </div>
{/if}
