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
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
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
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($erreur); ?></p>
            <?php } ?>

            <!-- Liste des utilisateurs -->
            <div class="admin-card admin-card--liste">
                <div class="liste-entete">
                    <div>
                        <h2>Liste des utilisateurs</h2>
                        <p class="liste-compteur" id="compteur"><?php echo count($utilisateurs); ?> utilisateurs</p>
                    </div>
                    <!-- Ouvre la fenêtre d'ajout (voir script_admin.js) -->
                    <button type="button" id="ouvrir-ajout" class="btn btn-primary btn-sm">+ Ajouter un utilisateur</button>
                </div>

                <!-- Barre de recherche et filtre par rôle (voir script_admin.js) -->
                <div class="liste-outils">
                    <input type="search" id="recherche" class="outil-recherche" placeholder="Rechercher un utilisateur" aria-label="Rechercher un utilisateur">
                    <select id="filtre-role" class="outil-filtre" aria-label="Filtrer par rôle">
                        <option value="">Tous les rôles</option>
                        <?php foreach ($roles as $role) { ?>
                            <option value="<?php echo $role; ?>"><?php echo $role; ?></option>
                        <?php } ?>
                    </select>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $ligne) { ?>
                            <?php
                            // Initiales pour la pastille, et texte utilisé par la recherche
                            $initiales = mb_strtoupper(mb_substr($ligne['prenom_utilisateur'], 0, 1) . mb_substr($ligne['nom_utilisateur'], 0, 1));
                            $texteRecherche = mb_strtolower($ligne['prenom_utilisateur'] . ' ' . $ligne['nom_utilisateur'] . ' ' . $ligne['mail']);
                            ?>
                            <tr data-role="<?php echo htmlspecialchars($ligne['role']); ?>" data-recherche="<?php echo htmlspecialchars($texteRecherche); ?>">
                                <td data-label="Utilisateur">
                                    <div class="utilisateur">
                                        <span class="avatar" aria-hidden="true"><?php echo htmlspecialchars($initiales); ?></span>
                                        <span class="utilisateur-texte">
                                            <span class="utilisateur-nom"><?php echo htmlspecialchars($ligne['prenom_utilisateur']); ?> <?php echo htmlspecialchars(mb_strtoupper($ligne['nom_utilisateur'])); ?></span>
                                            <span class="utilisateur-mail"><?php echo htmlspecialchars($ligne['mail']); ?></span>
                                        </span>
                                    </div>
                                </td>
                                <td data-label="Téléphone"><?php if ($ligne['telephone'] != null) { echo htmlspecialchars($ligne['telephone']); } ?></td>
                                <td data-label="Rôle"><?php echo htmlspecialchars($ligne['role']); ?></td>
                                <td data-label="Statut"><span class="statut <?php echo $ligne['statut_validation']; ?>"><?php echo $statuts[$ligne['statut_validation']]; ?></span></td>
                                <td data-label="Actions">
                                    <div class="actions-boutons">
                                        <a href="page_admin.php?modifier=<?php echo $ligne['id_utilisateur']; ?>" class="btn btn-outline btn-sm">Modifier</a>

                                        <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="inline">
                                            <input type="hidden" name="id_utilisateur" value="<?php echo $ligne['id_utilisateur']; ?>">

                                            <?php if ($ligne['statut_validation'] == 'valide') { ?>
                                                <button type="submit" name="action" value="desactiver" class="btn btn-outline btn-sm">Désactiver</button>
                                            <?php } else { ?>
                                                <button type="submit" name="action" value="activer" class="btn btn-primary btn-sm">Activer</button>
                                            <?php } ?>

                                            <!-- data-suppression : script_admin.js ouvre la fenêtre d'avertissement avant de supprimer -->
                                            <button type="submit" name="action" value="supprimer" class="btn btn-danger btn-sm" data-suppression data-id="<?php echo $ligne['id_utilisateur']; ?>" data-nom="<?php echo htmlspecialchars($ligne['prenom_utilisateur'] . ' ' . mb_strtoupper($ligne['nom_utilisateur'])); ?>">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <p class="liste-vide" id="liste-vide" hidden>Aucun utilisateur ne correspond à votre recherche.</p>
            </div>

        </div>
    </section>

</main>

    <!-- ===== FENÊTRE : AJOUTER UN UTILISATEUR ===== -->
    <dialog id="dialogue-ajout" class="modale" aria-labelledby="titre-ajout">
        <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="modale-form">
            <input type="hidden" name="action" value="ajouter">
            <input type="hidden" name="id_utilisateur" value="0">

            <div class="modale-entete">
                <h2 id="titre-ajout">Ajouter un utilisateur</h2>
                <button type="button" class="modale-fermer" data-fermer aria-label="Fermer">&times;</button>
            </div>

            <div class="modale-grille">
                <label class="champ">Prénom
                    <input type="text" name="prenom" required autofocus>
                </label>
                <label class="champ">Nom
                    <input type="text" name="nom" required>
                </label>
                <label class="champ champ--large">Email
                    <input type="email" name="mail" required>
                </label>
                <label class="champ">Téléphone
                    <input type="text" name="telephone">
                </label>
                <label class="champ">Rôle
                    <select name="role">
                        <?php foreach ($roles as $role) { ?>
                            <option value="<?php echo $role; ?>"><?php echo $role; ?></option>
                        <?php } ?>
                    </select>
                </label>
                <label class="champ champ--large">Mot de passe provisoire
                    <input type="password" name="mdp" minlength="8" required autocomplete="new-password">
                    <span class="champ-aide">8 caractères minimum.</span>
                </label>
            </div>

            <div class="modale-pied">
                <button type="button" class="btn btn-outline" data-fermer>Annuler</button>
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </dialog>

    <?php if ($u != null) { ?>
        <!-- ===== FENÊTRE : MODIFIER UN UTILISATEUR (ouverte automatiquement par script_admin.js) ===== -->
        <dialog id="dialogue-modifier" class="modale" aria-labelledby="titre-modifier">
            <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="modale-form">
                <input type="hidden" name="action" value="modifier">
                <input type="hidden" name="id_utilisateur" value="<?php echo $u->getIdUtilisateur(); ?>">

                <div class="modale-entete">
                    <h2 id="titre-modifier">Modifier un utilisateur</h2>
                    <button type="button" class="modale-fermer" data-fermer aria-label="Fermer">&times;</button>
                </div>

                <div class="modale-grille">
                    <label class="champ">Prénom
                        <input type="text" name="prenom" value="<?php echo htmlspecialchars($u->getPrenom()); ?>" required autofocus>
                    </label>
                    <label class="champ">Nom
                        <input type="text" name="nom" value="<?php echo htmlspecialchars($u->getNom()); ?>" required>
                    </label>
                    <label class="champ champ--large">Email
                        <input type="email" name="mail" value="<?php echo htmlspecialchars($u->getEmail()); ?>" required>
                    </label>
                    <label class="champ champ--large">Téléphone
                        <input type="text" name="telephone" value="<?php if ($u->getTelephone() != null) { echo htmlspecialchars($u->getTelephone()); } ?>">
                    </label>
                    <label class="champ">Rôle
                        <select name="role">
                            <?php foreach ($roles as $role) { ?>
                                <option value="<?php echo $role; ?>" <?php if ($u->getRole() == $role) { echo 'selected'; } ?>><?php echo $role; ?></option>
                            <?php } ?>
                        </select>
                    </label>
                    <label class="champ">Statut
                        <select name="statut">
                            <?php foreach ($statuts as $statut => $libelle) { ?>
                                <option value="<?php echo $statut; ?>" <?php if ($u->getStatutValidation() == $statut) { echo 'selected'; } ?>><?php echo $libelle; ?></option>
                            <?php } ?>
                        </select>
                    </label>
                </div>

                <div class="modale-pied">
                    <button type="button" class="btn btn-outline" data-fermer>Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </dialog>
    <?php } ?>

    <!-- ===== FENÊTRE : AVERTISSEMENT AVANT SUPPRESSION D'UN UTILISATEUR ===== -->
    <dialog id="dialogue-suppression" class="modale modale--petite modale--suppression" aria-labelledby="titre-suppression">
        <form method="post" action="../../src/traitement/ActionAdmin_malik.php" class="modale-form">
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="id_utilisateur" id="suppression-id" value="0">

            <div class="modale-entete">
                <h2 id="titre-suppression">Supprimer cet utilisateur ?</h2>
                <button type="button" class="modale-fermer" data-fermer aria-label="Fermer">&times;</button>
            </div>

            <p class="alerte-suppression">Vous allez supprimer définitivement le compte de <strong id="suppression-nom"></strong>. Cette action est <strong>irréversible</strong>.</p>
            <p>Seront aussi supprimés : ses candidatures, ses inscriptions aux événements, sa participation comme organisateur et ses signalements.</p>
            <p class="champ-aide">Si cet utilisateur est l'auteur d'offres ou de messages du forum, la suppression sera refusée. Vous pouvez alors simplement désactiver son compte.</p>

            <div class="modale-pied">
                <button type="button" class="btn btn-outline" data-fermer autofocus>Annuler</button>
                <button type="submit" class="btn btn-danger">Supprimer définitivement</button>
            </div>
        </form>
    </dialog>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_admin.js"></script>
</body>
</html>
