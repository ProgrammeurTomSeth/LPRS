<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/evenement_anais.php';
require_once '../../src/repository/EvenementRepository_ahmed.php';

// Page réservée aux gestionnaires
if (!estConnecte() || $_SESSION['role'] != 'gestionnaire') {
    header('Location: ../connexion/connexion.php');
    exit;
}

// Liste des événements qui attendent la validation
$evenementRepository = new EvenementRepository_ahmed();
$evenementsEnAttente = $evenementRepository->getEvenementsEnAttente();

// Liste des événements déjà publiés (l'admin peut les modifier sans nouvelle validation)
$evenementsPublies = $evenementRepository->getEvenementsPublies();

// Message laissé après la création d'un événement (src/traitement/EvenementTraitement_Tom.php)
$message = "";
if (!empty($_SESSION['admin_message'])) {
    $message = $_SESSION['admin_message'];
    $_SESSION['admin_message'] = "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'admin_evenements';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des événements — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="page_admin.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Administration</p>
            <h1>Gestion des événements</h1>
            <p>Validez ou refusez les événements proposés avant leur publication.</p>
        </div>
    </section>

    <section class="section admin-section">
        <div class="container">

            <?php if ($message != "") { ?>
                <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($message); ?></p>
            <?php } ?>

            <!-- Événements qui attendent la validation -->
            <div class="admin-card admin-card--liste">
                <div class="liste-entete">
                    <div>
                        <h2>Événements à valider</h2>
                        <p class="liste-compteur" data-unite="événement"><?php echo count($evenementsEnAttente); ?> <?php echo count($evenementsEnAttente) > 1 ? 'événements' : 'événement'; ?></p>
                        <p class="liste-note">Un événement créé par l'administration est publié directement.</p>
                    </div>
                    <!-- Le formulaire de création a sa propre page -->
                    <a href="../evenement/creation_evenement_Tom.php" class="btn btn-primary btn-sm">+ Créer un événement</a>
                </div>

                <?php if (count($evenementsEnAttente) == 0) { ?>
                    <p class="liste-vide">Aucun événement en attente de validation.</p>
                <?php } else { ?>
                    <!-- Barre de recherche (voir script_admin.js) -->
                    <div class="liste-outils">
                        <input type="search" class="outil-recherche" placeholder="Rechercher un événement" aria-label="Rechercher un événement à valider">
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Places</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evenementsEnAttente as $evenement) { ?>
                                <tr data-recherche="<?php echo htmlspecialchars(mb_strtolower($evenement['titre'] . ' ' . $evenement['type_evenement'] . ' ' . $evenement['lieu'])); ?>">
                                    <td data-label="Événement">
                                        <span class="utilisateur-texte">
                                            <span class="utilisateur-nom"><?php echo htmlspecialchars($evenement['titre']); ?></span>
                                            <span class="utilisateur-mail"><?php echo htmlspecialchars($evenement['type_evenement']); ?></span>
                                        </span>
                                    </td>
                                    <td data-label="Date" class="cellule-date"><?php echo $evenement['date_fr']; ?></td>
                                    <td data-label="Lieu"><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                                    <td data-label="Places"><?php if ($evenement['nb_places'] != null) { echo $evenement['nb_places']; } else { echo '—'; } ?></td>
                                    <td data-label="Actions">
                                        <div class="actions-boutons actions-boutons--2">
                                            <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="inline">
                                                <input type="hidden" name="id_evenement" value="<?php echo $evenement['id_evenement']; ?>">
                                                <button type="submit" name="action" value="valider_evenement" class="btn btn-primary btn-sm">Valider</button>
                                                <button type="submit" name="action" value="refuser_evenement" class="btn btn-danger btn-sm">Refuser</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <p class="liste-vide" hidden>Aucun événement ne correspond à votre recherche.</p>
                <?php } ?>
            </div>

            <!-- Événements publiés : l'admin peut les modifier, ils restent publiés -->
            <div class="admin-card admin-card--liste">
                <div class="liste-entete">
                    <div>
                        <h2>Événements publiés</h2>
                        <p class="liste-compteur" data-unite="événement"><?php echo count($evenementsPublies); ?> <?php echo count($evenementsPublies) > 1 ? 'événements' : 'événement'; ?></p>
                        <p class="liste-note">Une modification faite par l'administration est publiée directement, sans nouvelle validation. Une suppression est définitive.</p>
                    </div>
                </div>

                <?php if (count($evenementsPublies) == 0) { ?>
                    <p class="liste-vide">Aucun événement publié.</p>
                <?php } else { ?>
                    <!-- Barre de recherche (voir script_admin.js) -->
                    <div class="liste-outils">
                        <input type="search" class="outil-recherche" placeholder="Rechercher un événement" aria-label="Rechercher un événement publié">
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Inscrits</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($evenementsPublies as $evenement) { ?>
                                <tr data-recherche="<?php echo htmlspecialchars(mb_strtolower($evenement['titre'] . ' ' . $evenement['type_evenement'] . ' ' . $evenement['lieu'])); ?>">
                                    <td data-label="Événement">
                                        <span class="utilisateur-texte">
                                            <span class="utilisateur-nom"><?php echo htmlspecialchars($evenement['titre']); ?></span>
                                            <span class="utilisateur-mail"><?php echo htmlspecialchars($evenement['type_evenement']); ?></span>
                                        </span>
                                    </td>
                                    <td data-label="Date" class="cellule-date"><?php echo $evenement['date_fr']; ?></td>
                                    <td data-label="Lieu"><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                                    <td data-label="Inscrits"><?php echo $evenement['nombre_inscrits']; ?><?php if ($evenement['nb_places'] != null) { echo ' / ' . $evenement['nb_places']; } ?></td>
                                    <td data-label="Actions">
                                        <div class="actions-boutons actions-boutons--2">
                                            <a href="../evenement/gestion_evenement_malik.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn btn-outline btn-sm">Modifier</a>

                                            <!-- data-confirmer : script_admin.js demande confirmation avant d'envoyer le formulaire -->
                                            <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="inline" data-confirmer="Supprimer définitivement cet événement ? Les inscriptions seront aussi supprimées.">
                                                <input type="hidden" name="id_evenement" value="<?php echo $evenement['id_evenement']; ?>">
                                                <button type="submit" name="action" value="supprimer_evenement" class="btn btn-danger btn-sm">Supprimer</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <p class="liste-vide" hidden>Aucun événement ne correspond à votre recherche.</p>
                <?php } ?>
            </div>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_admin.js"></script>
</body>
</html>
