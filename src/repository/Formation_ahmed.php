<?php

// Repository : toutes les requêtes SQL sur la table formation
class Formation_ahmed
{
    private $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Renvoie un objet formation_anais, ou null si l'id n'existe pas
    public function getFormation($id_formation)
    {
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

    // Renvoie le tableau de toutes les formations
    public function getAllFormations()
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM formation ORDER BY nom_formation ASC");
        $req->execute();
        return $req->fetchAll();
    }
}
