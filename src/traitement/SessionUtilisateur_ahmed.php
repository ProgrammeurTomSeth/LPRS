<?php

// Gestion de la session utilisateur commune à toutes les pages publiques

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estConnecte(){
    return isset($_SESSION['id_utilisateur']);
}

// Affiche les boutons de droite de la barre de navigation.
// $racine : chemin relatif de la page vers le dossier public/ (ex : '../' ou '')
function afficherActionsHeader($racine){
    $racine = htmlspecialchars($racine, ENT_QUOTES, 'UTF-8');
    if (estConnecte()) {
        echo '<a href="' . $racine . '../src/traitement/DeconnexionTraitement_ahmed.php" class="btn btn-outline header-login">Déconnexion</a>' . "\n";
        echo '            <a href="' . $racine . 'profil/profil_utilisateur.php" class="btn btn-primary header-cta">Profil</a>' . "\n";
    } else {
        echo '<a href="' . $racine . 'connexion/connexion.php" class="btn btn-outline header-login">Connexion</a>' . "\n";
        echo '            <a href="' . $racine . 'inscription/inscription.php" class="btn btn-primary header-cta">Inscription Rapide</a>' . "\n";
    }
}
