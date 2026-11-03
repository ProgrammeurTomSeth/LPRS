<?php

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Offre_Tom.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../repository/CreationOffreRepository.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../../public/offre/liste_offres_Tom.php');
    exit;
}

if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null) {
    header('Location: ../../public/offre/liste_offres_Tom.php');
    exit;
}

$roleUtilisateur = $utilisateur->getRole();
$idEntreprise = $utilisateur->getIdEntreprise();
$idUtilisateurAuteur = $utilisateur->getIdUtilisateur();

if (($roleUtilisateur != 'partenaire' && $roleUtilisateur != 'alumni') || empty($idEntreprise) || $utilisateur->getStatutValidation() != 'valide') {
    $_SESSION['creation_offre_erreur'] = "Vous n'êtes pas autorisé à créer une offre.";
    header('Location: ../../public/offre/liste_offres_Tom.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['creation_offre_csrf']) || !hash_equals($_SESSION['creation_offre_csrf'], $_POST['csrf_token'])) {
    $_SESSION['creation_offre_erreur'] = "Le formulaire a expiré ou n'est pas valide. Veuillez réessayer.";
    header('Location: ../../public/offre/creation_offre.php');
    exit;
}

$titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$missions = isset($_POST['missions']) ? trim($_POST['missions']) : '';
$salaireSaisi = isset($_POST['salaire']) ? trim($_POST['salaire']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$profilCible = isset($_POST['profil_cible']) ? trim($_POST['profil_cible']) : '';

$typesAutorises = array('stage', 'alternance', 'cdd', 'cdi');
$profilsAutorises = array('tous', 'etudiant', 'alumni');

$_SESSION['creation_offre_ancien'] = array(
    'titre' => $titre,
    'description' => $description,
    'missions' => $missions,
    'salaire' => $salaireSaisi,
    'type' => $type,
    'profil_cible' => $profilCible
);

if ($titre == '' || $description == '' || $missions == '' || $type == '' || $profilCible == '') {
    $_SESSION['creation_offre_erreur'] = "Veuillez remplir tous les champs obligatoires.";
    header('Location: ../../public/offre/creation_offre.php');
    exit;
}

if (!in_array($type, $typesAutorises, true)) {
    $_SESSION['creation_offre_erreur'] = "Le type de contrat sélectionné n'est pas valide.";
    header('Location: ../../public/offre/creation_offre.php');
    exit;
}

if (!in_array($profilCible, $profilsAutorises, true)) {
    $_SESSION['creation_offre_erreur'] = "Le profil ciblé sélectionné n'est pas valide.";
    header('Location: ../../public/offre/creation_offre.php');
    exit;
}

$salaire = null;

if ($salaireSaisi != '') {
    $salaireNormalise = str_replace(',', '.', $salaireSaisi);

    if (!is_numeric($salaireNormalise) || (float) $salaireNormalise < 0) {
        $_SESSION['creation_offre_erreur'] = "Le salaire doit être un nombre positif ou nul.";
        header('Location: ../../public/offre/creation_offre.php');
        exit;
    }

    $salaire = (float) $salaireNormalise;
}

if (strlen($titre) > 255) {
    $_SESSION['creation_offre_erreur'] = "Le titre ne doit pas dépasser 255 caractères.";
    header('Location: ../../public/offre/creation_offre.php');
    exit;
}

$offre = new Offre_Tom();

$offre->setTitre($titre);
$offre->setDescription($description);
$offre->setMissions($missions);
$offre->setSalaire($salaire);
$offre->setType($type);

$offre->setStatut('ouverte');
$offre->setProfilCible($profilCible);

$offre->setRefEntreprise($idEntreprise);
$offre->setIdUtilisateurAuteur($idUtilisateurAuteur);

try {
    $creationOffreRepository = new CreationOffreRepository();
    $idOffre = $creationOffreRepository->ajouterOffre($offre);

    unset($_SESSION['creation_offre_ancien']);
    unset($_SESSION['creation_offre_csrf']);

    $_SESSION['creation_offre_succes'] = "Votre offre a bien été créée et publiée.";

    header('Location: ../../public/offre/liste_offres_Tom.php?resultat=creation_succes');
    exit;

} catch (Exception $e) {
    $_SESSION['creation_offre_erreur'] = "Une erreur est survenue lors de l'enregistrement de l'offre. Veuillez réessayer.";

    header('Location: ../../public/offre/creation_offre.php');
    exit;
}