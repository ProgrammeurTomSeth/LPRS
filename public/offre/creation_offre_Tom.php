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
    try {
        $_SESSION['creation_offre_csrf'] = bin2hex(random_bytes(32));
    } catch (Random\RandomException $e) {
        error_log($e->getMessage());
        http_response_code(500);
        exit("Impossible de sécuriser le formulaire. Veuillez réessayer plus tard.");
    }
}

$erreur = "";

if (!empty($_SESSION['creation_offre_erreur'])) {
    $erreur = $_SESSION['creation_offre_erreur'];
    unset($_SESSION['creation_offre_erreur']);
}

$anciennesValeurs = array();

if (!empty($_SESSION['creation_offre_anciennes_valeurs'])) {
    $anciennesValeurs = $_SESSION['creation_offre_anciennes_valeurs'];
    unset($_SESSION['creation_offre_anciennes_valeurs']);
}

function ancienneValeur($nom, $anciennesValeurs){
    return htmlspecialchars($anciennesValeurs[$nom] ?? '', ENT_QUOTES, 'UTF-8');
}

$pageActive = 'offres';

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Publier une offre — Lycée Robert Schuman</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="creation_offre_Tom.css">
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
                / Publier une offre
            </p>

            <h1>Publier une offre</h1>

            <p>Proposez une opportunité professionnelle aux étudiants et aux anciens élèves.</p>
        </div>
    </section>

    <section class="section creation-offre-section">

        <div class="container">

            <div class="creation-offre-introduction">
                <h2>Informations sur l'offre</h2>
                <p>
                    Complétez les informations ci-dessous pour publier votre offre.
                    Les champs marqués d'un astérisque sont obligatoires.
                </p>
            </div>

            <?php if ($erreur != "") { ?>
                <div class="form-status form-status--error" role="alert">
                    <?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php } ?>

            <form action="../../src/traitement/CreationOffreTraitement_Tom.php" method="POST" class="form-creation-offre" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['creation_offre_csrf'], ENT_QUOTES, 'UTF-8'); ?>">

                <div class="form-group">
                    <label for="titre">Titre de l'offre *</label>

                    <input type="text" id="titre" name="titre" placeholder="Exemple : Développeur web en alternance" value="<?php echo ancienneValeur('titre', $anciennesValeurs); ?>" maxlength="150" required>
                    <span class="form-error" id="erreur-titre"></span>
                </div>

                <div class="form-group">
                    <label for="description">Description de l'offre *</label>
                    <textarea id="description" name="description" rows="5" placeholder="Présentez votre entreprise et le poste proposé..." required><?php echo ancienneValeur('description', $anciennesValeurs); ?></textarea>
                    <span class="form-error" id="erreur-description"></span>
                </div>

                <div class="form-group">
                    <label for="missions">Missions proposées *</label>
                    <textarea id="missions" name="missions" rows="5" placeholder="Décrivez les missions et responsabilités..." required><?php echo ancienneValeur('missions', $anciennesValeurs); ?></textarea>
                    <span class="form-error" id="erreur-missions"></span>
                </div>

                <div class="form-group">
                    <label for="salaire">Salaire ou rémunération (facultatif)</label>
                    <input type="number" id="salaire" name="salaire" min="0" step="0.01" placeholder="Exemple : 1200" value="<?php echo ancienneValeur('salaire', $anciennesValeurs); ?>">
                    <span class="form-error" id="erreur-salaire"></span>
                </div>

                <div class="form-group">
                    <label for="type">Type de contrat *</label>

                    <select id="type" name="type" required>
                        <option value="">Choisir un type de contrat</option>
                        <option value="stage" <?php echo (ancienneValeur('type', $anciennesValeurs) == 'stage') ? 'selected' : ''; ?>>Stage</option>
                        <option value="alternance" <?php echo (ancienneValeur('type', $anciennesValeurs) == 'alternance') ? 'selected' : ''; ?>>Alternance</option>
                        <option value="cdd" <?php echo (ancienneValeur('type', $anciennesValeurs) == 'cdd') ? 'selected' : ''; ?>>CDD</option>
                        <option value="cdi" <?php echo (ancienneValeur('type', $anciennesValeurs) == 'cdi') ? 'selected' : ''; ?>>CDI</option>
                    </select>
                    <span class="form-error" id="erreur-type"></span>
                </div>

                <div class="form-group">
                    <label for="profil_cible">Profil recherché *</label>

                    <select id="profil_cible" name="profil_cible" required>
                        <option value="">Choisir un profil</option>
                        <option value="tous" <?php echo (ancienneValeur('profil_cible', $anciennesValeurs) == 'tous') ? 'selected' : ''; ?>>Tous les profils</option>
                        <option value="etudiant" <?php echo (ancienneValeur('profil_cible', $anciennesValeurs) == 'etudiant') ? 'selected' : ''; ?>>Étudiant</option>
                        <option value="alumni" <?php echo (ancienneValeur('profil_cible', $anciennesValeurs) == 'alumni') ? 'selected' : ''; ?>>Alumni</option>
                    </select>
                    <span class="form-error" id="erreur-profil_cible"></span>
                </div>

                <p class="form-required-info">* Champs obligatoires</p>

                <div class="form-actions">
                    <a href="liste_offres_Tom.php" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Publier l'offre</button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="creation_offre_Tom.js"></script>
</body>
</html>