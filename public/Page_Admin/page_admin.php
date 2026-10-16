<?php require_once __DIR__ . '/../../src/traitement/ActionAdmin_malik.php'; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Administration - Utilisateurs</title>
    <link rel="stylesheet" href="page_admin.css">
</head>
<body>

<h1>Gestion des utilisateurs</h1>

<!-- Formulaire : Ajouter, ou Modifier si on a cliqué sur Modifier -->
<?php if ($u): ?>
    <h2>Modifier un utilisateur</h2>
    <form method="post" class="formulaire">
        <input type="hidden" name="action" value="modifier">
        <input type="hidden" name="id_utilisateur" value="<?= $u->getIdUtilisateur() ?>">

        <input type="text" name="nom" value="<?= $u->getNom() ?>" required>
        <input type="text" name="prenom" value="<?= $u->getPrenom() ?>" required>
        <input type="email" name="mail" value="<?= $u->getEmail() ?>" required>
        <input type="text" name="telephone" value="<?= $u->getTelephone() ?>">

        <select name="role">
            <option value="etudiant" <?= $u->getRole() === 'etudiant' ? 'selected' : '' ?>>etudiant</option>
            <option value="alumni" <?= $u->getRole() === 'alumni' ? 'selected' : '' ?>>alumni</option>
            <option value="partenaire" <?= $u->getRole() === 'partenaire' ? 'selected' : '' ?>>partenaire</option>
            <option value="professeur" <?= $u->getRole() === 'professeur' ? 'selected' : '' ?>>professeur</option>
            <option value="gestionnaire" <?= $u->getRole() === 'gestionnaire' ? 'selected' : '' ?>>gestionnaire</option>
        </select>

        <select name="statut">
            <option value="valide" <?= $u->getStatutValidation() === 'valide' ? 'selected' : '' ?>>Activé</option>
            <option value="refuse" <?= $u->getStatutValidation() === 'refuse' ? 'selected' : '' ?>>Désactivé</option>
            <option value="en_attente" <?= $u->getStatutValidation() === 'en_attente' ? 'selected' : '' ?>>En attente</option>
        </select>

        <button type="submit">Enregistrer</button>
        <a href="page_admin.php">Annuler</a>
    </form>
<?php else: ?>
    <h2>Ajouter un utilisateur</h2>
    <form method="post" class="formulaire">
        <input type="hidden" name="action" value="ajouter">
        <input type="hidden" name="id_utilisateur" value="0">

        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" required>
        <input type="email" name="mail" placeholder="Email" required>
        <input type="text" name="telephone" placeholder="Téléphone">
        <input type="password" name="mdp" placeholder="Mot de passe (8 car. min.)" minlength="8" required>

        <select name="role">
            <option value="etudiant">etudiant</option>
            <option value="alumni">alumni</option>
            <option value="partenaire">partenaire</option>
            <option value="professeur">professeur</option>
            <option value="gestionnaire">gestionnaire</option>
        </select>

        <button type="submit">Ajouter</button>
    </form>
<?php endif; ?>

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
    <?php foreach ($utilisateurs as $ligne): ?>
        <tr>
            <td><?= $ligne->getIdUtilisateur() ?></td>
            <td><?= $ligne->getNom() ?></td>
            <td><?= $ligne->getPrenom() ?></td>
            <td><?= $ligne->getEmail() ?></td>
            <td><?= $ligne->getTelephone() ?></td>
            <td><?= $ligne->getRole() ?></td>
            <td class="<?= $ligne->getStatutValidation() ?>">
                <?php if ($ligne->getStatutValidation() === 'valide'): ?>
                    Activé
                <?php elseif ($ligne->getStatutValidation() === 'refuse'): ?>
                    Désactivé
                <?php else: ?>
                    En attente
                <?php endif; ?>
            </td>
            <td>
                <a href="page_admin.php?modifier=<?= $ligne->getIdUtilisateur() ?>">Modifier</a>

                <form method="post" class="inline">
                    <input type="hidden" name="id_utilisateur" value="<?= $ligne->getIdUtilisateur() ?>">
                    <button type="submit" name="action" value="supprimer">Supprimer</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

</body>
</html>