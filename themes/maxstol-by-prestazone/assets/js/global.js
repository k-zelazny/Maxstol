// Toggle Search
document.addEventListener('click', (event) => {
  if (event.target.closest('[data-action="toggleSearch"]')) {
    event.preventDefault();
    const headerBottom = document.querySelector('#header .header-bottom');
    headerBottom.classList.toggle('active');
  }
});
// END Toggle Search