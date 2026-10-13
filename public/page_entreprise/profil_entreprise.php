<?php

require_once __DIR__ . '/../../src/traitement/SessionUtilisateur_ahmed.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entreprises — Lycée Robert Shuman</title>
    <link rel="stylesheet" href="profil_entreprise.css">
</head>
<body>
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

<form action="../../src/traitement/rattachement_entreprise.php" method="post">
    <input type="hidden" name="id_entreprise" value="3">
    <button type="submit">S'abonner à cette entreprise</button>
</form>




<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Schuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 Lycée Robert Schuman. Tous droits réservés.</p>
    </div>
</footer>
</body>
</html>