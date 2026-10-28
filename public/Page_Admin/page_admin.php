<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';

// Page réservée aux gestionnaires
if (!estConnecte() || $_SESSION['role'] != 'gestionnaire') {
    header('Location: ../connexion/connexion.php');
    exit;
}

$repository = new UtilisateurRepository_malik();
$utilisateurs = $repository->getAllUtilisateurs();

// Si on a cliqué sur "Modifier" (?modifier=ID), on charge l'utilisateur à modifier
$u = null;
if (isset($_GET['modifier'])) {
    $u = $repository->getUtilisateurParId((int) $_GET['modifier']);
}

// Message laissé par src/traitement/ActionAdmin_malik.php
$erreur = "";
if (!empty($_SESSION['admin_erreur'])) {
    $erreur = $_SESSION['admin_erreur'];
    $_SESSION['admin_erreur'] = "";
}

// Rôles proposés dans les listes déroulantes
$roles = array('etudiant', 'alumni', 'partenaire', 'professeur', 'gestionnaire');

// Libellés affichés pour chaque statut
$statuts = array(
        'valide' => 'Activé',
        'refuse' => 'Désactivé',
        'en_attente' => 'En attente'
);

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'admin';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="page_admin.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Administration</p>
            <h1>Gestion des utilisateurs</h1>
            <p>Validez les inscriptions, modifiez ou supprimez les comptes de la plateforme.</p>
        </div>
    </section>

    <section class="section admin-section">
        <div class="container">

            <?php if ($erreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo $erreur; ?></p>
            <?php } ?>

            <!-- Formulaire : Ajouter, ou Modifier si on a cliqué sur Modifier -->
            <div class="admin-card">
                <?php if ($u != null) { ?>
                    <h2>Modifier un utilisateur</h2>
                    <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="admin-form">
                        <input type="hidden" name="action" value="modifier">
                        <input type="hidden" name="id_utilisateur" value="<?php echo $u->getIdUtilisateur(); ?>">

                        <input type="text" name="nom" value="<?php echo htmlspecialchars($u->getNom()); ?>" required>
                        <input type="text" name="prenom" value="<?php echo htmlspecialchars($u->getPrenom()); ?>" required>
                        <input type="email" name="mail" value="<?php echo htmlspecialchars($u->getEmail()); ?>" required>
                        <input type="text" name="telephone" value="<?php if ($u->getTelephone() != null) { echo htmlspecialchars($u->getTelephone()); } ?>">

                        <select name="role">
                            <?php foreach ($roles as $role) { ?>
                                <option value="<?php echo $role; ?>" <?php if ($u->getRole() == $role) { echo 'selected'; } ?>><?php echo $role; ?></option>
                            <?php } ?>
                        </select>

                        <select name="statut">
                            <?php foreach ($statuts as $statut => $libelle) { ?>
                                <option value="<?php echo $statut; ?>" <?php if ($u->getStatutValidation() == $statut) { echo 'selected'; } ?>><?php echo $libelle; ?></option>
                            <?php } ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                        <a href="page_admin.php" class="btn btn-outline btn-sm">Annuler</a>
                    </form>
                <?php } else { ?>
                    <h2>Ajouter un utilisateur</h2>
                    <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="admin-form">
                        <input type="hidden" name="action" value="ajouter">
                        <input type="hidden" name="id_utilisateur" value="0">

                        <input type="text" name="nom" placeholder="Nom" required>
                        <input type="text" name="prenom" placeholder="Prénom" required>
                        <input type="email" name="mail" placeholder="Email" required>
                        <input type="text" name="telephone" placeholder="Téléphone">
                        <input type="password" name="mdp" placeholder="Mot de passe (8 car. min.)" minlength="8" required>

                        <select name="role">
                            <?php foreach ($roles as $role) { ?>
                                <option value="<?php echo $role; ?>"><?php echo $role; ?></option>
                            <?php } ?>
                        </select>

                        <button type="submit" class="btn btn-primary btn-sm">Ajouter</button>
                    </form>
                <?php } ?>
            </div>

            <!-- Liste des utilisateurs -->
            <div class="admin-card">
                <h2>Liste des utilisateurs</h2>
                <div class="table-wrapper">
                    <table class="admin-table">
                        <thead>
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
                        </thead>
                        <tbody>
                            <?php foreach ($utilisateurs as $ligne) { ?>
                                <tr>
                                    <td><?php echo $ligne['id_utilisateur']; ?></td>
                                    <td><?php echo htmlspecialchars($ligne['nom_utilisateur']); ?></td>
                                    <td><?php echo htmlspecialchars($ligne['prenom_utilisateur']); ?></td>
                                    <td><?php echo htmlspecialchars($ligne['mail']); ?></td>
                                    <td><?php if ($ligne['telephone'] != null) { echo htmlspecialchars($ligne['telephone']); } ?></td>
                                    <td><?php echo $ligne['role']; ?></td>
                                    <td><span class="statut <?php echo $ligne['statut_validation']; ?>"><?php echo $statuts[$ligne['statut_validation']]; ?></span></td>
                                    <td class="actions">
                                        <a href="page_admin.php?modifier=<?php echo $ligne['id_utilisateur']; ?>" class="btn btn-outline btn-sm">Modifier</a>

                                        <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="inline">
                                            <input type="hidden" name="id_utilisateur" value="<?php echo $ligne['id_utilisateur']; ?>">

                                            <?php if ($ligne['statut_validation'] == 'valide') { ?>
                                                <button type="submit" name="action" value="desactiver" class="btn btn-outline btn-sm">Désactiver</button>
                                            <?php } else { ?>
                                                <button type="submit" name="action" value="activer" class="btn btn-primary btn-sm">Activer</button>
                                            <?php } ?>

                                            <button type="submit" name="action" value="supprimer" class="btn btn-danger btn-sm">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_admin.js"></script>
</body>
</html>
