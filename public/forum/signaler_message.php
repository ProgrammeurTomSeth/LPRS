<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/traitement/ForumRegles_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Forum_ahmed.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/ForumRepository_ahmed.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/SignalementForumRepository_ahmed.php';

// Le forum est réservé aux membres connectés
if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$idConnecte = $_SESSION['id_utilisateur'];
$roleConnecte = $_SESSION['role'];

$idMessage = 0;
if (isset($_GET['id'])) {
    $idMessage = (int) $_GET['id'];
}

// getMessage() renvoie un OBJET Forum_ahmed (ou null) : on utilise les getters
$forumRepository = new ForumRepository_ahmed();
$message = $forumRepository->getMessage($idMessage);

if ($message == null || !peutAccederCanal($message->getCanal(), $roleConnecte) || !peutSignaler($message->getIdUtilisateur(), $idConnecte, $roleConnecte)) {
    $_SESSION['forum_erreur'] = "Vous ne pouvez pas signaler ce message.";
    header('Location: forum.php');
    exit;
}

$idPost = $forumRepository->getIdPost($idMessage);

$signalementRepository = new SignalementForumRepository_ahmed();
if ($signalementRepository->aDejaSignale($idMessage, $idConnecte)) {
    $_SESSION['forum_erreur'] = "Vous avez déjà signalé ce message.";
    header('Location: discussion_forum.php?id=' . $idPost);
    exit;
}

// Auteur du message (getUtilisateurParId() renvoie un OBJET Utilisateur_Malik)
$utilisateurRepository = new UtilisateurRepository_malik();
$auteur = $utilisateurRepository->getUtilisateurParId($message->getIdUtilisateur());

// Extrait du message signalé : 300 premiers caractères
$extrait = $message->getText();
if (mb_strlen($extrait) > 300) {
    $extrait = mb_substr($extrait, 0, 300) . '…';
}

// Message d'erreur laissé par src/traitement/SignalementTraitement_ahmed.php
$messageErreur = "";
if (!empty($_SESSION['forum_erreur'])) {
    $messageErreur = $_SESSION['forum_erreur'];
    $_SESSION['forum_erreur'] = "";
}

// Pour la barre latérale et le menu
$canalActif = $message->getCanal();
$pageActive = 'forum';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signaler un message — Forum — Lycée Robert Schuman</title>
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
                <a href="discussion_forum.php?id=<?php echo $idPost; ?>">Discussion</a>
                <span aria-hidden="true">›</span>
                <span>Signaler</span>
            </nav>

            <div class="forum-intro">
                <h1>Signaler un message</h1>
                <p>Votre signalement est envoyé à l'équipe de modération, qui relira le message. L'auteur ne saura pas qui l'a signalé.</p>
            </div>

            <?php if ($messageErreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($messageErreur); ?></p>
            <?php } ?>

            <!-- Rappel du message signalé -->
            <blockquote class="message-cite">
                <p class="commentaire-meta">
                    <?php if ($auteur != null) { ?>
                        <strong><?php echo htmlspecialchars($auteur->getPrenom() . ' ' . $auteur->getNom()); ?></strong>
                    <?php } ?>
                    <span class="badge-canal"><?php echo $canauxForum[$message->getCanal()]; ?></span>
                </p>
                <?php if ($message->getTitre() != null) { ?>
                    <p class="message-cite-titre"><?php echo htmlspecialchars($message->getTitre()); ?></p>
                <?php } ?>
                <p><?php echo nl2br(htmlspecialchars($extrait)); ?></p>
            </blockquote>

            <form action="../../src/traitement/SignalementTraitement_ahmed.php" method="post" class="forum-form">
                <input type="hidden" name="id_forum" value="<?php echo $message->getIdForum(); ?>">

                <fieldset class="motifs">
                    <legend>Pourquoi signalez-vous ce message ?</legend>
                    <?php foreach ($motifsSignalement as $valeur => $libelle) { ?>
                        <label class="motif">
                            <input type="radio" name="motif" value="<?php echo $valeur; ?>" required>
                            <?php echo $libelle; ?>
                        </label>
                    <?php } ?>
                </fieldset>

                <label for="commentaire">Commentaire <span class="forum-aide">(obligatoire pour « Autre raison »)</span></label>
                <textarea id="commentaire" name="commentaire" rows="4" maxlength="500" placeholder="Donnez des précisions à l'équipe de modération..."></textarea>

                <div class="forum-form-boutons">
                    <a href="discussion_forum.php?id=<?php echo $idPost; ?>#message-<?php echo $message->getIdForum(); ?>" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Envoyer le signalement</button>
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
