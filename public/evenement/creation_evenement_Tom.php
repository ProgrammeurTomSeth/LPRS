<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

// Page réservée aux utilisateurs connectés
if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

// Message d'erreur laissé par src/traitement/EvenementTraitement_Tom.php
$erreur = "";
if (!empty($_SESSION['evenement_erreur'])) {
    $erreur = $_SESSION['evenement_erreur'];
    $_SESSION['evenement_erreur'] = "";
}

// Les gestionnaires ne peuvent pas créer d'événement : on n'affiche pas le formulaire
$afficherFormulaire = true;
if ($_SESSION['role'] == 'gestionnaire') {
    $erreur = "Les gestionnaires ne peuvent pas créer d'événement.";
    $afficherFormulaire = false;
}

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
                <li><a href="liste_evenement_Tom.php">Événements</a></li>
                <li><a href="../offre/liste_offres_Tom.php">Offres</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <?php afficherActionsHeader(); ?>
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

                <?php if ($erreur != "") { ?>
                    <div class="alert alert--error" role="alert">
                        <?php echo htmlspecialchars($erreur); ?>
                    </div>
                <?php } ?>

                <?php if ($afficherFormulaire) { ?>

                    <div class="alert alert--info">
                        Le créateur est automatiquement ajouté comme organisateur. Si le créateur est étudiant, un professeur doit faire partie des organisateurs pour que l'événement soit publié.
                    </div>
                    <form method="POST" action="../../src/traitement/EvenementTraitement_Tom.php">
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
                <?php } ?>

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

<script src="creation_evenement_Tom.js"></script>
</body>
</html>
