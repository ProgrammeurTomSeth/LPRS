<?php

// Modèle : représente une ligne de la table formation
class formation_anais{
    private $id_formation;
    private $nom_formation;
    private $type_formation;
    private $description;

    public function getIdFormation(){
        return $this->id_formation;
    }

    public function setIdFormation($id_formation){
        $this->id_formation = $id_formation;
    }

    public function getNomFormation(){
        return $this->nom_formation;
    }

    public function setNomFormation($nom_formation){
        $this->nom_formation = $nom_formation;
    }

    public function getTypeFormation(){
        return $this->type_formation;
    }

    public function setTypeFormation($type_formation){
        $this->type_formation = $type_formation;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($description){
        $this->description = $description;
    }
}
