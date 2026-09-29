<?php
class bdd {
    private $connexionBdd;
    private $identifiant = "admin";
    private $motDePasse ="1234";
    private $nomBdd = "utilisateur";
    private $host = "localhost";

    public function __construct()
    {
        $this->connexionBdd = new PDO("mysql:host=".$this->host.";dbname=".$this->nomBdd, $this->identifiant, $this->motDePasse);
    }

    public function getConnexionBdd(){
        return $this->connexionBdd;
    }



}