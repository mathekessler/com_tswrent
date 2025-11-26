document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.select-link').forEach(link => {
    link.addEventListener('click', (event) => {
      event.preventDefault();

      const el = event.currentTarget;
      const funcName = el.getAttribute('data-function');

      const product = {
        id: el.getAttribute('data-id'),
        name: el.getAttribute('data-title'),
        uri: el.getAttribute('data-uri'),
      };

      if (funcName && typeof window.parent[funcName] === 'function') {
        window.parent[funcName](product);
      } 

      // Close the modal
      const modalEl = document.getElementById('ModalSelect');
    });
  });
});
