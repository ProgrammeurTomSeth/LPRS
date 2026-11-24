<?php

class Formation_ahmed{
    private $connexionBdd;

    public function __construct(){
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    public function getFormation($id_formation){
        $req = $this->connexionBdd->prepare("SELECT * FROM formation WHERE id_formation = :id");
        $req->execute(array('id' => $id_formation));
        $ligne = $req->fetch();
        if ($ligne) {
            $formation = new formation_anais();
            $formation->setIdFormation($ligne['id_formation']);
            $formation->setNomFormation($ligne['nom_formation']);
            $formation->setTypeFormation($ligne['type_formation']);
            return $formation;
        }
        return null;
    }

    public function getAllFormations(){
        $req = $this->connexionBdd->prepare(
            "SELECT * FROM formation ORDER BY nom_formation ASC"
        );
        $req->execute();
        return $req->fetchAll();
    }

    public function nomFormationExiste($nom_formation, $id_formation = null){
        $sql = "SELECT COUNT(*) FROM formation WHERE LOWER(nom_formation) = LOWER(:nom)";
        if ($id_formation !== null) {
            $sql .= " AND id_formation != :id";
        }
        $req = $this->connexionBdd->prepare($sql);
        $parametres = array('nom' => trim($nom_formation));
        if ($id_formation !== null) {
            $parametres['id'] = $id_formation;
        }
        $req->execute($parametres);
        return $req->fetchColumn() > 0;
    }

    public function ajouterFormation($formation){
        $req = $this->connexionBdd->prepare("INSERT INTO formation (nom_formation, type_formation) VALUES (:nom, :type)");
        return $req->execute(array(
            'nom' => trim($formation->getNomFormation()),
            'type' => $formation->getTypeFormation()
        ));
    }

    public function modifierFormation($formation){
        $req = $this->connexionBdd->prepare("UPDATE formation SET nom_formation = :nom, type_formation = :type WHERE id_formation = :id");
        return $req->execute(array(
            'nom' => trim($formation->getNomFormation()),
            'type' => $formation->getTypeFormation(),
            'id' => $formation->getIdFormation()
        ));
    }

    public function supprimerFormation($id_formation){
        $req = $this->connexionBdd->prepare("DELETE FROM formation WHERE id_formation = :id");
        return $req->execute(array('id' => $id_formation));
    }
}