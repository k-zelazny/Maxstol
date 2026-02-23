<section class="contact-form container-xs">
  <h2 class="contact-form__title h3">{l s='Get in touch with us' d='Maxstol.Theme.Contact'}</h2>
  <p class="contact-form__desc body-small">{l s='Having a technical issue? Want to send feedback on a beta feature? Need details about our business plan? Let us know.' d='Maxstol.Theme.Contact'}</p>
  
  <form action="{if $page.page_name === 'index'}{$urls.base_url}{else}{$urls.pages.contact}{/if}" method="post" {if $contact.allow_file_upload}enctype="multipart/form-data"{/if}>
    {if $notifications}
      <div class="alert {if $notifications.nw_error}alert-danger{else}alert-success{/if}">
        <ul>
          {foreach $notifications.messages as $notif}
            <li>{$notif}</li>
          {/foreach}
        </ul>
      </div>
    {/if}

    {if !$notifications || $notifications.nw_error}
      <section class="form-fields">
        <div class="mb-3">
          <label class="form-label">{l s='Your email' d='Maxstol.Theme.Contact'}</label>
          <input
            class="form-control"
            name="from"
            type="email"
            value="{$contact.email}"
            placeholder="{l s='your@email.com' d='Shop.Forms.Help'}"
          >
        </div>

        <div class="mb-3">
          <label class="form-label">{l s='Subject' d='Shop.Forms.Labels'}</label>
          <select name="id_contact" class="form-select">
            {foreach from=$contact.contacts item=contact_elt}
              <option value="{$contact_elt.id_contact}">{$contact_elt.name}</option>
            {/foreach}
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label">{l s='Your message' d='Maxstol.Theme.Contact'}</label>
          <textarea
            class="form-control"
            name="message"
            rows="3"
          >{if $contact.message}{$contact.message}{/if}</textarea>
        </div>

        {if isset($id_module)}
          <div class="mb-3">
            {hook h='displayGDPRConsent' id_module=$id_module}
          </div>
        {/if}

      </section>

      <footer class="form-footer">
        <style>
          input[name=url] {
            display: none !important;
          }
        </style>
        <input type="text" name="url" value=""/>
        <input type="hidden" name="token" value="{$token}" />
        <input class="btn btn-primary" type="submit" name="submitMessage" value="{l s='Send message' d='Maxstol.Theme.Contact'}">
      </footer>
    {/if}
  </form>
</section>
