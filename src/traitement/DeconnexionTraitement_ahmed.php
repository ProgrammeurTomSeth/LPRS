<?php

// Déconnexion : on supprime la session puis on revient à l'accueil

require_once 'SessionUtilisateur_ahmed.php';

session_destroy();

header('Location: ../../public/page_acceuille/page_accueil.php');
exit;
