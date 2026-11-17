<?php

// Traitement de la page public/evenement/gestion_evenement_malik.php
// Deux actions possibles :
//   - "modifier"        : enregistre la fiche de l'événement
//   - "refuser_inscrit" : refuse l'inscription d'une personne

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/evenement_anais.php';
require_once '../repository/EvenementRepository_ahmed.php';

// 1) Il faut être connecté
if (!estConnecte()) {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$idConnecte = $_SESSION['id_utilisateur'];
$idEvenement = (int) lireChamp('id_evenement');
$action = lireChamp('action');

// Page vers laquelle on revient à la fin
$pageGestion = '../../public/evenement/gestion_evenement_malik.php?id=' . $idEvenement;

$evenementRepository = new EvenementRepository_ahmed();

// 2) On revérifie que l'utilisateur est bien organisateur de CET événement.
//    (Le formulaire peut être modifié par quelqu'un de malin : on ne lui fait pas confiance.)
if (!$evenementRepository->estOrganisateur($idEvenement, $idConnecte)) {
    $_SESSION['message_profil'] = "Vous n'êtes pas organisateur de cet événement.";
    header('Location: ../../public/profil/profil_utilisateur.php');
    exit;
}

// 3) Action "modifier" : on enregistre la fiche
if ($action == 'modifier') {

    $typeEvenement = lireChamp('type_evenement');
    $titre = lireChamp('titre');
    $description = lireChamp('description');
    $lieu = lireChamp('lieu');
    $elementsRequis = lireChamp('elements_requis');
    $nbPlaces = lireChamp('nb_places');

    // Mêmes vérifications que pour la création
    if (empty($typeEvenement) || empty($titre) || empty($description) || empty($lieu)) {
        $_SESSION['gestion_erreur'] = "Veuillez remplir tous les champs obligatoires.";
        header('Location: ' . $pageGestion);
        exit;
    }
    if ($nbPlaces != null && (int) $nbPlaces <= 0) {
        $_SESSION['gestion_erreur'] = "Le nombre de places doit être un nombre entier positif.";
        header('Location: ' . $pageGestion);
        exit;
    }

    $evenement = new evenement_anais();
    $evenement->setIdEvenement($idEvenement);
    $evenement->setTypeEvenement($typeEvenement);
    $evenement->setTitre($titre);
    $evenement->setDescription($description);
    $evenement->setLieu($lieu);
    $evenement->setElementsRequis($elementsRequis);
    $evenement->setNbPlaces($nbPlaces);

    $evenementRepository->modifierEvenement($evenement);

    // L'événement a changé : il doit être revalidé avant d'être publié à nouveau.
    // (Mêmes règles qu'à la création.)
    $roleConnecte = $_SESSION['role'];

    if ($roleConnecte == 'gestionnaire') {
        // L'admin est celui qui valide : l'événement reste publié tel quel
        $_SESSION['gestion_message'] = "Les modifications ont été enregistrées.";

    } elseif ($roleConnecte == 'etudiant') {
        // Un étudiant a modifié : le professeur doit de nouveau accepter, puis l'admin valide
        $evenementRepository->redemanderAccordProfesseurs($idEvenement);
        $evenementRepository->remettreEnAttente($idEvenement);
        $_SESSION['gestion_message'] = "Les modifications ont été enregistrées. Le professeur doit de nouveau accepter l'événement, puis un gestionnaire le validera avant qu'il soit publié.";

    } else {
        // Un professeur (ou un autre organisateur) a modifié : seul l'admin doit revalider
        $evenementRepository->remettreEnAttente($idEvenement);
        $_SESSION['gestion_message'] = "Les modifications ont été enregistrées. L'événement sera de nouveau publié après validation par un gestionnaire.";
    }
}

// 4) Action "refuser_inscrit" : on refuse l'inscription d'une personne
if ($action == 'refuser_inscrit') {
    $idInscrit = (int) lireChamp('id_utilisateur');
    $evenementRepository->refuserInscription($idEvenement, $idInscrit);
    $_SESSION['gestion_message'] = "L'inscription a été refusée.";
}

// 5) On revient sur la page de gestion
header('Location: ' . $pageGestion);
exit;
