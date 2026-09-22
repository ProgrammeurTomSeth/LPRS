<?php

session_start();
$pdo = require_once('../../src/bdd/bdd.php');

function verifToken(string $token): bool {
    return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
}

$erreurs = [];
$valeurs = [
    'nom' => '',
    'prenom' => '',
    'email' => '',
    'telephone' => ''
];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/inscription.php');
    exit;
}

if (!verifToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['erreurs'] = ['general' => "Erreur de sécurité. Veuillez réessayer."];
    header('Location: ../public/inscription.php');
    exit;
}

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$mdp = $_POST['mdp'] ?? '';
$telephone = trim($_POST['telephone'] ?? '');

$valeurs = compact('nom', 'prenom', 'email', 'telephone');

/* ---------------- VALIDATIONS ---------------- */

if (empty($nom)) {
    $erreurs['nom'] = "Veuillez entrer votre nom.";
} elseif (mb_strlen($nom) < 2) {
    $erreurs['nom'] = "Le nom doit contenir au moins 2 caractères.";
}

if (empty($prenom)) {
    $erreurs['prenom'] = "Veuillez entrer votre prénom.";
} elseif (mb_strlen($prenom) < 2) {
    $erreurs['prenom'] = "Le prénom doit contenir au moins 2 caractères.";
}

if (empty($email)) {
    $erreurs['email'] = "Veuillez entrer un email.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs['email'] = "Format d'email invalide.";
} elseif (mb_strlen($email) > 255) {
    $erreurs['email'] = "Email trop long.";
}

if (empty($mdp)) {
    $erreurs['mdp'] = "Veuillez entrer un mot de passe.";
} elseif (mb_strlen($mdp) < 6) {
    $erreurs['mdp'] = "Le mot de passe doit contenir au moins 6 caractères.";
} else {
    $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=

\[\]

{};:\'",.<>\/?\\|`~]).{6,}$/';
    if (!preg_match($regex, $mdp)) {
        $erreurs['mdp'] = "Le mot de passe doit contenir une majuscule, une minuscule, un chiffre et un caractère spécial.";
    }
}

if (!empty($telephone) && !preg_match('/^[0-9]{10}$/', $telephone)) {
    $erreurs['telephone'] = "Le numéro de téléphone doit contenir 10 chiffres.";
}

/* ---------------- INSERTION SI OK ---------------- */

if (empty($erreurs)) {
    try {
        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

        $requete = $pdo->prepare(
            "INSERT INTO utilisateur(nom, prenom, email, mdp, telephone)
             VALUES (:nom, :prenom, :email, :mdp, :telephone)"
        );

        $requete->execute([
            "nom" => $nom,
            "prenom" => $prenom,
            "email" => $email,
            "mdp" => $mdpHash,
            "telephone" => $telephone
        ]);

        $_SESSION['succes'] = "Votre compte a été créé !";
        $_SESSION['valeurs'] = ['nom' => '', 'prenom' => '', 'email' => '', 'telephone' => ''];

        unset($_SESSION['token']);

        header('Location: ../public/formulaireInscription.php');
        exit;

    } catch (PDOException $e) {

        if ($e->getCode() === '23000') {
            $erreurs['email'] = "Cet email est déjà utilisé.";
        } else {
            $erreurs['general'] = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
}

$_SESSION['erreurs'] = $erreurs;
$_SESSION['valeurs'] = $valeurs;

header('Location: ../public/inscription.php');
exit;
