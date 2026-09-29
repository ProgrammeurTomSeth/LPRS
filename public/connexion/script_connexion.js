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

// Afficher / masquer le mot de passe
const passwordToggle = document.getElementById('password-toggle');
const passwordInput = document.getElementById('mdp');

if (passwordToggle && passwordInput) {
  passwordToggle.addEventListener('click', () => {
    const visible = passwordInput.type === 'password';
    passwordInput.type = visible ? 'text' : 'password';
    passwordToggle.textContent = visible ? 'Masquer' : 'Afficher';
    passwordToggle.setAttribute('aria-pressed', String(visible));
  });
}

// Évite le double envoi du formulaire
const loginForm = document.getElementById('login-form');

if (loginForm) {
  loginForm.addEventListener('submit', () => {
    const submitBtn = loginForm.querySelector('button[type="submit"]');
    if (submitBtn) submitBtn.disabled = true;
  });
}
