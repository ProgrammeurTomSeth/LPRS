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

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null || !in_array($utilisateur->getRole(), array('partenaire', 'alumni'), true) || $utilisateur->getStatutValidation() != 'valide' || empty($utilisateur->getIdEntreprise())) {
    header('Location: liste_offres_Tom.php');
    exit;
}

if (empty($_SESSION['mes_offres_csrf'])) {
    $_SESSION['mes_offres_csrf'] = bin2hex(random_bytes(32));
}

$offreRepository = new OffreRepository_Tom();
$offres = $offreRepository->getMesOffresEntreprise($utilisateur->getIdEntreprise());
$message = '';
$typeMessage = '';

if (isset($_GET['resultat'])) {
    if ($_GET['resultat'] == 'statut_modifie') {
        $message = "Le statut de l'offre a bien été modifié.";
        $typeMessage = 'succes';
    } elseif ($_GET['resultat'] == 'erreur') {
        $message = "Impossible de modifier le statut de cette offre.";
        $typeMessage = 'erreur';
    }
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

$pageActive = 'offres';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes offres — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="mes_offres_Tom.css">
</head>
<body>
<?php require_once '../commun/entete.php'; ?>
<main id="main">
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                / Mes offres
            </p>
            <h1>Mes offres</h1>
            <p>Gérez les offres publiées par votre entreprise et consultez leur état de publication.</p>
        </div>
    </section>
    <section class="section mes-offres-section">
        <div class="container">
            <div class="mes-offres-entete">
                <div>
                    <h2>Les offres de mon entreprise</h2>
                    <p class="section-description">
                        Vous pouvez gérer toutes les offres associées à votre entreprise.
                    </p>
                </div>
                <div class="mes-offres-entete-actions">
                    <a href="liste_offres_Tom.php" class="btn btn-outline">
                        Retour aux offres
                    </a>
                    <a href="creation_offre_Tom.php" class="btn btn-primary">
                        Créer une offre
                    </a>
                </div>
            </div>
            <?php if ($message != '') { ?>
                <div class="message message-<?php echo htmlspecialchars($typeMessage); ?>" role="status">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php } ?>
            <?php if (count($offres) == 0) { ?>
                <div class="offres-vides">
                    <h3>Aucune offre publiée</h3>
                    <p>
                        Votre entreprise ne possède actuellement aucune offre.
                    </p>
                    <a href="creation_offre_Tom.php" class="btn btn-primary">
                        Créer une offre
                    </a>
                </div>
            <?php } else { ?>
                <div class="mes-offres-liste">
                    <?php foreach ($offres as $offre) { ?>
                        <?php
                        $statutOuvert = ($offre['statut'] == 'ouverte');
                        $nomAuteur = trim($offre['prenom_utilisateur'] . ' ' . $offre['nom_utilisateur']);
                        ?>
                        <article class="mes-offres-carte">
                            <div class="mes-offres-carte-entete">
                                <span class="offre-type">
                                    <?php
                                    echo htmlspecialchars($types[$offre['type']] ?? $offre['type']);
                                    ?>
                                </span>
                                <span class="offre-statut <?php echo $statutOuvert ? 'statut-ouverte' : 'statut-fermee'; ?>">
                                    <?php echo $statutOuvert ? 'Ouverte' : 'Clôturée'; ?>
                                </span>
                            </div>

                            <h3>
                                <?php echo htmlspecialchars($offre['titre']); ?>
                            </h3>

                            <p class="offre-entreprise">
                                <?php echo htmlspecialchars($offre['nom_entreprise'] ?? 'Entreprise'); ?>
                            </p>
                            <div class="mes-offres-description">
                                <?php echo nl2br(htmlspecialchars($offre['description'])); ?>
                            </div>
                            <div class="mes-offres-informations">
                                <p>
                                    <strong>Auteur :</strong>
                                    <?php echo htmlspecialchars($nomAuteur != '' ? $nomAuteur : 'Inconnu'); ?>
                                </p>

                                <p>
                                    <strong>Profil recherché :</strong>
                                    <?php
                                    echo htmlspecialchars(
                                            $profils[$offre['profil_cible']] ?? $offre['profil_cible']
                                    );
                                    ?>
                                </p>

                                <p>
                                    <strong>Salaire :</strong>
                                    <?php
                                    echo $offre['salaire_fr'] !== null ? htmlspecialchars($offre['salaire_fr']) . ' €' : 'Non renseigné';
                                    ?>
                                </p>

                                <p>
                                    <strong>Publication :</strong>
                                    <?php echo htmlspecialchars($offre['date_fr']); ?>
                                </p>
                            </div>
                            <div class="mes-offres-actions">
                                <form method="POST" action="../../src/traitement/GestionOffreTraitement_Tom.php">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['mes_offres_csrf']); ?>">
                                    <input type="hidden" name="id_offre" value="<?php echo (int) $offre['id_offre']; ?>">
                                    <input type="hidden" name="statut" value="<?php echo $statutOuvert ? 'cloturee' : 'ouverte'; ?>">
                                    <button type="submit" class="btn <?php echo $statutOuvert ? 'btn-danger' : 'btn-primary'; ?>">
                                        <?php echo $statutOuvert ? 'Clôturer l’offre' : 'Rouvrir l’offre'; ?>
                                    </button>
                                </form>
                                <a href="liste_offres_Tom.php" class="btn btn-outline">
                                    Voir les offres
                                </a>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

</body>
</html>