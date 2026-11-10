<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/OffreRepository_Tom.php';

$messages = array(
        'succes' => 'Votre candidature a bien été enregistrée.',
        'connexion' => 'Vous devez être connecté pour postuler.',
        'profil' => 'Seuls les étudiants et les alumni peuvent postuler.',
        'profil_offre' => 'Cette offre ne correspond pas à votre profil.',
        'deja_postule' => 'Vous avez déjà postulé à cette offre.',
        'indisponible' => 'Cette offre n’est plus disponible.',
        'motivation' => 'Veuillez saisir votre motivation.',
        'erreur' => 'Une erreur est survenue. Veuillez réessayer.'
);

$message = "";
$resultat = "";

if (isset($_GET['resultat']) && isset($messages[$_GET['resultat']])) {
    $resultat = $_GET['resultat'];
    $message = $messages[$resultat];
}

$types = array(
        'stage' => 'Stage',
        'alternance' => 'Alternance',
        'cdd' => 'CDD',
        'cdi' => 'CDI'
);

$profils = array(
        'tous' => 'Étudiants et alumni',
        'etudiant' => 'Étudiants',
        'alumni' => 'Alumni'
);

$roleUtilisateur = "";
$peutCreerOffre = false;

if (estConnecte()) {
    $utilisateurRepository = new UtilisateurRepository_malik();
    $utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);
    if ($utilisateur != null) {
        $roleUtilisateur = $utilisateur->getRole();
        $peutCreerOffre = (($roleUtilisateur == 'partenaire' || $roleUtilisateur == 'alumni') && $utilisateur->getStatutValidation() == 'valide' && !empty($utilisateur->getIdEntreprise()));
    }
}

$offreRepository = new OffreRepository_Tom();
$offres = $offreRepository->getOffresOuvertes();

$pageActive = 'offres';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres d'emploi et de stage — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="liste_offres_Tom.css">
</head>
<body>
<?php require_once '../commun/entete.php'; ?>

<main id="main">
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                / Offres
            </p>
            <h1>Offres d'emploi et de stage</h1>
            <p>
                Découvrez les opportunités proposées par les entreprises partenaires et postulez aux offres qui vous intéressent.
            </p>
        </div>
    </section>

    <section class="section offres-section">
        <div class="container">
            <h2>Les offres disponibles</h2>
            <p class="section-description">
                Retrouvez ici les offres actuellement ouvertes aux candidatures.
            </p>

            <?php if ($peutCreerOffre) { ?>
                <div class="offres-actions">
                    <a href="mes_offres_Tom.php" class="btn btn-primary">
                        Gérer mes offres
                    </a>
                </div>
            <?php } ?>
            <?php if ($message != "") { ?>
                <?php if ($resultat == 'succes') { ?>
                    <div class="message message-succes" role="status">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php } else { ?>
                    <div class="message message-erreur" role="alert">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php if (count($offres) == 0) { ?>
                <div class="offres-vides">
                    <h3>Aucune offre disponible</h3>
                    <p>
                        Il n'y a actuellement aucune offre ouverte.
                        Revenez consulter cette page ultérieurement.
                    </p>
                </div>
            <?php } else { ?>
                <div class="offres-grille">
                    <?php foreach ($offres as $offre) { ?>
                        <?php
                        $bonRole = ($roleUtilisateur == 'etudiant' || $roleUtilisateur == 'alumni');
                        $profilCompatible = ($offre['profil_cible'] == 'tous' || $offre['profil_cible'] == $roleUtilisateur);
                        $peutPostuler = ($bonRole && $profilCompatible);
                        $dejaPostule = false;
                        if (estConnecte()) {
                            $dejaPostule = $offreRepository->aDejaPostule($offre['id_offre'], $_SESSION['id_utilisateur']);
                        }
                        ?>
                        <article class="offre-carte">
                            <div class="offre-entete">
                                <span class="offre-type">
                                    <?php echo isset($types[$offre['type']]) ? $types[$offre['type']] : htmlspecialchars($offre['type']); ?>
                                </span>
                                <span class="offre-statut">
                                    Ouverte
                                </span>
                            </div>
                            <h3>
                                <?php echo htmlspecialchars($offre['titre']); ?>
                            </h3>
                            <p class="offre-entreprise">
                                <?php if ($offre['nom_entreprise'] != null) { ?>
                                    <?php echo htmlspecialchars($offre['nom_entreprise']); ?>
                                <?php } else { ?>
                                    Entreprise non renseignée
                                <?php } ?>
                            </p>
                            <div class="offre-description">
                                <?php echo htmlspecialchars($offre['description']); ?>
                            </div>
                            <?php if ($offre['missions'] != null) { ?>
                                <div class="offre-missions">
                                    <h4>Missions</h4>
                                    <p>
                                        <?php echo htmlspecialchars($offre['missions']); ?>
                                    </p>
                                </div>
                            <?php } ?>
                            <div class="offre-informations">
                                <p>
                                    <strong>Profil recherché :</strong>
                                    <?php echo isset($profils[$offre['profil_cible']]) ? $profils[$offre['profil_cible']] : htmlspecialchars($offre['profil_cible']); ?>
                                </p>
                                <p>
                                    <strong>Salaire :</strong>
                                    <?php if ($offre['salaire_fr'] != null) { ?>
                                        <?php echo htmlspecialchars($offre['salaire_fr']); ?> €
                                    <?php } else { ?>
                                        Non renseigné
                                    <?php } ?>
                                </p>
                                <p>
                                    <strong>Publication :</strong>
                                    <?php echo htmlspecialchars($offre['date_fr']); ?>
                                </p>
                            </div>
                            <div class="offre-candidature">
                                <?php if ($dejaPostule) { ?>
                                    <p class="deja-postule">
                                        Vous avez déjà postulé à cette offre.
                                    </p>
                                <?php } elseif (!estConnecte()) { ?>
                                    <p>
                                        Connectez-vous avec un compte étudiant ou alumni pour postuler.
                                    </p>
                                    <a class="bouton" href="../connexion/connexion.php">
                                        Se connecter
                                    </a>
                                <?php } elseif (!$bonRole) { ?>
                                    <p>
                                        La candidature est réservée aux étudiants et aux alumni.
                                    </p>
                                <?php } elseif (!$peutPostuler) { ?>
                                    <p>
                                        Cette offre ne correspond pas à votre profil.
                                    </p>
                                <?php } else { ?>
                                    <form method="POST" action="../../src/traitement/ListeOffreTraitement_Tom.php" class="form-candidature">
                                        <input type="hidden" name="id_offre" value="<?php echo (int) $offre['id_offre']; ?>">
                                        <label for="motivation-<?php echo (int) $offre['id_offre']; ?>">
                                            Votre motivation
                                        </label>
                                        <textarea id="motivation-<?php echo (int) $offre['id_offre']; ?>" name="motivation" rows="4" maxlength="5000" required placeholder="Expliquez pourquoi cette offre vous intéresse..."></textarea>
                                        <button type="submit" class="bouton">
                                            Postuler
                                        </button>
                                    </form>
                                <?php } ?>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="liste_offres_Tom.js"></script>
</body>
</html>