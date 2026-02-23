<div class="contact__details">
  <div class="contact__item">
    <div class="contact__info">
      <h3>{$shop.name}</h3>
      {$contact_infos.address.address1 nofilter}
      {$contact_infos.address.address2 nofilter},
      {$contact_infos.address.postcode nofilter}
      {$contact_infos.address.city nofilter}
      </div>
  </div>
  
  {if $contact_infos.phone}
    <div class="contact__item">
      <h3>{l s='Phone number' d='Modules.Pscontactinfo.Shop'}</h3>
      <div class="contact__info"><a href="tel:{$contact_infos.phone}">{$contact_infos.phone}</a></div>
    </div>
  {/if}
  
  {if $contact_infos.email && $display_email}
    <div class="contact__item">
      <h3>{l s='Email' d='Modules.Pscontactinfo.Shop'}</h3>
      <div class="contact__info contact__info--email">{mailto address=$contact_infos.email encode="javascript"}</div>
    </div>
  {/if}
</div>
