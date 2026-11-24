// Menu mobile
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

// Un formulaire avec data-confirmer demande confirmation avant d'être envoyé (« Ne pas retenir »)
document.querySelectorAll('form[data-confirmer]').forEach((formulaire) => {
    formulaire.addEventListener('submit', (event) => {
        if (!confirm(formulaire.dataset.confirmer)) {
            event.preventDefault();
        }
    });
});
