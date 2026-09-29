<?php

// Prépare les données affichées par public/inscription/inscription.php

session_start();

require_once __DIR__ . '/../repository/Formation_ahmed.php';
require_once __DIR__ . '/../repository/EntrepriseRepository_Tom.php';

$erreurs = $_SESSION['inscription_erreurs'] ?? array();
$valeurs = $_SESSION['inscription_valeurs'] ?? array();
$succes = $_SESSION['inscription_succes'] ?? null;
unset($_SESSION['inscription_erreurs'], $_SESSION['inscription_valeurs'], $_SESSION['inscription_succes']);

// Rôles qu'un visiteur peut choisir (voir colonne `role` de la table utilisateur)
$roles = array(
    'etudiant' => 'Étudiant',
    'alumni' => 'Ancien élève (alumni)',
    'partenaire' => 'Partenaire entreprise',
    'professeur' => 'Professeur',
);
$typesFormation = array(
    'bac_pro' => 'Bac pro',
    'bac_techno' => 'Bac techno',
    'bts' => 'BTS',
);

$formations = array();
$entreprises = array();
try {
    $formations = (new Formation_ahmed())->getAllFormations();
    $entreprises = (new EntrepriseRepository_Tom())->getAllEntreprises();
} catch (PDOException $e) {
    $erreurs[] = "Impossible de charger les formations et les entreprises pour le moment.";
}

function e($valeur){
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}

function ancienneValeur($champ){
    global $valeurs;
    return isset($valeurs[$champ]) ? (string) $valeurs[$champ] : '';
}
