<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'formations';

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
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="page_formation.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Formations</p>
            <h1>Nos formations</h1>
            <p>Découvrez les formations proposées par le lycée, du bac professionnel au BTS.</p>
        </div>
    </section>

    <!-- ===== FORMATIONS ===== -->
    <section class="section formations-section">
        <div class="container">
            <div class="formations-grid">

                <article class="formation-card">
                    <h3>BTS SIO</h3>
                    <span class="badge">BTS</span>
                    <p>Le BTS SIO signifie Brevet de Technicien Supérieur Services Informatiques aux Organisations. C’est une formation de deux ans après le bac, reconnue par l’État et équivalente à 120 crédits ECTS. Elle prépare à une insertion rapide dans le monde du travail ou à la poursuite d’études dans le domaine informatique.</p>
                </article>

                <article class="formation-card">
                    <h3>Bac technologique STI2D</h3>
                    <span class="badge">Bac technologique</span>
                    <p>Le bac STI2D s’adresse aux lycéens intéressés par l’innovation technologique, l’écologie, la conception de produits et systèmes industriels, ainsi que par la transition énergétique et le respect de l’environnement. Il vise à développer des compétences techniques solides tout en conservant un socle d’enseignements généraux. Les élèves apprennent à comprendre le fonctionnement des systèmes techniques de l’industrie ou du quotidien et à travailler sur des projets concrets, favorisant l’esprit d’initiative, la collaboration et la résolution de problèmes.</p>
                </article>

            </div>
        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_formation.js"></script>
</body>
</html>
