<?php

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/Utilisisateur_Malik.php';

class InscriptionRepository_ahmed{
    private $connexionbdd;

    public function __construct(){
        $this->connexionbdd = (new bdd())->getConnexionBdd();
    }

    public function mailExiste($mail){
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE mail = :mail";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':mail', $mail);
        $req->execute();
        return $req->fetchColumn() > 0;
    }

    public function ajouterUtilisateur(Utilisateur_Malik $utilisateur){
        $sql = "INSERT INTO utilisateur (nom_utilisateur, prenom_utilisateur, mail, mdp, telephone, date_naissance, role, statut_validation, cv, annee_promo, id_formation, specialite, motif_inscription, id_entreprise, poste) VALUES (:nom_utilisateur, :prenom_utilisateur, :mail, :mdp, :telephone, :date_naissance, :role, :statut_validation, :cv, :annee_promo, :id_formation, :specialite, :motif_inscription, :id_entreprise, :poste)";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':nom_utilisateur', $utilisateur->getNom());
        $req->bindValue(':prenom_utilisateur', $utilisateur->getPrenom());
        $req->bindValue(':mail', $utilisateur->getEmail());
        $req->bindValue(':mdp', $utilisateur->getMdp());
        $req->bindValue(':telephone', $utilisateur->getTelephone());
        $req->bindValue(':date_naissance', $utilisateur->getDateNaissance());
        $req->bindValue(':role', $utilisateur->getRole());
        $req->bindValue(':statut_validation', $utilisateur->getStatutValidation());
        $req->bindValue(':cv', $utilisateur->getCv());
        $req->bindValue(':annee_promo', $utilisateur->getAnneePromo());
        $req->bindValue(':id_formation', $utilisateur->getIdFormation(), $utilisateur->getIdFormation() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $req->bindValue(':specialite', $utilisateur->getSpecialite());
        $req->bindValue(':motif_inscription', $utilisateur->getMotifInscription());
        $req->bindValue(':id_entreprise', $utilisateur->getIdEntreprise(), $utilisateur->getIdEntreprise() === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $req->bindValue(':poste', $utilisateur->getPoste());
        $req->execute();
        $utilisateur->setIdUtilisateur($this->connexionbdd->lastInsertId());
        return $utilisateur;
    }
}
