<?php

require_once __DIR__ . '/../../src/traitement/SessionUtilisateur_ahmed.php';

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
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>

                <a href="#" class="btn btn-outline">+ Ajouter une entreprise</a>
            </div>

            <div class="cards-grid">

                <!-- Exemple d'une carte entreprise (sera remplacé par ta boucle PHP) -->
                <article class="card">
                    <h3>Nom de l'entreprise</h3>
                    <p><strong>Activité :</strong> Informatique</p>
                    <p><strong>Adresse :</strong> 1 rue du Code, Paris</p>
                    <p><strong>Site web :</strong>
                        <a href="https://exemple.com" target="_blank" rel="noopener">https://exemple.com</a>
                    </p>

                    <div class="card-actions">
                        <a href="#" class="btn btn-sm btn-outline">Voir</a>
                        <a href="#" class="btn btn-sm btn-outline">Modifier</a>
                        <a href="#" class="btn btn-sm btn-danger">Supprimer</a>
                    </div>
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

<script>
    // Menu mobile
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }
</script>
</body>
</html>
