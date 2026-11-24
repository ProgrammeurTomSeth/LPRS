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
    'en_attente' => 'En attente',
    'publie' => 'Publié',
    'refuse' => 'Refusé'
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
// CV : seulement pour les étudiants et les alumni
$aUnCv = ($utilisateur->getRole() == 'etudiant' || $utilisateur->getRole() == 'alumni');
if ($aUnCv) {
    if ($utilisateur->getCv() != null) {
        $infos['CV'] = 'Déposé';
    } else {
        $infos['CV'] = 'Aucun CV déposé';
    }
}
$infos['Membre depuis'] = $utilisateur->getDateInscription();

// Les partenaires ont une page profil simplifiée : leur profil, puis de gros boutons d'action
$estPartenaire = ($utilisateur->getRole() == 'partenaire');
// Les gestionnaires ont aussi une page simplifiée : leur profil et, à droite, tous les liens de gestion
$estGestionnaire = ($utilisateur->getRole() == 'gestionnaire');
// La gestion des offres demande un compte validé rattaché à une entreprise (même règle que mes_offres_Tom.php)
$peutGererOffres = ($utilisateur->getStatutValidation() == 'valide' && $utilisateur->getIdEntreprise() != null);

// Initiales affichées dans le rond (mb_ : versions de substr/strtoupper qui gèrent les accents)
$initiales = mb_strtoupper(mb_substr($utilisateur->getPrenom(), 0, 1) . mb_substr($utilisateur->getNom(), 0, 1));

$evenementRepository = new EvenementRepository_ahmed();
$evenementsOrganises = $evenementRepository->getEvenementsOrganises($utilisateur->getIdUtilisateur());
$evenementsInscrits = $evenementRepository->getEvenementsInscrits($utilisateur->getIdUtilisateur());
// Événements créés par un étudiant pour lesquels ce professeur doit répondre
$invitations = $evenementRepository->getInvitationsEnAttente($utilisateur->getIdUtilisateur());

