<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/modele/Entreprise_Tom.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';

// Entreprise demandée dans l'URL (?id=...)
$entreprise = null;
$erreurChargement = "";
if (empty($_GET['id'])) {
    $erreurChargement = "Aucune entreprise sélectionnée.";
} else {
    $entrepriseRepository = new EntrepriseRepository_Tom();
    $entreprise = $entrepriseRepository->getEntreprise((int) $_GET['id']);
    if ($entreprise == null) {
        $erreurChargement = "Cette entreprise n'existe pas.";
    }
}

// Nom affiché dans le titre de la page
$nomAffiche = 'Entreprise';
if ($entreprise != null) {
    $nomAffiche = $entreprise->getNomEntreprise();
}

// Seuls les alumni et les partenaires connectés peuvent se rattacher à une entreprise
$utilisateurConnecte = null;
$peutSeRattacher = false;
$idEntrepriseUtilisateur = null;
if (estConnecte()) {
    $utilisateurRepository = new UtilisateurRepository_malik();
    $utilisateurConnecte = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);
    if ($utilisateurConnecte != null) {
        $idEntrepriseUtilisateur = $utilisateurConnecte->getIdEntreprise();
        if ($utilisateurConnecte->getRole() == 'alumni' || $utilisateurConnecte->getRole() == 'partenaire') {
            $peutSeRattacher = true;
        }
    }
}

$estMonEntreprise = ($entreprise != null && $idEntrepriseUtilisateur == $entreprise->getIdEntreprise());

// Poste actuel de l'utilisateur ("" s'il n'en a pas)
$posteUtilisateur = "";
if ($utilisateurConnecte != null && $utilisateurConnecte->getPoste() != null) {
    $posteUtilisateur = $utilisateurConnecte->getPoste();
}

// Messages laissés par src/traitement/rattachement_entreprise.php
$messageSucces = "";
$messageErreur = "";
if (!empty($_SESSION['entreprise_message'])) {
    $messageSucces = $_SESSION['entreprise_message'];
    $_SESSION['entreprise_message'] = "";
}
if (!empty($_SESSION['entreprise_erreur'])) {
    $messageErreur = $_SESSION['entreprise_erreur'];
    $_SESSION['entreprise_erreur'] = "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'entreprises';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nomAffiche); ?> — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="profil_entreprise.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / <a href="page_entreprise.php">Entreprises</a> / <?php echo htmlspecialchars($nomAffiche); ?></p>
            <?php if ($entreprise != null) { ?>
                <h1><?php echo htmlspecialchars($entreprise->getNomEntreprise()); ?></h1>
                <?php if ($entreprise->getActivite() != null) { ?>
                    <p><?php echo htmlspecialchars($entreprise->getActivite()); ?></p>
                <?php } ?>
            <?php } else { ?>
                <h1>Entreprise introuvable</h1>
            <?php } ?>
        </div>
    </section>

    <!-- ===== FICHE ENTREPRISE ===== -->
    <section class="section entreprises-section">
        <div class="container">
            <div class="fiche-card">

                <?php if ($messageSucces != "") { ?>
                    <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($messageSucces); ?></p>
                <?php } ?>
                <?php if ($messageErreur != "") { ?>
                    <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($messageErreur); ?></p>
                <?php } ?>

                <?php if ($erreurChargement != "") { ?>
                    <p class="form-status form-status--error" role="alert"><?php echo $erreurChargement; ?></p>
                    <a href="page_entreprise.php" class="btn btn-outline">Retour à la liste</a>
                <?php } else { ?>

                    <div class="fiche-entete">
                        <h2><?php echo htmlspecialchars($entreprise->getNomEntreprise()); ?></h2>
                        <?php if ($estMonEntreprise) { ?>
                            <span class="badge">Votre entreprise</span>
                        <?php } ?>
                    </div>

                    <dl class="fiche-infos">
                        <div>
                            <dt>Activité</dt>
                            <dd><?php if ($entreprise->getActivite() != null) { echo htmlspecialchars($entreprise->getActivite()); } else { echo 'Non renseignée'; } ?></dd>
                        </div>
                        <div>
                            <dt>Adresse</dt>
                            <dd><?php if ($entreprise->getAdresse() != null) { echo htmlspecialchars($entreprise->getAdresse()); } else { echo 'Non renseignée'; } ?></dd>
                        </div>
                        <div>
                            <dt>Site web</dt>
                            <dd><a href="<?php echo htmlspecialchars($entreprise->getSiteWeb()); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($entreprise->getSiteWeb()); ?></a></dd>
                        </div>
                    </dl>

                    <!-- ===== RATTACHEMENT ===== -->
                    <div class="fiche-rattachement">
                        <?php if (!estConnecte()) { ?>
                            <p><a href="../connexion/connexion.php">Connectez-vous</a> pour vous rattacher à cette entreprise.</p>
                        <?php } elseif (!$peutSeRattacher) { ?>
                            <p>Seuls les alumni et les partenaires peuvent se rattacher à une entreprise.</p>
                        <?php } elseif ($estMonEntreprise) { ?>
                            <p>Vous êtes rattaché à cette entreprise<?php if ($posteUtilisateur != "") { echo ' en tant que <strong>' . htmlspecialchars($posteUtilisateur) . '</strong>'; } ?>.</p>
                            <?php if ($utilisateurConnecte->getRole() != 'partenaire') { ?>
                                <form action="../../src/traitement/rattachement_entreprise.php" method="post">
                                    <input type="hidden" name="id_entreprise" value="<?php echo $entreprise->getIdEntreprise(); ?>">
                                    <input type="hidden" name="action" value="detacher">
                                    <input type="hidden" name="retour" value="profil">
                                    <button type="submit" class="btn btn-danger">Me détacher de cette entreprise</button>
                                </form>
                            <?php } ?>
                        <?php } else { ?>
                            <h3>Vous travaillez ici ?</h3>
                            <?php if ($idEntrepriseUtilisateur != null) { ?>
                                <p>Vous êtes actuellement rattaché à une autre entreprise. Ce rattachement la remplacera.</p>
                            <?php } ?>
                            <form action="../../src/traitement/rattachement_entreprise.php" method="post" class="rattachement-form">
                                <input type="hidden" name="id_entreprise" value="<?php echo $entreprise->getIdEntreprise(); ?>">
                                <input type="hidden" name="action" value="rattacher">
                                <input type="hidden" name="retour" value="profil">
                                <label for="poste">Votre poste <span class="optionnel">(facultatif)</span></label>
                                <input type="text" id="poste" name="poste" maxlength="150" value="<?php echo htmlspecialchars($posteUtilisateur); ?>" placeholder="Exemple : Développeur web">
                                <button type="submit" class="btn btn-primary">Me rattacher à cette entreprise</button>
                            </form>
                        <?php } ?>
                    </div>

                    <div class="fiche-actions">
                        <a href="page_entreprise.php" class="btn btn-outline">Retour à la liste</a>
                    </div>

                <?php } ?>
            </div>
        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_entreprise.js"></script>
</body>
</html>
