<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';

// Liste des alumni validés
$utilisateurRepository = new UtilisateurRepository_malik();
$alumni = $utilisateurRepository->getAnnuaireAlumni();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuaire des anciens élèves — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
                <li><a href="page_annuaire.php">Annuaire</a></li>
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
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Annuaire</p>
            <h1>Annuaire des anciens élèves</h1>
            <p>Retrouvez les alumni du lycée et gardez le contact.</p>
        </div>
    </section>

    <!-- ===== ANNUAIRE ===== -->
    <section class="section annuaire-section">
        <div class="container">
            <div class="annuaire-card">
                <?php if (count($alumni) == 0) { ?>
                    <p class="liste-vide">Aucun alumni validé pour le moment.</p>
                <?php } else { ?>
                    <table class="annuaire-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumni as $a) { ?>
                                <tr>
                                    <td data-label="Nom"><?php echo htmlspecialchars($a['nom_utilisateur']); ?></td>
                                    <td data-label="Prénom"><?php echo htmlspecialchars($a['prenom_utilisateur']); ?></td>
                                    <td data-label="Email"><?php echo htmlspecialchars($a['mail']); ?></td>
                                    <td data-label="Téléphone"><?php if ($a['telephone'] != null) { echo htmlspecialchars($a['telephone']); } else { echo '—'; } ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
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

<script src="script_annuaire.js"></script>
</body>
</html>
