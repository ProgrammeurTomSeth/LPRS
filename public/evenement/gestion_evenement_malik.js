// Menu mobile (bouton "hamburger"), comme sur les autres pages
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', function () {
        const estOuvert = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(estOuvert));
    });
}

// Avant de refuser un inscrit, on demande une confirmation.
// Si l'utilisateur clique sur "Annuler", le formulaire n'est pas envoyé.
const formulairesRefuser = document.querySelectorAll('.form-refuser');

formulairesRefuser.forEach(function (formulaire) {
    formulaire.addEventListener('submit', function (evenement) {
        if (!confirm('Voulez-vous vraiment refuser cette inscription ?')) {
            evenement.preventDefault();
        }
    });
});
