<?php

// Affiche le CV d'un candidat à l'auteur de l'offre et aux membres de son entreprise.
// Les CV sont rangés dans uploads/cv/, hors du dossier public : on ne peut les lire qu'en passant par ce fichier,
// qui vérifie d'abord les droits.

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/OffreRepository_Tom.php';

function refuserAcces($code, $texte)
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $texte;
    exit;
}

if (!estConnecte()) {
    header('Location: ../connexion/connexion.php');
    exit;
}

$idOffre = (isset($_GET['offre']) && ctype_digit((string) $_GET['offre'])) ? (int) $_GET['offre'] : 0;
$idCandidat = (isset($_GET['candidat']) && ctype_digit((string) $_GET['candidat'])) ? (int) $_GET['candidat'] : 0;

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null || !in_array($utilisateur->getRole(), array('partenaire', 'alumni'), true) || $utilisateur->getStatutValidation() != 'valide' || empty($utilisateur->getIdEntreprise())) {
    refuserAcces(403, 'Accès refusé.');
}

// L'offre doit être celle de son entreprise, et la personne doit avoir postulé à cette offre
$offreRepository = new OffreRepository_Tom();
if (!$offreRepository->getOffreEntreprise($idOffre, $utilisateur->getIdEntreprise()) || !$offreRepository->getCandidature($idOffre, $idCandidat)) {
    refuserAcces(403, 'Accès refusé.');
}

$candidat = $utilisateurRepository->getUtilisateurParId($idCandidat);
if ($candidat == null || empty($candidat->getCv())) {
    refuserAcces(404, 'Ce candidat n\'a pas déposé de CV.');
}

// basename() : le nom enregistré en base ne peut jamais pointer en dehors du dossier des CV
$fichier = __DIR__ . '/../../uploads/cv/' . basename($candidat->getCv());
if (!is_file($fichier)) {
    refuserAcces(404, 'Le fichier du CV est introuvable.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="cv.pdf"');
header('Content-Length: ' . filesize($fichier));
header('X-Content-Type-Options: nosniff');
readfile($fichier);
