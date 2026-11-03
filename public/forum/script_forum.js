// Script commun aux pages du forum

// Menu mobile
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

// Gestionnaire : confirmation avant de supprimer un message.
// Le texte de la question est dans l'attribut data-confirmation du formulaire.
document.querySelectorAll('.form-supprimer').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!confirm(form.dataset.confirmation)) {
            event.preventDefault(); // l'utilisateur a cliqué sur "Annuler"
        }
    });
});

// Bouton "Répondre" : affiche le formulaire de réponse du commentaire.
// data-formulaire = id du formulaire à ouvrir,
// data-mention = "@Prénom " quand on répond à une réponse.
document.querySelectorAll('.bouton-repondre').forEach((bouton) => {
    bouton.addEventListener('click', () => {
        const formulaire = document.getElementById(bouton.dataset.formulaire);
        const zoneTexte = formulaire.querySelector('textarea');

        formulaire.hidden = false;
        if (bouton.dataset.mention !== '' && zoneTexte.value === '') {
            zoneTexte.value = bouton.dataset.mention;
        }
        zoneTexte.focus();
    });
});

// Bouton "Annuler" d'un formulaire de réponse : vide et cache le formulaire
document.querySelectorAll('.bouton-annuler-reponse').forEach((bouton) => {
    bouton.addEventListener('click', () => {
        const formulaire = bouton.closest('form');
        formulaire.reset();
        formulaire.hidden = true;
    });
});

// Arrivée sur un message précis (adresse terminée par #message-12) :
// s'il est dans un bloc de réponses replié, on ouvre le bloc pour le montrer
if (location.hash.startsWith('#message-')) {
    const message = document.querySelector(location.hash);
    if (message) {
        const bloc = message.closest('details');
        if (bloc) {
            bloc.open = true;
        }
        message.scrollIntoView({ block: 'center' });
    }
}
