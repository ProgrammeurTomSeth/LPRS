<?php

// Traitement du formulaire de connexion (public/connexion/connexion.php)

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';

// Si on arrive ici sans formulaire, retour à la page de connexion
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$mail = lireChamp('mail');
$mdp = lireChamp('mdp');
$erreur = "";

if (empty($mail) || empty($mdp)) {
    $erreur = "Veuillez saisir votre email et votre mot de passe.";
} else {
    $repository = new UtilisateurRepository_malik();
    $utilisateur = $repository->getUtilisateurParMail($mail);

    // password_verify compare le mot de passe saisi avec le mot de passe haché en base
    if ($utilisateur == null || password_verify($mdp, $utilisateur->getMdp()) == false) {
        $erreur = "Email ou mot de passe incorrect.";
    } elseif ($utilisateur->getStatutValidation() == 'en_attente') {
        $erreur = "Votre compte est en attente de validation par un gestionnaire.";
    } elseif ($utilisateur->getStatutValidation() == 'refuse') {
        $erreur = "Votre demande d'inscription a été refusée.";
    }
}

// En cas d'erreur : on garde le message et le mail en session, puis retour au formulaire
if ($erreur != "") {
    $_SESSION['connexion_erreur'] = $erreur;
    $_SESSION['connexion_mail'] = $mail;
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

// Connexion réussie : on mémorise l'utilisateur dans la session
$_SESSION['id_utilisateur'] = $utilisateur->getIdUtilisateur();
$_SESSION['role'] = $utilisateur->getRole();
$_SESSION['nom_utilisateur'] = $utilisateur->getNom();
$_SESSION['prenom_utilisateur'] = $utilisateur->getPrenom();

header('Location: ../../public/page_entreprise/page_entreprise.php');
exit;
