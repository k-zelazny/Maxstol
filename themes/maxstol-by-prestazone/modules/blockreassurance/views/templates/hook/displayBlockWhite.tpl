
<div class="blockreassurance">
  <p class="footer-block-title caption">{l s='Payment methods' d='Maxstol.Theme.Footer'}</p>
  
  <div class="blocks">
    {foreach from=$blocks item=$block key=$key name=blocks}
      <div class="block-icon">
        <img src="{$block['custom_icon']}">
      </div>
    {/foreach}
  </div>
</div>
