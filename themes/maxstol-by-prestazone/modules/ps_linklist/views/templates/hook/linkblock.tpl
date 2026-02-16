{foreach $linkBlocks as $linkBlock}
  <div class="footer-block linklist">
    <p class="footer-block-title caption">{$linkBlock.title}</p>
    
    <ul id="footer_sub_menu_{$linkBlock.id}" class="footer__block__content footer__block__content-list collapse">
      {foreach $linkBlock.links as $link}
        <li>
          <a
              id="{$link.id}-{$linkBlock.id}"
              class="{$link.class} extra-small"
              href="{$link.url}"
              title="{$link.description}"
              {if !empty($link.target)} target="{$link.target}" {/if}
          >
            {$link.title}
          </a>
        </li>
      {/foreach}
    </ul>
  </div>
{/foreach}
