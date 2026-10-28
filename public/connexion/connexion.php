<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

// Message d'erreur et mail laissés par ConnexionTraitement_ahmed.php
$erreur = "";
$mail = "";
if (!empty($_SESSION['connexion_erreur'])) {
    $erreur = $_SESSION['connexion_erreur'];
    if (!empty($_SESSION['connexion_mail'])) {
        $mail = $_SESSION['connexion_mail'];
    }
    // On vide le message pour qu'il ne s'affiche qu'une seule fois
    $_SESSION['connexion_erreur'] = "";
    $_SESSION['connexion_mail'] = "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'connexion';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Lycée Robert Schuman</title>
    <meta name="description" content="Connectez-vous à votre espace : étudiants, anciens élèves, professeurs et partenaires entreprise.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="connexion.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

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

                <?php if ($erreur != "") { ?>
                    <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($erreur); ?></p>
                <?php } ?>

                <div class="form-group">
                    <label for="mail">Email</label>
                    <input type="email" id="mail" name="mail" maxlength="255" autocomplete="email" value="<?php echo htmlspecialchars($mail); ?>" required <?php if ($mail == "") { echo 'autofocus'; } ?>>
                </div>

                <div class="form-group">
                    <label for="mdp">Mot de passe</label>
                    <div class="password-field">
                        <input type="password" id="mdp" name="mdp" autocomplete="current-password" required <?php if ($mail != "") { echo 'autofocus'; } ?>>
                        <button type="button" class="password-toggle" id="password-toggle" aria-controls="mdp" aria-pressed="false">Afficher</button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Se connecter</button>

                <p class="login-footer">Pas encore de compte ? <a href="../inscription/inscription.php">Inscrivez-vous</a></p>
                <p class="login-footer">Connexion <a href="../Page_Admin/page_admin.php">Admin</a></p>

            </form>
        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_connexion.js"></script>
</body>
</html>
