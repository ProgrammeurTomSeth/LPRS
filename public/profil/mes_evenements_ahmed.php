<?php

// Page « Événements » du profil : événements créés par l'utilisateur et événements auxquels il est inscrit.
// (affichée depuis les cartes d'action de profil_utilisateur.php)

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/EvenementRepository_ahmed.php';

// Page réservée aux utilisateurs connectés
if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

// Compte supprimé entre-temps : on ferme la session
if ($utilisateur == null) {
    header('Location: ../../src/traitement/DeconnexionTraitement_ahmed.php');
    exit;
}

$statutsEvenement = array(
    'brouillon' => 'Brouillon',
    'en_attente' => 'En attente',
    'publie' => 'Publié',
    'refuse' => 'Refusé'
);
$statutsInscription = array(
    'acceptee' => 'Acceptée',
    'refusee' => 'Refusée'
);

$evenementRepository = new EvenementRepository_ahmed();
$evenementsOrganises = $evenementRepository->getEvenementsOrganises($utilisateur->getIdUtilisateur());
$evenementsInscrits = $evenementRepository->getEvenementsInscrits($utilisateur->getIdUtilisateur());

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'profil';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes événements — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <!-- ?v=date du fichier : le navigateur recharge le CSS dès qu'il change (évite l'ancien style en cache) -->
    <link rel="stylesheet" href="mes_evenements_ahmed.css?v=<?php echo filemtime(__DIR__ . '/mes_evenements_ahmed.css'); ?>">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / <a href="profil_utilisateur.php">Mon profil</a> / Événements</p>
            <h1>Mes événements</h1>
            <p>Les événements que vous avez créés et ceux auxquels vous êtes inscrit.</p>
        </div>
    </section>

    <!-- ===== ÉVÉNEMENTS ===== -->
    <section class="section evenements-section">
        <div class="container evenements-page">

            <div class="evenements-barre">
                <a href="profil_utilisateur.php" class="btn btn-outline">Retour au profil</a>
                <?php if ($utilisateur->getRole() != 'gestionnaire') { ?>
                    <a href="../evenement/creation_evenement_Tom.php" class="btn btn-primary">Créer un événement</a>
                <?php } ?>
            </div>

                <!-- ===== ÉVÉNEMENTS CRÉÉS ===== -->
                <div class="profil-card evenements-card">
                    <h2>Mes événements créés</h2>
                    <?php if (count($evenementsOrganises) == 0) { ?>
                        <p class="evenements-vide">Vous n'avez créé aucun événement pour le moment.</p>
                    <?php } else { ?>
                        <div class="table-wrapper">
                            <table class="evenements-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Titre</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Lieu</th>
                                        <th scope="col">Places</th>
                                        <th scope="col">Statut</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evenementsOrganises as $evenement) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($evenement['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($evenement['type_evenement']); ?></td>
                                            <td><?php echo $evenement['date_fr']; ?></td>
                                            <td><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                                            <td><?php if ($evenement['nb_places'] != null) { echo $evenement['nb_places']; } else { echo '—'; } ?></td>
                                            <td><span class="badge"><?php
                                                // Tant qu'un professeur n'a pas répondu, on l'indique à la place de "En attente"
                                                if ($evenement['statut'] == 'en_attente' && $evenement['nb_reponses_attendues'] > 0) {
                                                    echo 'En attente du professeur';
                                                } elseif (isset($statutsEvenement[$evenement['statut']])) {
                                                    echo $statutsEvenement[$evenement['statut']];
                                                } else {
                                                    // Statut absent du tableau (ex : base pas à jour) : pas de warning
                                                    echo 'Inconnu';
                                                }
                                            ?></span></td>
                                            <!-- Lien vers la page de gestion de cet événement -->
                                            <td><a href="../evenement/gestion_evenement_malik.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn btn-outline btn-sm">Gérer</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>

                <!-- ===== ÉVÉNEMENTS INSCRITS ===== -->
                <div class="profil-card evenements-card">
                    <h2>Mes inscriptions aux événements</h2>
                    <?php if (count($evenementsInscrits) == 0) { ?>
                        <p class="evenements-vide">Vous n'êtes inscrit à aucun événement pour le moment.</p>
                    <?php } else { ?>
                        <div class="table-wrapper">
                            <table class="evenements-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Titre</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Lieu</th>
                                        <th scope="col">Inscrit le</th>
                                        <th scope="col">Inscription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evenementsInscrits as $evenement) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($evenement['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($evenement['type_evenement']); ?></td>
                                            <td><?php echo $evenement['date_fr']; ?></td>
                                            <td><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                                            <td><?php echo $evenement['date_inscription_fr']; ?></td>
                                            <td><span class="badge"><?php echo $statutsInscription[$evenement['statut_inscription']]; ?></span></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="mes_evenements_ahmed.js"></script>
</body>
</html>
