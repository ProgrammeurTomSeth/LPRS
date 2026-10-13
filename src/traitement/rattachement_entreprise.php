<?php

// Rattache (ou détache) l'utilisateur connecté à une entreprise

require_once __DIR__ . '/SessionUtilisateur_ahmed.php';
require_once __DIR__ . '/../repository/InscriptionRepository_ahmed.php';
require_once __DIR__ . '/../repository/EntrepriseRepository_Tom.php';
require_once __DIR__ . '/EntrepriseFormulaire_ahmed.php';

if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$idEntreprise = $_POST['id_entreprise'] ?? '';
$action = $_POST['action'] ?? 'rattacher';
$poste = trim($_POST['poste'] ?? '');

// Retour vers la fiche de l'entreprise si on vient de là, sinon vers la liste
$retour = '../../public/page_entreprise/page_entreprise.php';
if (($_POST['retour'] ?? '') === 'profil' && ctype_digit((string) $idEntreprise)) {
    $retour = '../../public/page_entreprise/profil_entreprise.php?id=' . (int) $idEntreprise;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $retour);
    exit;
}

try {
    $repository = new InscriptionRepository_ahmed();
    $utilisateur = $repository->getUtilisateurParId($_SESSION['id_utilisateur']);

    if ($utilisateur === null) {
        header('Location: DeconnexionTraitement_ahmed.php');
        exit;
    }

    if (!in_array($utilisateur->getRole(), ROLES_RATTACHEMENT, true)) {
        $_SESSION['entreprise_erreur'] = "Seuls les alumni et les partenaires peuvent se rattacher à une entreprise.";
    } elseif ($action === 'detacher') {
        if ($utilisateur->getRole() === 'partenaire') {
            $_SESSION['entreprise_erreur'] = "Un partenaire doit rester rattaché à une entreprise. Rattachez-vous à une autre entreprise pour changer.";
        } elseif ((int) $utilisateur->getIdEntreprise() !== (int) $idEntreprise) {
            $_SESSION['entreprise_erreur'] = "Vous n'êtes pas rattaché à cette entreprise.";
        } else {
            $repository->modifierEntrepriseUtilisateur($utilisateur->getIdUtilisateur(), null, null);
            $_SESSION['entreprise_message'] = "Vous n'êtes plus rattaché à cette entreprise.";
        }
    } else {
        $entreprise = ctype_digit((string) $idEntreprise)
            ? (new EntrepriseRepository_Tom())->getEntreprise((int) $idEntreprise)
            : null;

        if ($entreprise === null) {
            $_SESSION['entreprise_erreur'] = "Cette entreprise n'existe pas.";
        } elseif (mb_strlen($poste) > 150) {
            $_SESSION['entreprise_erreur'] = "Le poste ne doit pas dépasser 150 caractères.";
        } else {
            // Poste vide : on garde celui déjà renseigné
            $posteFinal = $poste !== '' ? $poste : $utilisateur->getPoste();
            $repository->modifierEntrepriseUtilisateur($utilisateur->getIdUtilisateur(), (int) $entreprise->getIdEntreprise(), $posteFinal);
            $_SESSION['entreprise_message'] = "Vous êtes maintenant rattaché à " . $entreprise->getNomEntreprise() . ".";
        }
    }
} catch (PDOException $e) {
    $_SESSION['entreprise_erreur'] = "Une erreur est survenue, veuillez réessayer.";
}

header('Location: ' . $retour);
exit;
