<?php

require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Entreprise_Tom.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $activite = trim($_POST['activite'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $siteWeb = trim($_POST['siteWeb'] ?? '');

    if ($nom && $activite && $adresse && $siteWeb) {

        $repository = new EntrepriseRepository_Tom();

        $resultat = $repository->ajouterEntreprise(
            $nom,
            $activite,
            $adresse,
            $siteWeb
        );

        if ($resultat) {
            header("Location: ../profil/profil_utilisateur.php");
            exit;
        } else {
            $message = "Site web déjà utilisé ou invalide.";
        }
    }
}

