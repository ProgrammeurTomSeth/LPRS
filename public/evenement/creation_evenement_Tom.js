const navToggle = document.getElementById('nav-toggle');
const mainNav = document.getElementById('main-nav');

if (navToggle && mainNav) {
    navToggle.addEventListener('click', () => {
        const isOpen = mainNav.classList.toggle('open');
        navToggle.setAttribute('aria-expanded', String(isOpen));
    });
}

const rechercheInput = document.getElementById('recherche_utilisateur');
const resultats = document.getElementById('resultats_recherche');
const selection = document.getElementById('organisateurs_selectionnes');
const utilisateursSelectionnes = new Map();

if (rechercheInput) {
    rechercheInput.addEventListener('input',
        function () {

            const recherche = this.value.trim();

            if (recherche.length < 2) {
                resultats.innerHTML = '';
                return;
            }

            fetch('../../src/traitement/EvenementTraitement_Tom.php?recherche=' + encodeURIComponent(recherche))
                .then(response => response.json())
                .then(utilisateurs => {
                    resultats.innerHTML = '';
                    if (utilisateurs.length === 0) {
                        resultats.innerHTML =
                            '<div class="aucun-resultat">' +
                            'Aucun utilisateur trouvé.' +
                            '</div>';
                        return;
                    }
                    utilisateurs.forEach(
                        utilisateur => {
                            if (
                                utilisateursSelectionnes.has(
                                    String(utilisateur.id_utilisateur)
                                )
                            ) {
                                return;
                            }
                            const div = document.createElement('div')
                            div.className = 'utilisateur';
                            div.innerHTML =
                                '<strong>' +
                                escapeHtml(
                                    utilisateur.prenom_utilisateur +
                                    ' ' +
                                    utilisateur.nom_utilisateur
                                ) +
                                '</strong>' +
                                '<br>' +
                                escapeHtml(
                                    utilisateur.mail
                                ) +
                                ' - ' +
                                escapeHtml(
                                    utilisateur.role
                                ) +
                                ' ' +
                                '<button type="button">' +
                                'Ajouter' +
                                '</button>';

                            div.querySelector('button')
                                .addEventListener(
                                    'click',
                                    function () {

                                        ajouterOrganisateur(
                                            utilisateur
                                        );

                                        resultats.innerHTML =
                                            '';
                                        rechercheInput.value =
                                            '';
                                    }
                                );

                            resultats.appendChild(div);
                        }
                    );
                })
                .catch(() => {

                    resultats.innerHTML =
                        '<div class="aucun-resultat">' +
                        'Erreur lors de la recherche.' +
                        '</div>';
                });
        }
    );
}

function ajouterOrganisateur(utilisateur) {
    const id = String(utilisateur.id_utilisateur);
    if (utilisateursSelectionnes.has(id)) {
        return;
    }
    utilisateursSelectionnes.set(id, utilisateur);
    afficherOrganisateurs();
}

function supprimerOrganisateur(id) {
    utilisateursSelectionnes.delete(String(id));
    afficherOrganisateurs();
}

function afficherOrganisateurs() {
    selection.innerHTML = '';
    utilisateursSelectionnes.forEach(
        utilisateur => {
            const div = document.createElement('div');
            div.className = 'organisateur';
            div.innerHTML = escapeHtml(
                    utilisateur.prenom_utilisateur +
                    ' ' +
                    utilisateur.nom_utilisateur
                ) +
                ' (' +
                escapeHtml(
                    utilisateur.role
                ) +
                ')' +
                ' ' +
                '<button type="button">X</button>' +

                '<input type="hidden" ' +
                'name="coorganisateurs[]" ' +
                'value="' +
                escapeHtml(
                    utilisateur.id_utilisateur
                ) +
                '">';
            div.querySelector('button').addEventListener(
                    'click',
                    function () {
                        supprimerOrganisateur(
                            utilisateur.id_utilisateur
                        );
                    }
                );
            selection.appendChild(div);
        }
    );
}

function escapeHtml(texte) {
    const div = document.createElement('div');
    div.textContent = texte;
    return div.innerHTML;
}
