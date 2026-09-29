// Menu mobile
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
  navToggle.addEventListener('click', () => {
    const isOpen = mainNav.classList.toggle('open');
    navToggle.setAttribute('aria-expanded', String(isOpen));
  });

  mainNav.querySelectorAll('a').forEach((link) => {
    link.addEventListener('click', () => {
      mainNav.classList.remove('open');
      navToggle.setAttribute('aria-expanded', 'false');
    });
  });
}

// Formulaire de contact (démo : pas d'envoi réel, à brancher sur un back-end)
const contactForm = document.getElementById('contact-form');
const formStatus = document.getElementById('form-status');

if (contactForm && formStatus) {
  contactForm.addEventListener('submit', (event) => {
    event.preventDefault();
    formStatus.hidden = false;
    formStatus.textContent = 'Merci ! Votre message a bien été envoyé.';
    contactForm.reset();
  });
}

// Formulaire d'inscription (démo : pas d'envoi réel, à brancher sur un back-end)
const registerForm = document.getElementById('register-form');
const registerStatus = document.getElementById('register-status');

if (registerForm && registerStatus) {
  registerForm.addEventListener('submit', (event) => {
    event.preventDefault();

    if (!registerForm.checkValidity()) {
      registerForm.reportValidity();
      return;
    }

    const submitBtn = registerForm.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;

    registerStatus.hidden = false;
    registerStatus.className = 'form-status form-status--success';
    registerStatus.textContent = 'Merci ! Votre demande d\'inscription a bien été envoyée, notre équipe vous recontactera sous 48h.';

    registerForm.reset();
    if (submitBtn) submitBtn.disabled = false;
  });
}
