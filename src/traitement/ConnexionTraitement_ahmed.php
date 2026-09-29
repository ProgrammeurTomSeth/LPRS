<?php

session_start();

require_once __DIR__ . '/../repository/InscriptionRepository_ahmed.php';

$pageConnexion = '../../public/connexion/connexion.php';
$pageAccueil = '../../public/page_acceuille/page_accueil.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $pageConnexion);
    exit;
}

$mail = trim($_POST['mail'] ?? '');
$mdp = $_POST['mdp'] ?? '';

$erreur = null;
if ($mail === '' || $mdp === '') {
    $erreur = "Veuillez saisir votre email et votre mot de passe.";
} else {
    try {
        $utilisateur = (new InscriptionRepository_ahmed())->getUtilisateurParMail($mail);
    } catch (PDOException $e) {
        $utilisateur = null;
        $erreur = "Une erreur est survenue, veuillez réessayer.";
    }

    if ($erreur === null) {
        // Même message si l'email ou le mot de passe est faux, pour ne pas révéler les comptes existants
        if ($utilisateur === null || !password_verify($mdp, $utilisateur->getMdp())) {
            $erreur = "Email ou mot de passe incorrect.";
        } elseif ($utilisateur->getStatutValidation() === 'en_attente') {
            $erreur = "Votre compte est en attente de validation par un gestionnaire.";
        } elseif ($utilisateur->getStatutValidation() === 'refuse') {
            $erreur = "Votre demande d'inscription a été refusée.";
        }
    }
}

if ($erreur !== null) {
    $_SESSION['connexion_erreur'] = $erreur;
    $_SESSION['connexion_mail'] = $mail;
    header('Location: ' . $pageConnexion);
    exit;
}

session_regenerate_id(true);
$_SESSION['id_utilisateur'] = (int) $utilisateur->getIdUtilisateur();
$_SESSION['role'] = $utilisateur->getRole();
$_SESSION['nom_utilisateur'] = $utilisateur->getNom();
$_SESSION['prenom_utilisateur'] = $utilisateur->getPrenom();

header('Location: ' . $pageAccueil);
exit;
