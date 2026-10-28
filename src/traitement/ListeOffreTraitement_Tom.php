<?php

// Candidature de l'utilisateur connecté à une offre (bouton "Postuler")

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Offre_Tom.php';
require_once '../repository/OffreRepository_Tom.php';

// $resultat est envoyé dans l'URL : la page liste affiche le message correspondant
if (!estConnecte()) {
    $resultat = 'connexion';
} elseif ($_SESSION['role'] != 'etudiant' && $_SESSION['role'] != 'alumni') {
    $resultat = 'profil';
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $resultat = 'erreur';
} else {
    $idOffre = (int) lireChamp('id_offre');
    $motivation = lireChamp('motivation');
    $idUtilisateur = $_SESSION['id_utilisateur'];

    $repository = new OffreRepository_Tom();
    $offre = $repository->getOffre($idOffre);

    if ($offre == null || $offre->getStatut() != 'ouverte') {
        $resultat = 'indisponible';
    } elseif (empty($motivation)) {
        $resultat = 'motivation';
    } elseif ($offre->getProfilCible() != 'tous' && $offre->getProfilCible() != $_SESSION['role']) {
        $resultat = 'profil_offre';
    } elseif ($repository->aDejaPostule($idOffre, $idUtilisateur)) {
        $resultat = 'deja_postule';
    } else {
        $repository->ajouterCandidature($idOffre, $idUtilisateur, $motivation);
        $resultat = 'succes';
    }
}

header('Location: ../../public/offre/liste_offres_Tom.php?resultat=' . $resultat);
exit;
