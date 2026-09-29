<?php

session_start();

require_once('../../src/bdd/bdd.php');
$bdd = new bdd();
$pdo = $bdd->getConnexionBdd();

// --- Vérification des champs obligatoires ---
if (
    empty($_POST['nom']) ||
    empty($_POST['prenom']) ||
    empty($_POST['email']) ||
    empty($_POST['mdp']) ||
    empty($_POST['mdp_confirm']) ||
    empty($_POST['profil'])
) {
    die("Tous les champs obligatoires doivent être remplis.");
}

$nom        = htmlspecialchars($_POST['nom']);
$prenom     = htmlspecialchars($_POST['prenom']);
$email      = htmlspecialchars($_POST['email']);
$mdp        = $_POST['mdp'];
$mdp_confirm = $_POST['mdp_confirm'];
$telephone  = !empty($_POST['telephone']) ? htmlspecialchars($_POST['telephone']) : null;
$profil     = htmlspecialchars($_POST['profil']);

// --- Vérification mot de passe ---
if ($mdp !== $mdp_confirm) {
    die("Les mots de passe ne correspondent pas.");
}

// --- Vérification email déjà utilisé ---
$check = $pdo->prepare("SELECT mail FROM utilisateur WHERE mail = ?");
$check->execute([$email]);

if ($check->rowCount() > 0) {
    die("Cet email est déjà utilisé.");
}

// --- Hash du mot de passe ---
$mdp_hash = password_hash($mdp, PASSWORD_DEFAULT);

// --- Insertion dans la base ---
$sql = "INSERT INTO utilisateur 
        (nom_utilisateur, prenom_utilisateur, mail, mdp, telephone, role, statut_validation, date_inscription)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    $nom,
    $prenom,
    $email,
    $mdp_hash,
    $telephone,
    $profil,
    "en_attente"
]);

echo "Votre compte a été créé et est en attente de validation par un gestionnaire.";

