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

if (!isset($_GET['id']) || !ctype_digit((string) $_GET['id'])) {
    header('Location: mes_offres_Tom.php');
    exit;
}

$idOffre = (int) $_GET['id'];
$idEntreprise = $utilisateur->getIdEntreprise();
$offreRepository = new OffreRepository_Tom();
$offre = $offreRepository->getOffreEntreprise($idOffre, $idEntreprise);

if (!$offre) {
    header('Location: mes_offres_Tom.php');
    exit;
}

if (empty($_SESSION['modifier_offre_csrf'])) {
    $_SESSION['modifier_offre_csrf'] = bin2hex(random_bytes(32));
}

$erreur = '';

if (isset($_SESSION['modifier_offre_erreur'])) {
    $erreur = $_SESSION['modifier_offre_erreur'];
    unset($_SESSION['modifier_offre_erreur']);
}

$anciennesValeurs = array();

if (isset($_SESSION['modifier_offre_anciennes_valeurs'])) {
    $anciennesValeurs = $_SESSION['modifier_offre_anciennes_valeurs'];
    unset($_SESSION['modifier_offre_anciennes_valeurs']);
}

function valeurModification($nom, $offre, $anciennesValeurs){
    $valeur = $anciennesValeurs[$nom] ?? $offre[$nom] ?? '';
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}
$pageActive = 'offres';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une offre — Lycée Robert Schuman</title>
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
                <a href="mes_offres_Tom.php">Mes offres</a>
                / Modifier
            </p>
            <h1>Modifier une offre</h1>
            <p>
                Modifiez les informations de votre offre professionnelle.
            </p>
        </div>
    </section>
    <section class="section creation-offre-section">
        <div class="container">
            <div class="creation-offre-introduction">
                <h2>Informations sur l'offre</h2>
                <p>
                    Modifiez les champs que vous souhaitez mettre à jour.
                    Les champs marqués d'un astérisque sont obligatoires.
                </p>
            </div>
            <?php if ($erreur != '') { ?>
                <div class="form-status form-status--error" role="alert">
                    <?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php } ?>
            <form action="../../src/traitement/ModifierOffreTraitement_Tom.php" method="POST" class="form-creation-offre">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['modifier_offre_csrf'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="id_offre" value="<?php echo (int) $idOffre; ?>">
                <div class="form-group">
                    <label for="titre">Titre de l'offre *</label>
                    <input type="text" id="titre" name="titre" maxlength="200" required value="<?php echo valeurModification('titre', $offre, $anciennesValeurs); ?>">
                </div>
                <div class="form-group">
                    <label for="description">Description de l'offre *</label>
                    <textarea id="description" name="description" rows="5" required><?php echo valeurModification('description', $offre, $anciennesValeurs); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="missions">Missions proposées *</label>
                    <textarea id="missions" name="missions" rows="5" required><?php echo valeurModification('missions', $offre, $anciennesValeurs); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="salaire">Salaire ou rémunération (facultatif)</label>
                    <input type="number" id="salaire" name="salaire" min="0" step="0.01" value="<?php echo valeurModification('salaire', $offre, $anciennesValeurs); ?>">
                </div>
                <div class="form-group">
                    <label for="type">Type de contrat *</label>
                    <select id="type" name="type" required>
                        <option value="">Choisir un type de contrat</option>
                        <option value="stage" <?php echo valeurModification('type', $offre, $anciennesValeurs) == 'stage' ? 'selected' : ''; ?>>
                            Stage
                        </option>
                        <option value="alternance" <?php echo valeurModification('type', $offre, $anciennesValeurs) == 'alternance' ? 'selected' : ''; ?>>
                            Alternance
                        </option>
                        <option value="cdd" <?php echo valeurModification('type', $offre, $anciennesValeurs) == 'cdd' ? 'selected' : ''; ?>>
                            CDD
                        </option>
                        <option value="cdi" <?php echo valeurModification('type', $offre, $anciennesValeurs) == 'cdi' ? 'selected' : ''; ?>>
                            CDI
                        </option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="profil_cible">Profil recherché *</label>
                    <select id="profil_cible" name="profil_cible" required>
                        <option value="">Choisir un profil</option>
                        <option value="tous" <?php echo valeurModification('profil_cible', $offre, $anciennesValeurs) == 'tous' ? 'selected' : ''; ?>>
                            Tous les profils
                        </option>
                        <option value="etudiant" <?php echo valeurModification('profil_cible', $offre, $anciennesValeurs) == 'etudiant' ? 'selected' : ''; ?>>
                            Étudiant
                        </option>
                        <option value="alumni" <?php echo valeurModification('profil_cible', $offre, $anciennesValeurs) == 'alumni' ? 'selected' : ''; ?>>
                            Alumni
                        </option>
                    </select>
                </div>

                <p class="form-required-info">* Champs obligatoires</p>
                <div class="form-actions">
                    <a href="mes_offres_Tom.php" class="btn btn-outline">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </section>
</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="creation_offre_Tom.js"></script>

</body>
</html>