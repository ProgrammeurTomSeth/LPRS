<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos formations — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="page_formation.css">
</head>
<body>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<!-- ===== HEADER / NAV ===== -->
<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="../page_acceuille/page_accueil.php">Programme</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Activités</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
                <li><a href="../page_entreprise/page_entreprise.php">Entreprise</a></li>
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

<!-- ===== FOOTER ===== -->
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

<script src="script_formation.js"></script>
</body>
</html>
