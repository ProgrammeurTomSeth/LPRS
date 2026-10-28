<?php

// Traitement des boutons Ajouter / Enregistrer / Activer / Désactiver / Supprimer de public/Page_Admin/page_admin.php

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';

// Réservé aux gestionnaires
if (!estConnecte() || $_SESSION['role'] != 'gestionnaire') {
    header('Location: ../../public/connexion/connexion.php');
    exit;
}

$repository = new UtilisateurRepository_malik();
$action = lireChamp('action');
$id = (int) lireChamp('id_utilisateur');
$nom = lireChamp('nom');
$prenom = lireChamp('prenom');
$mail = lireChamp('mail');
$telephone = lireChamp('telephone');
$role = lireChamp('role');

if ($action == 'ajouter') {
    $mdp = lireChamp('mdp');
    // Ajout seulement si les champs sont remplis et le mail pas déjà utilisé
    if (!empty($nom) && !empty($prenom) && !empty($mail) && !empty($mdp) && strlen($mdp) >= 8 && $repository->getUtilisateurParMail($mail) == null) {
        $utilisateur = new Utilisateur_Malik();
        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setEmail($mail);
        $utilisateur->setMdp(password_hash($mdp, PASSWORD_DEFAULT));
        $utilisateur->setTelephone($telephone);
        $utilisateur->setRole($role);
        $utilisateur->setStatutValidation('valide'); // créé par un gestionnaire : déjà validé
        $utilisateur->setIdGestionnaireCreateur($_SESSION['id_utilisateur']);
        $repository->ajouterUtilisateur($utilisateur);
    }
} elseif ($action == 'modifier') {
    $statut = lireChamp('statut');
    $autre = $repository->getUtilisateurParMail($mail);
    // Le mail ne doit pas appartenir à un autre utilisateur
    $mailLibre = ($autre == null || $autre->getIdUtilisateur() == $id);
    $statutValide = ($statut == 'valide' || $statut == 'refuse' || $statut == 'en_attente');

    if (!empty($nom) && !empty($prenom) && !empty($mail) && $mailLibre && $statutValide) {
        $utilisateur = new Utilisateur_Malik();
        $utilisateur->setIdUtilisateur($id);
        $utilisateur->setNom($nom);
        $utilisateur->setPrenom($prenom);
        $utilisateur->setEmail($mail);
        $utilisateur->setTelephone($telephone);
        $utilisateur->setRole($role);
        $utilisateur->setStatutValidation($statut);
        $repository->modifierUtilisateur($utilisateur);
    }
} elseif ($action == 'activer' || $action == 'desactiver') {
    // Un gestionnaire ne peut pas désactiver son propre compte (il se bloquerait l'accès)
    if ($action == 'desactiver' && $id == $_SESSION['id_utilisateur']) {
        $_SESSION['admin_erreur'] = "Vous ne pouvez pas désactiver votre propre compte.";
    } else {
        $utilisateur = $repository->getUtilisateurParId($id);
        if ($utilisateur != null) {
            if ($action == 'activer') {
                $utilisateur->setStatutValidation('valide');
            } else {
                $utilisateur->setStatutValidation('refuse');
            }
            $repository->modifierUtilisateur($utilisateur);
        }
    }
} elseif ($action == 'supprimer') {
    // La suppression échoue si l'utilisateur est l'auteur d'une offre ou d'un message du forum
    try {
        $repository->supprimerUtilisateur($id);
    } catch (PDOException $e) {
        $_SESSION['admin_erreur'] = "Impossible de supprimer cet utilisateur : il est l'auteur d'offres ou de messages.";
    }
}

header('Location: ../../public/Page_Admin/page_admin.php');
exit;