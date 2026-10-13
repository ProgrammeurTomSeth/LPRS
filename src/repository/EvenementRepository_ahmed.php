<?php

require_once __DIR__ . '/../bdd/bdd.php';

class EvenementRepository_ahmed{
    private $connexionbdd;

    public function __construct(){
        $this->connexionbdd = (new bdd())->getConnexionBdd();
    }

    // Événements dont l'utilisateur fait partie des organisateurs
    public function getEvenementsOrganises($id_utilisateur){
        $sql = "SELECT e.* FROM evenement e INNER JOIN organise_evenement o ON o.id_evenement = e.id_evenement WHERE o.id_utilisateur = :id_utilisateur ORDER BY e.date_evenement DESC";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Événements auxquels l'utilisateur est inscrit
    public function getEvenementsInscrits($id_utilisateur){
        $sql = "SELECT e.*, i.date_inscription, i.statut_inscription FROM evenement e INNER JOIN inscription_evenement i ON i.id_evenement = e.id_evenement WHERE i.id_utilisateur = :id_utilisateur ORDER BY e.date_evenement DESC";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}
