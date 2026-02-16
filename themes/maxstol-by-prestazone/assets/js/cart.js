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