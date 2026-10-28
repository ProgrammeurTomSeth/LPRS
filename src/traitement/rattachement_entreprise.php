<?php

// Rattache (ou détache) l'utilisateur connecté à une entreprise

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../modele/Entreprise_Tom.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../repository/EntrepriseRepository_Tom.php';

if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$idEntreprise = (int) lireChamp('id_entreprise');
$action = lireChamp('action');
$poste = lireChamp('poste');

// Retour vers la fiche de l'entreprise si on vient de là, sinon vers la liste
$retour = '../../public/page_entreprise/page_entreprise.php';
if (lireChamp('retour') == 'profil') {
    $retour = '../../public/page_entreprise/profil_entreprise.php?id=' . $idEntreprise;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Location: ' . $retour);
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur->getRole() != 'alumni' && $utilisateur->getRole() != 'partenaire') {
    $_SESSION['entreprise_erreur'] = "Seuls les alumni et les partenaires peuvent se rattacher à une entreprise.";
} elseif ($action == 'detacher') {
    if ($utilisateur->getRole() == 'partenaire') {
        $_SESSION['entreprise_erreur'] = "Un partenaire doit rester rattaché à une entreprise. Rattachez-vous à une autre entreprise pour changer.";
    } elseif ($utilisateur->getIdEntreprise() != $idEntreprise) {
        $_SESSION['entreprise_erreur'] = "Vous n'êtes pas rattaché à cette entreprise.";
    } else {
        $utilisateurRepository->modifierEntrepriseUtilisateur($utilisateur->getIdUtilisateur(), null, null);
        $_SESSION['entreprise_message'] = "Vous n'êtes plus rattaché à cette entreprise.";
    }
} else {
    $entrepriseRepository = new EntrepriseRepository_Tom();
    $entreprise = $entrepriseRepository->getEntreprise($idEntreprise);

    if ($entreprise == null) {
        $_SESSION['entreprise_erreur'] = "Cette entreprise n'existe pas.";
    } else {
        // Poste vide : on garde celui déjà renseigné
        if (empty($poste)) {
            $poste = $utilisateur->getPoste();
        }
        $utilisateurRepository->modifierEntrepriseUtilisateur($utilisateur->getIdUtilisateur(), $idEntreprise, $poste);
        $_SESSION['entreprise_message'] = "Vous êtes maintenant rattaché à " . $entreprise->getNomEntreprise() . ".";
    }
}

header('Location: ' . $retour);
exit;
