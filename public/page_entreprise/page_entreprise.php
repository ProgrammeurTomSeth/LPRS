<?php

require_once __DIR__ . '/../../src/traitement/EntrepriseFormulaire_ahmed.php';

$recherche = trim($_GET['search'] ?? '');
$entreprises = array();
$erreurChargement = null;
try {
    $entreprises = (new recherche_entreprise())->rechercher($recherche);
} catch (PDOException $e) {
    $erreurChargement = "Impossible de charger les entreprises pour le moment.";
}


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entreprises — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="page_entreprise.css">
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

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Entreprises</p>
            <h1>Liste des entreprises</h1>
            <p>Retrouvez les entreprises partenaires du lycée.</p>
        </div>
    </section>

    <!-- ===== ENTREPRISES ===== -->
    <section class="section entreprises-section">
        <div class="container">

            <div class="toolbar">
                <!-- Barre de recherche SANS JS -->
                <form method="GET" class="search-container">
                    <input type="text" name="search" placeholder="Rechercher une entreprise..." aria-label="Rechercher une entreprise"
                           value="<?= e($recherche) ?>">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            </div>

            <?php if ($messageSucces): ?>
                <p class="form-status form-status--success" role="status"><?= e($messageSucces) ?></p>
            <?php endif; ?>
            <?php if ($messageErreur): ?>
                <p class="form-status form-status--error" role="alert"><?= e($messageErreur) ?></p>
            <?php endif; ?>

            <?php if ($erreurChargement): ?>
                <p class="form-status form-status--error" role="alert"><?= e($erreurChargement) ?></p>
            <?php elseif (count($entreprises) === 0): ?>
                <p class="liste-vide">
                    <?php if ($recherche !== ''): ?>
                        Aucune entreprise ne correspond à « <?= e($recherche) ?> ».
                        <a href="page_entreprise.php">Voir toutes les entreprises</a>
                    <?php else: ?>
                        Aucune entreprise n'est enregistrée pour le moment.
                    <?php endif; ?>
                </p>
            <?php else: ?>
                <div class="cards-grid">
                    <?php foreach ($entreprises as $entreprise): ?>
                        <?php $estMonEntreprise = $idEntrepriseUtilisateur === (int) $entreprise->getIdEntreprise(); ?>
                        <article class="card<?= $estMonEntreprise ? ' card--mon-entreprise' : '' ?>">
                            <h3><?= e($entreprise->getNomEntreprise()) ?></h3>
                            <?php if ($estMonEntreprise): ?>
                                <span class="badge">Votre entreprise</span>
                            <?php endif; ?>
                            <p><strong>Activité :</strong> <?= e($entreprise->getActivite() ?? 'Non renseignée') ?></p>
                            <p><strong>Adresse :</strong> <?= e($entreprise->getAdresse() ?? 'Non renseignée') ?></p>
                            <p><strong>Site web :</strong>
                                <a href="<?= e($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener"><?= e($entreprise->getSiteWeb()) ?></a>
                            </p>

                            <div class="card-actions">
                                <a href="profil_entreprise.php?id=<?= (int) $entreprise->getIdEntreprise() ?>" class="btn btn-sm btn-outline">Voir</a>
                                <?php if ($peutSeRattacher && !$estMonEntreprise): ?>
                                    <form action="../../src/traitement/rattachement_entreprise.php" method="post">
                                        <input type="hidden" name="id_entreprise" value="<?= (int) $entreprise->getIdEntreprise() ?>">
                                        <input type="hidden" name="action" value="rattacher">
                                        <button type="submit" class="btn btn-sm btn-primary">Me rattacher</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

<script src="script_entreprise.js"></script>
</body>
</html>
