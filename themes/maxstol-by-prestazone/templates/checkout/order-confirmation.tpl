{extends file=$layout}
{$componentName = 'order-confirmation'}

{block name='content'}

  {block name='order_confirmation_header'}
    {include file='checkout/_partials/progress.tpl'}
  {/block}

  {block name='hook_payment_return'}
  {/block}

  {block name='hook_order_confirmation'}
    {$HOOK_ORDER_CONFIRMATION nofilter}
  {/block}

  {block name='hook_order_confirmation_1'}
    {hook h='displayOrderConfirmation1'}
  {/block}

{/block}
