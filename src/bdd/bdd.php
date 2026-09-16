<?php
class bdd {
    private $connexionBdd;
    private $identifiant = "root";
    private $motDePasse ="";
    private $nomBdd = "LPRS";
    private $host = "localhost";

    public function __construct()
    {
        $this->connexionBdd = new PDO("mysql:host=".$this->host.";dbname=".$this->nomBdd, $this->identifiant, $this->motDePasse);
    }


    public function getConnexionBdd(){
        return $this->connexionBdd;
    }



}