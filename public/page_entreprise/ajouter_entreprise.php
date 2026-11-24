
<?php

session_start();

require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Entreprise_Tom.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';

$message = '';

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../connexion/connexion.php");
    exit;
}

try {

    // Récupérer la connexion à la BDD
    $bdd = new Bdd();
    $connexion = $bdd->getConnexionBdd();

    // Vérifier le rôle et le statut de validation
    $requete = $connexion->prepare("
        SELECT role, statut_validation
        FROM utilisateur
        WHERE id_utilisateur = :id
    ");

    $requete->execute([
            'id' => $_SESSION['id_utilisateur']
    ]);

    $utilisateur = $requete->fetch(PDO::FETCH_ASSOC);

    // Vérifier que l'utilisateur existe
    if (!$utilisateur) {
        session_destroy();
        header("Location: ../connexion/connexion.php");
        exit;
    }

    // Seuls les alumni et partenaires validés sont autorisés
    if (
            !in_array($utilisateur['role'], ['alumni', 'partenaire'], true)
            || $utilisateur['statut_validation'] !== 'valide'
    ) {
        header("Location: ../profil/profil_utilisateur.php");
        exit;
    }

} catch (PDOException $e) {

    error_log($e->getMessage());
    $message = "Une erreur est survenue lors de la vérification de votre compte.";
}


// TRAITEMENT DU FORMULAIRE
if (
        $_SERVER['REQUEST_METHOD'] === 'POST'
        && empty($message)
) {

    $nom = trim($_POST['nom'] ?? '');
    $activite = trim($_POST['activite'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $siteWeb = trim($_POST['siteWeb'] ?? '');

    // Vérifier les champs
    if (
            empty($nom)
            || empty($activite)
            || empty($adresse)
            || empty($siteWeb)
    ) {

        $message = "Veuillez remplir tous les champs.";

    } elseif (!filter_var($siteWeb, FILTER_VALIDATE_URL)) {

        $message = "Veuillez saisir une adresse de site web valide.";

    } else {

        try {

            $repository = new EntrepriseRepository_Tom();

            $resultat = $repository->ajouterEntreprise(
                    $nom,
                    $activite,
                    $adresse,
                    $siteWeb
            );

            if ($resultat === true) {

                // Redirection après ajout réussi
                header("Location: ../profil/profil_utilisateur.php");
                exit;

            } else {

                $message = "Ce site web est déjà utilisé par une entreprise.";

            }

        } catch (PDOException $e) {

            error_log($e->getMessage());
            $message = "Erreur lors de l'enregistrement de l'entreprise.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ajouter une entreprise</title>

    <link rel="stylesheet" href="ajout_entreprise.css">
</head>

<body>

<h1>Ajouter une entreprise</h1>

<?php if (!empty($message)): ?>
    <p class="message">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </p>
<?php endif; ?>

<form method="POST" action="" id="formEntreprise">

    <label for="nom">Nom de l'entreprise</label>
    <input
            type="text"
            id="nom"
            name="nom"
            required
            value="<?= htmlspecialchars($_POST['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="activite">Activité</label>
    <input
            type="text"
            id="activite"
            name="activite"
            required
            value="<?= htmlspecialchars($_POST['activite'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="adresse">Adresse</label>
    <input
            type="text"
            id="adresse"
            name="adresse"
            required
            value="<?= htmlspecialchars($_POST['adresse'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <label for="siteWeb">Site web</label>
    <input
            type="url"
            id="siteWeb"
            name="siteWeb"
            placeholder="https://www.exemple.fr"
            required
            value="<?= htmlspecialchars($_POST['siteWeb'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <button type="submit">
        Ajouter l'entreprise
    </button>

</form>

<button
        type="button"
        onclick="history.back()"
        class="btn-retour"
>
    ← Retour
</button>

<script src="creation_entreprise.js"></script>

</body>
</html>
