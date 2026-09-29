<?php

require_once __DIR__ . '/../../src/traitement/ConnexionFormulaire_ahmed.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Lycée Robert Shuman</title>
    <meta name="description" content="Connectez-vous à votre espace : étudiants, anciens élèves, professeurs et partenaires entreprise.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_connexion.css">
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

        <a href="../inscription/inscription.php" class="btn btn-primary header-cta">Inscription Rapide</a>

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
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Connexion</p>
            <h1>Connexion</h1>
            <p>Accédez à votre espace : offres, événements et réseau du lycée.</p>
        </div>
    </section>

    <!-- ===== FORMULAIRE DE CONNEXION ===== -->
    <section class="section login-section">
        <div class="container">
            <form class="login-form" id="login-form" action="../../src/traitement/ConnexionTraitement_ahmed.php" method="POST">

                <h2>Se connecter</h2>

                <?php if ($erreur): ?>
                    <p class="form-status form-status--error" role="alert"><?= e($erreur) ?></p>
                <?php endif; ?>

                <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" maxlength="255" autocomplete="email" value="<?= e($mail) ?>" required <?= $mail === '' ? 'autofocus' : '' ?>>
                </div>

                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <div class="password-field">
                        <input type="password" id="mdp" name="mdp" autocomplete="current-password" required <?= $mail !== '' ? 'autofocus' : '' ?>>
                        <button type="button" class="password-toggle" id="password-toggle" aria-controls="mdp" aria-pressed="false">Afficher</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Se connecter</button>

                <p class="login-footer">Pas encore de compte ? <a href="../inscription/inscription.php">Inscrivez-vous</a></p>
                <p class="login-footer">Connexion <a href="connexion.php">Admin</a></p>

            </form>
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

<script src="script_connexion.js"></script>
</body>
</html>
