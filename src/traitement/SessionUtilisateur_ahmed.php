<?php

// Fichier inclus en premier par toutes les pages et tous les traitements :
// il démarre la session et contient les fonctions communes.
session_start();

// Renvoie true si un utilisateur est connecté
function estConnecte()
{
    return isset($_SESSION['id_utilisateur']);
}

// Renvoie la valeur d'un champ envoyé en POST, ou null s'il est absent ou vide
// ($_POST est superglobal : il est visible à l'intérieur de la fonction)
function lireChamp($nom)
{
    if (isset($_POST[$nom]) && $_POST[$nom] != "") {
        return $_POST[$nom];
    }
    return null;
}

// Renvoie les initiales de l'utilisateur connecté (ex : "AB" pour Ahmed Benmechichi)
// mb_substr / mb_strtoupper gèrent les lettres accentuées (É, Ç...)
function initialesUtilisateur()
{
    $prenom = isset($_SESSION['prenom_utilisateur']) ? $_SESSION['prenom_utilisateur'] : '';
    $nom = isset($_SESSION['nom_utilisateur']) ? $_SESSION['nom_utilisateur'] : '';
    return mb_strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}

// Affiche les boutons à droite de la barre de navigation
// (toutes les pages sont dans public/<dossier>/, d'où les chemins en ../)
function afficherActionsHeader()
{
    if (estConnecte()) {
        // Nom complet pour l'infobulle et les lecteurs d'écran (le bouton n'affiche que les initiales)
        $nomComplet = htmlspecialchars($_SESSION['prenom_utilisateur'] . ' ' . $_SESSION['nom_utilisateur']);
        $initiales = htmlspecialchars(initialesUtilisateur());

        echo '<a href="../../src/traitement/DeconnexionTraitement_ahmed.php" class="btn btn-outline header-login">Déconnexion</a>';
        echo '<a href="../profil/profil_utilisateur.php" class="btn btn-primary header-cta header-profil" title="Mon profil (' . $nomComplet . ')" aria-label="Mon profil (' . $nomComplet . ')">';
        // Icône utilisateur (dessin SVG, aria-hidden : elle est décorative)
        echo '<svg class="header-profil-icone" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>';
        echo '<span class="header-profil-initiales">' . $initiales . '</span>';
        echo '</a>';
    } else {
        echo '<a href="../connexion/connexion.php" class="btn btn-outline header-login">Connexion</a>';
        echo '<a href="../inscription/inscription.php" class="btn btn-primary header-cta">Inscription</a>';
    }
}
