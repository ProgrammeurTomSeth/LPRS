<?php

session_start();

require_once('../../src/bdd/bdd.php');
require_once __DIR__ . '/../../src/traitement/SessionUtilisateur_ahmed.php';

$bdd = new bdd();
$pdo = $bdd->getConnexionBdd();

// Utilisation de la vue v_annuaire_alumni
$sql = "SELECT * FROM v_annuaire_alumni";

try {
    $stmt = $pdo->query($sql);
    $alumni = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Erreur lors du chargement de l'annuaire.");
}
