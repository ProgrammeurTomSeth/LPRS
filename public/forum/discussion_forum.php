<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/traitement/ForumRegles_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Forum_ahmed.php';
require_once '../../src/repository/ForumRepository_ahmed.php';
require_once '../../src/repository/SignalementForumRepository_ahmed.php';

// Le forum est réservé aux membres connectés
if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$idConnecte = $_SESSION['id_utilisateur'];
$roleConnecte = $_SESSION['role'];

$idPost = 0;
if (isset($_GET['id'])) {
    $idPost = (int) $_GET['id'];
}

// Tri des commentaires (?tri=recents), par défaut dans l'ordre de la discussion
$tri = 'anciens';
if (isset($_GET['tri']) && $_GET['tri'] == 'recents') {
    $tri = 'recents';
}

// getPost(), getCommentaires() et getReponsesAuxCommentaires() renvoient des TABLEAUX
$forumRepository = new ForumRepository_ahmed();
$post = $forumRepository->getPost($idPost);

// Sujet inexistant, ou canal interdit pour ce rôle : retour à la liste
if ($post == null || !peutAccederCanal($post['canal'], $roleConnecte)) {
    $_SESSION['forum_erreur'] = "Ce sujet n'existe pas ou vous n'y avez pas accès.";
    header('Location: forum.php');
    exit;
}

$commentaires = $forumRepository->getCommentaires($idPost, $tri);

// Réponses aux commentaires rangées par commentaire :
// tableau associatif id du commentaire => liste de ses réponses
$reponsesParCommentaire = array();
$reponses = $forumRepository->getReponsesAuxCommentaires($idPost);
foreach ($reponses as $reponse) {
    $reponsesParCommentaire[$reponse['id_forum_parent']][] = $reponse;
}
$nbMessages = count($commentaires) + count($reponses);

// Messages que j'ai déjà signalés (pour afficher « Signalé » au lieu du bouton)
$signalementRepository = new SignalementForumRepository_ahmed();
$idsSignales = $signalementRepository->getIdsSignalesPar($idConnecte);

$moderateur = estModerateur($roleConnecte);
$mesInitiales = initiales($_SESSION['prenom_utilisateur'], $_SESSION['nom_utilisateur']);

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

