<?php

// Traitement de la page public/evenement/creation_evenement_Tom.php :
// 1) recherche des co-organisateurs (appelée par le JavaScript avec ?recherche=...)
// 2) création de l'événement (formulaire en POST)

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

// Les gestionnaires ne peuvent pas créer d'événement
if ($roleConnecte == 'gestionnaire') {
    header('Location: ../../public/evenement/creation_evenement_Tom.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();

// 1) Recherche : on renvoie la liste au format JSON, lu par le JavaScript de la page
if (isset($_GET['recherche'])) {
    $utilisateurs = $utilisateurRepository->rechercherOrganisateurs($_GET['recherche'], $idConnecte);
    echo json_encode($utilisateurs);
    exit;
}

// 2) Création de l'événement
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

// Liste des organisateurs : tableau associatif id => rôle
// (une même clé ne peut exister qu'une fois, donc pas de doublon)
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

// Un étudiant doit avoir un professeur parmi les organisateurs pour publier
$professeurPresent = false;
foreach ($organisateurs as $id => $role) {
    if ($role == 'professeur') {
        $professeurPresent = true;
    }
}

$statut = 'publie';
if ($roleConnecte == 'etudiant' && $professeurPresent == false) {
    $statut = 'brouillon';
}

// Création de l'objet événement puis enregistrement
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

foreach ($organisateurs as $id => $role) {
    $evenementRepository->ajouterOrganisateur($idEvenement, $id);
}

if ($statut == 'publie') {
    $_SESSION['message_profil'] = "L'événement a été créé et publié.";
} else {
    $_SESSION['message_profil'] = "L'événement a été créé en brouillon. Il sera publié lorsqu'un professeur sera ajouté aux organisateurs.";
}

header('Location: ../../public/profil/profil_utilisateur.php');
exit;
