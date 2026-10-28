<?php

// Classe de connexion à la base de données (cours PDO)
class Bdd
{
    private $connexionBdd;

    public function __construct()
    {
        // Paramètres du poste de développement (WAMP)
        $host = "localhost";
        $nomBdd = "lprs";
        $identifiant = "root";
        $motDePasse = "";

        // Sur le serveur, le fichier config.local.php (jamais envoyé sur Git)
        // contient un tableau associatif qui remplace ces valeurs
        if (file_exists(__DIR__ . "/config.local.php")) {
            $config = require __DIR__ . "/config.local.php";
            $host = $config['host'];
            $nomBdd = $config['nomBdd'];
            $identifiant = $config['identifiant'];
            $motDePasse = $config['motDePasse'];
        }

        try {
            $this->connexionBdd = new PDO("mysql:host=" . $host . ";dbname=" . $nomBdd . ";charset=utf8mb4", $identifiant, $motDePasse);
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            exit;
        }
    }

    public function getConnexionBdd()
    {
        return $this->connexionBdd;
    }
}
