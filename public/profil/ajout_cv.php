
<?php
require_once '../../src/bdd/bdd.php';
require_once '../../src/traitement/SessionUtilisateur_ahmed.php';

// Vérification connexion
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../connexion/connexion.php");
    exit;
}

$id = $_SESSION['id_utilisateur'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['cv'])) {

    $fichier = $_FILES['cv'];

    if ($fichier['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['message_profil'] = "Erreur lors de l'envoi du CV.";
        header("Location: profil_utilisateur.php");
        exit;
    }

    // Vérification extension
    $extension = strtolower(pathinfo($fichier['name'], PATHINFO_EXTENSION));

    if ($extension !== 'pdf') {
        $_SESSION['message_profil'] = "Seul le format PDF est accepté.";
        header("Location: profil_utilisateur.php");
        exit;
    }

    // Nom du fichier
    $nouveauNom = "cv_" . $id . "_" . time() . ".pdf";

    // Chemin du dossier
    $dossier = "../../uploads/cv/";

    if (!is_dir($dossier)) {
        mkdir($dossier, 0755, true);
    }

    $destination = $dossier . $nouveauNom;

    // Déplacement du CV
    if (move_uploaded_file($fichier['tmp_name'], $destination)) {

        // Mise à jour BDD
        $bdd = (new bdd())->getConnexionBdd();

        $sql = "UPDATE utilisateur
                SET cv = :cv
                WHERE id_utilisateur = :id";

        $req = $bdd->prepare($sql);

        $req->execute([
            ':cv' => $nouveauNom,
            ':id' => $id
        ]);

        $_SESSION['message_profil'] = "Votre CV a été enregistré avec succès.";

    } else {
        $_SESSION['message_profil'] = "Impossible de déplacer le CV.";
    }

    header("Location: profil_utilisateur.php");
    exit;
}
?>

