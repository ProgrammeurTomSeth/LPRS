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

// Affiche les boutons à droite de la barre de navigation
// (toutes les pages sont dans public/<dossier>/, d'où les chemins en ../)
function afficherActionsHeader()
{
    if (estConnecte()) {
        echo '<a href="../../src/traitement/DeconnexionTraitement_ahmed.php" class="btn btn-outline header-login">Déconnexion</a>';
        echo '<a href="../profil/profil_utilisateur.php" class="btn btn-primary header-cta">Profil</a>';
    } else {
        echo '<a href="../connexion/connexion.php" class="btn btn-outline header-login">Connexion</a>';
        echo '<a href="../inscription/inscription.php" class="btn btn-primary header-cta">Inscription Rapide</a>';
    }
}
