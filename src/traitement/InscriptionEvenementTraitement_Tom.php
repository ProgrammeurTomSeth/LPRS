<?php

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/evenement_anais.php';
require_once '../repository/EvenementRepository_ahmed.php';

if (!estConnecte()) {
    $resultat = 'connexion';
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $resultat = 'erreur';
} elseif ($_SESSION['role'] == 'gestionnaire') {
    // Cahier des charges : tout le monde peut s'inscrire, sauf les gestionnaires
    $resultat = 'gestionnaire';
} else {
    $idEvenement = (int) lireChamp('id_evenement');
    $idUtilisateur = $_SESSION['id_utilisateur'];

    $repository = new EvenementRepository_ahmed();
    $evenement = $repository->getEvenement($idEvenement);

    // Événement inexistant, pas encore publié ou déjà passé
    if ($evenement == null || $evenement->getStatut() != 'publie' || strtotime($evenement->getDateEvenement()) < time()) {
        $resultat = 'indisponible';
    } elseif ($repository->estInscrit($idEvenement, $idUtilisateur)) {
        $resultat = 'deja_inscrit';
    } elseif ($evenement->getNbPlaces() != null && $repository->compterInscrits($idEvenement) >= $evenement->getNbPlaces()) {
        $resultat = 'complet';
    } else {
        $repository->inscrire($idEvenement, $idUtilisateur);
        $resultat = 'succes';
    }
}

header('Location: ../../public/evenement/liste_evenement_Tom.php?resultat=' . $resultat);
exit;
