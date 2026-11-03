<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/traitement/ForumRegles_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Forum_ahmed.php';
require_once '../../src/repository/ForumRepository_ahmed.php';

// Le forum est réservé aux membres connectés
if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$idConnecte = $_SESSION['id_utilisateur'];
$roleConnecte = $_SESSION['role'];

// Canal choisi dans l'URL (?canal=...) : par défaut le canal général.
// Un canal interdit pour ce rôle n'est pas accepté.
$canalActif = 'general';
if (isset($_GET['canal'])) {
    if ($_GET['canal'] == 'mes_posts' || peutAccederCanal($_GET['canal'], $roleConnecte)) {
        $canalActif = $_GET['canal'];
    }
}

// getPostsParCanal() et getMesPosts() renvoient des TABLEAUX (fetchAll),
// on lit donc chaque post avec $post['colonne'] et non avec des getters.
$forumRepository = new ForumRepository_ahmed();
if ($canalActif == 'mes_posts') {
    $posts = $forumRepository->getMesPosts($idConnecte);
    $titreCanal = 'Mes posts';
    $descriptionCanal = 'Les sujets que vous avez écrits et ceux dans lesquels vous avez répondu.';
} else {
    $posts = $forumRepository->getPostsParCanal($canalActif);
    $titreCanal = $canauxForum[$canalActif];
    $descriptionCanal = $descriptionsCanaux[$canalActif];
}

// Messages laissés par les traitements du forum
$messageSucces = "";
$messageErreur = "";
if (!empty($_SESSION['forum_message'])) {
    $messageSucces = $_SESSION['forum_message'];
    $_SESSION['forum_message'] = "";
}
if (!empty($_SESSION['forum_erreur'])) {
    $messageErreur = $_SESSION['forum_erreur'];
    $_SESSION['forum_erreur'] = "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'forum';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
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
                <span><?php echo $titreCanal; ?></span>
            </nav>

            <div class="forum-intro">
                <h1><?php echo $titreCanal; ?></h1>
                <p><?php echo $descriptionCanal; ?></p>
            </div>

            <?php if ($messageSucces != "") { ?>
                <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($messageSucces); ?></p>
            <?php } ?>
            <?php if ($messageErreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($messageErreur); ?></p>
            <?php } ?>

            <?php if (count($posts) == 0) { ?>
                <div class="forum-vide">
                    <?php if ($canalActif == 'mes_posts') { ?>
                        <p>Vous n'avez encore écrit aucun sujet ni aucun commentaire.</p>
                    <?php } else { ?>
                        <p>Aucun sujet dans ce canal pour le moment.</p>
                        <?php if (peutCreerPost($canalActif, $roleConnecte)) { ?>
                            <a href="nouveau_sujet.php?canal=<?php echo $canalActif; ?>" class="btn btn-primary btn-sm">Lancer la première discussion</a>
                        <?php } ?>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <div class="forum-liste">
                    <?php foreach ($posts as $post) { ?>
                        <?php
                        // Extrait du message : 180 premiers caractères (mb_ : gère les accents)
                        $extrait = $post['text'];
                        if (mb_strlen($extrait) > 180) {
                            $extrait = mb_substr($extrait, 0, 180) . '…';
                        }
                        ?>
                        <article class="sujet-carte">
                            <div class="sujet-badges">
                                <span class="badge-canal"><?php echo $canauxForum[$post['canal']]; ?></span>
                                <?php if ($post['id_utilisateur'] == $idConnecte) { ?>
                                    <span class="badge-gris">Votre sujet</span>
                                <?php } ?>
                            </div>
                            <h2><a href="discussion_forum.php?id=<?php echo $post['id_forum']; ?>"><?php echo htmlspecialchars((string) $post['titre']); ?></a></h2>
                            <p class="sujet-extrait"><?php echo htmlspecialchars($extrait); ?></p>
                            <div class="sujet-pied">
                                <div class="auteur">
                                    <span class="avatar avatar--petit <?php echo classeAvatar($post['id_utilisateur']); ?>" aria-hidden="true"><?php echo htmlspecialchars(initiales($post['prenom_utilisateur'], $post['nom_utilisateur'])); ?></span>
                                    <div>
                                        <strong><?php echo htmlspecialchars($post['prenom_utilisateur'] . ' ' . $post['nom_utilisateur']); ?></strong>
                                        <span class="auteur-role"><?php echo $rolesForum[$post['role']]; ?></span>
                                        <span class="auteur-date"><?php echo tempsEcoule($post['minutes'], $post['date_fr']); ?></span>
                                    </div>
                                </div>
                                <a href="discussion_forum.php?id=<?php echo $post['id_forum']; ?>" class="sujet-nb">
                                    <?php echo $post['nb_reponses']; ?> commentaire<?php if ($post['nb_reponses'] > 1) { echo 's'; } ?>
                                </a>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <?php require_once 'barre_laterale_forum.php'; ?>

    </div>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_forum.js"></script>
</body>
</html>
