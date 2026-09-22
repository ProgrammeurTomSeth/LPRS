<?php

class formation_anais{
    private $id_formation;
    private $nom_formation;
    private $type_formation;

    public function __construct($id_formation, $nom_formation, $type_formation){
        $this->id_formation = $id_formation;
        $this->nom_formation = $nom_formation;
        $this->type_formation = $type_formation;
    }
    public function getIdformation(){
        return $this->id_formation;
    }
    public function getNom_formation(){
        return $this->nom_formation;
    }
    public function getType_formation(){
        return $this->type_formation;
    }
    public function setId_formation($id_formation){
        $this->id_formation = $id_formation;
        return $this;
    }
    public function setNom_formation($nom_formation){
        $this->nom_formation = $nom_formation;
        return $this;
    }
    public function setType_formation($type_formation){
        $this->type_formation = $type_formation;
        return $this;
    }


}