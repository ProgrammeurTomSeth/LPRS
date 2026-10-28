<?php

// Modèle : représente une ligne de la table utilisateur
class Utilisateur_Malik
{
    private $id_utilisateur;
    private $nom_utilisateur;
    private $prenom_utilisateur;
    private $mail;
    private $mdp;
    private $telephone;
    private $date_naissance;
    private $role;
    private $statut_validation;
    private $cv;
    private $annee_promo;
    private $id_formation;
    private $specialite;
    private $motif_inscription;
    private $id_entreprise;
    private $poste;
    private $id_gestionnaire_createur;
    private $date_inscription;

    public function getIdUtilisateur()
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur($id_utilisateur)
    {
        $this->id_utilisateur = $id_utilisateur;
    }

    public function getNom()
    {
        return $this->nom_utilisateur;
    }

    public function setNom($nom_utilisateur)
    {
        $this->nom_utilisateur = $nom_utilisateur;
    }

    public function getPrenom()
    {
        return $this->prenom_utilisateur;
    }

    public function setPrenom($prenom_utilisateur)
    {
        $this->prenom_utilisateur = $prenom_utilisateur;
    }

    public function getEmail()
    {
        return $this->mail;
    }

    public function setEmail($mail)
    {
        $this->mail = $mail;
    }

    public function getMdp()
    {
        return $this->mdp;
    }

    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    public function getDateNaissance()
    {
        return $this->date_naissance;
    }

    public function setDateNaissance($date_naissance)
    {
        $this->date_naissance = $date_naissance;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    public function getStatutValidation()
    {
        return $this->statut_validation;
    }

    public function setStatutValidation($statut_validation)
    {
        $this->statut_validation = $statut_validation;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function setCv($cv)
    {
        $this->cv = $cv;
    }

    public function getAnneePromo()
    {
        return $this->annee_promo;
    }

    public function setAnneePromo($annee_promo)
    {
        $this->annee_promo = $annee_promo;
    }

    public function getIdFormation()
    {
        return $this->id_formation;
    }

    public function setIdFormation($id_formation)
    {
        $this->id_formation = $id_formation;
    }

    public function getSpecialite()
    {
        return $this->specialite;
    }

    public function setSpecialite($specialite)
    {
        $this->specialite = $specialite;
    }

    public function getMotifInscription()
    {
        return $this->motif_inscription;
    }

    public function setMotifInscription($motif_inscription)
    {
        $this->motif_inscription = $motif_inscription;
    }

    public function getIdEntreprise()
    {
        return $this->id_entreprise;
    }

    public function setIdEntreprise($id_entreprise)
    {
        $this->id_entreprise = $id_entreprise;
    }

    public function getPoste()
    {
        return $this->poste;
    }

    public function setPoste($poste)
    {
        $this->poste = $poste;
    }

    public function getIdGestionnaireCreateur()
    {
        return $this->id_gestionnaire_createur;
    }

    public function setIdGestionnaireCreateur($id_gestionnaire_createur)
    {
        $this->id_gestionnaire_createur = $id_gestionnaire_createur;
    }

    public function getDateInscription()
    {
        return $this->date_inscription;
    }

    public function setDateInscription($date_inscription)
    {
        $this->date_inscription = $date_inscription;
    }
}
