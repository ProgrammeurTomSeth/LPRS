<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/traitement/ForumRegles_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/repository/SignalementForumRepository_ahmed.php';

// Page réservée au gestionnaire (seul modérateur du forum)
if (!estConnecte() || !estModerateur($_SESSION['role'])) {
    header('Location: ../connexion/connexion.php');
    exit;
}

// getSignalements() renvoie des TABLEAUX : une ligne par signalement.
// On les regroupe par message signalé, pour afficher chaque message une seule fois
// avec la liste de ses signalements : tableau associatif id du message => infos.
$signalementRepository = new SignalementForumRepository_ahmed();
$messagesSignales = array();
foreach ($signalementRepository->getSignalements() as $ligne) {
    $idMessage = $ligne['id_forum'];
    if (!isset($messagesSignales[$idMessage])) {
        $messagesSignales[$idMessage] = array(
            'message' => $ligne,
            'signalements' => array()
        );
    }
    $messagesSignales[$idMessage]['signalements'][] = $ligne;
}

// Messages laissés par src/traitement/ModerationForumTraitement_ahmed.php
$messageSucces = "";
$messageErreur = "";
if (!empty($_SESSION['moderation_message'])) {
    $messageSucces = $_SESSION['moderation_message'];
    $_SESSION['moderation_message'] = "";
}
if (!empty($_SESSION['moderation_erreur'])) {
    $messageErreur = $_SESSION['moderation_erreur'];
    $_SESSION['moderation_erreur'] = "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'admin';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modération du forum — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="moderation_forum.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / <a href="page_admin.php">Administration</a> / Modération du forum</p>
            <h1>Modération du forum</h1>
            <p>Relisez les messages signalés par les membres : supprimez-les ou ignorez le signalement.</p>
        </div>
    </section>

    <section class="section moderation-section">
        <div class="container">

            <a href="page_admin.php" class="moderation-retour">← Retour à la gestion des utilisateurs</a>

            <?php if ($messageSucces != "") { ?>
                <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($messageSucces); ?></p>
            <?php } ?>
            <?php if ($messageErreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($messageErreur); ?></p>
            <?php } ?>

            <h2 class="moderation-titre">
                <?php echo count($messagesSignales); ?> message<?php if (count($messagesSignales) > 1) { echo 's'; } ?> signalé<?php if (count($messagesSignales) > 1) { echo 's'; } ?>
            </h2>

            <?php if (count($messagesSignales) == 0) { ?>
                <div class="moderation-vide">
                    <p>Aucun signalement en attente. Le forum est calme !</p>
                    <a href="../forum/forum.php" class="btn btn-outline btn-sm">Voir le forum</a>
                </div>
            <?php } ?>

            <?php foreach ($messagesSignales as $idMessage => $infos) { ?>
                <?php $message = $infos['message']; ?>
                <article class="moderation-carte">

                    <div class="moderation-entete">
                        <div class="moderation-badges">
                            <span class="badge-canal"><?php echo $canauxForum[$message['canal']]; ?></span>
                            <span class="badge-type"><?php if ($message['id_forum_parent'] == null) { echo 'Sujet'; } else { echo 'Commentaire'; } ?></span>
                            <span class="badge-nombre"><?php echo count($infos['signalements']); ?> signalement<?php if (count($infos['signalements']) > 1) { echo 's'; } ?></span>
                        </div>
                        <a href="../forum/discussion_forum.php?id=<?php echo $message['id_post']; ?>#message-<?php echo $idMessage; ?>" class="moderation-lien">Voir dans la discussion</a>
                    </div>

                    <!-- Le message signalé -->
                    <div class="moderation-message">
                        <p class="moderation-auteur">
                            <strong><?php echo htmlspecialchars($message['prenom_auteur'] . ' ' . $message['nom_auteur']); ?></strong>
                            (<?php echo $rolesForum[$message['role_auteur']]; ?>) — le <?php echo $message['date_message_fr']; ?>
                            <?php if ($message['titre_post'] != null) { ?>
                                <br>Discussion : « <?php echo htmlspecialchars($message['titre_post']); ?> »
                            <?php } ?>
                        </p>
                        <?php if ($message['titre'] != null) { ?>
                            <p class="moderation-message-titre"><?php echo htmlspecialchars($message['titre']); ?></p>
                        <?php } ?>
                        <p class="moderation-message-texte"><?php echo nl2br(htmlspecialchars($message['text'])); ?></p>
                    </div>

                    <!-- Les signalements reçus pour ce message -->
                    <ul class="moderation-signalements">
                        <?php foreach ($infos['signalements'] as $signalement) { ?>
                            <li>
                                <strong><?php echo $motifsSignalement[$signalement['motif']]; ?></strong>
                                — signalé par <?php echo htmlspecialchars($signalement['prenom_signaleur'] . ' ' . $signalement['nom_signaleur']); ?>
                                le <?php echo $signalement['date_signalement_fr']; ?>
                                <?php if ($signalement['commentaire'] != null) { ?>
                                    <span class="moderation-commentaire">« <?php echo htmlspecialchars($signalement['commentaire']); ?> »</span>
                                <?php } ?>
                            </li>
                        <?php } ?>
                    </ul>

                    <div class="moderation-actions">
                        <form action="../../src/traitement/ModerationForumTraitement_ahmed.php" method="post" class="form-confirmer" data-confirmation="Garder ce message en ligne et supprimer ses signalements ?">
                            <input type="hidden" name="action" value="ignorer">
                            <input type="hidden" name="id_forum" value="<?php echo $idMessage; ?>">
                            <button type="submit" class="btn btn-outline btn-sm">Ignorer le signalement</button>
                        </form>
                        <form action="../../src/traitement/ModerationForumTraitement_ahmed.php" method="post" class="form-confirmer" data-confirmation="Supprimer définitivement ce message (et ses réponses) ?">
                            <input type="hidden" name="action" value="supprimer_message">
                            <input type="hidden" name="id_forum" value="<?php echo $idMessage; ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Supprimer le message</button>
                        </form>
                    </div>
                </article>
            <?php } ?>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_moderation.js"></script>
</body>
</html>
