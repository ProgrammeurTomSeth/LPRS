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

// Formulaire d'inscription : affiche les champs correspondant au profil choisi
const registerForm = document.getElementById('register-form');
const roleSelect = document.getElementById('role');

if (registerForm && roleSelect) {
  const majChampsSelonRole = () => {
    const role = roleSelect.value;

    registerForm.querySelectorAll('[data-roles]').forEach((element) => {
      element.hidden = !element.dataset.roles.split(' ').includes(role);
    });

    registerForm.querySelectorAll('[data-required-roles]').forEach((champ) => {
      champ.required = champ.dataset.requiredRoles.split(' ').includes(role);
    });

    // Les champs masqués sont désactivés : ni validés ni envoyés
    registerForm.querySelectorAll('input, select, textarea').forEach((champ) => {
      champ.disabled = champ.closest('[hidden]') !== null;
    });
  };

  roleSelect.addEventListener('change', majChampsSelonRole);
  majChampsSelonRole();

  const mdp = document.getElementById('mdp');
  const mdpConfirm = document.getElementById('mdp_confirm');
  if (mdp && mdpConfirm) {
    const verifierMdp = () => {
      mdpConfirm.setCustomValidity(mdpConfirm.value !== mdp.value ? 'Les mots de passe ne correspondent pas.' : '');
    };
    mdp.addEventListener('input', verifierMdp);
    mdpConfirm.addEventListener('input', verifierMdp);
  }
}
