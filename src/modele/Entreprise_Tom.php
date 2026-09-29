<?php

class Entreprise_Tom{
    private $id_entreprise;
    private $nom_entreprise;
    private $activite;
    private $adresse;
    private $site_web;

    public function __construct($id_entreprise, $nom_entreprise, $activite, $adresse, $site_web) {
        $this->id_entreprise = $id_entreprise;
        $this->nom_entreprise = $nom_entreprise;
        $this->activite = $activite;
        $this->adresse = $adresse;
        $this->site_web = $site_web;
    }

    public function getIdEntreprise(){
        return $this->id_entreprise;
    }

    public function setIdEntreprise($id_entreprise){
        $this->id_entreprise = $id_entreprise;
    }

    public function getNomEntreprise(){
        return $this->nom_entreprise;
    }

    public function setNomEntreprise($nom_entreprise){
        $this->nom_entreprise = $nom_entreprise;
    }

    public function getActivite(){
        return $this->activite;
    }

    public function setActivite($activite){
        $this->activite = $activite;
    }

    public function getAdresse(){
        return $this->adresse;
    }

    public function setAdresse($adresse){
        $this->adresse = $adresse;
    }

    public function getSiteWeb(){
        return $this->site_web;
    }

    public function setSiteWeb($site_web){
        $this->site_web = $site_web;
    }
}