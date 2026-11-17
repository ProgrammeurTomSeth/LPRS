<?php

// Traitement du formulaire d'inscription (public/inscription/inscription.php)

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../modele/formation_anais.php';
require_once '../modele/Entreprise_Tom.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../repository/Formation_ahmed.php';
require_once '../repository/EntrepriseRepository_Tom.php';
require_once '../mail/Mailer_ahmed.php';

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../public/inscription/inscription.php');
    exit;
}

// Récupération des champs (null si le champ est vide)
$role = lireChamp('role');
$nom = lireChamp('nom_utilisateur');
$prenom = lireChamp('prenom_utilisateur');
$mail = lireChamp('mail');
$mdp = lireChamp('mdp');
$mdpConfirm = lireChamp('mdp_confirm');
$telephone = lireChamp('telephone');
$dateNaissance = lireChamp('date_naissance');
$idFormation = lireChamp('id_formation');
$anneePromo = lireChamp('annee_promo');
$specialite = lireChamp('specialite');
$idEntreprise = lireChamp('id_entreprise');
$poste = lireChamp('poste');
$motifInscription = lireChamp('motif_inscription');
$consentement = lireChamp('consentement');

$erreurs = array();

// --- Champs communs ---
if (empty($nom) || empty($prenom)) {
    $erreurs[] = "Le nom et le prénom sont obligatoires.";
}
if (empty($mail) || filter_var($mail, FILTER_VALIDATE_EMAIL) == false) {
    $erreurs[] = "L'adresse email n'est pas valide.";
}
if (empty($mdp) || strlen($mdp) < 8) {
    $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
} elseif ($mdp != $mdpConfirm) {
    $erreurs[] = "Les mots de passe ne correspondent pas.";
}
if ($role != 'etudiant' && $role != 'alumni' && $role != 'partenaire' && $role != 'professeur') {
    $erreurs[] = "Veuillez choisir un profil.";
}
if (empty($consentement)) {
    $erreurs[] = "Vous devez accepter l'utilisation de vos informations.";
}

// --- Champs selon le profil ---
if ($role == 'etudiant' || $role == 'alumni') {
    $formationRepository = new Formation_ahmed();
    if (empty($idFormation) || $formationRepository->getFormation($idFormation) == null) {
        $erreurs[] = "Veuillez choisir une formation.";
    }
    if ($role == 'alumni' && empty($anneePromo)) {
        $erreurs[] = "L'année de promotion est obligatoire pour un alumni.";
    } elseif (!empty($anneePromo) && ((int) $anneePromo < 1950 || (int) $anneePromo > date('Y') + 5)) {
        $erreurs[] = "L'année de promotion n'est pas valide.";
    }
    $idEntreprise = null;
    if ($role == 'etudiant') {
        $poste = null;
    }
} elseif ($role == 'partenaire') {
    $entrepriseRepository = new EntrepriseRepository_Tom();

    if (empty($idEntreprise) || $entrepriseRepository->getEntreprise($idEntreprise) == null) {
        $erreurs[] = "Veuillez choisir votre entreprise.";
    }

    if (empty($poste)) {
        $erreurs[] = "Le poste est obligatoire pour un partenaire.";
    }

    if (empty($motifInscription)) {
        $erreurs[] = "Le motif d'inscription est obligatoire pour un partenaire.";
    }

    $idFormation = null;
    $anneePromo = null;
    $specialite = null;
} elseif ($role == 'professeur') {
    if (empty($specialite)) {
        $erreurs[] = "La spécialité est obligatoire pour un professeur.";
    }
    $idFormation = null;
    $anneePromo = null;
    $idEntreprise = null;
    $poste = null;
    $motifInscription = null;
}

// Le motif n'est demandé qu'à un partenaire
if ($role != 'partenaire') {
    $motifInscription = null;
}

