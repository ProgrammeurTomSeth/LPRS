<?php

// Traitement des boutons de public/Page_Admin/moderation_forum.php (gestionnaire seulement) :
// 1) supprimer le message signalé (ses réponses et signalements partent avec)
// 2) ignorer : supprimer les signalements mais garder le message

require_once 'SessionUtilisateur_ahmed.php';
require_once 'ForumRegles_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Forum_ahmed.php';
require_once '../repository/ForumRepository_ahmed.php';
require_once '../repository/SignalementForumRepository_ahmed.php';

// Réservé au gestionnaire (modérateur)
if (!estConnecte() || !estModerateur($_SESSION['role'])) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../public/Page_Admin/moderation_forum.php');
    exit;
}

$action = lireChamp('action');
$idMessage = (int) lireChamp('id_forum');

$forumRepository = new ForumRepository_ahmed();
$signalementRepository = new SignalementForumRepository_ahmed();

if ($forumRepository->getMessage($idMessage) == null) {
    $_SESSION['moderation_erreur'] = "Ce message n'existe plus.";
} elseif ($action == 'supprimer_message') {
    $forumRepository->supprimerMessage($idMessage);
    $_SESSION['moderation_message'] = "Le message a été supprimé du forum.";
} elseif ($action == 'ignorer') {
    $signalementRepository->supprimerSignalementsDuMessage($idMessage);
    $_SESSION['moderation_message'] = "Les signalements ont été supprimés, le message reste en ligne.";
} else {
    $_SESSION['moderation_erreur'] = "Action inconnue.";
}

header('Location: ../../public/Page_Admin/moderation_forum.php');
exit;
