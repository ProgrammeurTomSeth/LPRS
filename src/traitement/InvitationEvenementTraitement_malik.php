<?php

// Traitement des boutons Accepter / Refuser d'une invitation à organiser un événement
// (section "Invitations à organiser un événement" de public/profil/profil_utilisateur.php)

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/evenement_anais.php';
require_once '../repository/EvenementRepository_ahmed.php';

// Réservé aux professeurs connectés
if (!estConnecte() || $_SESSION['role'] != 'professeur') {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$idProfesseur = $_SESSION['id_utilisateur'];
$idEvenement = (int) lireChamp('id_evenement');
$action = lireChamp('action');

$evenementRepository = new EvenementRepository_ahmed();

if ($action == 'accepter') {
    // repondreInvitation ne modifie que si l'invitation existe et est encore en attente
    if ($evenementRepository->repondreInvitation($idEvenement, $idProfesseur, 'acceptee')) {
        $_SESSION['message_profil'] = "Vous avez accepté de participer à l'événement. Il sera publié après validation par un gestionnaire.";
    }
} elseif ($action == 'refuser') {
    if ($evenementRepository->repondreInvitation($idEvenement, $idProfesseur, 'refusee')) {
        // S'il ne reste plus aucun professeur (accepté ou en attente), l'événement ne peut pas avoir lieu : on le refuse
        if ($evenementRepository->compterProfesseursRestants($idEvenement) == 0) {
            $evenementRepository->refuserEvenement($idEvenement);
        }
        $_SESSION['message_profil'] = "Vous avez refusé de participer à l'événement.";
    }
}

header('Location: ../../public/profil/profil_utilisateur.php');
exit;
