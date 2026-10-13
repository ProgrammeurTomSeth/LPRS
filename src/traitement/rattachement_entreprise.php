<?php
session_start();
require_once('../../src/bdd/bdd.php');
$bdd = new bdd();
$pdo = $bdd->getConnexionBdd();

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    die("Vous devez être connecté pour vous abonner.");
}

$idUser = $_SESSION['id_utilisateur'];
$idEntreprise = $_POST['id_entreprise'] ?? null;

if (!$idEntreprise) {
    die("Aucune entreprise sélectionnée.");
}

// Vérifier que l'entreprise existe
$checkEntreprise = $pdo->prepare("SELECT id_entreprise FROM entreprise WHERE id_entreprise = ?");
$checkEntreprise->execute([$idEntreprise]);

if ($checkEntreprise->rowCount() === 0) {
    die("Cette entreprise n'existe pas dans la base.");
}

// Mise à jour de la table utilisateur
$sql = "UPDATE utilisateur 
        SET id_entreprise = :idEntreprise
        WHERE id_utilisateur = :idUser";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':idEntreprise' => $idEntreprise,
    ':idUser' => $idUser
]);

// Redirection vers la page entreprise
header("Location:page_accueil.php" . $idEntreprise);
exit;