// --- CV (PDF) : obligatoire pour un étudiant, facultatif pour un alumni ---
$cvEnvoye = isset($_FILES['cv']) && $_FILES['cv']['error'] != UPLOAD_ERR_NO_FILE;
if ($role == 'etudiant' || $role == 'alumni') {
    if (!$cvEnvoye) {
        if ($role == 'etudiant') {
            $erreurs[] = "Le CV est obligatoire pour un étudiant.";
        }
    } elseif ($_FILES['cv']['error'] != UPLOAD_ERR_OK || $_FILES['cv']['size'] > 2 * 1024 * 1024) {
        $erreurs[] = "Le CV n'a pas pu être envoyé (2 Mo maximum).";
    } elseif (strtolower(pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION)) != 'pdf' || file_get_contents($_FILES['cv']['tmp_name'], false, null, 0, 4) != '%PDF') {
        // Un vrai PDF commence toujours par "%PDF" : on ne se fie pas seulement à l'extension
        $erreurs[] = "Le CV doit être au format PDF.";
    }
}


$repository = new UtilisateurRepository_malik();
if (count($erreurs) == 0 && $repository->getUtilisateurParMail($mail) != null) {
    $erreurs[] = "Cette adresse email est déjà utilisée.";
}

// En cas d'erreur : on garde les messages et les valeurs saisies (sauf les mots de passe)
if (count($erreurs) > 0) {
    $_SESSION['inscription_erreurs'] = $erreurs;
    $_SESSION['inscription_valeurs'] = array(
        'role' => $role,
        'nom_utilisateur' => $nom,
        'prenom_utilisateur' => $prenom,
        'mail' => $mail,
        'telephone' => $telephone,
        'date_naissance' => $dateNaissance,
        'id_formation' => $idFormation,
        'annee_promo' => $anneePromo,
        'specialite' => $specialite,
        'id_entreprise' => $idEntreprise,
        'poste' => $poste,
        'motif_inscription' => $motifInscription,
        'consentement' => $consentement
    );
    header('Location: ../../public/inscription/inscription.php');
    exit;
}

// Enregistrement du CV dans LPRS/uploads/cv/ avec un nom unique (jamais le nom envoyé)
$cv = null;
if (($role == 'etudiant' || $role == 'alumni') && $cvEnvoye) {
    $dossier = '../../uploads/cv/';
    if (!is_dir($dossier)) {
        mkdir($dossier, 0755, true);
    }
    $nomCv = bin2hex(random_bytes(16)) . '.pdf';
    if (move_uploaded_file($_FILES['cv']['tmp_name'], $dossier . $nomCv)) {
        $cv = $nomCv;
    }
}

// Création de l'objet utilisateur puis enregistrement en base
$utilisateur = new Utilisateur_Malik();
$utilisateur->setNom($nom);
$utilisateur->setPrenom($prenom);
$utilisateur->setEmail($mail);
$utilisateur->setMdp(password_hash($mdp, PASSWORD_DEFAULT)); // le mot de passe est haché, jamais stocké en clair
$utilisateur->setTelephone($telephone);
$utilisateur->setDateNaissance($dateNaissance);
$utilisateur->setRole($role);
$utilisateur->setStatutValidation('en_attente');
$utilisateur->setCv($cv);
$utilisateur->setAnneePromo($anneePromo);
$utilisateur->setIdFormation($idFormation);
$utilisateur->setSpecialite($specialite);
$utilisateur->setMotifInscription($motifInscription);
$utilisateur->setIdEntreprise($idEntreprise);
$utilisateur->setPoste($poste);

$repository->ajouterUtilisateur($utilisateur);

// Email de confirmation : si l'envoi échoue, l'inscription reste enregistrée
$mailer = new Mailer_ahmed();
$mailer->envoyerCompteCree($utilisateur);

$_SESSION['inscription_succes'] = "Votre compte a été créé. Il est en attente de validation par un gestionnaire.";
header('Location: ../../public/inscription/inscription.php');
exit;
