<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$erreur = "";
if (!empty($_SESSION['evenement_erreur'])) {
    $erreur = $_SESSION['evenement_erreur'];
    $_SESSION['evenement_erreur'] = "";
}

$afficherFormulaire = true;

// Le gestionnaire (admin) peut aussi créer un événement : il est publié directement
$estGestionnaire = ($_SESSION['role'] == 'gestionnaire');

if ($estGestionnaire) {
    $pageActive = 'admin_evenements';
} else {
    $pageActive = 'evenements';
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="creation_evenement_Tom.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

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
                        <?php if ($estGestionnaire) { ?>
                            Vous êtes administrateur : l'événement sera publié directement, sans validation. Les co-organisateurs sont facultatifs.
                        <?php } else { ?>
                            Le créateur est automatiquement ajouté comme organisateur. Si vous êtes étudiant, au moins un professeur doit faire partie des organisateurs. L'événement sera publié après validation par un gestionnaire.
                        <?php } ?>
                    </div>
                    <form method="POST" action="../../src/traitement/EvenementTraitement_Tom.php">
                        <div class="form-group">
                            <label for="type_evenement">Type d'événement *</label>
                            <input type="text" id="type_evenement" name="type_evenement" maxlength="100" required placeholder="Exemple : Conférence, rencontre, atelier...">
                        </div>

                        <div class="form-group">
                            <label for="titre">Titre *</label>
                            <input type="text" id="titre" name="titre" maxlength="200" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="lieu">Lieu / adresse *</label>
                            <input type="text" id="lieu" name="lieu" maxlength="255" required placeholder="Exemple : Lycée Robert Schuman, Dugny">
                        </div>

                        <div class="form-group">
                            <label for="date_evenement">Date et heure *</label>
                            <input type="datetime-local" id="date_evenement" name="date_evenement" min="<?php echo date('Y-m-d\TH:i'); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="elements_requis">Éléments requis</label>
                            <input type="text" id="elements_requis" name="elements_requis" maxlength="255" placeholder="Facultatif">
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

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="creation_evenement_Tom.js"></script>
</body>
</html>
