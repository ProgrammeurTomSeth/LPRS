// Menu mobile
const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

// Recherche et filtre par rôle dans la liste des utilisateurs.
// Chaque ligne du tableau porte data-role et data-recherche (nom, prénom et email en minuscules).
const champRecherche = document.getElementById('recherche');
const filtreRole = document.getElementById('filtre-role');
const compteur = document.getElementById('compteur');
const messageVide = document.getElementById('liste-vide');
const lignes = document.querySelectorAll('.admin-table tbody tr');

function filtrerUtilisateurs() {
    const texte = champRecherche.value.trim().toLowerCase();
    const role = filtreRole.value;
    let visibles = 0;

    lignes.forEach((ligne) => {
        const memeRole = (role === '' || ligne.dataset.role === role);
        const memeTexte = ligne.dataset.recherche.includes(texte);
        ligne.hidden = !(memeRole && memeTexte);
        if (!ligne.hidden) {
            visibles++;
        }
    });

    compteur.textContent = visibles + (visibles > 1 ? ' utilisateurs' : ' utilisateur');
    messageVide.hidden = (visibles > 0);
}

if (champRecherche && filtreRole) {
    champRecherche.addEventListener('input', filtrerUtilisateurs);
    filtreRole.addEventListener('change', filtrerUtilisateurs);
}

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
