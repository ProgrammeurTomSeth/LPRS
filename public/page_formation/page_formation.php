<?php

require_once('../../src/traitement/traitement_formation.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Nos Formations</title>
    <link rel="stylesheet" href="../page_formation/page_formation.css">
</head>

<body>

<header class="header">
    <div class="header-container">
        <h1 class="logo">LPRS — Formations</h1>
        <nav>
            <ul class="nav-links">
                <li><a href="../page_acceuille/page_accueil.php">Accueil</a></li>
                <li><a href="#">Entreprises</a></li>
                <li><a href="#">Formations</a></li>
                <li><a href="#">Profil</a></li>
            </ul>
        </nav>
    </div>
</header>

<main class="container">

    <h2 class="title">Découvrez nos formations</h2>

    <section class="formations-grid">

        <!-- Exemple de carte formation (à dupliquer) -->
        <div class="formation-card">
            <h3 class="formation-title">BTS SIO</h3>
            <p><strong>Type :</strong> BTS </p>
            <p class="formation-details">
                <strong>Détails :</strong> Le BTS SIO signifie Brevet de Technicien Supérieur Services Informatiques aux Organisations. C’est une formation de deux ans après le bac, reconnue par l’État et équivalente à 120 crédits ECTS. Elle prépare à une insertion rapide dans le monde du travail ou à la poursuite d’études dans le domaine informatique.
            </p>
        </div>

        <div class="formation-card">
            <h3 class="formation-title">Bac technologique STI2D</h3>
            <p><strong>Type :</strong> bac technologique</p>
            <p class="formation-details">
                <strong>Détails :</strong> Le bac STI2D s’adresse aux lycéens intéressés par l’innovation technologique, l’écologie, la conception de produits et systèmes industriels, ainsi que par la transition énergétique et le respect de l’environnement. Il vise à développer des compétences techniques solides tout en conservant un socle d’enseignements généraux. Les élèves apprennent à comprendre le fonctionnement des systèmes techniques de l’industrie ou du quotidien et à travailler sur des projets concrets, favorisant l’esprit d’initiative, la collaboration et la résolution de problèmes.
            </p>
        </div>


        <!-- Tu peux en ajouter autant que tu veux -->

    </section>

</main>

<footer class="footer">
    <p>© 2026 Lycée Robert Schuman — Tous droits réservés.</p>
</footer>

</body>
</html>
