<?php

require_once 'SessionUtilisateur_ahmed.php';
require_once '../bdd/bdd.php';
require_once '../modele/Offre_Tom.php';
require_once '../repository/OffreRepository_Tom.php';
require_once '../modele/Utilisateur_Malik.php';
require_once '../repository/UtilisateurRepository_malik.php';
require_once '../mail/Mailer_ahmed.php';

if (!estConnecte()) {
    $resultat = 'connexion';
} elseif ($_SESSION['role'] != 'etudiant' && $_SESSION['role'] != 'alumni') {
    $resultat = 'profil';
} elseif ($_SERVER["REQUEST_METHOD"] != "POST") {
    $resultat = 'erreur';
} else {
    $idOffre = (int) lireChamp('id_offre');
    $motivation = lireChamp('motivation');
    $idUtilisateur = $_SESSION['id_utilisateur'];

    $repository = new OffreRepository_Tom();
    $offre = $repository->getOffre($idOffre);

    if ($offre == null || $offre->getStatut() != 'ouverte') {
        $resultat = 'indisponible';
    } elseif (empty($motivation)) {
        $resultat = 'motivation';
    } elseif ($offre->getProfilCible() != 'tous' && $offre->getProfilCible() != $_SESSION['role']) {
        $resultat = 'profil_offre';
    } elseif ($repository->aDejaPostule($idOffre, $idUtilisateur)) {
        $resultat = 'deja_postule';
    } else {
        $repository->ajouterCandidature($idOffre, $idUtilisateur, $motivation);
        $resultat = 'succes';

        // Email à l'auteur de l'offre. Si l'envoi échoue, la candidature reste enregistrée.
        try {
            $utilisateurRepository = new UtilisateurRepository_malik();
            $auteur = $utilisateurRepository->getUtilisateurParId($offre->getIdUtilisateurAuteur());
            $candidat = $utilisateurRepository->getUtilisateurParId($idUtilisateur);
            if ($auteur != null && $candidat != null) {
                $mailer = new Mailer_ahmed();
                $mailer->envoyerNouvelleCandidature($auteur, $offre, $candidat);
            }
        } catch (Exception $e) {
            error_log("Email de candidature non envoyé : " . $e->getMessage());
        }
    }
}

header('Location: ../../public/offre/liste_offres_Tom.php?resultat=' . $resultat);
exit;
