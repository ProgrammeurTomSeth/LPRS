<?php

$message = "";
$erreur = "";

require_once __DIR__ . '/../../src/traitement/EvenementTraitement_Tom.php';
require_once __DIR__ . '/../../src/traitement/SessionUtilisateur_ahmed.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="creation_evenement_Tom.css">
</head>
<body>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="../page_acceuille/page_accueil.php">Programme</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Activités</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <?php afficherActionsHeader('../'); ?>
        </div>

        <button class="nav-toggle" id="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main id="main">

    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Créer un événement</p>
            <h1>Créer un événement</h1>
            <p>Organisez une conférence, une rencontre ou un atelier avec la communauté du lycée.</p>
        </div>
    </section>

    <section class="section form-section">
        <div class="container">
            <div class="form-card">

                <h2>Nouvel événement</h2>

                <?php if ($message !== ""): ?>
                    <div class="alert alert--success">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <?php if ($erreur !== ""): ?>
                    <div class="alert alert--error" role="alert">
                        <?= htmlspecialchars($erreur) ?>
                    </div>
                <?php endif; ?>

                <?php if ($erreur === "" && $message === ""): ?>

                    <div class="alert alert--info">
                        Le créateur est automatiquement ajouté comme organisateur. Si le créateur est étudiant, un professeur doit faire partie des organisateurs pour que l'événement soit publié.
                    </div>
                    <form method="POST">
                        <div class="form-group">
                            <label for="type_evenement">Type d'événement *</label>
                            <input type="text" id="type_evenement" name="type_evenement" required placeholder="Exemple : Conférence, rencontre, atelier...">
                        </div>

                        <div class="form-group">
                            <label for="titre">Titre *</label>
                            <input type="text" id="titre" name="titre" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="lieu">Lieu / adresse *</label>
                            <input type="text" id="lieu" name="lieu" required placeholder="Exemple : Lycée Robert Schuman, Dugny">
                        </div>

                        <div class="form-group">
                            <label for="elements_requis">Éléments requis</label>
                            <input type="text" id="elements_requis" name="elements_requis" placeholder="Facultatif">
                        </div>

                        <div class="form-group">
                            <label for="nb_places">Nombre de places</label>
                            <input type="number" id="nb_places" name="nb_places" min="1" placeholder="Facultatif">
                        </div>

                        <div class="form-group">
                            <label for="recherche_utilisateur">Ajouter des co-organisateurs</label>
                            <input type="text" id="recherche_utilisateur" placeholder="Rechercher par nom, prénom ou email" autocomplete="off">
                            <div id="resultats_recherche" class="resultats"></div>
                        </div>

                        <div class="form-group">
                            <label>Co-organisateurs sélectionnés</label>
                            <div id="organisateurs_selectionnes"></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">Créer l'événement</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </section>

</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 Lycée Robert Shuman. Tous droits réservés.</p>
    </div>
</footer>

<script>
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }
</script>

<script>
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
</script>
</body>
</html>