<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/formation_anais.php';
require_once '../../src/repository/Formation_ahmed.php';
$pageActive = 'formations';
$formationRepository = new Formation_ahmed();
$formations = $formationRepository->getAllFormations();
$typesFormation = array(
        'bac_pro' => 'Bac professionnel',
        'bac_techno' => 'Bac technologique',
        'bts' => 'BTS');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos formations — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="page_formation.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                / Formations
            </p>
            <h1>Nos formations</h1>
            <p>Découvrez les formations proposées par le lycée, du bac professionnel au BTS.</p>
        </div>
    </section>
    <section class="section formations-section">
        <div class="container">
            <div class="formations-grid">
                <?php if (count($formations) == 0) { ?>
                    <p class="formations-vide">Aucune formation n'est disponible pour le moment.</p>
                <?php } else { ?>
                    <?php foreach ($formations as $formation) { ?>
                        <article class="formation-card">
                            <h3>
                                <?php echo htmlspecialchars($formation['nom_formation']); ?>
                            </h3>
                            <span class="badge">
                                <?php
                                if (isset($typesFormation[$formation['type_formation']])) {
                                    echo htmlspecialchars($typesFormation[$formation['type_formation']]);
                                } else {
                                    echo htmlspecialchars($formation['type_formation']);
                                }
                                ?>
                            </span>
                            <?php if (!empty($formation['description'])) { ?>
                                <p><?php echo nl2br(htmlspecialchars($formation['description'])); ?></p>
                            <?php } else { ?>
                                <p>La description de cette formation sera bientôt disponible.</p>
                            <?php } ?>
                        </article>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </section>
</main>
<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_formation.js"></script>
</body>
</html>