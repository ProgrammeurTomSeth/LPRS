<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';

if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null) {
    header('Location: liste_offres_Tom.php');
    exit;
}

$roleUtilisateur = $utilisateur->getRole();
$idEntreprise = $utilisateur->getIdEntreprise();

if (($roleUtilisateur != 'partenaire' && $roleUtilisateur != 'alumni') || empty($idEntreprise) || $utilisateur->getStatutValidation() != 'valide') {
    $_SESSION['creation_offre_erreur'] = "La création d'offres est réservée aux partenaires et aux alumni validés, rattachés à une entreprise.";
    header('Location: liste_offres_Tom.php');
    exit;
}

if (empty($_SESSION['creation_offre_csrf'])) {
    $_SESSION['creation_offre_csrf'] = bin2hex(random_bytes(32));
}

$erreur = '';

if (!empty($_SESSION['creation_offre_erreur'])) {
    $erreur = $_SESSION['creation_offre_erreur'];
    unset($_SESSION['creation_offre_erreur']);
}

$ancien = array(
    'titre' => '',
    'description' => '',
    'missions' => '',
    'salaire' => '',
    'type' => '',
    'profil_cible' => 'tous'
);

if (isset($_SESSION['creation_offre_ancien'])) {
    $ancien = array_merge($ancien, $_SESSION['creation_offre_ancien']);
    unset($_SESSION['creation_offre_ancien']);
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Créer une offre — Lycée Robert Shuman</title>

    <meta name="description" content="Publiez une offre d'emploi, de stage ou d'alternance auprès de la communauté du Lycée Robert Shuman.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="creation_offre.css">
</head>

<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <section class="page-hero">
        <div class="hero-overlay"></div>

        <div class="container hero-content">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                /
                <a href="liste_offres_Tom.php">Offres</a>
                /
                Créer une offre
            </p>

            <h1>Créer une offre</h1>

            <p>
                Proposez une opportunité professionnelle aux étudiants
                et aux anciens élèves du lycée.
            </p>
        </div>
    </section>

    <section class="section creation-offre-section">

        <div class="container">

            <div class="creation-offre-introduction">
                <span class="eyebrow">Espace entreprise</span>

                <h2>Publier une nouvelle offre</h2>

                <p>
                    Présentez votre opportunité et précisez les profils
                    que vous souhaitez recruter.
                </p>
            </div>

            <div class="creation-offre-card">

                <?php if ($erreur != '') { ?>
                    <div class="form-status form-status--error" role="alert">
                        <?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                <?php } ?>

                <div class="information-entreprise">
                    <strong>Publication au nom de votre entreprise</strong>

                    <p>
                        Votre offre sera automatiquement associée à votre
                        entreprise et à votre compte utilisateur.
                    </p>
                </div>

                <form method="POST" action="../../src/traitement/CreationOffreTraitement_Tom.php" class="form-creation-offre">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['creation_offre_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="form-group">
                        <label for="titre">Titre de l'offre *</label>

                        <input type="text" id="titre" name="titre" maxlength="255" required placeholder="Exemple : Développeur web en alternance" value="<?php echo htmlspecialchars($ancien['titre'], ENT_QUOTES, 'UTF-8'); ?>">

                        <small>
                            Donnez un intitulé clair au poste proposé.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="description">Description de l'offre *</label>

                        <textarea id="description" name="description" rows="5" required placeholder="Présentez votre entreprise et le poste proposé..."><?php echo htmlspecialchars($ancien['description'], ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="missions">Missions proposées *</label>

                        <textarea id="missions" name="missions" rows="6" required placeholder="Décrivez les principales missions qui seront confiées au candidat..."><?php echo htmlspecialchars($ancien['missions'], ENT_QUOTES, 'UTF-8'); ?></textarea>

                        <small>
                            Détaillez les activités et responsabilités du futur candidat.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="salaire">Salaire proposé</label>

                        <div class="input-avec-suffixe">
                            <input type="number" id="salaire" name="salaire" min="0" step="0.01" placeholder="Exemple : 1200" value="<?php echo htmlspecialchars($ancien['salaire'], ENT_QUOTES, 'UTF-8'); ?>">

                            <span>€</span>
                        </div>

                        <small>
                            Champ facultatif. Indiquez le montant en euros.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="type">Type de contrat *</label>

                        <select id="type" name="type" required>
                            <option value="">Sélectionnez un type de contrat</option>

                            <option value="stage" <?php echo $ancien['type'] == 'stage' ? 'selected' : ''; ?>>
                                Stage
                            </option>

                            <option value="alternance" <?php echo $ancien['type'] == 'alternance' ? 'selected' : ''; ?>>
                                Alternance
                            </option>

                            <option value="cdd" <?php echo $ancien['type'] == 'cdd' ? 'selected' : ''; ?>>
                                CDD
                            </option>

                            <option value="cdi" <?php echo $ancien['type'] == 'cdi' ? 'selected' : ''; ?>>
                                CDI
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="profil_cible">Profil ciblé *</label>

                        <select id="profil_cible" name="profil_cible" required>
                            <option value="">Sélectionnez le profil recherché</option>

                            <option value="tous" <?php echo $ancien['profil_cible'] == 'tous' ? 'selected' : ''; ?>>
                                Étudiants et alumni
                            </option>

                            <option value="etudiant" <?php echo $ancien['profil_cible'] == 'etudiant' ? 'selected' : ''; ?>>
                                Étudiants uniquement
                            </option>

                            <option value="alumni" <?php echo $ancien['profil_cible'] == 'alumni' ? 'selected' : ''; ?>>
                                Alumni uniquement
                            </option>
                        </select>
                    </div>

                    <p class="form-obligatoire">
                        * Champs obligatoires
                    </p>

                    <div class="form-actions">
                        <a href="liste_offres_Tom.php" class="btn btn-outline">
                            Annuler
                        </a>

                        <button type="submit" class="btn btn-primary btn-lg">
                            Publier l'offre
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="creation_offre.js"></script>

</body>
</html>