// Message laissé par une autre page (ex : création d'un événement)
$messageSucces = "";
if (!empty($_SESSION['message_profil'])) {
    $messageSucces = $_SESSION['message_profil'];
    $_SESSION['message_profil'] = "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'profil';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <!-- ?v=date du fichier : le navigateur recharge le CSS dès qu'il change (évite l'ancien style en cache) -->
    <link rel="stylesheet" href="profil_utilisateur.css?v=<?php echo filemtime(__DIR__ . '/profil_utilisateur.css'); ?>">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

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
        <div class="container profil-layout<?php if ($estPartenaire) { echo ' profil-layout--partenaire'; } elseif ($estGestionnaire) { echo ' profil-layout--partenaire profil-layout--admin'; } ?>">
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

                <?php if ($aUnCv) { ?>
                    <!-- Dépôt ou remplacement du CV (traitement : src/traitement/ajout_cv_inscription.php) -->
                    <form class="profil-cv" method="post" action="../../src/traitement/ajout_cv_inscription.php" enctype="multipart/form-data">
                        <label for="cv">Mon CV (PDF, 2 Mo maximum)</label>
                        <input type="file" id="cv" name="cv" accept="application/pdf,.pdf" required>
                        <button type="submit" class="btn btn-outline btn-sm"><?php if ($utilisateur->getCv() != null) { echo 'Remplacer mon CV'; } else { echo 'Envoyer mon CV'; } ?></button>
                    </form>
                <?php } ?>

                <?php if ($estPartenaire || $estGestionnaire) { ?>
                    <!-- Partenaire et gestionnaire : seul le bouton de déconnexion reste ici, les actions sont dans les cartes à droite -->
                    <div class="profil-actions">
                        <a href="../../src/traitement/DeconnexionTraitement_ahmed.php" class="btn btn-outline">Se déconnecter</a>
                    </div>
                <?php } else { ?>
                <div class="profil-actions">
                    <?php if ($utilisateur->getRole() != 'gestionnaire') { ?>
                        <a href="../evenement/creation_evenement_Tom.php" class="btn btn-primary">Créer un événement</a>
                    <?php } ?>
                    <a href="../../src/traitement/DeconnexionTraitement_ahmed.php" class="btn btn-outline">Se déconnecter</a>
                </div>
                <div class="profil-actions">
                    <?php if (
                            $utilisateur->getRole() === 'alumni' ||
                            $utilisateur->getRole() === 'gestionnaire' ||
                            $utilisateur->getRole() === 'partenaire'
                    ) { ?>
                        <a href="../page_entreprise/ajouter_entreprise.php" class="btn btn-primary">Ajouter une entreprise</a>
                    <?php } ?>

                </div>
                <?php } ?>
            </div>

            <div class="profil-evenements">

                <?php if ($estGestionnaire) { ?>
                    <!-- ===== LIENS DE GESTION DU GESTIONNAIRE : cartes cliquables, regroupées ===== -->
                    <div class="profil-card actions-card">
                        <h2 class="actions-groupe-titre">Administration</h2>
                        <div class="actions-choix">
                            <a class="action-carte" href="../Page_Admin/page_admin.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"></circle><path d="M2 21c0-4 3-6 7-6s7 2 7 6"></path><path d="M17 4a4 4 0 0 1 0 8"></path><path d="M22 21c0-3-1-5-4-6"></path></svg>
                                <span class="action-titre">Utilisateurs</span>
                                <span class="action-description">Gérer les comptes, valider les inscriptions et modifier les profils.</span>
                            </a>
                            <a class="action-carte" href="../Page_Admin/page_admin_Evenement.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M9 16l2 2 4-4"></path></svg>
                                <span class="action-titre">Événements à valider</span>
                                <span class="action-description">Valider ou refuser les événements proposés, modifier ou supprimer.</span>
                            </a>
                            <a class="action-carte" href="../Page_Admin/moderation_forum.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path><path d="M12 8v4M12 15h.01"></path></svg>
                                <span class="action-titre">Modération du forum</span>
                                <span class="action-description">Traiter les messages signalés par les membres.</span>
                            </a>
                            <a class="action-carte" href="../Page_Admin/gestion_formations_Tom.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5z"></path><path d="M6 12v5c3 2 9 2 12 0v-5"></path></svg>
                                <span class="action-titre">Formations</span>
                                <span class="action-description">Gérer les formations du lycée.</span>
                            </a>
                        </div>
                        <h2 class="actions-groupe-titre">Mon espace</h2>
                        <div class="actions-choix">
                            <a class="action-carte" href="mes_evenements_ahmed.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18"></path></svg>
                                <span class="action-titre">Mes événements</span>
                                <span class="action-description">Consultez les événements que vous avez créés.</span>
                            </a>
                            <a class="action-carte" href="../page_entreprise/ajouter_entreprise.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"></rect><path d="M9 8h1M14 8h1M9 12h1M14 12h1M10 21v-4h4v4"></path></svg>
                                <span class="action-titre">Ajouter une entreprise</span>
                                <span class="action-description">Créez la fiche d'une entreprise partenaire.</span>
                            </a>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($estPartenaire) { ?>
                    <!-- ===== ACTIONS DU PARTENAIRE : trois grandes cartes cliquables ===== -->
                    <div class="profil-card actions-card">
                        <h2>Que souhaitez-vous faire ?</h2>
                        <div class="actions-choix">
                            <a class="action-carte" href="mes_evenements_ahmed.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4M8 2v4M3 10h18M12 14v4M10 16h4"></path></svg>
                                <span class="action-titre">Événements</span>
                                <span class="action-description">Créez un événement, suivez ceux que vous avez créés et vos inscriptions.</span>
                            </a>
                            <a class="action-carte" href="../page_entreprise/ajouter_entreprise.php">
                                <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"></rect><path d="M9 8h1M14 8h1M9 12h1M14 12h1M10 21v-4h4v4"></path></svg>
                                <span class="action-titre">Ajouter une entreprise</span>
                                <span class="action-description">Déclarez votre entreprise ou rattachez-vous à une fiche existante.</span>
                            </a>
                            <?php if ($peutGererOffres) { ?>
                                <a class="action-carte" href="../offre/mes_offres_Tom.php">
                                    <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"></rect><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2M2 13h20"></path></svg>
                                    <span class="action-titre">Gestion des offres</span>
                                    <span class="action-description">Publiez vos offres, consultez les candidatures et répondez aux candidats.</span>
                                </a>
                            <?php } else { ?>
                                <!-- Pas encore rattaché à une entreprise : la carte mène à la liste des entreprises (bouton « Me rattacher ») -->
                                <a class="action-carte" href="../page_entreprise/page_entreprise.php">
                                    <svg class="action-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"></rect><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2M2 13h20"></path></svg>
                                    <span class="action-titre">Gestion des offres</span>
                                    <span class="action-description">Rattachez-vous d'abord à votre entreprise (bouton « Me rattacher ») pour publier des offres.</span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <?php if ($messageSucces != "") { ?>
                    <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($messageSucces); ?></p>
                <?php } ?>

                <!-- ===== INVITATIONS À ORGANISER (professeurs) ===== -->
                <?php if ($utilisateur->getRole() == 'professeur') { ?>
                    <div class="profil-card evenements-card">
                        <h2>Invitations à organiser un événement</h2>
                        <?php if (count($invitations) == 0) { ?>
                        <p class="evenements-vide">Aucune invitation en attente pour le moment.</p>
                        <?php } else { ?>
                        <p>Un étudiant vous a ajouté comme co-organisateur, ou a modifié un événement que vous organisez. L'événement ne pourra être envoyé à l'administration qu'après votre accord.</p>
                        <div class="table-wrapper">
                            <table class="evenements-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Titre</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Lieu</th>
                                        <th scope="col">Proposé par</th>
                                        <th scope="col">Réponse</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invitations as $invitation) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($invitation['titre']); ?></td>
                                            <td><?php echo htmlspecialchars($invitation['type_evenement']); ?></td>
                                            <td><?php echo htmlspecialchars($invitation['lieu']); ?></td>
                                            <td><?php echo htmlspecialchars($invitation['createurs']); ?></td>
                                            <td>
                                                <form method="post" action="../../src/traitement/InvitationEvenementTraitement_malik.php" class="form-invitation">
                                                    <input type="hidden" name="id_evenement" value="<?php echo $invitation['id_evenement']; ?>">
                                                    <button type="submit" name="action" value="accepter" class="btn btn-primary btn-sm">Accepter</button>
                                                    <button type="submit" name="action" value="refuser" class="btn btn-danger btn-sm">Refuser</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <?php } ?>
                    </div>
                <?php } ?>

                <?php if (!$estPartenaire && !$estGestionnaire) { ?>
                    <!-- Les partenaires et gestionnaires retrouvent ces deux tableaux dans mes_evenements_ahmed.php -->
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
                <?php } ?>

            </div>
        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_profil.js"></script>
</body>
</html>
