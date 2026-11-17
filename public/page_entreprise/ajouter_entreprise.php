<?php

require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Entreprise_Tom.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $activite = trim($_POST['activite'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $siteWeb = trim($_POST['siteWeb'] ?? '');

    if ($nom && $activite && $adresse && $siteWeb) {

        $repository = new EntrepriseRepository_Tom();

        $resultat = $repository->ajouterEntreprise(
            $nom,
            $activite,
            $adresse,
            $siteWeb
        );

        if ($resultat) {
            header("Location: ../profil/profil_utilisateur.php");
            exit;
        } else {
            $message = "Site web déjà utilisé ou invalide.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une entreprise</title>
    <link rel="stylesheet" href="ajout_entreprise.css">
</head>

<body>

<h1>Ajouter une entreprise</h1>

    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Nom de l'entreprise</label>
        <input type="text" name="nom" required>

        <label>Activité</label>
        <input type="text" name="activite" required>

        <label>Adresse</label>
        <input type="text" name="adresse" required>

        <label>Site web</label>
        <input type="text" name="siteWeb" required>

        <button type="submit">Ajouter l'entreprise</button>

    </form>
<button onclick="history.back()" class="btn-retour">
    ← Retour
</button>
    <script src="creation_entreprise.js"></script>

</body>
</html>