// Pour la barre latérale et le menu
$canalActif = $post['canal'];
$pageActive = 'forum';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars((string) $post['titre']); ?> — Forum — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="forum.css">
    <link rel="stylesheet" href="discussion_forum.css">
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
                <a href="forum.php?canal=<?php echo $post['canal']; ?>"><?php echo $canauxForum[$post['canal']]; ?></a>
            </nav>

            <?php if ($messageSucces != "") { ?>
                <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($messageSucces); ?></p>
            <?php } ?>
            <?php if ($messageErreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($messageErreur); ?></p>
            <?php } ?>

            <!-- ===== SUJET ===== -->
            <article class="post-carte" id="message-<?php echo $post['id_forum']; ?>">
                <!-- Image d'illustration choisie selon le canal (voir discussion_forum.css) -->
                <div class="post-banniere post-banniere--<?php echo $post['canal']; ?>" aria-hidden="true"></div>

                <div class="post-contenu">
                    <div class="sujet-badges">
                        <span class="badge-canal"><?php echo $canauxForum[$post['canal']]; ?></span>
                        <?php if ($post['id_utilisateur'] == $idConnecte) { ?>
                            <span class="badge-gris">Votre sujet</span>
                        <?php } ?>
                    </div>

                    <h1><?php echo htmlspecialchars((string) $post['titre']); ?></h1>

                    <div class="auteur">
                        <span class="avatar <?php echo classeAvatar($post['id_utilisateur']); ?>" aria-hidden="true"><?php echo htmlspecialchars(initiales($post['prenom_utilisateur'], $post['nom_utilisateur'])); ?></span>
                        <div>
                            <strong><?php echo htmlspecialchars($post['prenom_utilisateur'] . ' ' . $post['nom_utilisateur']); ?></strong>
                            <span class="badge-role"><?php echo $rolesForum[$post['role']]; ?></span>
                            <span class="auteur-date auteur-date--ligne"><?php echo tempsEcoule($post['minutes'], $post['date_fr']); ?></span>
                        </div>
                    </div>

                    <!-- nl2br garde les retours à la ligne, APRÈS htmlspecialchars -->
                    <p class="post-texte"><?php echo nl2br(htmlspecialchars($post['text'])); ?></p>

                    <div class="message-actions">
                        <?php if (peutSignaler($post['id_utilisateur'], $idConnecte, $roleConnecte)) { ?>
                            <?php if (in_array($post['id_forum'], $idsSignales)) { ?>
                                <span class="action-faite">Signalé</span>
                            <?php } else { ?>
                                <a href="signaler_message.php?id=<?php echo $post['id_forum']; ?>" class="action-lien action-signaler">Signaler</a>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($moderateur) { ?>
                            <form action="../../src/traitement/ForumTraitement_ahmed.php" method="post" class="form-supprimer" data-confirmation="Supprimer ce sujet et tous ses commentaires ?">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id_forum" value="<?php echo $post['id_forum']; ?>">
                                <button type="submit" class="action-lien action-supprimer">Supprimer le sujet</button>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </article>

            <!-- ===== COMMENTAIRES ===== -->
            <div class="commentaires-entete">
                <h2><?php echo $nbMessages; ?> commentaire<?php if ($nbMessages > 1) { echo 's'; } ?></h2>
                <div class="tri">
                    <a href="discussion_forum.php?id=<?php echo $idPost; ?>&amp;tri=anciens" class="tri-option" <?php if ($tri == 'anciens') { echo 'aria-current="true"'; } ?>>Les plus anciens</a>
                    <a href="discussion_forum.php?id=<?php echo $idPost; ?>&amp;tri=recents" class="tri-option" <?php if ($tri == 'recents') { echo 'aria-current="true"'; } ?>>Les plus récents</a>
                </div>
            </div>

            <!-- Ajouter un commentaire (réponse directe au sujet) -->
            <form action="../../src/traitement/ForumTraitement_ahmed.php" method="post" class="commentaire-nouveau">
                <input type="hidden" name="action" value="repondre">
                <input type="hidden" name="id_parent" value="<?php echo $post['id_forum']; ?>">
                <span class="avatar avatar--moi" aria-hidden="true"><?php echo htmlspecialchars($mesInitiales); ?></span>
                <div class="commentaire-champ">
                    <label for="nouveau-commentaire" class="visuellement-cache">Ajouter un commentaire</label>
                    <textarea id="nouveau-commentaire" name="text" rows="1" maxlength="5000" required placeholder="Ajouter un commentaire..."></textarea>
                    <div class="commentaire-boutons">
                        <button type="reset" class="btn btn-sm btn-texte">Annuler</button>
                        <button type="submit" class="btn btn-sm btn-primary">Publier</button>
                    </div>
                </div>
            </form>

            <?php if (count($commentaires) == 0) { ?>
                <p class="forum-vide-texte">Personne n'a encore commenté. Soyez le premier !</p>
            <?php } ?>

            <div class="commentaires">
                <?php foreach ($commentaires as $commentaire) { ?>
                    <?php
                    $idCommentaire = $commentaire['id_forum'];
                    $sousReponses = array();
                    if (isset($reponsesParCommentaire[$idCommentaire])) {
                        $sousReponses = $reponsesParCommentaire[$idCommentaire];
                    }
                    ?>
                    <article class="commentaire" id="message-<?php echo $idCommentaire; ?>">
                        <span class="avatar <?php echo classeAvatar($commentaire['id_utilisateur']); ?>" aria-hidden="true"><?php echo htmlspecialchars(initiales($commentaire['prenom_utilisateur'], $commentaire['nom_utilisateur'])); ?></span>

                        <div class="commentaire-corps">
                            <p class="commentaire-meta">
                                <strong><?php echo htmlspecialchars($commentaire['prenom_utilisateur'] . ' ' . $commentaire['nom_utilisateur']); ?></strong>
                                <span class="auteur-role"><?php echo $rolesForum[$commentaire['role']]; ?></span>
                                <span class="auteur-date"><?php echo tempsEcoule($commentaire['minutes'], $commentaire['date_fr']); ?></span>
                            </p>
                            <p class="commentaire-texte"><?php echo nl2br(htmlspecialchars($commentaire['text'])); ?></p>

                            <div class="message-actions">
                                <button type="button" class="action-lien bouton-repondre" data-formulaire="form-reponse-<?php echo $idCommentaire; ?>" data-mention="">Répondre</button>
                                <?php if (peutSignaler($commentaire['id_utilisateur'], $idConnecte, $roleConnecte)) { ?>
                                    <?php if (in_array($idCommentaire, $idsSignales)) { ?>
                                        <span class="action-faite">Signalé</span>
                                    <?php } else { ?>
                                        <a href="signaler_message.php?id=<?php echo $idCommentaire; ?>" class="action-lien action-signaler">Signaler</a>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($moderateur) { ?>
                                    <form action="../../src/traitement/ForumTraitement_ahmed.php" method="post" class="form-supprimer" data-confirmation="Supprimer ce commentaire et ses réponses ?">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id_forum" value="<?php echo $idCommentaire; ?>">
                                        <button type="submit" class="action-lien action-supprimer">Supprimer</button>
                                    </form>
                                <?php } ?>
                            </div>

                            <!-- Formulaire de réponse au commentaire : caché, ouvert par le bouton Répondre (script_forum.js) -->
                            <form action="../../src/traitement/ForumTraitement_ahmed.php" method="post" class="form-reponse" id="form-reponse-<?php echo $idCommentaire; ?>" hidden>
                                <input type="hidden" name="action" value="repondre">
                                <input type="hidden" name="id_parent" value="<?php echo $idCommentaire; ?>">
                                <label for="texte-reponse-<?php echo $idCommentaire; ?>" class="visuellement-cache">Votre réponse</label>
                                <textarea id="texte-reponse-<?php echo $idCommentaire; ?>" name="text" rows="2" maxlength="5000" required placeholder="Répondre à <?php echo htmlspecialchars($commentaire['prenom_utilisateur']); ?>..."></textarea>
                                <div class="commentaire-boutons">
                                    <button type="button" class="btn btn-sm btn-texte bouton-annuler-reponse">Annuler</button>
                                    <button type="submit" class="btn btn-sm btn-primary">Répondre</button>
                                </div>
                            </form>

                            <?php if (count($sousReponses) > 0) { ?>
                                <!-- <details> : les réponses s'affichent au clic, sans JavaScript -->
                                <details class="sous-reponses">
                                    <summary><?php echo count($sousReponses); ?> réponse<?php if (count($sousReponses) > 1) { echo 's'; } ?></summary>

                                    <?php foreach ($sousReponses as $reponse) { ?>
                                        <article class="commentaire commentaire--reponse" id="message-<?php echo $reponse['id_forum']; ?>">
                                            <span class="avatar avatar--petit <?php echo classeAvatar($reponse['id_utilisateur']); ?>" aria-hidden="true"><?php echo htmlspecialchars(initiales($reponse['prenom_utilisateur'], $reponse['nom_utilisateur'])); ?></span>

                                            <div class="commentaire-corps">
                                                <p class="commentaire-meta">
                                                    <strong><?php echo htmlspecialchars($reponse['prenom_utilisateur'] . ' ' . $reponse['nom_utilisateur']); ?></strong>
                                                    <span class="auteur-role"><?php echo $rolesForum[$reponse['role']]; ?></span>
                                                    <span class="auteur-date"><?php echo tempsEcoule($reponse['minutes'], $reponse['date_fr']); ?></span>
                                                </p>
                                                <p class="commentaire-texte"><?php echo nl2br(htmlspecialchars($reponse['text'])); ?></p>

                                                <div class="message-actions">
                                                    <!-- Répondre à une réponse : même formulaire que le commentaire, avec @Prénom -->
                                                    <button type="button" class="action-lien bouton-repondre" data-formulaire="form-reponse-<?php echo $idCommentaire; ?>" data-mention="@<?php echo htmlspecialchars($reponse['prenom_utilisateur']); ?> ">Répondre</button>
                                                    <?php if (peutSignaler($reponse['id_utilisateur'], $idConnecte, $roleConnecte)) { ?>
                                                        <?php if (in_array($reponse['id_forum'], $idsSignales)) { ?>
                                                            <span class="action-faite">Signalé</span>
                                                        <?php } else { ?>
                                                            <a href="signaler_message.php?id=<?php echo $reponse['id_forum']; ?>" class="action-lien action-signaler">Signaler</a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                    <?php if ($moderateur) { ?>
                                                        <form action="../../src/traitement/ForumTraitement_ahmed.php" method="post" class="form-supprimer" data-confirmation="Supprimer cette réponse ?">
                                                            <input type="hidden" name="action" value="supprimer">
                                                            <input type="hidden" name="id_forum" value="<?php echo $reponse['id_forum']; ?>">
                                                            <button type="submit" class="action-lien action-supprimer">Supprimer</button>
                                                        </form>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </article>
                                    <?php } ?>
                                </details>
                            <?php } ?>
                        </div>
                    </article>
                <?php } ?>
            </div>
        </div>

        <?php require_once 'barre_laterale_forum.php'; ?>

    </div>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_forum.js"></script>
</body>
</html>
