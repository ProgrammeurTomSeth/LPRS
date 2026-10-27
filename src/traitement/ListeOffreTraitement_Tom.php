<?php

require_once __DIR__ . '/SessionUtilisateur_ahmed.php';
require_once __DIR__ . '/../bdd/bdd.php';

$pdo = (new bdd())->getConnexionBdd();

$offres = [];
$message = '';
$typeMessage = '';
$idUtilisateur = estConnecte() ? (int) ($_SESSION['id_utilisateur'] ?? 0) : 0;
$roleUtilisateur = $_SESSION['role'] ?? '';
$candidaturesUtilisateur = [];

$messages = [
    'succes' => [
        'type' => 'succes',
        'texte' => 'Votre candidature a bien été enregistrée.'
    ],
    'connexion' => [
        'type' => 'erreur',
        'texte' => 'Vous devez être connecté pour postuler.'
    ],
    'profil' => [
        'type' => 'erreur',
        'texte' => 'Seuls les étudiants et les alumni peuvent postuler.'
    ],
    'profil_offre' => [
        'type' => 'erreur',
        'texte' => 'Cette offre ne correspond pas à votre profil.'
    ],
    'deja_postule' => [
        'type' => 'erreur',
        'texte' => 'Vous avez déjà postulé à cette offre.'
    ],
    'indisponible' => [
        'type' => 'erreur',
        'texte' => 'Cette offre n’est plus disponible.'
    ],
    'motivation' => [
        'type' => 'erreur',
        'texte' => 'Veuillez saisir votre motivation.'
    ],
    'erreur' => [
        'type' => 'erreur',
        'texte' => 'Une erreur est survenue. Veuillez réessayer.'
    ]
];

$resultat = $_GET['resultat'] ?? '';

if (isset($messages[$resultat])) {
    $typeMessage = $messages[$resultat]['type'];
    $message = $messages[$resultat]['texte'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!estConnecte()) {
        header('Location: liste_offres_Tom.php?resultat=connexion');
        exit;
    }

    if (!in_array($roleUtilisateur, ['etudiant', 'alumni'], true)) {
        header('Location: liste_offres_Tom.php?resultat=profil');
        exit;
    }

    $idOffrePostulee = $_POST['id_offre'] ?? '';
    $motivation = trim($_POST['motivation'] ?? '');

    if (
        !is_scalar($idOffrePostulee) ||
        !ctype_digit((string) $idOffrePostulee) ||
        (int) $idOffrePostulee <= 0
    ) {
        header('Location: liste_offres_Tom.php?resultat=indisponible');
        exit;
    }

    if ($motivation === '') {
        header('Location: liste_offres_Tom.php?resultat=motivation');
        exit;
    }

    $idOffrePostulee = (int) $idOffrePostulee;

    try {
        $pdo->beginTransaction();

        $requete = $pdo->prepare(
            "SELECT id_offre, profil_cible
             FROM offre
             WHERE id_offre = :id_offre
             AND statut = 'ouverte'
             FOR UPDATE"
        );

        $requete->execute([
            ':id_offre' => $idOffrePostulee
        ]);

        $offre = $requete->fetch(PDO::FETCH_ASSOC);

        if (!$offre) {
            $pdo->rollBack();

            header('Location: liste_offres_Tom.php?resultat=indisponible');
            exit;
        }

        if (
            $offre['profil_cible'] !== 'tous' &&
            $offre['profil_cible'] !== $roleUtilisateur
        ) {
            $pdo->rollBack();

            header('Location: liste_offres_Tom.php?resultat=profil_offre');
            exit;
        }

        $requete = $pdo->prepare(
            "SELECT COUNT(*)
             FROM candidature
             WHERE id_utilisateur = :id_utilisateur
             AND id_offre = :id_offre"
        );

        $requete->execute([
            ':id_utilisateur' => $idUtilisateur,
            ':id_offre' => $idOffrePostulee
        ]);

        if ((int) $requete->fetchColumn() > 0) {
            $pdo->rollBack();

            header('Location: liste_offres_Tom.php?resultat=deja_postule');
            exit;
        }

        $requete = $pdo->prepare(
            "INSERT INTO candidature
             (id_utilisateur, id_offre, motivation)
             VALUES (:id_utilisateur, :id_offre, :motivation)"
        );

        $requete->execute([
            ':id_utilisateur' => $idUtilisateur,
            ':id_offre' => $idOffrePostulee,
            ':motivation' => $motivation
        ]);

        $pdo->commit();

        header('Location: liste_offres_Tom.php?resultat=succes');
        exit;

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log('Erreur candidature : ' . $e->getMessage());

        if ($e->getCode() === '23000') {
            header('Location: liste_offres_Tom.php?resultat=deja_postule');
            exit;
        }

        header('Location: liste_offres_Tom.php?resultat=erreur');
        exit;
    }
}

try {
    $requete = $pdo->prepare(
        "SELECT
            o.id_offre,
            o.titre,
            o.description,
            o.missions,
            o.salaire,
            o.type,
            o.profil_cible,
            o.date_publication,
            e.nom_entreprise
         FROM offre o
         LEFT JOIN entreprise e
            ON e.id_entreprise = o.ref_entreprise
         WHERE o.statut = 'ouverte'
         ORDER BY o.date_publication DESC"
    );

    $requete->execute();
    $offres = $requete->fetchAll(PDO::FETCH_ASSOC);

    if (estConnecte()) {
        $requete = $pdo->prepare(
            "SELECT id_offre
             FROM candidature
             WHERE id_utilisateur = :id_utilisateur"
        );

        $requete->execute([
            ':id_utilisateur' => $idUtilisateur
        ]);

        $candidaturesUtilisateur = array_map(
            'intval',
            $requete->fetchAll(PDO::FETCH_COLUMN)
        );
    }

} catch (PDOException $e) {
    error_log('Erreur chargement offres : ' . $e->getMessage());
    $message = 'Impossible de charger les offres pour le moment.';
    $typeMessage = 'erreur';
}