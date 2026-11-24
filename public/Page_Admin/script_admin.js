// Menu mobile
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

// Recherche (et filtre éventuel) dans chaque liste de la page.
// Chaque ligne du tableau porte data-recherche (texte en minuscules) et, si la liste a un filtre, data-role.
document.querySelectorAll('.admin-card--liste').forEach((carte) => {
    const champRecherche = carte.querySelector('.outil-recherche');
    if (!champRecherche) {
        return; // liste vide : pas de barre de recherche
    }

    const filtre = carte.querySelector('.outil-filtre');
    const compteur = carte.querySelector('.liste-compteur');
    const messageVide = carte.querySelector('.liste-vide');
    const lignes = carte.querySelectorAll('.admin-table tbody tr');
    const unite = (compteur && compteur.dataset.unite) ? compteur.dataset.unite : 'utilisateur';

    function filtrer() {
        const texte = champRecherche.value.trim().toLowerCase();
        let visibles = 0;

        lignes.forEach((ligne) => {
            const memeRole = (!filtre || filtre.value === '' || ligne.dataset.role === filtre.value);
            const memeTexte = ligne.dataset.recherche.includes(texte);
            ligne.hidden = !(memeRole && memeTexte);
            if (!ligne.hidden) {
                visibles++;
            }
        });

        if (compteur) {
            compteur.textContent = visibles + ' ' + unite + (visibles > 1 ? 's' : '');
        }
        if (messageVide) {
            messageVide.hidden = (visibles > 0);
        }
    }

    champRecherche.addEventListener('input', filtrer);
    if (filtre) {
        filtre.addEventListener('change', filtrer);
    }
});

// Un formulaire avec data-confirmer demande confirmation avant d'être envoyé (ex. suppression)
document.querySelectorAll('form[data-confirmer]').forEach((formulaire) => {
    formulaire.addEventListener('submit', (event) => {
        if (!confirm(formulaire.dataset.confirmer)) {
            event.preventDefault();
        }
    });
});

// Fenêtre « Ajouter un utilisateur » : s'ouvre au milieu de la page avec showModal()
const fenetreAjout = document.getElementById('dialogue-ajout');
const boutonAjout = document.getElementById('ouvrir-ajout');

if (fenetreAjout && boutonAjout) {
    boutonAjout.addEventListener('click', () => {
        fenetreAjout.showModal();
    });

    // Boutons « × » et « Annuler »
    fenetreAjout.querySelectorAll('[data-fermer]').forEach((bouton) => {
        bouton.addEventListener('click', () => {
            fenetreAjout.close();
        });
    });

    // Un clic sur le fond sombre (en dehors de la fenêtre) la ferme aussi
    fenetreAjout.addEventListener('click', (event) => {
        if (event.target === fenetreAjout) {
            fenetreAjout.close();
        }
    });

    // On vide le formulaire à la fermeture
    fenetreAjout.addEventListener('close', () => {
        fenetreAjout.querySelector('form').reset();
    });
}

// Fenêtre « Modifier un utilisateur » : le PHP ne l'écrit dans la page que si l'adresse contient ?modifier=ID.
// On l'ouvre donc tout de suite ; en la fermant, on revient à la page sans ?modifier=ID.
const fenetreModif = document.getElementById('dialogue-modifier');

if (fenetreModif) {
    fenetreModif.showModal();

    fenetreModif.querySelectorAll('[data-fermer]').forEach((bouton) => {
        bouton.addEventListener('click', () => {
            fenetreModif.close();
        });
    });

    fenetreModif.addEventListener('click', (event) => {
        if (event.target === fenetreModif) {
            fenetreModif.close();
        }
    });

    fenetreModif.addEventListener('close', () => {
        window.location.href = 'page_admin.php';
    });
}
