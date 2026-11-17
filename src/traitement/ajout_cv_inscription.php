<?php

// Traitement du formulaire "Mon CV" de public/profil/profil_utilisateur.php :
// un étudiant ou un alumni dépose (ou remplace) son CV au format PDF

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';

// Vérification de la connexion
if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../public/profil/profil_utilisateur.php');
    exit;
}

$repository = new UtilisateurRepository_malik();
$utilisateur = $repository->getUtilisateurParId($_SESSION['id_utilisateur']);

// Seuls les étudiants et les alumni ont un CV
if ($utilisateur == null || ($utilisateur->getRole() != 'etudiant' && $utilisateur->getRole() != 'alumni')) {
    $_SESSION['message_profil'] = "Seuls les étudiants et les alumni peuvent ajouter un CV.";
    header('Location: ../../public/profil/profil_utilisateur.php');
    exit;
}

// Vérifications du fichier : envoyé, 2 Mo maximum, vrai PDF (commence par "%PDF")
$erreur = "";
if (!isset($_FILES['cv']) || $_FILES['cv']['error'] == UPLOAD_ERR_NO_FILE) {
    $erreur = "Veuillez sélectionner un fichier PDF.";
} elseif ($_FILES['cv']['error'] != UPLOAD_ERR_OK || $_FILES['cv']['size'] > 2 * 1024 * 1024) {
    $erreur = "Le CV n'a pas pu être envoyé (2 Mo maximum).";
} elseif (strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION)) != 'pdf' || file_get_contents($_FILES['cv']['tmp_name'], false, null, 0, 4) != '%PDF') {
    $erreur = "Le CV doit être au format PDF.";
}

if ($erreur != "") {
    $_SESSION['message_profil'] = $erreur;
    header('Location: ../../public/profil/profil_utilisateur.php');
    exit;
}

// Même dossier et même nommage que pour l'inscription (InscriptionTraitement_ahmed.php)
$dossier = '../../uploads/cv/';
if (!is_dir($dossier)) {
    mkdir($dossier, 0755, true);
}
$nomCv = bin2hex(random_bytes(16)) . '.pdf';

if (move_uploaded_file($_FILES['cv']['tmp_name'], $dossier . $nomCv)) {
    // On supprime l'ancien CV pour ne pas garder de fichiers inutiles
    $ancienCv = $utilisateur->getCv();
    if ($ancienCv != null && file_exists($dossier . basename($ancienCv))) {
        unlink($dossier . basename($ancienCv));
    }
    $repository->modifierCv($utilisateur->getIdUtilisateur(), $nomCv);
    $_SESSION['message_profil'] = "Votre CV a été enregistré.";
} else {
    $_SESSION['message_profil'] = "Erreur lors de l'enregistrement du CV.";
}

header('Location: ../../public/profil/profil_utilisateur.php');
exit;
