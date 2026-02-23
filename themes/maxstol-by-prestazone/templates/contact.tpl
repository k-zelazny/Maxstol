{extends file='page.tpl'}

{block name='page_header_container'}{/block}

{block name='page_content'}
  {hook h='displayContactRightColumn'}
  
  {hook h='displayContactContent'}
  
  {hook h='displayContactLeftColumn'}
{/block}
