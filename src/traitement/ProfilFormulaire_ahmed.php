<?php

// Prépare les données affichées par public/profil/profil_utilisateur.php

require_once __DIR__ . '/SessionUtilisateur_ahmed.php';
require_once __DIR__ . '/../repository/InscriptionRepository_ahmed.php';
require_once __DIR__ . '/../repository/Formation_ahmed.php';
require_once __DIR__ . '/../repository/EntrepriseRepository_Tom.php';

if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$roles = array(
    'etudiant' => 'Étudiant',
    'alumni' => 'Ancien élève (alumni)',
    'partenaire' => 'Partenaire entreprise',
    'professeur' => 'Professeur',
    'gestionnaire' => 'Gestionnaire',
);

$erreur = null;
$utilisateur = null;
$formation = null;
$entreprise = null;
try {
    $utilisateur = (new InscriptionRepository_ahmed())->getUtilisateurParId($_SESSION['id_utilisateur']);
    if ($utilisateur !== null && $utilisateur->getIdFormation() !== null) {
        $formation = (new Formation_ahmed())->getFormation($utilisateur->getIdFormation());
    }
    if ($utilisateur !== null && $utilisateur->getIdEntreprise() !== null) {
        $entreprise = (new EntrepriseRepository_Tom())->getEntreprise($utilisateur->getIdEntreprise());
    }
} catch (PDOException $e) {
    $erreur = "Impossible de charger votre profil pour le moment.";
}

// Compte supprimé entre-temps : on ferme la session
if ($erreur === null && $utilisateur === null) {
    header('Location: ../../src/traitement/DeconnexionTraitement_ahmed.php');
    exit;
}

// Lignes affichées sur le profil (les champs vides sont ignorés)
$infos = array();
if ($utilisateur !== null) {
    $infos = array(
        'Email' => $utilisateur->getEmail(),
        'Téléphone' => $utilisateur->getTelephone(),
        'Date de naissance' => $utilisateur->getDateNaissance() ? date('d/m/Y', strtotime($utilisateur->getDateNaissance())) : null,
        'Formation' => $formation !== null ? $formation->getNom_formation() : null,
        'Année de promotion' => $utilisateur->getAnneePromo(),
        'Spécialité' => $utilisateur->getSpecialite(),
        'Entreprise' => $entreprise !== null ? $entreprise->getNomEntreprise() : null,
        'Poste' => $utilisateur->getPoste(),
        'Membre depuis' => $utilisateur->getDateInscription() ? date('d/m/Y', strtotime($utilisateur->getDateInscription())) : null,
    );
    $infos = array_filter($infos, function ($valeur) {
        return $valeur !== null && $valeur !== '';
    });
}

function e($valeur){
    return htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
}
