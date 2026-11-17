<?php

// Page de gestion d'un événement, réservée à ses organisateurs :
//   - modifier la fiche de l'événement
//   - voir la liste des inscrits et refuser une inscription
// Les formulaires sont envoyés à src/traitement/GestionEvenementTraitement_malik.php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/evenement_anais.php';
require_once '../../src/repository/EvenementRepository_ahmed.php';

// 1) Il faut être connecté
if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

// 2) On récupère l'id de l'événement dans l'adresse (gestion_evenement_malik.php?id=3)
$idEvenement = 0;
if (isset($_GET['id'])) {
    $idEvenement = (int) $_GET['id'];
}

$evenementRepository = new EvenementRepository_ahmed();
$evenement = $evenementRepository->getEvenement($idEvenement);

// 3) L'événement doit exister ET l'utilisateur doit en être organisateur
if ($evenement == null || !$evenementRepository->estOrganisateur($idEvenement, $_SESSION['id_utilisateur'])) {
    $_SESSION['message_profil'] = "Vous ne pouvez pas gérer cet événement.";
    header('Location: ../profil/profil_utilisateur.php');
    exit;
}

// 4) Liste des inscrits
$inscrits = $evenementRepository->getInscrits($idEvenement);

// 5) Messages laissés par le traitement
$message = "";
if (!empty($_SESSION['gestion_message'])) {
    $message = $_SESSION['gestion_message'];
    $_SESSION['gestion_message'] = "";
}
$erreur = "";
if (!empty($_SESSION['gestion_erreur'])) {
    $erreur = $_SESSION['gestion_erreur'];
    $_SESSION['gestion_erreur'] = "";
}

$pageActive = 'evenements';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer un événement — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="gestion_evenement_malik.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../profil/profil_utilisateur.php">Mon profil</a> / Gérer un événement</p>
            <h1><?php echo htmlspecialchars($evenement->getTitre()); ?></h1>
            <p>Modifiez la fiche de l'événement et gérez les inscriptions.</p>
        </div>
    </section>

    <section class="section gestion-section">
        <div class="container">

            <?php if ($message != "") { ?>
                <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($message); ?></p>
            <?php } ?>
            <?php if ($erreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($erreur); ?></p>
            <?php } ?>

            <!-- ===== PARTIE 1 : MODIFIER LA FICHE ===== -->
            <div class="gestion-card">
                <h2>Modifier la fiche</h2>

                <form method="post" action="../../src/traitement/GestionEvenementTraitement_malik.php">
                    <input type="hidden" name="id_evenement" value="<?php echo $evenement->getIdEvenement(); ?>">
                    <input type="hidden" name="action" value="modifier">

                    <div class="form-group">
                        <label for="type_evenement">Type d'événement *</label>
                        <input type="text" id="type_evenement" name="type_evenement" required value="<?php echo htmlspecialchars($evenement->getTypeEvenement()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="titre">Titre *</label>
                        <input type="text" id="titre" name="titre" required value="<?php echo htmlspecialchars($evenement->getTitre()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required><?php echo htmlspecialchars($evenement->getDescription()); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="lieu">Lieu / adresse *</label>
                        <input type="text" id="lieu" name="lieu" required value="<?php echo htmlspecialchars($evenement->getLieu()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="elements_requis">Éléments requis</label>
                        <input type="text" id="elements_requis" name="elements_requis" placeholder="Facultatif" value="<?php echo htmlspecialchars((string) $evenement->getElementsRequis()); ?>">
                    </div>

                    <div class="form-group">
                        <label for="nb_places">Nombre de places</label>
                        <input type="number" id="nb_places" name="nb_places" min="1" placeholder="Facultatif" value="<?php echo htmlspecialchars((string) $evenement->getNbPlaces()); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </form>
            </div>

            <!-- ===== PARTIE 2 : LISTE DES INSCRITS ===== -->
            <div class="gestion-card">
                <h2>Inscrits (<?php echo count($inscrits); ?>)</h2>

                <?php if (count($inscrits) == 0) { ?>
                    <p class="gestion-vide">Personne n'est encore inscrit à cet événement.</p>
                <?php } else { ?>
                    <div class="table-wrapper">
                        <table class="gestion-table">
                            <thead>
                                <tr>
                                    <th scope="col">Nom</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Rôle</th>
                                    <th scope="col">Inscrit le</th>
                                    <th scope="col">Statut</th>
                                    <th scope="col">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inscrits as $inscrit) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($inscrit['prenom_utilisateur'] . ' ' . $inscrit['nom_utilisateur']); ?></td>
                                        <td><?php echo htmlspecialchars($inscrit['mail']); ?></td>
                                        <td><?php echo htmlspecialchars($inscrit['role']); ?></td>
                                        <td><?php echo $inscrit['date_inscription_fr']; ?></td>
                                        <td>
                                            <?php if ($inscrit['statut_inscription'] == 'acceptee') { ?>
                                                Acceptée
                                            <?php } else { ?>
                                                Refusée
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($inscrit['statut_inscription'] == 'acceptee') { ?>
                                                <!-- class="form-refuser" : le JS demande une confirmation avant l'envoi -->
                                                <form method="post" action="../../src/traitement/GestionEvenementTraitement_malik.php" class="form-refuser">
                                                    <input type="hidden" name="id_evenement" value="<?php echo $evenement->getIdEvenement(); ?>">
                                                    <input type="hidden" name="id_utilisateur" value="<?php echo $inscrit['id_utilisateur']; ?>">
                                                    <input type="hidden" name="action" value="refuser_inscrit">
                                                    <button type="submit" class="btn btn-danger btn-sm">Refuser</button>
                                                </form>
                                            <?php } else { ?>
                                                —
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

            <a href="../profil/profil_utilisateur.php" class="btn btn-outline">Retour à mon profil</a>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="gestion_evenement_malik.js"></script>
</body>
</html>
