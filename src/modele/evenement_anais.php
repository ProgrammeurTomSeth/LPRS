<?php

class evenement_anais{
    private $id_evenement;
    private $type_evenement;
    private $description;
    private $lieu;
    private $element_requis;
    private $nb_place;
    private $date_evenement;

    public function __construct($id_evenement, $type_evenement, $description, $lieu, $element_requis, $nb_place, $date_evenement ){
        $this->id_evenement = $id_evenement;
        $this->type_evenement = $type_evenement;
        $this->description = $description;
        $this->lieu = $lieu;
        $this->element_requis = $element_requis;
        $this->nb_place = $nb_place;
        $this->date_evenement = $date_evenement;
    }

    public function getIdEvenement(){
        return $this->id_evenement;
    }
    public function setIdEvenement($id_evenement){
        $this->id_evenement = $id_evenement;
    }
    public function getTypeEvenement(){
        return $this->type_evenement;
    }

    public function setTypeEvenement($type_evenement){
        $this->type_evenement = $type_evenement;
    }
    public function getDescription(){
        return $this->description;
    }
    public function setDescription($description){
        $this->description = $description;
    }
    public function getLieu(){
        return $this->lieu;
    }
    public function setLieu($lieu){
        $this->lieu = $lieu;
    }
    public function getElementRequis(){
        return $this->element_requis;
    }
    public function setElementRequis($element_requis){
        $this->element_requis = $element_requis;
    }
    public function getNbPlace(){
        return $this->nb_place;
    }
    public function setNbPlace($nb_place){
        $this->nb_place = $nb_place;
    }
    public function getDateEvenement(){
        return $this->date_evenement;
    }
}
