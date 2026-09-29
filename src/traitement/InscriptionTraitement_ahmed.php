<?php

session_start();

require_once __DIR__ . '/../modele/Utilisisateur_Malik.php';
require_once __DIR__ . '/../repository/InscriptionRepository_ahmed.php';
require_once __DIR__ . '/../repository/Formation_ahmed.php';
require_once __DIR__ . '/../repository/EntrepriseRepository_Tom.php';

$pageInscription = '../../public/inscription/inscription.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $pageInscription);
    exit;
}

// Rôles qu'un visiteur peut choisir (professeur et gestionnaire sont créés par un gestionnaire)
$rolesAutorises = array('etudiant', 'alumni', 'partenaire');

$nom = trim($_POST['nom_utilisateur'] ?? '');
$prenom = trim($_POST['prenom_utilisateur'] ?? '');
$mail = trim($_POST['mail'] ?? '');
$mdp = $_POST['mdp'] ?? '';
$mdpConfirm = $_POST['mdp_confirm'] ?? '';
$telephone = trim($_POST['telephone'] ?? '');
$dateNaissance = trim($_POST['date_naissance'] ?? '');
$role = $_POST['role'] ?? '';
$idFormation = $_POST['id_formation'] ?? '';
$anneePromo = trim($_POST['annee_promo'] ?? '');
$specialite = trim($_POST['specialite'] ?? '');
$idEntreprise = $_POST['id_entreprise'] ?? '';
$poste = trim($_POST['poste'] ?? '');
$motifInscription = trim($_POST['motif_inscription'] ?? '');

$erreurs = array();

// --- Champs communs ---
if ($nom === '' || $prenom === '') {
    $erreurs[] = "Le nom et le prénom sont obligatoires.";
}
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}
if (strlen($mdp) < 8) {
    $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
} elseif ($mdp !== $mdpConfirm) {
    $erreurs[] = "Les mots de passe ne correspondent pas.";
}
if ($telephone !== '' && !preg_match('/^[0-9 +().-]{6,20}$/', $telephone)) {
    $erreurs[] = "Le numéro de téléphone n'est pas valide.";
}
if ($dateNaissance !== '') {
    $date = DateTime::createFromFormat('Y-m-d', $dateNaissance);
    if (!$date || $date->format('Y-m-d') !== $dateNaissance || $date > new DateTime()) {
        $erreurs[] = "La date de naissance n'est pas valide.";
    }
}
if (!in_array($role, $rolesAutorises, true)) {
    $erreurs[] = "Veuillez choisir un profil.";
}
if (empty($_POST['consentement'])) {
    $erreurs[] = "Vous devez accepter l'utilisation de vos informations.";
}

// --- Champs selon le profil ---
if ($role === 'etudiant' || $role === 'alumni') {
    if ($idFormation === '' || (new Formation_ahmed())->getFormation((int) $idFormation) === null) {
        $erreurs[] = "Veuillez choisir une formation.";
    }
    $anneeMax = (int) date('Y') + 5;
    if ($role === 'alumni' && $anneePromo === '') {
        $erreurs[] = "L'année de promotion est obligatoire pour un alumni.";
    } elseif ($anneePromo !== '' && (!ctype_digit($anneePromo) || (int) $anneePromo < 1950 || (int) $anneePromo > $anneeMax)) {
        $erreurs[] = "L'année de promotion n'est pas valide.";
    }
    $idEntreprise = '';
    if ($role === 'etudiant') {
        $poste = '';
    }
} elseif ($role === 'partenaire') {
    if ($idEntreprise === '' || (new EntrepriseRepository_Tom())->getEntreprise((int) $idEntreprise) === null) {
        $erreurs[] = "Veuillez choisir votre entreprise.";
    }
    if ($poste === '') {
        $erreurs[] = "Le poste est obligatoire pour un partenaire.";
    }
    $idFormation = '';
    $anneePromo = '';
    $specialite = '';
}

if (mb_strlen($motifInscription) > 255) {
    $erreurs[] = "Le motif d'inscription ne doit pas dépasser 255 caractères.";
}

$repository = new InscriptionRepository_ahmed();
if (empty($erreurs) && $repository->mailExiste($mail)) {
    $erreurs[] = "Cette adresse email est déjà utilisée.";
}

// --- CV (optionnel, PDF uniquement, 2 Mo max) ---
$cv = null;
if (empty($erreurs) && $role !== 'partenaire' && isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
    $fichier = $_FILES['cv'];
    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        $erreurs[] = "L'envoi du CV a échoué.";
    } elseif ($fichier['size'] > 2 * 1024 * 1024) {
        $erreurs[] = "Le CV ne doit pas dépasser 2 Mo.";
    } elseif ((new finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']) !== 'application/pdf') {
        $erreurs[] = "Le CV doit être un fichier PDF.";
    } else {
        $dossier = __DIR__ . '/../../public/uploads/cv/';
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }
        $nomFichier = bin2hex(random_bytes(16)) . '.pdf';
        if (move_uploaded_file($fichier['tmp_name'], $dossier . $nomFichier)) {
            $cv = 'uploads/cv/' . $nomFichier;
        } else {
            $erreurs[] = "Impossible d'enregistrer le CV.";
        }
    }
}

if (!empty($erreurs)) {
    $anciennesValeurs = $_POST;
    unset($anciennesValeurs['mdp'], $anciennesValeurs['mdp_confirm']);
    $_SESSION['inscription_erreurs'] = $erreurs;
    $_SESSION['inscription_valeurs'] = $anciennesValeurs;
    header('Location: ' . $pageInscription);
    exit;
}

$utilisateur = new Utilisateur_Malik(
    null,
    $nom,
    $prenom,
    $mail,
    password_hash($mdp, PASSWORD_DEFAULT),
    $telephone !== '' ? $telephone : null,
    $dateNaissance !== '' ? $dateNaissance : null,
    $role,
    'en_attente',
    $cv,
    $anneePromo !== '' ? (int) $anneePromo : null,
    $idFormation !== '' ? (int) $idFormation : null,
    $specialite !== '' ? $specialite : null,
    $motifInscription !== '' ? $motifInscription : null,
    $idEntreprise !== '' ? (int) $idEntreprise : null,
    $poste !== '' ? $poste : null,
    null,
    null
);

try {
    $repository->ajouterUtilisateur($utilisateur);
} catch (PDOException $e) {
    if ($cv !== null) {
        @unlink(__DIR__ . '/../../public/' . $cv);
    }
    $_SESSION['inscription_erreurs'] = array("Une erreur est survenue lors de l'inscription, veuillez réessayer.");
    $anciennesValeurs = $_POST;
    unset($anciennesValeurs['mdp'], $anciennesValeurs['mdp_confirm']);
    $_SESSION['inscription_valeurs'] = $anciennesValeurs;
    header('Location: ' . $pageInscription);
    exit;
}

$_SESSION['inscription_succes'] = "Votre compte a été créé. Il est en attente de validation par un gestionnaire.";
header('Location: ' . $pageInscription);
exit;
