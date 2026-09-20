<?php
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Utilisateur.php';
class UtilisateurRepository_malik
{
    private $connexionbdd;
    public function __construct(){
        $this->connexionbdd = (new Bdd())->getConnexionbdd();
    }
}