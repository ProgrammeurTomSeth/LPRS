<?php

// Traitement des formulaires du forum (public/forum/) :
// 1) créer un post                                   (action = creer_post)
// 2) commenter un post ou répondre à un commentaire  (action = repondre)
// 3) supprimer un message : gestionnaire seulement   (action = supprimer)

require_once 'SessionUtilisateur_ahmed.php';
require_once 'ForumRegles_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Forum_ahmed.php';
require_once '../repository/ForumRepository_ahmed.php';

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
$action = lireChamp('action');

$forumRepository = new ForumRepository_ahmed();

// 1) Créer un post (formulaire de public/forum/nouveau_sujet.php)
if ($action == 'creer_post') {
    $canal = lireChamp('canal');
    $titre = trim((string) lireChamp('titre'));
    $text = trim((string) lireChamp('text'));

    $erreur = "";
    if (!peutCreerPost($canal, $roleConnecte)) {
        $erreur = "Vous n'avez pas le droit de créer un post dans ce canal.";
    } elseif ($titre == "" || $text == "") {
        $erreur = "Veuillez saisir un titre et un message.";
    } elseif (mb_strlen($titre) > 200) {
        $erreur = "Le titre ne doit pas dépasser 200 caractères.";
    } elseif (mb_strlen($text) > 5000) {
        $erreur = "Le message ne doit pas dépasser 5000 caractères.";
    }

    if ($erreur != "") {
        $_SESSION['forum_erreur'] = $erreur;
        // On garde ce qui a été tapé pour ne pas le perdre
        $_SESSION['forum_saisie'] = array('canal' => $canal, 'titre' => $titre, 'text' => $text);
        header('Location: ../../public/forum/nouveau_sujet.php');
        exit;
    }

    $post = new Forum_ahmed();
    $post->setTitre($titre);
    $post->setText($text);
    $post->setCanal($canal);
    $post->setIdUtilisateur($idConnecte);
    $post->setIdForumParent(null);
    $idPost = $forumRepository->ajouterMessage($post);

    $_SESSION['forum_message'] = "Votre sujet a été publié.";
    header('Location: ../../public/forum/discussion_forum.php?id=' . $idPost);
    exit;
}

// 2) Commenter un post, ou répondre à un commentaire
if ($action == 'repondre') {
    $idParent = (int) lireChamp('id_parent');
    $text = trim((string) lireChamp('text'));

    // getMessage() renvoie un OBJET Forum_ahmed (ou null)
    $parent = $forumRepository->getMessage($idParent);

    if ($parent == null || !peutAccederCanal($parent->getCanal(), $roleConnecte)) {
        $_SESSION['forum_erreur'] = "Ce message n'existe pas ou vous n'y avez pas accès.";
        header('Location: ../../public/forum/forum.php');
        exit;
    }

    $idPost = $forumRepository->getIdPost($idParent);

    // On garde seulement 2 niveaux sous le post (comme la maquette) :
    // répondre à une réponse revient à répondre au même commentaire
    if ($parent->estUnPost() || $parent->getIdForumParent() == $idPost) {
        $idParentFinal = $idParent;                    // post ou commentaire
    } else {
        $idParentFinal = $parent->getIdForumParent(); // réponse -> son commentaire
    }

    $erreur = "";
    if ($text == "") {
        $erreur = "Votre message est vide.";
    } elseif (mb_strlen($text) > 5000) {
        $erreur = "Le message ne doit pas dépasser 5000 caractères.";
    }

    if ($erreur != "") {
        $_SESSION['forum_erreur'] = $erreur;
        header('Location: ../../public/forum/discussion_forum.php?id=' . $idPost);
        exit;
    }

    // Un commentaire n'a pas de titre et reprend le canal du post
    $reponse = new Forum_ahmed();
    $reponse->setTitre(null);
    $reponse->setText($text);
    $reponse->setCanal($parent->getCanal());
    $reponse->setIdUtilisateur($idConnecte);
    $reponse->setIdForumParent($idParentFinal);
    $idReponse = $forumRepository->ajouterMessage($reponse);

    $_SESSION['forum_message'] = "Votre message a été publié.";
    header('Location: ../../public/forum/discussion_forum.php?id=' . $idPost . '#message-' . $idReponse);
    exit;
}

// 3) Supprimer un message : seul le gestionnaire (modérateur) peut le faire
if ($action == 'supprimer') {
    $idMessage = (int) lireChamp('id_forum');
    $message = $forumRepository->getMessage($idMessage);

    if ($message == null || !estModerateur($roleConnecte)) {
        $_SESSION['forum_erreur'] = "Vous ne pouvez pas supprimer ce message.";
        header('Location: ../../public/forum/forum.php');
        exit;
    }

    // On cherche la discussion AVANT de supprimer le message
    $idPost = $forumRepository->getIdPost($idMessage);
    $forumRepository->supprimerMessage($idMessage);

    if ($message->estUnPost()) {
        $_SESSION['forum_message'] = "Le sujet et toutes ses réponses ont été supprimés.";
        header('Location: ../../public/forum/forum.php?canal=' . $message->getCanal());
    } else {
        $_SESSION['forum_message'] = "Le message a été supprimé.";
        header('Location: ../../public/forum/discussion_forum.php?id=' . $idPost);
    }
    exit;
}

// Action inconnue
header('Location: ../../public/forum/forum.php');
exit;
