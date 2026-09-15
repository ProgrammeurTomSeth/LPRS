<?php

class Entreprise_Tom{
    private PDO $pdo;
    public function _construct(){
        $this->pdo = new PDO('mysql:host=localhost;dbname=lprs;charset=utf8mb4', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection(): PDO{
        return $this->pdo;
    }
}