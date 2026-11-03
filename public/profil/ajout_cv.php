<?php
require_once '../../src/bdd/bdd.php';
require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../connexion.php");
    exit;
}

$id = $_SESSION['id_utilisateur'];

if (!empty($_FILES['cv']['name'])) {

    $fichier = $_FILES['cv'];
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

    if ($extension !== 'pdf') {
        die("Format invalide : seul le PDF est accepté.");
    }

    $nouveauNom = "cv_" . $id . "_" . time() . ".pdf";
    $destination = "../../uploads/cv/" . $nouveauNom;

    if (move_uploaded_file($fichier['tmp_name'], $destination)) {

        $bdd = (new bdd())->getConnexionBdd();
        $sql = "UPDATE utilisateur SET cv = :cv WHERE id_utilisateur = :id";
        $req = $bdd->prepare($sql);
        $req->bindValue(':cv', $nouveauNom);
        $req->bindValue(':id', $id);

        $req->execute();

        // DEBUG
        var_dump($req->errorInfo());

        header("Location: profil_utilisateur.php?cv=ok");
        exit;

    } else {
        die("Erreur lors de l'upload.");
    }
}
?>
