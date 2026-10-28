<?php

// Modèle : représente une ligne de la table evenement
class evenement_anais
{
    private $id_evenement;
    private $type_evenement;
    private $titre;
    private $description;
    private $lieu;
    private $elements_requis;
    private $nb_places;
    private $date_evenement;
    private $statut;

    public function getIdEvenement()
    {
        return $this->id_evenement;
    }

    public function setIdEvenement($id_evenement)
    {
        $this->id_evenement = $id_evenement;
    }

    public function getTypeEvenement()
    {
        return $this->type_evenement;
    }

    public function setTypeEvenement($type_evenement)
    {
        $this->type_evenement = $type_evenement;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getLieu()
    {
        return $this->lieu;
    }

    public function setLieu($lieu)
    {
        $this->lieu = $lieu;
    }

    public function getElementsRequis()
    {
        return $this->elements_requis;
    }

    public function setElementsRequis($elements_requis)
    {
        $this->elements_requis = $elements_requis;
    }

    public function getNbPlaces()
    {
        return $this->nb_places;
    }

    public function setNbPlaces($nb_places)
    {
        $this->nb_places = $nb_places;
    }

    public function getDateEvenement()
    {
        return $this->date_evenement;
    }

    public function setDateEvenement($date_evenement)
    {
        $this->date_evenement = $date_evenement;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut)
    {
        $this->statut = $statut;
    }
}
