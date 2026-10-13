<?php
require_once __DIR__ . '/../../src/traitement/ActionAdmin_malik.php';

$admin = new ActionAdmin_malik();

// Traitement des boutons (formulaires en POST), puis retour sur la page
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id_utilisateur'] ?? 0);

    if ($action === 'ajouter') {
        $admin->ajouter(trim($_POST['nom']), trim($_POST['prenom']), trim($_POST['mail']), $_POST['mdp'], trim($_POST['telephone']), $_POST['role'], null);
    } elseif ($action === 'modifier') {
        $admin->modifier($id, trim($_POST['nom']), trim($_POST['prenom']), trim($_POST['mail']), trim($_POST['telephone']), $_POST['role']);
    } elseif ($action === 'valider') {
        $admin->valider($id);
    } elseif ($action === 'refuser') {
        $admin->refuser($id);
    } elseif ($action === 'supprimer') {
        $admin->supprimer($id);
    }
    header('Location: page_admin.php');
    exit;
}

function e($valeur){
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

$utilisateurs = $admin->getTous();
$enAttente = $admin->compterEnAttente();
// Si ?modifier=ID : le formulaire passe en mode modification
$aModifier = isset($_GET['modifier']) ? $admin->getUn((int) $_GET['modifier']) : null;
$roles = ['etudiant', 'alumni', 'partenaire', 'professeur', 'gestionnaire'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration - Utilisateurs</title>
    <link rel="stylesheet" href="page_admin.css">
</head>
<body>

<h1>Gestion des utilisateurs</h1>
<p><?= $enAttente ?> inscription(s) en attente de validation.</p>

<!-- Formulaire : ajout, ou modification si ?modifier=ID -->
<h2><?= $aModifier ? 'Modifier un utilisateur' : 'Ajouter un utilisateur' ?></h2>
<form method="post" class="formulaire">
    <input type="hidden" name="action" value="<?= $aModifier ? 'modifier' : 'ajouter' ?>">
    <?php if ($aModifier): ?>
        <input type="hidden" name="id_utilisateur" value="<?= e($aModifier->getIdUtilisateur()) ?>">
    <?php endif; ?>

    <input type="text" name="nom" placeholder="Nom" value="<?= $aModifier ? e($aModifier->getNom()) : '' ?>" required>
    <input type="text" name="prenom" placeholder="Prénom" value="<?= $aModifier ? e($aModifier->getPrenom()) : '' ?>" required>
    <input type="email" name="mail" placeholder="Email" value="<?= $aModifier ? e($aModifier->getEmail()) : '' ?>" required>
    <input type="text" name="telephone" placeholder="Téléphone" value="<?= $aModifier ? e($aModifier->getTelephone()) : '' ?>">

    <?php if (!$aModifier): ?>
        <input type="password" name="mdp" placeholder="Mot de passe (8 car. min.)" minlength="8" required>
    <?php endif; ?>

    <select name="role">
        <?php foreach ($roles as $r): ?>
            <option value="<?= $r ?>" <?= ($aModifier && $aModifier->getRole() === $r) ? 'selected' : '' ?>><?= $r ?></option>
        <?php endforeach; ?>
    </select>

    <button type="submit"><?= $aModifier ? 'Enregistrer' : 'Ajouter' ?></button>
    <?php if ($aModifier): ?><a href="page_admin.php">Annuler</a><?php endif; ?>
</form>

<!-- Liste des utilisateurs -->
<h2>Liste des utilisateurs</h2>
<table>
    <tr>
        <th>ID</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Téléphone</th>
        <th>Rôle</th>
        <th>Statut</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($utilisateurs as $u): ?>
        <tr>
            <td><?= e($u->getIdUtilisateur()) ?></td>
            <td><?= e($u->getNom()) ?></td>
            <td><?= e($u->getPrenom()) ?></td>
            <td><?= e($u->getEmail()) ?></td>
            <td><?= e($u->getTelephone()) ?></td>
            <td><?= e($u->getRole()) ?></td>
            <td class="<?= e($u->getStatutValidation()) ?>"><?= e($u->getStatutValidation()) ?></td>
            <td>
                <form method="post" class="inline">
                    <input type="hidden" name="id_utilisateur" value="<?= e($u->getIdUtilisateur()) ?>">

                    <?php if ($u->getStatutValidation() !== 'valide'): ?>
                        <button type="submit" name="action" value="valider">Valider</button>
                    <?php endif; ?>
                    <?php if ($u->getStatutValidation() === 'en_attente'): ?>
                        <button type="submit" name="action" value="refuser">Refuser</button>
                    <?php endif; ?>
                </form>

                <a href="page_admin.php?modifier=<?= e($u->getIdUtilisateur()) ?>">Modifier</a>

                <form method="post" class="inline" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                    <input type="hidden" name="id_utilisateur" value="<?= e($u->getIdUtilisateur()) ?>">
                    <button type="submit" name="action" value="supprimer">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>