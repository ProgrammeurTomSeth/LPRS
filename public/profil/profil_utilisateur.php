<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/modele/formation_anais.php';
require_once '../../src/modele/Entreprise_Tom.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/Formation_ahmed.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';
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

$roles = array(
    'etudiant' => 'Étudiant',
    'alumni' => 'Ancien élève (alumni)',
    'partenaire' => 'Partenaire entreprise',
    'professeur' => 'Professeur',
    'gestionnaire' => 'Gestionnaire'
);
$statutsEvenement = array(
    'brouillon' => 'Brouillon',
    'publie' => 'Publié'
);
$statutsInscription = array(
    'acceptee' => 'Acceptée',
    'refusee' => 'Refusée'
);

// Lignes affichées sur le profil : tableau associatif libellé => valeur
// (on n'ajoute une ligne que si l'information est renseignée)
$infos = array();
$infos['Email'] = $utilisateur->getEmail();
if ($utilisateur->getTelephone() != null) {
    $infos['Téléphone'] = $utilisateur->getTelephone();
}
if ($utilisateur->getDateNaissance() != null) {
    $infos['Date de naissance'] = $utilisateur->getDateNaissance();
}
if ($utilisateur->getIdFormation() != null) {
    $formationRepository = new Formation_ahmed();
    $formation = $formationRepository->getFormation($utilisateur->getIdFormation());
    $infos['Formation'] = $formation->getNomFormation();
}
if ($utilisateur->getAnneePromo() != null) {
    $infos['Année de promotion'] = $utilisateur->getAnneePromo();
}
if ($utilisateur->getSpecialite() != null) {
    $infos['Spécialité'] = $utilisateur->getSpecialite();
}
if ($utilisateur->getIdEntreprise() != null) {
    $entrepriseRepository = new EntrepriseRepository_Tom();
    $entreprise = $entrepriseRepository->getEntreprise($utilisateur->getIdEntreprise());
    $infos['Entreprise'] = $entreprise->getNomEntreprise();
}
if ($utilisateur->getPoste() != null) {
    $infos['Poste'] = $utilisateur->getPoste();
}
$infos['Membre depuis'] = $utilisateur->getDateInscription();

// Initiales affichées dans le rond (mb_ : versions de substr/strtoupper qui gèrent les accents)
$initiales = mb_strtoupper(mb_substr($utilisateur->getPrenom(), 0, 1) . mb_substr($utilisateur->getNom(), 0, 1));

$evenementRepository = new EvenementRepository_ahmed();
$evenementsOrganises = $evenementRepository->getEvenementsOrganises($utilisateur->getIdUtilisateur());
$evenementsInscrits = $evenementRepository->getEvenementsInscrits($utilisateur->getIdUtilisateur());

// Message laissé par une autre page (ex : création d'un événement)
$messageSucces = "";
if (!empty($_SESSION['message_profil'])) {
    $messageSucces = $_SESSION['message_profil'];
    $_SESSION['message_profil'] = "";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="profil_utilisateur.css">
</head>
<body>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<!-- ===== HEADER / NAV ===== -->
<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="../page_acceuille/page_accueil.php">Programme</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Activités</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
                <li><a href="../page_entreprise/page_entreprise.php">Entreprise</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <?php afficherActionsHeader(); ?>
        </div>

        <button class="nav-toggle" id="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Mon profil</p>
            <h1>Mon profil</h1>
            <p>Retrouvez les informations de votre compte.</p>
        </div>
    </section>

    <!-- ===== PROFIL ===== -->
    <section class="section profil-section">
        <div class="container profil-layout">
            <div class="profil-card">

                <div class="profil-identite">
                    <div class="profil-avatar" aria-hidden="true">
                        <?php echo htmlspecialchars($initiales); ?>
                    </div>
                    <div>
                        <h2><?php echo htmlspecialchars($utilisateur->getPrenom() . ' ' . $utilisateur->getNom()); ?></h2>
                        <span class="badge"><?php echo $roles[$utilisateur->getRole()]; ?></span>
                    </div>
                </div>

                <dl class="profil-infos">
                    <?php foreach ($infos as $libelle => $valeur) { ?>
                        <div>
                            <dt><?php echo $libelle; ?></dt>
                            <dd><?php echo htmlspecialchars($valeur); ?></dd>
                        </div>
                    <?php } ?>
                </dl>

                <div class="profil-actions">
                    <?php if ($utilisateur->getRole() != 'gestionnaire') { ?>
                        <a href="../evenement/creation_evenement_Tom.php" class="btn btn-primary">Créer un événement</a>
                    <?php } ?>
                    <a href="../../src/traitement/DeconnexionTraitement_ahmed.php" class="btn btn-outline">Se déconnecter</a>
                </div>

            </div>

            <div class="profil-evenements">

                <?php if ($messageSucces != "") { ?>
                    <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($messageSucces); ?></p>
                <?php } ?>

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
                                            <td><span class="badge"><?php echo $statutsEvenement[$evenement['statut']]; ?></span></td>
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
        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 Lycée Robert Shuman. Tous droits réservés.</p>
    </div>
</footer>

<script src="script_profil.js"></script>
</body>
</html>
