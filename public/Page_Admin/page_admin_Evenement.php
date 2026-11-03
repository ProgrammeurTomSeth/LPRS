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

            <!-- Événements qui attendent la validation -->
            <div class="admin-card">
                <h2>Événements à valider</h2>

                <?php if (count($evenementsEnAttente) == 0) { ?>
                    <p class="admin-vide">Aucun événement en attente de validation.</p>
                <?php } else { ?>
                    <div class="table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Titre</th>
                                    <th>Type</th>
                                    <th>Lieu</th>
                                    <th>Places</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($evenementsEnAttente as $evenement) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($evenement['titre']); ?></td>
                                        <td><?php echo htmlspecialchars($evenement['type_evenement']); ?></td>
                                        <td><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                                        <td><?php if ($evenement['nb_places'] != null) { echo $evenement['nb_places']; } else { echo '—'; } ?></td>
                                        <td class="actions">
                                            <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="inline">
                                                <input type="hidden" name="id_evenement" value="<?php echo $evenement['id_evenement']; ?>">
                                                <button type="submit" name="action" value="valider_evenement" class="btn btn-primary btn-sm">Valider</button>
                                                <button type="submit" name="action" value="refuser_evenement" class="btn btn-danger btn-sm">Refuser</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_admin.js"></script>
</body>
</html>
