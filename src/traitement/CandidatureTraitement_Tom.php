<?php

// « Ne pas retenir » : l'auteur (ou un membre de l'entreprise) retire une candidature de la liste,
// puis le candidat reçoit un email.

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Offre_Tom.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../repository/OffreRepository_Tom.php';
require_once '../mail/Mailer_ahmed.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST' || !estConnecte()) {
    header('Location: ../../public/offre/mes_offres_Tom.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository_malik();
$utilisateur = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);

if ($utilisateur == null || !in_array($utilisateur->getRole(), array('partenaire', 'alumni'), true) || $utilisateur->getStatutValidation() != 'valide' || empty($utilisateur->getIdEntreprise())) {
    header('Location: ../../public/offre/liste_offres_Tom.php');
    exit;
}

if (!isset($_POST['csrf_token']) || !isset($_SESSION['mes_offres_csrf']) || !hash_equals($_SESSION['mes_offres_csrf'], $_POST['csrf_token'])) {
    header('Location: ../../public/offre/mes_offres_Tom.php?resultat=erreur');
    exit;
}

if (!isset($_POST['id_offre']) || !ctype_digit((string) $_POST['id_offre']) || !isset($_POST['id_candidat']) || !ctype_digit((string) $_POST['id_candidat'])) {
    header('Location: ../../public/offre/mes_offres_Tom.php?resultat=erreur');
    exit;
}

$idOffre = (int) $_POST['id_offre'];
$idCandidat = (int) $_POST['id_candidat'];

// Retour vers la liste des candidatures de l'offre, avec un résultat à afficher
function retourCandidatures($idOffre, $resultat)
{
    header('Location: ../../public/offre/candidats_offre_Tom.php?id=' . (int) $idOffre . '&resultat=' . $resultat);
    exit;
}

// L'offre doit appartenir à l'entreprise de l'utilisateur
$offreRepository = new OffreRepository_Tom();
if (!$offreRepository->getOffreEntreprise($idOffre, $utilisateur->getIdEntreprise())) {
    header('Location: ../../public/offre/mes_offres_Tom.php');
    exit;
}

// La candidature doit exister et ne pas avoir déjà été refusée
if (!$offreRepository->getCandidature($idOffre, $idCandidat) || !$offreRepository->masquerCandidature($idOffre, $idCandidat)) {
    retourCandidatures($idOffre, 'erreur');
}

// Email au candidat. Si l'envoi échoue, la candidature reste retirée de la liste.
$resultat = 'refusee';
try {
    $candidat = $utilisateurRepository->getUtilisateurParId($idCandidat);
    $offre = $offreRepository->getOffre($idOffre);
    if ($candidat != null && $offre != null) {
        $mailer = new Mailer_ahmed();
        if (!$mailer->envoyerCandidatureNonRetenue($candidat, $offre)) {
            $resultat = 'refusee_sans_email';
        }
    }
} catch (Exception $e) {
    error_log("Email de refus non envoyé : " . $e->getMessage());
    $resultat = 'refusee_sans_email';
}

retourCandidatures($idOffre, $resultat);
