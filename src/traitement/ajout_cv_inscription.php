<?php

require_once '../../src/bdd/bdd.php';
require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

// Vérification de la connexion
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../connexion/connexion.php");
    exit;
}

$id = $_SESSION['id_utilisateur'];

// Connexion à la base
$bdd = (new bdd())->getConnexionBdd();

// Vérification du rôle
$req = $bdd->prepare("
    SELECT role
    FROM utilisateur
    WHERE id_utilisateur = :id
");

$req->execute([
    ':id' => $id
]);

$role = strtolower((string)$req->fetchColumn());

if (!in_array($role, ['etudiant', 'alumni'], true)) {
    $_SESSION['message_profil'] =
        "Seuls les étudiants et les alumni peuvent ajouter un CV.";

    header("Location: inscripion.php");
    exit;
}

// Vérification de l'envoi du fichier
if (
    !isset($_FILES['cv']) ||
    $_FILES['cv']['error'] !== UPLOAD_ERR_OK
) {
    $_SESSION['message_profil'] = "Veuillez sélectionner un fichier PDF.";

    header("Location: inscription.php");
    exit;
}

$fichier = $_FILES['cv'];

// Vérification de l'extension
$extension = strtolower(
    pathinfo($fichier['name'], PATHINFO_EXTENSION)
);

if ($extension !== 'pdf') {
    $_SESSION['message_profil'] =
        "Seuls les fichiers PDF sont autorisés.";

    header("Location: inscription.php");
    exit;
}
// taille fichier
$fichier = $_FILES['cv'];

// Vérification de l'extension
$extension = strtolower(
    pathinfo($fichier['name'], PATHINFO_EXTENSION)
);

if ($extension !== 'pdf') {
    $_SESSION['message_profil'] =
        "Seuls les fichiers PDF sont autorisés.";

    header("Location: inscription.php");
    exit;
}

// Vérification de la taille (2 Mo maximum)
$tailleMax = 2 * 1024 * 1024; // 2 Mo

if ($fichier['size'] > $tailleMax) {
    $_SESSION['message_profil'] =
        "Le fichier PDF ne doit pas dépasser 2 Mo.";

    header("Location: inscription.php");
    exit;
}
// Création du dossier si nécessaire
$dossier = '../../uploads/cv/';

if (!is_dir($dossier)) {
    mkdir($dossier, 0755, true);
}

// Nom unique du fichier
$nomCV = 'cv_' . $id . '_' . time() . '.pdf';

$destination = $dossier . $nomCV;

// Déplacement du fichier
if (move_uploaded_file($fichier['tmp_name'], $destination)) {

    // Mise à jour de la base de données
    $update = $bdd->prepare("
        UPDATE utilisateur
        SET cv = :cv
        WHERE id_utilisateur = :id
    ");

    $update->execute([
        ':cv' => $nomCV,
        ':id' => $id
    ]);

    $_SESSION['message_profil'] =
        "Votre CV a été enregistré avec succès.";

} else {

    $_SESSION['message_profil'] =
        "Erreur lors de l'enregistrement du CV.";
}

header("Location: inscription.php");
exit;