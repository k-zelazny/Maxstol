{if !empty($groups)}
  {if !empty($include_assets_fallback)}
    <link rel="stylesheet" href="{$front_css_uri|escape:'htmlall':'UTF-8'}" type="text/css" media="all">
  {/if}
  <script type="text/javascript" src="{$front_js_uri|escape:'htmlall':'UTF-8'}"></script>

  <div
    class="product__configurator product__configurator--module js-pz-productconfigurator"
    data-product-id="{$id_product|intval}"
    data-customization-sync-url="{$customization_sync_url|escape:'htmlall':'UTF-8'}"
    data-cart-token="{$cart_token|escape:'htmlall':'UTF-8'}"
    data-sync-error-message="{$sync_error_message|escape:'htmlall':'UTF-8'}"
    data-choose-option-label="{$choose_option_label|escape:'htmlall':'UTF-8'}"
  >
    {foreach from=$groups item=group}
      <details
        class="config-group config-group--{$group.presentation|escape:'htmlall':'UTF-8'}"
        data-group-code="{$group.code|escape:'htmlall':'UTF-8'}"
        data-group-label="{$group.label|escape:'htmlall':'UTF-8'}"
        data-selection="{$group.selection|escape:'htmlall':'UTF-8'}"
        data-none-label="{$group.none_label|escape:'htmlall':'UTF-8'}"
        data-depends-on-group="{$group.depends_on_group|default:''|escape:'htmlall':'UTF-8'}"
        data-depends-on-option="{$group.depends_on_option|default:''|escape:'htmlall':'UTF-8'}"
        open
      >
        <summary class="config-group__summary">
          <span class="config-group__title">{$group.label}</span>
          <span class="config-group__value js-config-selected">{$group.selected_summary|default:''|escape:'htmlall':'UTF-8'}</span>
        </summary>

        {if $group.show_none_option}
          <div class="config-group__none">
            <label
              class="config-none-toggle js-pz-config-option{if $group.selected_summary == $group.none_label} is-selected{/if}"
              data-option-code=""
              data-option-label="{$group.none_label|escape:'htmlall':'UTF-8'}"
              data-option-price="0"
              data-product-id="0"
              data-attribute-id="0"
              data-is-default="0"
              data-selected="{if $group.selected_summary == $group.none_label}1{else}0{/if}"
            >
              <input
                class="js-pz-config-input"
                type="radio"
                name="pz_productconfigurator_group[{$group.code|escape:'htmlall':'UTF-8'}]"
                value=""
                {if $group.selected_summary == $group.none_label}checked="checked"{/if}
              >
              <span class="config-none-toggle__box" aria-hidden="true"></span>
              <span class="config-none-toggle__label">{$group.none_label}</span>
            </label>
          </div>
        {/if}

        <div
          class="config-group__options {if $group.presentation == 'swatch'}config-group__options--color{elseif $group.presentation == 'image'}config-group__options--image{else}config-group__options--text{/if}"
          {if $group.presentation == 'image' && $group.image_columns|intval > 0}
            data-image-columns="{$group.image_columns|intval}"
            style="--pzpc-image-columns: {$group.image_columns|intval};"
          {/if}
        >

          {foreach from=$group.options item=option}
            {assign var=option_impact_display value=$option.formatted_price|regex_replace:'/^\+/':''}
            <label
              class="config-option {if $group.presentation == 'image'}config-option--image{elseif $group.presentation == 'swatch'}config-option--color{else}config-option--text{/if} js-pz-config-option{if $option.is_default} is-selected{/if}"
              data-option-code="{$option.code|escape:'htmlall':'UTF-8'}"
              data-option-label="{$option.label|escape:'htmlall':'UTF-8'}"
              data-option-price="{$option.price}"
              data-product-id="{$option.product_id|intval}"
              data-attribute-id="{$option.attribute_id|intval}"
              data-is-default="{if $option.is_default}1{else}0{/if}"
              data-selected="{if $option.is_default}1{else}0{/if}"
            >
              <input
                class="js-pz-config-input {if $group.selection == 'multiple'}input-checkbox{else}input-radio{/if}"
                type="{if $group.selection == 'multiple'}checkbox{else}radio{/if}"
                name="pz_productconfigurator_group[{$group.code|escape:'htmlall':'UTF-8'}]{if $group.selection == 'multiple'}[]{/if}"
                value="{$option.code|escape:'htmlall':'UTF-8'}"
                {if $option.is_default}checked="checked"{/if}
              >
              <span
                class="config-option__card"
                data-raw-name="{$option.label|escape:'htmlall':'UTF-8'}"
                data-impact="{$option_impact_display|escape:'htmlall':'UTF-8'}"
              >
                {if $group.presentation == 'text'}
                  <span class="config-option__checkbox" aria-hidden="true"></span>
                {/if}
                {if $option.preview_kind != 'none'}
                  <span class="config-option__preview">
                    {if $option.preview_kind == 'image'}
                      <img class="config-option__preview-image" src="{$option.preview_image|escape:'htmlall':'UTF-8'}" alt="{$option.label|escape:'htmlall':'UTF-8'}" loading="lazy">
                    {elseif $option.preview_kind == 'texture'}
                      <span class="config-option__swatch config-option__swatch--texture" style="background-image: url('{$option.preview_image|escape:'htmlall':'UTF-8'}')"></span>
                    {elseif $option.preview_kind == 'color'}
                      <span class="config-option__swatch" style="background-color: {$option.preview_color|escape:'htmlall':'UTF-8'}"></span>
                    {/if}
                  </span>
                {/if}

                <span class="config-option__meta">
                  <span class="config-option__name-wrap">
                    <span class="config-option__name js-option-name">{$option.label}</span>
                    {if $group.presentation == 'text' && $option.description|trim != ''}
                      <span
                        class="config-option__info"
                        tabindex="0"
                        role="img"
                        aria-label="{$option_info_label|escape:'htmlall':'UTF-8'}: {$option.description|escape:'htmlall':'UTF-8'}"
                        data-tooltip="{$option.description|escape:'htmlall':'UTF-8'}"
                        title="{$option.description|escape:'htmlall':'UTF-8'}"
                      >
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <g clip-path="url(#clip0_2093_15295)">
                            <path d="M8 0C6.41775 0 4.87104 0.469192 3.55544 1.34824C2.23985 2.22729 1.21447 3.47672 0.608967 4.93853C0.00346629 6.40034 -0.15496 8.00887 0.153721 9.56072C0.462403 11.1126 1.22433 12.538 2.34315 13.6569C3.46197 14.7757 4.88743 15.5376 6.43928 15.8463C7.99113 16.155 9.59966 15.9965 11.0615 15.391C12.5233 14.7855 13.7727 13.7602 14.6518 12.4446C15.5308 11.129 16 9.58225 16 8C15.9977 5.87897 15.1541 3.84547 13.6543 2.34568C12.1545 0.845886 10.121 0.00229405 8 0V0ZM8 14.6667C6.68146 14.6667 5.39253 14.2757 4.2962 13.5431C3.19987 12.8106 2.34539 11.7694 1.84081 10.5512C1.33622 9.33305 1.2042 7.99261 1.46144 6.6994C1.71867 5.40619 2.35361 4.21831 3.28596 3.28596C4.21831 2.35361 5.4062 1.71867 6.6994 1.46143C7.99261 1.2042 9.33305 1.33622 10.5512 1.8408C11.7694 2.34539 12.8106 3.19987 13.5431 4.2962C14.2757 5.39253 14.6667 6.68146 14.6667 8C14.6647 9.76752 13.9617 11.4621 12.7119 12.7119C11.4621 13.9617 9.76752 14.6647 8 14.6667Z" fill="#CE2B2D"/>
                            <path d="M7.99983 6.66602H7.33317C7.15636 6.66602 6.98679 6.73625 6.86177 6.86128C6.73674 6.9863 6.6665 7.15587 6.6665 7.33268C6.6665 7.50949 6.73674 7.67906 6.86177 7.80409C6.98679 7.92911 7.15636 7.99935 7.33317 7.99935H7.99983V11.9993C7.99983 12.1762 8.07007 12.3457 8.1951 12.4708C8.32012 12.5958 8.48969 12.666 8.6665 12.666C8.84331 12.666 9.01288 12.5958 9.1379 12.4708C9.26293 12.3457 9.33317 12.1762 9.33317 11.9993V7.99935C9.33317 7.64573 9.19269 7.30659 8.94264 7.05654C8.69259 6.80649 8.35346 6.66602 7.99983 6.66602Z" fill="#CE2B2D"/>
                            <path d="M8 5.33398C8.55228 5.33398 9 4.88627 9 4.33398C9 3.7817 8.55228 3.33398 8 3.33398C7.44772 3.33398 7 3.7817 7 4.33398C7 4.88627 7.44772 5.33398 8 5.33398Z" fill="#CE2B2D"/>
                          </g>
                          <defs>
                            <clipPath id="clip0_2093_15295">
                              <rect width="16" height="16" fill="white"/>
                            </clipPath>
                          </defs>
                        </svg>
                      </span>
                    {/if}
                  </span>
                  <span class="config-option__impact js-option-impact{if $option_impact_display != ''} is-visible{/if}">{$option_impact_display}</span>
                </span>
              </span>
            </label>
          {/foreach}
        </div>
      </details>
    {/foreach}

    <input type="hidden" class="js-pz-productconfigurator-payload" name="{$payload_input_name|escape:'htmlall':'UTF-8'}" value="">
    <input type="hidden" class="js-pz-productconfigurator-total" name="{$payload_total_name|escape:'htmlall':'UTF-8'}" value="0">
  </div>
{/if}

<script type="application/json" id="pz-productconfigurator-schema">{$schema_json nofilter}</script>
<script type="application/json" id="pz-productconfigurator-data">{$impact_map_json nofilter}</script>
