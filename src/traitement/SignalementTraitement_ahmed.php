<?php

// Traitement du formulaire public/forum/signaler_message.php :
// enregistre le signalement d'un message, que le gestionnaire verra
// dans public/Page_Admin/moderation_forum.php

require_once 'SessionUtilisateur_ahmed.php';
require_once 'ForumRegles_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Forum_ahmed.php';
require_once '../modele/SignalementForum_ahmed.php';
require_once '../repository/ForumRepository_ahmed.php';
require_once '../repository/SignalementForumRepository_ahmed.php';

if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../public/forum/forum.php');
    exit;
}

$idConnecte = $_SESSION['id_utilisateur'];
$roleConnecte = $_SESSION['role'];

$idMessage = (int) lireChamp('id_forum');
$motif = lireChamp('motif');
$commentaire = trim((string) lireChamp('commentaire'));

$forumRepository = new ForumRepository_ahmed();
$signalementRepository = new SignalementForumRepository_ahmed();

// getMessage() renvoie un OBJET Forum_ahmed (ou null)
$message = $forumRepository->getMessage($idMessage);

// Mêmes vérifications que sur la page du formulaire
if ($message == null || !peutAccederCanal($message->getCanal(), $roleConnecte) || !peutSignaler($message->getIdUtilisateur(), $idConnecte, $roleConnecte)) {
    $_SESSION['forum_erreur'] = "Vous ne pouvez pas signaler ce message.";
    header('Location: ../../public/forum/forum.php');
    exit;
}

$idPost = $forumRepository->getIdPost($idMessage);

if ($signalementRepository->aDejaSignale($idMessage, $idConnecte)) {
    $_SESSION['forum_erreur'] = "Vous avez déjà signalé ce message.";
    header('Location: ../../public/forum/discussion_forum.php?id=' . $idPost);
    exit;
}

$erreur = "";
if (!isset($motifsSignalement[$motif])) {
    $erreur = "Veuillez choisir un motif.";
} elseif ($motif == 'autre' && $commentaire == "") {
    $erreur = "Précisez la raison du signalement dans le commentaire.";
} elseif (mb_strlen($commentaire) > 500) {
    $erreur = "Le commentaire ne doit pas dépasser 500 caractères.";
}

if ($erreur != "") {
    $_SESSION['forum_erreur'] = $erreur;
    header('Location: ../../public/forum/signaler_message.php?id=' . $idMessage);
    exit;
}

if ($commentaire == "") {
    $commentaire = null;
}

$signalement = new SignalementForum_ahmed();
$signalement->setIdForum($idMessage);
$signalement->setIdUtilisateur($idConnecte);
$signalement->setMotif($motif);
$signalement->setCommentaire($commentaire);
$signalementRepository->ajouterSignalement($signalement);

$_SESSION['forum_message'] = "Merci, votre signalement a été transmis à l'équipe de modération.";
header('Location: ../../public/forum/discussion_forum.php?id=' . $idPost . '#message-' . $idMessage);
exit;
