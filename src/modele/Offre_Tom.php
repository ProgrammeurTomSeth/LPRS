<?php

class Offre_Tom{
    private $id_offre;
    private $titre;
    private $description;
    private $missions;
    private $salaire;
    private $type;
    private $statut;
    private $profil_cible;
    private $date_publication;
    private $ref_entreprise;
    private $id_utilisateur_auteur;

    public function __construct($id_offre, $titre, $description, $missions, $salaire, $type, $statut, $profil_cible, $date_publication, $ref_entreprise, $id_utilisateur_auteur) {
        $this->id_offre = $id_offre;
        $this->titre = $titre;
        $this->description = $description;
        $this->missions = $missions;
        $this->salaire = $salaire;
        $this->type = $type;
        $this->statut = $statut;
        $this->profil_cible = $profil_cible;
        $this->date_publication = $date_publication;
        $this->ref_entreprise = $ref_entreprise;
        $this->id_utilisateur_auteur = $id_utilisateur_auteur;
    }

    public function getIdOffre(){
        return $this->id_offre;
    }

    public function setIdOffre($id_offre){
        $this->id_offre = $id_offre;
    }

    public function getTitre(){
        return $this->titre;
    }

    public function setTitre($titre){
        $this->titre = $titre;
    }

    public function getDescription(){
        return $this->description;
    }

    public function setDescription($description){
        $this->description = $description;
    }

    public function getMissions(){
        return $this->missions;
    }

    public function setMissions($missions){
        $this->missions = $missions;
    }

    public function getSalaire(){
        return $this->salaire;
    }

    public function setSalaire($salaire){
        $this->salaire = $salaire;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }

    public function getStatut(){
        return $this->statut;
    }

    public function setStatut($statut){
        $this->statut = $statut;
    }

    public function getProfilCible(){
        return $this->profil_cible;
    }

    public function setProfilCible($profil_cible){
        $this->profil_cible = $profil_cible;
    }

    public function getDatePublication(){
        return $this->date_publication;
    }

    public function setDatePublication($date_publication){
        $this->date_publication = $date_publication;
    }

    public function getRefEntreprise(){
        return $this->ref_entreprise;
    }

    public function setRefEntreprise($ref_entreprise){
        $this->ref_entreprise = $ref_entreprise;
    }

    public function getIdUtilisateurAuteur(){
        return $this->id_utilisateur_auteur;
    }

    public function setIdUtilisateurAuteur($id_utilisateur_auteur){
        $this->id_utilisateur_auteur = $id_utilisateur_auteur;
    }
}