<ul class="checkout-progress">
  <li id="cart" class="checkout-progress__item {if $page.page_name === 'cart' || $page.page_name === 'checkout' || $page.page_name === 'order-confirmation'}active{/if}">Koszyk</li>
  <li id="checkout" class="checkout-progress__item {if $page.page_name === 'checkout' || $page.page_name === 'order-confirmation'}active{/if}">Dostawa i płatność</li>
  <li id="order-confirmation" class="checkout-progress__item {if $page.page_name === 'order-confirmation'}active{/if}">Podsumowanie zamówienia</li>
</ul>