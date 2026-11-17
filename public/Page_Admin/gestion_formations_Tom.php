<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/formation_anais.php';
require_once '../../src/repository/Formation_ahmed.php';

// Page réservée aux gestionnaires
if (!estConnecte() || $_SESSION['role'] != 'gestionnaire') {
    header('Location: ../connexion/connexion.php');
    exit;
}

$formationRepository = new Formation_ahmed();
$formations = $formationRepository->getAllFormations();

// Récupération de la formation à modifier
$formationAModifier = null;

if (isset($_GET['modifier'])) {
    $formationAModifier = $formationRepository->getFormation((int) $_GET['modifier']);

    if ($formationAModifier == null) {
        $_SESSION['formation_erreur'] = "La formation demandée n'existe pas.";
        header('Location: gestion_formations_Tom.php');
        exit;
    }
}

// Récupération des messages après redirection
$message = '';
$erreur = '';

if (!empty($_SESSION['formation_message'])) {
    $message = $_SESSION['formation_message'];
    unset($_SESSION['formation_message']);
}

if (!empty($_SESSION['formation_erreur'])) {
    $erreur = $_SESSION['formation_erreur'];
    unset($_SESSION['formation_erreur']);
}

$pageActive = 'admin_formations';

$typesFormation = array(
    'bac_pro' => 'Bac professionnel',
    'bac_techno' => 'Bac technologique',
    'bts' => 'BTS'
);

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestion des formations — Lycée Robert Schuman</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="gestion_formations_Tom.css">
</head>

<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <section class="page-hero">
        <div class="hero-overlay"></div>

        <div class="container hero-content">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                /
                <a href="page_admin.php">Administration</a>
                /
                Formations
            </p>

            <h1>Gestion des formations</h1>

            <p>Ajoutez, modifiez ou supprimez les formations proposées par le lycée.</p>
        </div>
    </section>

    <section class="section formations-admin-section">

        <div class="container">

            <a href="page_admin.php" class="formations-retour">
                ← Retour à l'administration
            </a>

            <?php if ($message != '') { ?>
                <p class="form-status form-status--success" role="status">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php } ?>

            <?php if ($erreur != '') { ?>
                <p class="form-status form-status--error" role="alert">
                    <?php echo htmlspecialchars($erreur); ?>
                </p>
            <?php } ?>

            <!-- Formulaire d'ajout ou de modification -->
            <div class="formations-admin-card">

                <?php if ($formationAModifier != null) { ?>

                    <h2>Modifier une formation</h2>

                    <p>Modifiez le nom ou le type de cette formation.</p>

                    <form method="post" action="../../src/traitement/GestionFormationTraitement_Tom.php" class="formations-form">

                        <input type="hidden" name="action" value="modifier">

                        <input type="hidden" name="id_formation" value="<?php echo (int) $formationAModifier->getIdFormation(); ?>">

                        <div class="form-group">
                            <label for="nom_formation">Nom de la formation</label>

                            <input type="text" id="nom_formation" name="nom_formation" maxlength="150" value="<?php echo htmlspecialchars($formationAModifier->getNomFormation()); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="type_formation">Type de formation</label>

                            <select id="type_formation" name="type_formation" required>

                                <?php foreach ($typesFormation as $type => $libelle) { ?>

                                    <option value="<?php echo htmlspecialchars($type); ?>"<?php if ($formationAModifier->getTypeFormation() == $type) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($libelle); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="formations-form-actions">
                            <button type="submit" class="btn btn-primary">
                                Enregistrer les modifications
                            </button>

                            <a href="gestion_formations_Tom.php" class="btn btn-outline">
                                Annuler
                            </a>
                        </div>
                    </form>

                <?php } else { ?>

                    <h2>Ajouter une formation</h2>

                    <p>Renseignez les informations de la nouvelle formation.</p>

                    <form method="post" action="../../src/traitement/GestionFormationTraitement_Tom.php" class="formations-form">

                        <input type="hidden" name="action" value="ajouter">

                        <div class="form-group">
                            <label for="nom_formation">Nom de la formation</label>

                            <input type="text" id="nom_formation" name="nom_formation" maxlength="150" placeholder="Exemple : BTS SIO" required>
                        </div>

                        <div class="form-group">
                            <label for="type_formation">Type de formation</label>
                            <select id="type_formation" name="type_formation" required>
                                <option value="">Choisir un type</option>
                                <?php foreach ($typesFormation as $type => $libelle) { ?>
                                    <option value="<?php echo htmlspecialchars($type); ?>">
                                        <?php echo htmlspecialchars($libelle); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="formations-form-actions">
                            <button type="submit" class="btn btn-primary">
                                Ajouter la formation
                            </button>
                        </div>
                    </form>
                <?php } ?>
            </div>

            <!-- Liste des formations -->
            <div class="formations-admin-card">
                <div class="formations-liste-entete">
                    <div>
                        <h2>Liste des formations</h2>
                        <p>
                            <?php echo count($formations); ?>
                            formation<?php if (count($formations) > 1) { echo 's'; } ?>
                            enregistrée<?php if (count($formations) > 1) { echo 's'; } ?>
                        </p>
                    </div>
                </div>

                <?php if (count($formations) == 0) { ?>

                    <p class="formations-vide">
                        Aucune formation n'est enregistrée.
                    </p>

                <?php } else { ?>
                    <div class="formations-table-wrapper">
                        <table class="formations-table">
                            <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($formations as $formation) { ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?php echo htmlspecialchars($formation['nom_formation']); ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?php
                                        if (isset($typesFormation[$formation['type_formation']])) {
                                            echo htmlspecialchars($typesFormation[$formation['type_formation']]);
                                        } else {
                                            echo htmlspecialchars($formation['type_formation']);
                                        }
                                        ?>
                                    </td>

                                    <td class="formations-actions">

                                        <a href="gestion_formations_Tom.php?modifier=<?php echo (int) $formation['id_formation']; ?>" class="btn btn-outline btn-sm">
                                            Modifier
                                        </a>

                                        <form method="post" action="../../src/traitement/GestionFormationTraitement_Tom.php" class="form-suppression-formation" data-confirmation="<?php echo htmlspecialchars('Attention : supprimer « ' . $formation['nom_formation'] . ' » ? Les élèves actuellement inscrits dans cette formation n’auront plus de formation associée.', ENT_QUOTES); ?>">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id_formation" value="<?php echo (int) $formation['id_formation']; ?>">

                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Supprimer
                                            </button>
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

<script src="gestion_formations_Tom.js"></script>

</body>
</html>