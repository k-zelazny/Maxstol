// Featured Products
const swiperCart = new Swiper('.ps-featuredproducts .swiper', {
  slidesPerView: 1,
  spaceBetween: 12,
  autoplay: {
    delay: 5000,
    disableOnInteraction: true,
  },
  breakpoints: {
    576: {
      slidesPerView: 2,
      spaceBetween: 12,
    },
    768: {
      slidesPerView: 3,
      spaceBetween: 24,
    },
    992: {
      slidesPerView: 4,
      spaceBetween: 24,
    },
  }
});
// END Featured Products

// Buy now redirects to checkout after successful add-to-cart.
let buyNowPending = false;
let buyNowCheckoutUrl = '';
let buyNowResetTimer = null;

const resetBuyNowState = () => {
  buyNowPending = false;
  buyNowCheckoutUrl = '';

  if (buyNowResetTimer) {
    window.clearTimeout(buyNowResetTimer);
    buyNowResetTimer = null;
  }
};

document.addEventListener('click', (event) => {
  const buyNowButton = event.target.closest('.js-buy-now');
  if (!buyNowButton) {
    return;
  }

  buyNowPending = true;
  buyNowCheckoutUrl = buyNowButton.dataset.checkoutUrl || '';

  if (buyNowResetTimer) {
    window.clearTimeout(buyNowResetTimer);
  }

  buyNowResetTimer = window.setTimeout(() => {
    resetBuyNowState();
  }, 5000);
});

if (typeof prestashop !== 'undefined' && prestashop && typeof prestashop.on === 'function') {
  prestashop.on('updateCart', (event) => {
    if (!buyNowPending) {
      return;
    }

    const isAddToCart = !!(event && event.reason && event.reason.linkAction === 'add-to-cart');
    if (!isAddToCart) {
      return;
    }

    const hasError = !!(event && event.resp && event.resp.hasError);
    if (hasError) {
      resetBuyNowState();
      return;
    }

    const redirectUrl = buyNowCheckoutUrl;
    resetBuyNowState();

    if (redirectUrl !== '') {
      window.location.href = redirectUrl;
    }
  });

  prestashop.on('handleError', (event) => {
    if (!buyNowPending) {
      return;
    }

    if (event && event.eventType === 'addProductToCart') {
      resetBuyNowState();
    }
  });
}
