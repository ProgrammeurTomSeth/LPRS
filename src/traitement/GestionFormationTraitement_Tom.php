<?php
require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/formation_anais.php';
require_once '../repository/Formation_ahmed.php';
// Réservé aux gestionnaires
if (!estConnecte() || $_SESSION['role'] != 'gestionnaire') {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}
// Les actions doivent être envoyées en POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: ../../public/Page_Admin/gestion_formations_Tom.php');
    exit;
}
$action = lireChamp('action');
$formationRepository = new Formation_ahmed();
$pageGestion = '../../public/Page_Admin/gestion_formations_Tom.php';
// Types de formations autorisés
$typesAutorises = array('bac_pro', 'bac_techno', 'bts');
// --------------------------------------------------
// AJOUT D'UNE FORMATION
// --------------------------------------------------
if ($action == 'ajouter') {
    $nom = trim((string) lireChamp('nom_formation'));
    $type = lireChamp('type_formation');
    if ($nom == '' || $type == null) {
        $_SESSION['formation_erreur'] = "Veuillez renseigner le nom et le type de la formation.";
    } elseif (!in_array($type, $typesAutorises, true)) {
        $_SESSION['formation_erreur'] = "Le type de formation sélectionné n'est pas valide.";
    } elseif (strlen($nom) > 150) {
        $_SESSION['formation_erreur'] = "Le nom de la formation ne doit pas dépasser 150 caractères.";
    } elseif ($formationRepository->nomFormationExiste($nom)) {
        $_SESSION['formation_erreur'] = "Une formation portant déjà ce nom existe.";
    } else {
        $formation = new formation_anais();
        $formation->setNomFormation($nom);
        $formation->setTypeFormation($type);
        try {
            $formationRepository->ajouterFormation($formation);
            $_SESSION['formation_message'] = "La formation a été ajoutée avec succès.";
        } catch (PDOException $e) {
            // Le contrôle SQL protège aussi contre deux ajouts simultanés.
            if ($e->getCode() == '23000') {
                $_SESSION['formation_erreur'] = "Une formation portant déjà ce nom existe.";
            } else {
                $_SESSION['formation_erreur'] = "Une erreur est survenue lors de l'ajout de la formation.";
            }
        }
    }
} elseif ($action == 'modifier') {
    $idFormation = (int) lireChamp('id_formation');
    $nom = trim((string) lireChamp('nom_formation'));
    $type = lireChamp('type_formation');
    if ($idFormation <= 0 || $formationRepository->getFormation($idFormation) == null) {
        $_SESSION['formation_erreur'] = "La formation à modifier n'existe pas.";
    } elseif ($nom == '' || $type == null) {
        $_SESSION['formation_erreur'] = "Veuillez renseigner le nom et le type de la formation.";
    } elseif (!in_array($type, $typesAutorises, true)) {
        $_SESSION['formation_erreur'] = "Le type de formation sélectionné n'est pas valide.";
    } elseif (strlen($nom) > 150) {
        $_SESSION['formation_erreur'] = "Le nom de la formation ne doit pas dépasser 150 caractères.";
    } elseif ($formationRepository->nomFormationExiste($nom, $idFormation)) {
        $_SESSION['formation_erreur'] = "Une autre formation porte déjà ce nom.";
    } else {
        $formation = new formation_anais();
        $formation->setIdFormation($idFormation);
        $formation->setNomFormation($nom);
        $formation->setTypeFormation($type);
        try {
            $formationRepository->modifierFormation($formation);
            $_SESSION['formation_message'] = "La formation a été modifiée avec succès.";
        } catch (PDOException $e) {
            if ($e->getCode() == '23000') {
                $_SESSION['formation_erreur'] = "Une formation portant déjà ce nom existe.";
            } else {
                $_SESSION['formation_erreur'] = "Une erreur est survenue lors de la modification de la formation.";
            }
        }
    }
} elseif ($action == 'supprimer') {
    $idFormation = (int)lireChamp('id_formation');
    if ($idFormation <= 0 || $formationRepository->getFormation($idFormation) == null) {
        $_SESSION['formation_erreur'] = "La formation à supprimer n'existe pas.";
    } else {
        try {
            $formationRepository->supprimerFormation($idFormation);
            $_SESSION['formation_message'] = "La formation a été supprimée. Les élèves concernés n'ont désormais plus de formation associée.";
        } catch (PDOException $e) {
            $_SESSION['formation_erreur'] = "Une erreur est survenue lors de la suppression de la formation.";
        }
    }
} else {
    $_SESSION['formation_erreur'] = "L'action demandée n'est pas reconnue.";
}
header('Location: ' . $pageGestion);
exit;