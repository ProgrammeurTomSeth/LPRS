// Script de la page moderation_forum.php

// Menu mobile
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

// Confirmation avant de supprimer un message ou d'ignorer un signalement.
// Le texte de la question est dans l'attribut data-confirmation du formulaire.
document.querySelectorAll('.form-confirmer').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!confirm(form.dataset.confirmation)) {
            event.preventDefault(); // le gestionnaire a cliqué sur "Annuler"
        }
    });
});
