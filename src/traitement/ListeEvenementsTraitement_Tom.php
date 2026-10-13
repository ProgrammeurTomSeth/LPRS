<?php

require_once __DIR__ . '/SessionUtilisateur_ahmed.php';
require_once __DIR__ . '/../bdd/bdd.php';

$idUtilisateur = estConnecte() ? (int) $_SESSION['id_utilisateur'] : 0;
$message = '';
$typeMessage = '';
$resultat = $_GET['resultat'] ?? '';
$messages = [
    'succes' => [
        'type' => 'succes',
        'texte' => 'Votre inscription a bien été enregistrée.'
    ],
    'connexion' => [
        'type' => 'erreur',
        'texte' => 'Vous devez être connecté pour vous inscrire.'
    ],
    'deja_inscrit' => [
        'type' => 'erreur',
        'texte' => 'Vous êtes déjà inscrit à cet événement.'
    ],
    'complet' => [
        'type' => 'erreur',
        'texte' => 'Cet événement est complet.'
    ],
    'indisponible' => [
        'type' => 'erreur',
        'texte' => 'Cet événement est indisponible.'
    ],
    'erreur' => [
        'type' => 'erreur',
        'texte' => 'Une erreur est survenue. Veuillez réessayer.'
    ]
];
if (isset($messages[$resultat])) {
    $typeMessage = $messages[$resultat]['type'];
    $message = $messages[$resultat]['texte'];
}

$evenements = [];
try {
    $pdo = (new bdd())->getConnexionBdd();
    $sql = "SELECT e.id_evenement, e.type_evenement, e.titre, e.description, e.lieu, e.elements_requis, e.nb_places, e.date_evenement, COUNT(ie.id_utilisateur) AS nombre_inscrits, COALESCE(MAX(CASE WHEN ie.id_utilisateur = :id_utilisateur THEN 1 ELSE 0 END), 0) AS deja_inscrit FROM evenement e LEFT JOIN inscription_evenement ie ON ie.id_evenement = e.id_evenement AND ie.statut_inscription = 'acceptee' WHERE e.statut = 'publie' GROUP BY e.id_evenement, e.type_evenement, e.titre, e.description, e.lieu, e.elements_requis, e.nb_places, e.date_evenement ORDER BY e.date_evenement ASC";
    $requete = $pdo->prepare($sql);
    $requete->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
    $requete->execute();
    $evenements = $requete->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Erreur lors du chargement des événements : ' . $e->getMessage());
    $message = 'Impossible de charger les événements pour le moment.';
    $typeMessage = 'erreur';
}