<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/OffreRepository_Tom.php';

if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

// Seuls les membres d'une entreprise (partenaire ou alumni validé) gèrent les candidatures
$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null || !in_array($utilisateur->getRole(), array('partenaire', 'alumni'), true) || $utilisateur->getStatutValidation() != 'valide' || empty($utilisateur->getIdEntreprise())) {
    header('Location: liste_offres_Tom.php');
    exit;
}

if (empty($_SESSION['mes_offres_csrf'])) {
    $_SESSION['mes_offres_csrf'] = bin2hex(random_bytes(32));
}

// L'offre doit appartenir à l'entreprise de l'utilisateur
$idOffre = (isset($_GET['id']) && ctype_digit((string) $_GET['id'])) ? (int) $_GET['id'] : 0;
$offreRepository = new OffreRepository_Tom();
$offre = $offreRepository->getOffreEntreprise($idOffre, $utilisateur->getIdEntreprise());

if (!$offre) {
    header('Location: mes_offres_Tom.php');
    exit;
}

$candidatures = $offreRepository->getCandidatures($idOffre);

// Message laissé par src/traitement/CandidatureTraitement_Tom.php
$message = '';
$typeMessage = '';
if (isset($_GET['resultat'])) {
    if ($_GET['resultat'] == 'refusee') {
        $message = "La candidature a été retirée de la liste et le candidat a été prévenu par email.";
        $typeMessage = 'succes';
    } elseif ($_GET['resultat'] == 'refusee_sans_email') {
        $message = "La candidature a été retirée de la liste, mais l'email au candidat n'a pas pu être envoyé.";
        $typeMessage = 'erreur';
    } elseif ($_GET['resultat'] == 'erreur') {
        $message = "Impossible de modifier cette candidature.";
        $typeMessage = 'erreur';
    }
}

$roles = array(
        'etudiant' => 'Étudiant',
        'alumni' => 'Alumni'
);

$pageActive = 'offres';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candidatures — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="mes_offres_Tom.css">
    <link rel="stylesheet" href="candidats_offre_Tom.css">
</head>
<body>
<?php require_once '../commun/entete.php'; ?>
<main id="main">
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                / <a href="mes_offres_Tom.php">Mes offres</a>
                / Candidatures
            </p>
            <h1>Candidatures</h1>
            <p><?php echo htmlspecialchars($offre['titre']); ?></p>
        </div>
    </section>
    <section class="section mes-offres-section">
        <div class="container">
            <div class="mes-offres-entete">
                <div>
                    <h2><?php echo count($candidatures); ?> candidature<?php echo count($candidatures) > 1 ? 's' : ''; ?></h2>
                    <p class="section-description">
                        « Ne pas retenir » retire la candidature de cette liste et envoie un email au candidat.
                    </p>
                </div>
                <div class="mes-offres-entete-actions">
                    <a href="mes_offres_Tom.php" class="btn btn-outline">
                        Retour à mes offres
                    </a>
                </div>
            </div>

            <?php if ($message != '') { ?>
                <div class="message message-<?php echo htmlspecialchars($typeMessage); ?>" role="status">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php } ?>

            <?php if (count($candidatures) == 0) { ?>
                <div class="offres-vides">
                    <h3>Aucune candidature</h3>
                    <p>Personne n'a postulé à cette offre pour le moment.</p>
                </div>
            <?php } else { ?>
                <div class="candidats-liste">
                    <?php foreach ($candidatures as $candidature) { ?>
                        <article class="mes-offres-carte">
                            <div class="mes-offres-carte-entete">
                                <span class="offre-type">
                                    <?php echo htmlspecialchars($roles[$candidature['role']] ?? $candidature['role']); ?>
                                </span>
                                <span class="candidat-date">
                                    Reçue le <?php echo htmlspecialchars($candidature['date_fr']); ?>
                                </span>
                            </div>
                            <h3><?php echo htmlspecialchars($candidature['prenom_utilisateur'] . ' ' . $candidature['nom_utilisateur']); ?></h3>
                            <p class="offre-entreprise">
                                <a href="mailto:<?php echo htmlspecialchars($candidature['mail']); ?>"><?php echo htmlspecialchars($candidature['mail']); ?></a>
                            </p>
                            <h4 class="candidat-titre-motivation">Motivation</h4>
                            <div class="mes-offres-description">
                                <?php echo nl2br(htmlspecialchars($candidature['motivation'])); ?>
                            </div>
                            <div class="mes-offres-actions">
                                <?php if (!empty($candidature['cv'])) { ?>
                                    <a href="cv_candidat_Tom.php?offre=<?php echo (int) $idOffre; ?>&amp;candidat=<?php echo (int) $candidature['id_utilisateur']; ?>" class="btn btn-outline" target="_blank" rel="noopener">
                                        Voir le CV
                                    </a>
                                <?php } else { ?>
                                    <span class="candidat-sans-cv">Pas de CV</span>
                                <?php } ?>

                                <!-- data-confirmer : candidats_offre_Tom.js demande confirmation avant d'envoyer -->
                                <form method="POST" action="../../src/traitement/CandidatureTraitement_Tom.php" data-confirmer="Ne pas retenir cette candidature ? Elle sera retirée de la liste et le candidat recevra un email.">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['mes_offres_csrf']); ?>">
                                    <input type="hidden" name="id_offre" value="<?php echo (int) $idOffre; ?>">
                                    <input type="hidden" name="id_candidat" value="<?php echo (int) $candidature['id_utilisateur']; ?>">
                                    <button type="submit" class="btn btn-danger">Ne pas retenir</button>
                                </form>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="candidats_offre_Tom.js"></script>

</body>
</html>
