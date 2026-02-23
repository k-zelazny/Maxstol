<div class="pz-orderconfirmation">
  <div class="pz-orderconfirmation__overview">
    {foreach $products as $product}
      <div class="pz-orderconfirmation__overview-line">
        <div class="pz-orderconfirmation__line-image">
          <img width="56" height="56" src="{$product.image_url}" alt="{$product.name}">
        </div>
        
        <div class="pz-orderconfirmation__line-name">
          <a href="{$product.product_url}" target="_blank">{$product.name}</a>
        </div>
        
        <div class="pz-orderconfirmation__line-quantity">
          x{$product.quantity}
        </div>
        
        <div class="pz-orderconfirmation__line-price">
          {$product.price}
        </div>
      </div>
    {/foreach}
  </div>
  
  <div class="pz-orderconfirmation__sidebar">
    <div class="pz-orderconfirmation__sidebar-block">
      <h3 class="pz-orderconfirmation__sidebar-title">{l s='Order details' d='Modules.Pzorderconfirmation.Front'}</h3>
      
      <ul class="pz-orderconfirmation__sidebar-details">
        {if $order && $order->date_add}
          <li class="pz-orderconfirmation__details-line">
            <span class="label">{l s='Order date' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$order->date_add|date_format:"%e %B %Y"}</span>
          </li>
        {/if}
        
        {if $customerDetails && $customerDetails->email}
          <li class="pz-orderconfirmation__details-line">
            <span class="label">{l s='Email' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$customerDetails->email}</span>
          </li>
        {/if}
        
        {if $deliveryAddress && $deliveryAddress->phone}
          <li class="pz-orderconfirmation__details-line">
            <span class="label">{l s='Phone number' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$deliveryAddress->phone}</span>
          </li>
        {/if}
        
        {if $order && $order->payment}
          <li class="pz-orderconfirmation__details-line">
            <span class="label">{l s='Payment method' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$order->payment}</span>
          </li>
        {/if}
        
        {if $carrier && $carrier->name}
          <li class="pz-orderconfirmation__details-line">
            <span class="label">{l s='Shipping method' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$carrier->name}</span>
          </li>
        {/if}
        
        {if $deliveryAddress}
          <li class="pz-orderconfirmation__details-line">
            <span class="label">{l s='Delivery address' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">
              {$deliveryAddress->address1} {$deliveryAddress->address2}<br />
              {$deliveryAddress->postcode} {$deliveryAddress->city}
            </span>
          </li>
        {/if}
      </ul>
    </div>
    
    <div class="pz-orderconfirmation__sidebar-block">
      <h3 class="pz-orderconfirmation__sidebar-title">{l s='Order amount' d='Modules.Pzorderconfirmation.Front'}</h3>
      
      <ul class="pz-orderconfirmation__sidebar-summary">
        {if $summary && $summary.products}
          <li class="pz-orderconfirmation__summary-line">
            <span class="label">{l s='Price' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$summary.products}</span>
          </li>
        {/if}
        
        {if $summary && $summary.discount}
          <li class="pz-orderconfirmation__summary-line discount">
            <span class="label">{l s='Discount' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$summary.discount}</span>
          </li>
        {/if}
        
        {if $summary && $summary.shipping}
          <li class="pz-orderconfirmation__summary-line">
            <span class="label">{l s='Shipping' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$summary.shipping}</span>
          </li>
        {/if}
        
        {if $summary && $summary.tax}
          <li class="pz-orderconfirmation__summary-line">
            <span class="label">{l s='VAT' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$summary.tax}</span>
          </li>
        {/if}
        
        {if $summary && $summary.total}
          <li class="pz-orderconfirmation__summary-line">
            <span class="label">{l s='Total' d='Modules.Pzorderconfirmation.Front'}</span>
            <span class="value">{$summary.total}</span>
          </li>
        {/if}
      </ul>
    </div>
  </div>
</div>