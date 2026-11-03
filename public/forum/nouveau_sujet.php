<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/traitement/ForumRegles_ahmed.php';
require_once '../../src/bdd/bdd.php';

// Le forum est réservé aux membres connectés
if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$roleConnecte = $_SESSION['role'];

// Canaux proposés dans la liste : ceux où ce rôle peut créer un post
$canauxPossibles = array();
foreach ($canauxForum as $canal => $libelle) {
    if (peutCreerPost($canal, $roleConnecte)) {
        $canauxPossibles[$canal] = $libelle;
    }
}

// Un élève ne peut pas créer de sujet : retour à la liste avec un message
if (!peutCreerSujet($roleConnecte)) {
    $_SESSION['forum_erreur'] = "Les élèves ne peuvent pas créer de sujet, mais vous pouvez commenter ceux des autres membres.";
    header('Location: forum.php');
    exit;
}

// Valeurs du formulaire : vides, ou ce qui avait été tapé avant une erreur
$saisie = array('canal' => 'general', 'titre' => '', 'text' => '');
if (isset($_GET['canal']) && isset($canauxPossibles[$_GET['canal']])) {
    $saisie['canal'] = $_GET['canal'];
}
if (!empty($_SESSION['forum_saisie'])) {
    $saisie = $_SESSION['forum_saisie'];
    $_SESSION['forum_saisie'] = "";
}
// Le canal revient d'un formulaire : on vérifie qu'il est bien autorisé avant de l'afficher
if (!isset($canauxPossibles[$saisie['canal']])) {
    $saisie['canal'] = 'general';
}

// Message d'erreur laissé par src/traitement/ForumTraitement_ahmed.php
$messageErreur = "";
if (!empty($_SESSION['forum_erreur'])) {
    $messageErreur = $_SESSION['forum_erreur'];
    $_SESSION['forum_erreur'] = "";
}

// Pour la barre latérale et le menu
$canalActif = $saisie['canal'];
$pageActive = 'forum';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouveau sujet — Forum — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="forum.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main" class="forum-page">
    <div class="container forum-layout">

        <div class="forum-principal">

            <nav class="forum-ariane" aria-label="Fil d'Ariane">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                <span aria-hidden="true">›</span>
                <a href="forum.php">Forum</a>
                <span aria-hidden="true">›</span>
                <span>Nouveau sujet</span>
            </nav>

            <div class="forum-intro">
                <h1>Nouveau sujet</h1>
                <p>Posez votre question ou lancez une discussion avec les autres membres.</p>
            </div>

            <?php if ($messageErreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($messageErreur); ?></p>
            <?php } ?>

            <form action="../../src/traitement/ForumTraitement_ahmed.php" method="post" class="forum-form">
                <input type="hidden" name="action" value="creer_post">

                <label for="canal">Catégorie</label>
                <select id="canal" name="canal" required>
                    <?php foreach ($canauxPossibles as $canal => $libelle) { ?>
                        <option value="<?php echo $canal; ?>" <?php if ($canal == $saisie['canal']) { echo 'selected'; } ?>><?php echo $libelle; ?></option>
                    <?php } ?>
                </select>

                <label for="titre">Titre</label>
                <input type="text" id="titre" name="titre" maxlength="200" required placeholder="Ex : Comment trouver son stage de seconde ?" value="<?php echo htmlspecialchars($saisie['titre']); ?>">

                <label for="text">Message</label>
                <textarea id="text" name="text" rows="8" maxlength="5000" required placeholder="Expliquez votre question ou votre idée..."><?php echo htmlspecialchars($saisie['text']); ?></textarea>

                <div class="forum-form-boutons">
                    <a href="forum.php?canal=<?php echo $saisie['canal']; ?>" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Publier le sujet</button>
                </div>
            </form>
        </div>

        <?php require_once 'barre_laterale_forum.php'; ?>

    </div>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_forum.js"></script>
</body>
</html>
