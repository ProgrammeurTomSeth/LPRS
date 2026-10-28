<?php
require_once __DIR__ . '/../../src/traitement/traitement_annuaire.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page annuaire — Lycée Robert Shuman</title>
    <link rel="stylesheet" href="annuaire.css">

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
            <?php afficherActionsHeader('../'); ?>
        </div>

        <button class="nav-toggle" id="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<h1>Annuaire des Alumni Validés</h1>

<div class="container">
    <?php if (empty($alumni)): ?>
        <p class="empty">Aucun alumni validé pour le moment.</p>
    <?php else: ?>
        <table>
            <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Téléphone</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($alumni as $a): ?>
                <tr>
                    <td><?= htmlspecialchars($a['nom_utilisateur'] ?? "") ?></td>
                    <td><?= htmlspecialchars($a['prenom_utilisateur'] ?? "") ?></td>
                    <td><?= htmlspecialchars($a['mail'] ?? "") ?></td>
                    <td><?= htmlspecialchars($a['telephone'] ?? "") ?></td>

                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
<!-- ===== FOOTER ===== -->
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

