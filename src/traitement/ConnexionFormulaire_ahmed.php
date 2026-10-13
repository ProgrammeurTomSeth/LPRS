<?php

// Prépare les données affichées par public/connexion/connexion.php

require_once __DIR__ . '/SessionUtilisateur_ahmed.php';

$erreur = $_SESSION['connexion_erreur'] ?? null;
$mail = $_SESSION['connexion_mail'] ?? '';
unset($_SESSION['connexion_erreur'], $_SESSION['connexion_mail']);

function e($valeur){
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}
