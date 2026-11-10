<?php

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../repository/OffreRepository_Tom.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !estConnecte()) {
    header('Location: ../../public/offre/mes_offres_Tom.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['mes_offres_csrf']) || !hash_equals($_SESSION['mes_offres_csrf'], $_POST['csrf_token'])) {
    header('Location: ../../public/offre/mes_offres_Tom.php?resultat=erreur');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null || !in_array($utilisateur->getRole(), array('partenaire', 'alumni'), true) || $utilisateur->getStatutValidation() != 'valide' || empty($utilisateur->getIdEntreprise())) {
    header('Location: ../../public/offre/liste_offres_Tom.php');
    exit;
}

$idOffre = isset($_POST['id_offre']) ? (int) $_POST['id_offre'] : 0;
$statut = isset($_POST['statut']) ? $_POST['statut'] : '';

if ($idOffre <= 0 || !in_array($statut, array('ouverte', 'cloturee'), true)) {
    header('Location: ../../public/offre/mes_offres_Tom.php?resultat=erreur');
    exit;
}

try {
    $offreRepository = new OffreRepository_Tom();
    $modification = $offreRepository->modifierStatutOffreEntreprise($idOffre, $utilisateur->getIdEntreprise(), $statut);

    if ($modification) {
        header('Location: ../../public/offre/mes_offres_Tom.php?resultat=statut_modifie');
    } else {
        header('Location: ../../public/offre/mes_offres_Tom.php?resultat=erreur');
    }
    exit;
} catch (Exception $e) {
    header('Location: ../../public/offre/mes_offres_Tom.php?resultat=erreur');
    exit;
}