document.querySelectorAll('.questions__container-dropdown').forEach(dropdown => {
    dropdown.addEventListener('click', () => {
      dropdown.classList.toggle('active');
    });
  });