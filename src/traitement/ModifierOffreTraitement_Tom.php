<?php

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../repository/OffreRepository_Tom.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../../public/offre/mes_offres_Tom.php');
    exit;
}

if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null || !in_array($utilisateur->getRole(), array('partenaire', 'alumni'), true) || $utilisateur->getStatutValidation() != 'valide' || empty($utilisateur->getIdEntreprise())) {
    header('Location: ../../public/offre/liste_offres_Tom.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['modifier_offre_csrf']) || !hash_equals($_SESSION['modifier_offre_csrf'], $_POST['csrf_token'])) {
    header('Location: ../../public/offre/mes_offres_Tom.php?resultat=erreur');
    exit;
}

if (!isset($_POST['id_offre']) || !ctype_digit((string) $_POST['id_offre'])) {
    header('Location: ../../public/offre/mes_offres_Tom.php');
    exit;
}

$idOffre = (int) $_POST['id_offre'];
$idEntreprise = $utilisateur->getIdEntreprise();
$offreRepository = new OffreRepository_Tom();
$offreExistante = $offreRepository->getOffreEntreprise($idOffre, $idEntreprise);

if (!$offreExistante) {
    header('Location: ../../public/offre/mes_offres_Tom.php');
    exit;
}

$titre = isset($_POST['titre']) ? trim($_POST['titre']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$missions = isset($_POST['missions']) ? trim($_POST['missions']) : '';
$salaireSaisi = isset($_POST['salaire']) ? trim($_POST['salaire']) : '';
$type = isset($_POST['type']) ? trim($_POST['type']) : '';
$profilCible = isset($_POST['profil_cible']) ? trim($_POST['profil_cible']) : '';

$_SESSION['modifier_offre_anciennes_valeurs'] = array(
    'titre' => $titre,
    'description' => $description,
    'missions' => $missions,
    'salaire' => $salaireSaisi,
    'type' => $type,
    'profil_cible' => $profilCible
);

function retournerModification($idOffre, $message){
    $_SESSION['modifier_offre_erreur'] = $message;
    header('Location: ../../public/offre/Modifier_offre_Tom.php?id=' . (int) $idOffre);
    exit;
}

if ($titre == '' || $description == '' || $missions == '' || $type == '' || $profilCible == '') {
    retournerModification($idOffre, "Veuillez remplir tous les champs obligatoires.");
}

// La colonne titre de la table offre fait 200 caractères
if (mb_strlen($titre) > 200) {
    retournerModification($idOffre, "Le titre ne doit pas dépasser 200 caractères.");
}

$typesAutorises = array('stage', 'alternance', 'cdd', 'cdi');
$profilsAutorises = array('tous', 'etudiant', 'alumni');

if (!in_array($type, $typesAutorises, true)) {
    retournerModification($idOffre, "Le type de contrat sélectionné n'est pas valide.");
}

if (!in_array($profilCible, $profilsAutorises, true)) {
    retournerModification($idOffre, "Le profil recherché sélectionné n'est pas valide.");
}

$salaire = null;

if ($salaireSaisi != '') {
    $salaireNormalise = str_replace(',', '.', $salaireSaisi);
    if (!is_numeric($salaireNormalise) || (float) $salaireNormalise < 0) {
        retournerModification($idOffre, "Le salaire doit être un nombre positif ou nul.");
    }

    $salaire = (float) $salaireNormalise;
}

try {
    $offreRepository->modifierOffre($idOffre, $idEntreprise, $titre, $description, $missions, $salaire, $type, $profilCible);

    unset($_SESSION['modifier_offre_anciennes_valeurs']);
    unset($_SESSION['modifier_offre_erreur']);
    unset($_SESSION['modifier_offre_csrf']);

    header('Location: ../../public/offre/mes_offres_Tom.php?resultat=modification_succes');

    exit;

} catch (Exception $e) {
    error_log($e->getMessage());
    retournerModification($idOffre, "Une erreur est survenue pendant la modification de l'offre.");
}