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
        $sql = "INSERT INTO utilisateur (id_utilisateur,nom_utilisateur, prenom_utilisateur, mail, mdp, telephone, date_naissance, role, statut_validation, cv,annee_promo,id_formation,specialite,motif,inscription,poste,id_gestionnaire_createur,date_inscription)
                VALUES (:id_utilisateur,:nom_utilisateur, :prenom_utilisateur, :mail, :mdp, :telephone, :date_naissance, :role, :statut_validation, :cv,:annee_promo,:id_formation,:specialite,:motif,:inscription,:poste,:id_gestionnaire_createur,:date_inscription)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_utilisateur',           $utilisateur->getid_utilisateur());
        $req->bindValue(':nom_utilisateur',           $utilisateur->getnom_utilisateur());
        $req->bindValue(':prenom_utilisateur',        $utilisateur->getprenom_utilisateur());
        $req->bindValue(':mail',         $utilisateur->getmail());
        $req->bindValue(':mdp',           $utilisateur->getmdp());
        $req->bindValue(':telephone',     $utilisateur->gettelephone());
        $req->bindValue(':date_naissance', $utilisateur->getDate_naissance());
        $req->bindValue(':role',          $utilisateur->getrole());
        $req->bindValue(':statut_validation', $utilisateur->getstatut_validation());
        $req->bindValue(':cv',         $utilisateur->getcv());
        $req->bindValue(':annee_promo',   $utilisateur->getannee_promo());
        $req->bindValue(':id_formation', $utilisateur->getid_formation());
        $req->bindValue(':specialite', $utilisateur->getspecialite());
        $req->bindValue(':motif_inscription', $utilisateur->getmotif_inscription());
        $req->bindValue(':id_entreprise', $utilisateur->getid_entreprise());
        $req->bindValue(':poste', $utilisateur->getposte());
        $req->bindValue(':id_gestionnaire', $utilisateur->getid_gestionnaire());
        $req->bindValue(':date_inscription', $utilisateur->getdate_inscription());
        $req->execute();
    }
    public function modifierUtilisateur(Utilisateur $utilisateur){
        $sql = "UPDATE utilisateur SET id_utilisateur = :id_utilisatuer, nom_utilisateur = :nom_utilisateur, prenom_utilisateur = :prenom_utilisateur, mail = :mail, mdp = :mdp, telephone = :telephone, date_naissance = :date_naissance, role = :role, statut_validation = :statut_validation,cv = :cv,annee_promo = :annee_promo,id_formation = :id_formation,specialite = :specialite,motif = :motif,inscription = :inscription,poste = :poste,id_gestionnaire_createur = :id_gestionnaire_createur,date_inscription = :date_inscription  WHERE id_utilisateur = :idUtilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_utilisateur',           $utilisateur->getid_utilisateur());
        $req->bindValue(':nom_utilisateur',           $utilisateur->getnom_utilisateur());
        $req->bindValue(':prenom_utilisateur',        $utilisateur->getprenom_utilisateur());
        $req->bindValue(':mail',         $utilisateur->getmail());
        $req->bindValue(':mdp',           $utilisateur->getmdp());
        $req->bindValue(':telephone',     $utilisateur->gettelephone());
        $req->bindValue(':date_naissance', $utilisateur->getDate_naissance());
        $req->bindValue(':role',          $utilisateur->getrole());
        $req->bindValue(':statut_validation', $utilisateur->getstatut_validation());
        $req->bindValue(':cv',         $utilisateur->getcv());
        $req->bindValue(':annee_promo',   $utilisateur->getannee_promo());
        $req->bindValue(':id_formation', $utilisateur->getid_formation());
        $req->bindValue(':specialite', $utilisateur->getspecialite());
        $req->bindValue(':motif_inscription', $utilisateur->getmotif_inscription());
        $req->bindValue(':id_entreprise', $utilisateur->getid_entreprise());
        $req->bindValue(':poste', $utilisateur->getposte());
        $req->bindValue(':id_gestionnaire', $utilisateur->getid_gestionnaire());
        $req->bindValue(':date_inscription', $utilisateur->getdate_inscription());
        $req->execute();
    }
    public function supprimerUtilisateur($id_Utilisateur){
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = :id_Utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_Utilisateur', $id_Utilisateur);
        $req->execute();
    }
}
