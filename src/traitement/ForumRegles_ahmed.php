<?php

// Règles et fonctions communes du forum (cahier des charges, partie Forum).
// Fichier inclus par les pages du forum, la page de modération ET les traitements,
// pour que les mêmes règles soient vérifiées à l'affichage et à l'enregistrement.

// Nom affiché de chaque canal
$canauxForum = array(
    'general' => 'Général',
    'alumni_entreprises' => 'Alumni et entreprises',
    'etudiants' => 'Étudiants'
);

// Phrase d'explication affichée en haut de chaque canal
$descriptionsCanaux = array(
    'general' => 'Ouvert à tous les membres : chacun peut répondre aux sujets.',
    'alumni_entreprises' => 'Espace réservé aux anciens élèves et aux entreprises partenaires.',
    'etudiants' => 'Espace des étudiants et des professeurs : les professeurs lancent les sujets, les étudiants y répondent.'
);

// Nom affiché de chaque rôle (à côté de l'auteur)
$rolesForum = array(
    'etudiant' => 'Étudiant',
    'alumni' => 'Alumni',
    'partenaire' => 'Partenaire',
    'professeur' => 'Professeur',
    'gestionnaire' => 'Modération'
);

// Motifs proposés dans le formulaire de signalement (valeurs de l'enum en base)
$motifsSignalement = array(
    'spam' => 'Spam ou publicité',
    'insulte' => 'Propos insultants ou harcèlement',
    'donnees_personnelles' => 'Données personnelles (téléphone, adresse...)',
    'hors_sujet' => 'Hors sujet',
    'autre' => 'Autre raison'
);

// Renvoie true si le rôle peut lire le canal et répondre aux posts.
// Le gestionnaire voit tous les canaux pour pouvoir modérer.
function peutAccederCanal($canal, $role)
{
    $rolesAutorises = array(
        'general' => array('etudiant', 'alumni', 'partenaire', 'professeur', 'gestionnaire'),
        'alumni_entreprises' => array('alumni', 'partenaire', 'gestionnaire'),
        'etudiants' => array('etudiant', 'professeur', 'gestionnaire')
    );

    if (!isset($rolesAutorises[$canal])) {
        return false;
    }
    return in_array($role, $rolesAutorises[$canal]);
}

// Renvoie true si le rôle peut créer un sujet (dans au moins un canal).
// Un élève ne crée pas de sujet : il peut seulement commenter et répondre.
function peutCreerSujet($role)
{
    return $role != 'etudiant';
}

// Renvoie true si le rôle peut créer un nouveau sujet dans ce canal :
// il faut avoir le droit de créer un sujet ET avoir accès au canal
function peutCreerPost($canal, $role)
{
    if (!peutCreerSujet($role)) {
        return false;
    }
    return peutAccederCanal($canal, $role);
}

// Seul le gestionnaire est modérateur : lui seul peut supprimer un message
// et traiter les signalements (un membre ne peut pas supprimer ce qu'il a envoyé)
function estModerateur($role)
{
    return $role == 'gestionnaire';
}

// Un membre peut signaler le message d'un autre membre.
// Le gestionnaire n'a pas besoin de signaler : il supprime directement.
function peutSignaler($idAuteur, $idConnecte, $role)
{
    return $idAuteur != $idConnecte && !estModerateur($role);
}

// Texte « il y a ... » à partir du nombre de minutes écoulées
// (calculé par MySQL avec TIMESTAMPDIFF) ; au-delà d'une semaine on affiche la date
function tempsEcoule($minutes, $dateFr)
{
    if ($minutes < 1) {
        return "à l'instant";
    }
    if ($minutes < 60) {
        return "il y a " . $minutes . " min";
    }
    if ($minutes < 60 * 24) {
        $heures = floor($minutes / 60);
        return "il y a " . $heures . " heure" . ($heures > 1 ? "s" : "");
    }
    if ($minutes < 60 * 24 * 7) {
        $jours = floor($minutes / (60 * 24));
        return "il y a " . $jours . " jour" . ($jours > 1 ? "s" : "");
    }
    return "le " . $dateFr;
}

// Initiales affichées dans le rond de l'avatar (mb_ : gère les accents)
function initiales($prenom, $nom)
{
    return mb_strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}

// Couleur de l'avatar : toujours la même pour un utilisateur (6 couleurs dans forum.css)
function classeAvatar($idUtilisateur)
{
    return 'avatar--' . ($idUtilisateur % 6);
}
