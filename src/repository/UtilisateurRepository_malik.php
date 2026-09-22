<?php
require_once __DIR__ . '/../bdd/Bdd.php';
require_once __DIR__ . '/../modele/Utilisateur.php';
class UtilisateurRepository_malik
{
    private $connexionbdd;
    public function __construct(){
        $this->connexionbdd = (new Bdd())->getConnexionbdd();
    }

    public function getUtilisateur($id_Utilisateur)
    {
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = :id_Utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_Utilisateur', $id_Utilisateur);
        $req->execute();
        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        $utilisateur = new Utilisateur(
            $result['id_utilisateur'],
            $result['nom_Utilisateur'],
            $result['prenom_Utilisateur'],
            $result['mail'],
            $result['mdp'],
            $result['telephone'],
            $result['date_naissance'],
            $result['role'],
            $result['statut_validation'],
            $result['cv'],
            $result['annee_promo'],
            $result['id_formation'],
            $result['specialite'],
            $result['motif_inscription'],
            $result['id_entreprise'],
            $result['poste'],
            $result['id_gestionnaire_createur'],
            $result['date_inscription'],

        );
        return $utilisateur;
    }

    public function getUtilisateurParmail($mail) {
        $sql = "SELECT * FROM utilisateur WHERE mail = :mail";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':email', $mail);
        $req->execute();
        $result = $req->fetch();
        if (!$result) {
            return null;
        }

        $utilisateur = new Utilisateur(
            $result['id_utilisateur'],
            $result['nom_Utilisateur'],
            $result['prenom_Utilisateur'],
            $result['mail'],
            $result['mdp'],
            $result['telephone'],
            $result['date_naissance'],
            $result['role'],
            $result['statut_validation'],
            $result['cv'],
            $result['annee_promo'],
            $result['id_formation'],
            $result['specialite'],
            $result['motif_inscription'],
            $result['id_entreprise'],
            $result['poste'],
            $result['id_gestionnaire_createur'],
            $result['date_inscription'],

        );
        return $utilisateur;
    }


    public function getAllUtilisateurs(){
        $sql = "SELECT * FROM utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabUtilisateurs = array();
        foreach ($results as $result) {
            $utilisateur = new Utilisateur($result['id_utilisateur'], $result['nom_utilisateur'], $result['prenom_utilisateur'], $result['mail'], $result['mdp'], $result['telephone'], $result['date_naissance'], $result['role'], $result['statut_validation'], $result['cv'], $result['annee_promo'], $result['id_formation'], $result['specialite'], $result['motif_inscription'], $result['id_entreprise'], $result['poste'], $result['id_gestionnaire_createur'], $result['date_inscription']);
            $tabUtilisateurs[] = $utilisateur;
        }
        return $tabUtilisateurs;
    }

    public function ajouterUtilisateur(Utilisateur $utilisateur){
        $sql = "INSERT INTO utilisateur (id_utilisateur,nom_utilisateur, prenom_utilisateur, mail, mdp, telephone, date_naissance, role, etatut_validation, cv,annee_promo,)
                VALUES (:nom, :prenom, :email, :mdp, :telephone, :adresse, :dateNaissance, :role, :etatCompte, :dateCreation)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom',           $utilisateur->getNom());
        $req->bindValue(':prenom',        $utilisateur->getPrenom());
        $req->bindValue(':email',         $utilisateur->getEmail());
        $req->bindValue(':mdp',           $utilisateur->getMdp());
        $req->bindValue(':telephone',     $utilisateur->getTelephone());
        $req->bindValue(':adresse',       $utilisateur->getAdresse());
        $req->bindValue(':dateNaissance', $utilisateur->getDateNaissance());
        $req->bindValue(':role',          $utilisateur->getRole());
        $req->bindValue(':etatCompte',    $utilisateur->getEtatCompte());
        $req->bindValue(':dateCreation',  $utilisateur->getDateCreation());
        $req->execute();
    }
}
