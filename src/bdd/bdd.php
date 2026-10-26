<?php
class bdd {
    private $connexionBdd;
    // Valeurs par défaut : poste de développement (WAMP)
    private $identifiant = "root";
    private $motDePasse = "";
    private $nomBdd = "lprs";
    private $host = "localhost";

    public function __construct()
    {
        // Sur un serveur, les paramètres sont lus dans config.local.php
        // (fichier propre à chaque machine, jamais envoyé sur Git).
        $fichierConfig = __DIR__ . '/config.local.php';
        if (file_exists($fichierConfig)) {
            $config = require $fichierConfig;
            $this->host        = $config['host']        ?? $this->host;
            $this->nomBdd      = $config['nomBdd']      ?? $this->nomBdd;
            $this->identifiant = $config['identifiant'] ?? $this->identifiant;
            $this->motDePasse  = $config['motDePasse']  ?? $this->motDePasse;
        }

        $this->connexionBdd = new PDO(
            "mysql:host=" . $this->host . ";dbname=" . $this->nomBdd . ";charset=utf8mb4",
            $this->identifiant,
            $this->motDePasse,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public function getConnexionBdd(){
        return $this->connexionBdd;
    }
}
