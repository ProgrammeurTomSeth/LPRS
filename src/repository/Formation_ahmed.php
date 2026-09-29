<?php

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/Formation_anais.php';

class Formation_ahmed{
    private $connexionbdd;

    public function __construct(){
        $this->connexionbdd = (new bdd())->getConnexionBdd();
    }

    public function getFormation($id_formation){
        $sql = "SELECT * FROM formation WHERE id_formation = :id_formation";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_formation', $id_formation, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return $this->createFormation($result);
    }

    public function getFormationParNom($nom_formation){
        $sql = "SELECT * FROM formation WHERE nom_formation = :nom_formation";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':nom_formation', $nom_formation);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return $this->createFormation($result);
    }

    public function getAllFormations(){
        $sql = "SELECT * FROM formation ORDER BY nom_formation ASC";
        $req = $this->connexionbdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $formations = array();
        foreach ($results as $result) {
            $formations[] = $this->createFormation($result);
        }
        return $formations;
    }

    public function getFormationsParType($type_formation){
        $sql = "SELECT * FROM formation WHERE type_formation = :type_formation ORDER BY nom_formation ASC";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':type_formation', $type_formation);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $formations = array();
        foreach ($results as $result) {
            $formations[] = $this->createFormation($result);
        }
        return $formations;
    }

    public function getFormationsParIntervenant($id_utilisateur){
        $sql = "SELECT f.* FROM formation f INNER JOIN intervient_formation i ON i.id_formation = f.id_formation WHERE i.id_utilisateur = :id_utilisateur ORDER BY f.nom_formation ASC";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $formations = array();
        foreach ($results as $result) {
            $formations[] = $this->createFormation($result);
        }
        return $formations;
    }

    public function ajouterFormation(formation_anais $formation){
        $sql = "INSERT INTO formation (nom_formation, type_formation) VALUES (:nom_formation, :type_formation)";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':nom_formation', $formation->getNom_formation());
        $req->bindValue(':type_formation', $formation->getType_formation());
        $req->execute();
        $formation->setId_formation($this->connexionbdd->lastInsertId());
        return $formation;
    }

    public function modifierFormation(formation_anais $formation){
        $sql = "UPDATE formation SET nom_formation = :nom_formation, type_formation = :type_formation WHERE id_formation = :id_formation";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':nom_formation', $formation->getNom_formation());
        $req->bindValue(':type_formation', $formation->getType_formation());
        $req->bindValue(':id_formation', $formation->getIdformation(), PDO::PARAM_INT);
        return $req->execute();
    }

    public function supprimerFormation($id_formation){
        $sql = "DELETE FROM formation WHERE id_formation = :id_formation";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_formation', $id_formation, PDO::PARAM_INT);
        return $req->execute();
    }

    public function ajouterIntervenant($id_formation, $id_utilisateur){
        $sql = "INSERT INTO intervient_formation (id_utilisateur, id_formation) VALUES (:id_utilisateur, :id_formation)";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $req->bindValue(':id_formation', $id_formation, PDO::PARAM_INT);
        return $req->execute();
    }

    public function retirerIntervenant($id_formation, $id_utilisateur){
        $sql = "DELETE FROM intervient_formation WHERE id_utilisateur = :id_utilisateur AND id_formation = :id_formation";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $req->bindValue(':id_formation', $id_formation, PDO::PARAM_INT);
        return $req->execute();
    }

    private function createFormation($result){
        return new formation_anais(
            $result['id_formation'],
            $result['nom_formation'],
            $result['type_formation']
        );
    }
}
