document.addEventListener('DOMContentLoaded', function () {
  const faqItems = document.querySelectorAll('.faq-item');
  faqItems.forEach(function (item) {
    const button = item.querySelector('button');
    if (!button) return;
    button.addEventListener('click', function () {
      item.classList.toggle('is-open');
    });
  });

  const toggle = document.querySelector('.mobile-toggle');
  const nav = document.querySelector('.site-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', function () {
      nav.classList.toggle('is-open');
    });
  }

  const forms = document.querySelectorAll('form[data-ajax-form]');
  forms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      const button = form.querySelector('button[type="submit"]');
      const originalText = button ? button.textContent : '';
      if (button) {
        button.textContent = 'در حال ارسال...';
        button.disabled = true;
      }

      const formData = new FormData(form);
      fetch(window.location.href, {
        method: 'POST',
        body: formData
      })
        .then(function () {
          if (button) {
            button.textContent = 'ارسال شد';
          }
          form.reset();
        })
        .catch(function () {
          if (button) {
            button.textContent = originalText;
            button.disabled = false;
          }
        });
    });
  });
});
