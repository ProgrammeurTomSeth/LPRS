<?php

require_once __DIR__ . '/../bdd/bdd.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function redirigerVersEvenements($resultat) {
    header('Location: ../../public/evenement/liste_evenement_Tom.php?resultat=' . urlencode($resultat));
    exit;
}

if (!isset($_SESSION['id_utilisateur'])) {
    redirigerVersEvenements('connexion');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirigerVersEvenements('erreur');
}

$idEvenement = $_POST['id_evenement'] ?? '';

if (!is_scalar($idEvenement) || !ctype_digit((string) $idEvenement) || (int) $idEvenement <= 0) {
    redirigerVersEvenements('indisponible');
}

$idEvenement = (int) $idEvenement;
$idUtilisateur = (int) $_SESSION['id_utilisateur'];

try {
    $pdo = (new bdd())->getConnexionBdd();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->beginTransaction();
    $requete = $pdo->prepare("SELECT id_evenement, nb_places FROM evenement WHERE id_evenement = :id_evenement AND statut = 'publie' FOR UPDATE");

    $requete->execute([
        ':id_evenement' => $idEvenement
    ]);

    $evenement = $requete->fetch(PDO::FETCH_ASSOC);
    if (!$evenement) {
        $pdo->rollBack();
        redirigerVersEvenements('indisponible');
    }

    $requete = $pdo->prepare("SELECT COUNT(*) FROM inscription_evenement WHERE id_utilisateur = :id_utilisateur AND id_evenement = :id_evenement");
    $requete->execute([
        ':id_utilisateur' => $idUtilisateur,
        ':id_evenement' => $idEvenement
    ]);

    if ((int) $requete->fetchColumn() > 0) {
        $pdo->rollBack();
        redirigerVersEvenements('deja_inscrit');
    }

    $requete = $pdo->prepare("SELECT COUNT(*) FROM inscription_evenement WHERE id_evenement = :id_evenement AND statut_inscription = 'acceptee'");
    $requete->execute([
        ':id_evenement' => $idEvenement
    ]);
    $nombreInscrits = (int) $requete->fetchColumn();

    if ($evenement['nb_places'] !== null && $nombreInscrits >= (int) $evenement['nb_places']
    ) {
        $pdo->rollBack();
        redirigerVersEvenements('complet');
    }

    $requete = $pdo->prepare("INSERT INTO inscription_evenement (id_utilisateur, id_evenement, statut_inscription) VALUES (:id_utilisateur, :id_evenement, 'acceptee')");
    $requete->execute([
        ':id_utilisateur' => $idUtilisateur,
        ':id_evenement' => $idEvenement
    ]);
    $pdo->commit();
    redirigerVersEvenements('succes');
} catch (PDOException $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Erreur inscription événement : ' . $e->getMessage());
    redirigerVersEvenements('erreur');
}