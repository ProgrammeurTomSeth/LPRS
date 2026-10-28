<?php

// Repository : toutes les requêtes SQL sur la table utilisateur
class UtilisateurRepository_malik
{
    private $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Renvoie un objet Utilisateur_Malik, ou null si l'id n'existe pas
    public function getUtilisateurParId($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT *, DATE_FORMAT(date_naissance, '%d/%m/%Y') AS date_naissance_fr, DATE_FORMAT(date_inscription, '%d/%m/%Y') AS date_inscription_fr FROM utilisateur WHERE id_utilisateur = :id_utilisateur");
        $req->execute(array('id_utilisateur' => $id_utilisateur));
        $ligne = $req->fetch();

        if ($ligne) {
            return $this->creerUtilisateur($ligne);
        }
        return null;
    }

    // Renvoie un objet Utilisateur_Malik, ou null si le mail n'existe pas
    public function getUtilisateurParMail($mail)
    {
        $req = $this->connexionBdd->prepare("SELECT *, DATE_FORMAT(date_naissance, '%d/%m/%Y') AS date_naissance_fr, DATE_FORMAT(date_inscription, '%d/%m/%Y') AS date_inscription_fr FROM utilisateur WHERE mail = :mail");
        $req->execute(array('mail' => $mail));
        $ligne = $req->fetch();

        if ($ligne) {
            return $this->creerUtilisateur($ligne);
        }
        return null;
    }

    // Renvoie le tableau de tous les utilisateurs (page administrateur)
    public function getAllUtilisateurs()
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM utilisateur ORDER BY date_inscription DESC");
        $req->execute();
        return $req->fetchAll();
    }

    // Recherche des co-organisateurs possibles pour un événement
    public function rechercherOrganisateurs($recherche, $id_utilisateur_connecte)
    {
        $req = $this->connexionBdd->prepare("SELECT id_utilisateur, nom_utilisateur, prenom_utilisateur, mail, role FROM utilisateur WHERE (nom_utilisateur LIKE :r1 OR prenom_utilisateur LIKE :r2 OR mail LIKE :r3) AND role <> 'gestionnaire' AND statut_validation = 'valide' AND id_utilisateur <> :id ORDER BY nom_utilisateur, prenom_utilisateur LIMIT 10");
        $req->execute(array(
            'r1' => '%' . $recherche . '%',
            'r2' => '%' . $recherche . '%',
            'r3' => '%' . $recherche . '%',
            'id' => $id_utilisateur_connecte
        ));
        return $req->fetchAll();
    }

    public function ajouterUtilisateur($utilisateur)
    {
        $req = $this->connexionBdd->prepare("INSERT INTO utilisateur (nom_utilisateur, prenom_utilisateur, mail, mdp, telephone, date_naissance, role, statut_validation, annee_promo, id_formation, specialite, motif_inscription, id_entreprise, poste, id_gestionnaire_createur) VALUES (:nom, :prenom, :mail, :mdp, :telephone, :date_naissance, :role, :statut, :annee_promo, :id_formation, :specialite, :motif, :id_entreprise, :poste, :id_gestionnaire)");
        $req->execute(array(
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'mail' => $utilisateur->getEmail(),
            'mdp' => $utilisateur->getMdp(),
            'telephone' => $utilisateur->getTelephone(),
            'date_naissance' => $utilisateur->getDateNaissance(),
            'role' => $utilisateur->getRole(),
            'statut' => $utilisateur->getStatutValidation(),
            'annee_promo' => $utilisateur->getAnneePromo(),
            'id_formation' => $utilisateur->getIdFormation(),
            'specialite' => $utilisateur->getSpecialite(),
            'motif' => $utilisateur->getMotifInscription(),
            'id_entreprise' => $utilisateur->getIdEntreprise(),
            'poste' => $utilisateur->getPoste(),
            'id_gestionnaire' => $utilisateur->getIdGestionnaireCreateur()
        ));
    }

    // Modifie les champs que le gestionnaire peut changer
    public function modifierUtilisateur($utilisateur)
    {
        $req = $this->connexionBdd->prepare("UPDATE utilisateur SET nom_utilisateur = :nom, prenom_utilisateur = :prenom, mail = :mail, telephone = :telephone, role = :role, statut_validation = :statut WHERE id_utilisateur = :id");
        $req->execute(array(
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'mail' => $utilisateur->getEmail(),
            'telephone' => $utilisateur->getTelephone(),
            'role' => $utilisateur->getRole(),
            'statut' => $utilisateur->getStatutValidation(),
            'id' => $utilisateur->getIdUtilisateur()
        ));
    }

    public function supprimerUtilisateur($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
        $req->execute(array('id' => $id_utilisateur));
    }

    // Rattache l'utilisateur à une entreprise (null pour le détacher)
    public function modifierEntrepriseUtilisateur($id_utilisateur, $id_entreprise, $poste)
    {
        $req = $this->connexionBdd->prepare("UPDATE utilisateur SET id_entreprise = :id_entreprise, poste = :poste WHERE id_utilisateur = :id");
        $req->execute(array(
            'id_entreprise' => $id_entreprise,
            'poste' => $poste,
            'id' => $id_utilisateur
        ));
    }

    // Transforme une ligne de la base en objet Utilisateur_Malik
    private function creerUtilisateur($ligne)
    {
        $utilisateur = new Utilisateur_Malik();
        $utilisateur->setIdUtilisateur($ligne['id_utilisateur']);
        $utilisateur->setNom($ligne['nom_utilisateur']);
        $utilisateur->setPrenom($ligne['prenom_utilisateur']);
        $utilisateur->setEmail($ligne['mail']);
        $utilisateur->setMdp($ligne['mdp']);
        $utilisateur->setTelephone($ligne['telephone']);
        $utilisateur->setDateNaissance($ligne['date_naissance_fr']);
        $utilisateur->setRole($ligne['role']);
        $utilisateur->setStatutValidation($ligne['statut_validation']);
        $utilisateur->setCv($ligne['cv']);
        $utilisateur->setAnneePromo($ligne['annee_promo']);
        $utilisateur->setIdFormation($ligne['id_formation']);
        $utilisateur->setSpecialite($ligne['specialite']);
        $utilisateur->setMotifInscription($ligne['motif_inscription']);
        $utilisateur->setIdEntreprise($ligne['id_entreprise']);
        $utilisateur->setPoste($ligne['poste']);
        $utilisateur->setIdGestionnaireCreateur($ligne['id_gestionnaire_createur']);
        $utilisateur->setDateInscription($ligne['date_inscription_fr']);
        return $utilisateur;
    }
}
