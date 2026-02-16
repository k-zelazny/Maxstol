{$componentName = 'email-subscription'}

<div class="{$componentName}">
  <p class="footer-block-title caption">{l s='Sign up for the newsletter' d='Maxstol.Theme.Footer'}</p>
  
  <div class="{$componentName}__content">
    <div class="{$componentName}__content__right">
      <form action="{$urls.current_url}#blockEmailSubscription_{$hookName}" method="post">
        <div class="{$componentName}__content__infos">
          {if $conditions}
            <p>{$conditions}</p>
          {/if}
          
          {if $msg}
            <p class="alert {if $nw_error}alert-danger{else}alert-success{/if}">
              {$msg}
            </p>
          {/if}
          
          {hook h='displayNewsletterRegistration'}
          
          {if isset($id_module)}
            {hook h='displayGDPRConsent' id_module=$id_module}
          {/if}
        </div>
        
        <div class="{$componentName}__content__inputs inline-items">
          <input
            name="email"
            type="email"
            class="form-control"
            value="{$value}"
            placeholder="{l s='Email' d='Maxstol.Theme.Footer'}"
            aria-labelledby="block-newsletter-label-{$hookName}"
            required
          >
          
          <input
            class="btn btn-primary"
            name="submitNewsletter"
            type="submit"
            value="{l s='Subscribe' d='Shop.Theme.Actions'}"
          >
        </div>
        
        <input type="hidden" name="blockHookName" value="{$hookName}" />
        <input type="hidden" name="action" value="0">
      </form>
    </div>
  </div>
</div>
