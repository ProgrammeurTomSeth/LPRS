<?php

require_once(__DIR__ . '/../bdd/bdd.php');

$bdd = new bdd();
$pdo = $bdd->getConnexionBdd();

// Récupération des formations
$sql = "SELECT id_formation, nom_formation, type_formation FROM formation";
$stmt = $pdo->prepare($sql);
$stmt->execute();

$formations = $stmt->fetchAll(PDO::FETCH_ASSOC);
