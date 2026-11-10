<?php

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../modele/evenement_anais.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../repository/EvenementRepository_ahmed.php';

if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$idConnecte = $_SESSION['id_utilisateur'];
$roleConnecte = $_SESSION['role'];

if ($roleConnecte == 'gestionnaire') {
    header('Location: ../../public/evenement/creation_evenement_Tom.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();

if (isset($_GET['recherche'])) {
    $utilisateurs = $utilisateurRepository->rechercherOrganisateurs($_GET['recherche'], $idConnecte);
    echo json_encode($utilisateurs);
    exit;
}

$typeEvenement = lireChamp('type_evenement');
$titre = lireChamp('titre');
$description = lireChamp('description');
$lieu = lireChamp('lieu');
$elementsRequis = lireChamp('elements_requis');
$nbPlaces = lireChamp('nb_places');

$erreur = "";
if (empty($typeEvenement) || empty($titre) || empty($description) || empty($lieu)) {
    $erreur = "Veuillez remplir tous les champs obligatoires.";
} elseif ($nbPlaces != null && (int) $nbPlaces <= 0) {
    $erreur = "Le nombre de places doit être un nombre entier positif.";
}

if ($erreur != "") {
    $_SESSION['evenement_erreur'] = $erreur;
    header('Location: ../../public/evenement/creation_evenement_Tom.php');
    exit;
}

$organisateurs = array();
$organisateurs[$idConnecte] = $roleConnecte;

if (isset($_POST['coorganisateurs'])) {
    foreach ($_POST['coorganisateurs'] as $idCoorganisateur) {
        $coorganisateur = $utilisateurRepository->getUtilisateurParId((int) $idCoorganisateur);
        if ($coorganisateur != null && $coorganisateur->getRole() != 'gestionnaire' && $coorganisateur->getStatutValidation() == 'valide') {
            $organisateurs[$coorganisateur->getIdUtilisateur()] = $coorganisateur->getRole();
        }
    }
}

$professeurPresent = false;
foreach ($organisateurs as $id => $role) {
    if ($role == 'professeur') {
        $professeurPresent = true;
    }
}

if ($professeurPresent == false) {
    $_SESSION['evenement_erreur'] = "Vous devez ajouter au moins un professeur parmi les organisateurs.";
    header('Location: ../../public/evenement/creation_evenement_Tom.php');
    exit;
}

$statut = 'en_attente';

$evenement = new evenement_anais();
$evenement->setTypeEvenement($typeEvenement);
$evenement->setTitre($titre);
$evenement->setDescription($description);
$evenement->setLieu($lieu);
$evenement->setElementsRequis($elementsRequis);
$evenement->setNbPlaces($nbPlaces);
$evenement->setStatut($statut);

$evenementRepository = new EvenementRepository_ahmed();
$idEvenement = $evenementRepository->ajouterEvenement($evenement);

// Si c'est un étudiant qui crée l'événement, les professeurs ajoutés doivent d'abord accepter :
// leur participation est mise "en_attente". Les autres organisateurs sont acceptés directement.
foreach ($organisateurs as $id => $role) {
    if ($roleConnecte == 'etudiant' && $role == 'professeur' && $id != $idConnecte) {
        $evenementRepository->ajouterOrganisateur($idEvenement, $id, 'en_attente');
    } else {
        $evenementRepository->ajouterOrganisateur($idEvenement, $id, 'acceptee');
    }
}

if ($roleConnecte == 'etudiant') {
    $_SESSION['message_profil'] = "L'événement a été créé. Le professeur doit d'abord accepter d'y participer, puis un gestionnaire le validera.";
} else {
    $_SESSION['message_profil'] = "L'événement a été créé. Il sera publié après validation par un gestionnaire.";
}

header('Location: ../../public/profil/profil_utilisateur.php');
exit;
