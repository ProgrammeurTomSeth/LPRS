<?php

// Données communes à public/page_entreprise/page_entreprise.php et profil_entreprise.php

require_once __DIR__ . '/SessionUtilisateur_ahmed.php';
require_once __DIR__ . '/../repository/InscriptionRepository_ahmed.php';
require_once __DIR__ . '/../repository/EntrepriseRepository_Tom.php';
require_once __DIR__ . '/recherche_entreprise.php';

// Rôles qui peuvent se rattacher à une entreprise
const ROLES_RATTACHEMENT = array('alumni', 'partenaire');

$utilisateurConnecte = null;
if (estConnecte()) {
    try {
        $utilisateurConnecte = (new InscriptionRepository_ahmed())->getUtilisateurParId($_SESSION['id_utilisateur']);
    } catch (PDOException $e) {
        $utilisateurConnecte = null;
    }
}

$peutSeRattacher = $utilisateurConnecte !== null && in_array($utilisateurConnecte->getRole(), ROLES_RATTACHEMENT, true);
$idEntrepriseUtilisateur = $utilisateurConnecte !== null && $utilisateurConnecte->getIdEntreprise() !== null
    ? (int) $utilisateurConnecte->getIdEntreprise()
    : null;

// Messages laissés par src/traitement/rattachement_entreprise.php
$messageSucces = $_SESSION['entreprise_message'] ?? null;
$messageErreur = $_SESSION['entreprise_erreur'] ?? null;
unset($_SESSION['entreprise_message'], $_SESSION['entreprise_erreur']);

function e($valeur){
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}
