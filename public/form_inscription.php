<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'inscription — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="form_inscription.css">
</head>
<body>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<!-- ===== HEADER / NAV ===== -->
<header class="site-header">
    <div class="container header-inner">
        <a href="page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="page_acceuille/page_accueil.php">Programme</a></li>
                <li><a href="page_acceuille/page_accueil.php">Activités</a></li>
                <li><a href="page_acceuille/page_accueil.php">Contact</a></li>
            </ul>
        </nav>

        <a href="connexion/connexion.php" class="btn btn-outline header-cta">Connexion</a>

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
            <p class="breadcrumb"><a href="page_acceuille/page_accueil.php">Accueil</a> / Inscription</p>
            <h1>Inscription</h1>
            <p>Créez votre compte pour rejoindre la communauté du lycée.</p>
        </div>
    </section>

    <!-- ===== FORMULAIRE ===== -->
    <section class="section form-section">
        <div class="container">
            <form class="form-card" action="../src/traitement/traitement_inscription.php" method="POST" enctype="multipart/form-data">

                <h2>Créer un compte</h2>

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <input type="password" id="mdp" name="mdp" required>
                </div>

                <div class="form-group">
                    <label for="mdp_confirm">Confirmer le mot de passe</label>
                    <input type="password" id="mdp_confirm" name="mdp_confirm" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" id="telephone" name="telephone">
                </div>

                <div class="form-group">
                    <label for="profil">Profil</label>
                    <select id="profil" name="profil" required>
                        <option value="Etudiant">Étudiant</option>
                        <option value="Alumni">Alumni</option>
                        <option value="Partenaire">Partenaire</option>
                        <option value="Professeur">Professeur</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Créer le compte</button>
            </form>
        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="page_acceuille/page_accueil.php">Contact</a></li>
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
