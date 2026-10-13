<?php

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/Utilisateur_Malik.php';

class AdminRepository_Malik
{
    // Objet PDO : connexion à la base MySQL
    private $connexionBdd;

    public function __construct()
    {
        // Récupère la connexion PDO
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getUtilisateur($id_utilisateur)
    {
        // prepare : requête avec une case vide (:id_utilisateur)
        $req = $this->connexionBdd->prepare("SELECT * FROM utilisateur WHERE id_utilisateur = :id_utilisateur");
        // bindValue : remplit la case avec la valeur
        $req->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        // execute : lance la requête
        $req->execute();
        // fetch : lit une ligne (false si aucun résultat)
        $result = $req->fetch();

        return $result ? $this->construireUtilisateur($result) : null;
    }

    public function getUtilisateurParMail($mail)
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM utilisateur WHERE mail = :mail");
        $req->bindValue(':mail', $mail);
        $req->execute();
        $result = $req->fetch();

        return $result ? $this->construireUtilisateur($result) : null;
    }

    public function getAllUtilisateurs()
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM utilisateur ORDER BY date_inscription DESC");
        $req->execute();

        $tabUtilisateurs = array();
        foreach ($req->fetchAll() as $result) {
            $tabUtilisateurs[] = $this->construireUtilisateur($result);
        }
        return $tabUtilisateurs;
    }

    public function ajouterUtilisateur(Utilisateur_Malik $u)
    {
        $sql = "INSERT INTO utilisateur
                (nom_utilisateur, prenom_utilisateur, mail, mdp, telephone, date_naissance, role, statut_validation,
                 cv, annee_promo, id_formation, specialite, motif_inscription, id_entreprise, poste,
                 id_gestionnaire_createur, date_inscription)
                VALUES
                (:nom_utilisateur, :prenom_utilisateur, :mail, :mdp, :telephone, :date_naissance, :role, :statut_validation,
                 :cv, :annee_promo, :id_formation, :specialite, :motif_inscription, :id_entreprise, :poste,
                 :id_gestionnaire_createur, NOW())";
        $req = $this->connexionBdd->prepare($sql);
        $this->lierParametres($req, $u);
        $req->execute();
    }

    public function modifierUtilisateur(Utilisateur_Malik $u)
    {
        $sql = "UPDATE utilisateur SET
                nom_utilisateur = :nom_utilisateur, prenom_utilisateur = :prenom_utilisateur, mail = :mail,
                mdp = :mdp, telephone = :telephone, date_naissance = :date_naissance, role = :role,
                statut_validation = :statut_validation, cv = :cv, annee_promo = :annee_promo,
                id_formation = :id_formation, specialite = :specialite, motif_inscription = :motif_inscription,
                id_entreprise = :id_entreprise, poste = :poste, id_gestionnaire_createur = :id_gestionnaire_createur
                WHERE id_utilisateur = :id_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $this->lierParametres($req, $u);
        $req->bindValue(':id_utilisateur', $u->getIdUtilisateur(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerUtilisateur($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("UPDATE offre SET id_utilisateur_auteur = NULL WHERE id_utilisateur_auteur = :id");
        $req->bindValue(':id', $id_utilisateur, PDO::PARAM_INT);
        $req->execute();

        $req = $this->connexionBdd->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
        $req->bindValue(':id', $id_utilisateur, PDO::PARAM_INT);
        $req->execute();
    }

    /* ---------- Validation du compte ---------- */

    // Le compte peut se connecter
    public function validerUtilisateur($id_utilisateur)
    {
        return $this->changerStatut($id_utilisateur, 'valide');
    }

    // L'inscription est rejetée
    public function refuserUtilisateur($id_utilisateur)
    {
        return $this->changerStatut($id_utilisateur, 'refuse');
    }

    // Annule la décision
    public function remettreEnAttente($id_utilisateur)
    {
        return $this->changerStatut($id_utilisateur, 'en_attente');
    }

    // Renvoie le statut d'un utilisateur (null si l'id n'existe pas)
    public function getStatut($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT statut_validation FROM utilisateur WHERE id_utilisateur = :id");
        $req->bindValue(':id', $id_utilisateur, PDO::PARAM_INT);
        $req->execute();
        $statut = $req->fetchColumn();
        return $statut === false ? null : $statut;
    }

    // Nombre d'inscriptions à traiter
    public function compterEnAttente()
    {
        $req = $this->connexionBdd->prepare("SELECT COUNT(*) FROM utilisateur WHERE statut_validation = 'en_attente'");
        $req->execute();
        return (int) $req->fetchColumn();
    }

    // Change le statut d'un seul utilisateur (le WHERE évite de tous les modifier)
    private function changerStatut($id_utilisateur, $statut)
    {
        $req = $this->connexionBdd->prepare("UPDATE utilisateur SET statut_validation = :statut WHERE id_utilisateur = :id");
        $req->bindValue(':statut', $statut);
        $req->bindValue(':id', $id_utilisateur, PDO::PARAM_INT);
        return $req->execute();
    }

    // Remplit les cases (:nom_utilisateur...) d'une requête préparée avec les données de l'objet
    private function lierParametres($req, Utilisateur_Malik $u)
    {
        $req->bindValue(':nom_utilisateur', $u->getNom());
        $req->bindValue(':prenom_utilisateur', $u->getPrenom());
        $req->bindValue(':mail', $u->getEmail());
        $req->bindValue(':mdp', $u->getMdp());
        $req->bindValue(':telephone', $u->getTelephone());
        $req->bindValue(':date_naissance', $u->getDateNaissance());
        $req->bindValue(':role', $u->getRole());
        $req->bindValue(':statut_validation', $u->getStatutValidation());
        $req->bindValue(':cv', $u->getCv());
        $req->bindValue(':annee_promo', $u->getAnneePromo());
        $req->bindValue(':id_formation', $u->getIdFormation());
        $req->bindValue(':specialite', $u->getSpecialite());
        $req->bindValue(':motif_inscription', $u->getMotifInscription());
        $req->bindValue(':id_entreprise', $u->getIdEntreprise());
        $req->bindValue(':poste', $u->getPoste());
        $req->bindValue(':id_gestionnaire_createur', $u->getIdGestionnaireCreateur());
    }

    private function construireUtilisateur($r)
    {
        return new Utilisateur_Malik(
            $r['id_utilisateur'], $r['nom_utilisateur'], $r['prenom_utilisateur'], $r['mail'], $r['mdp'],
            $r['telephone'], $r['date_naissance'], $r['role'], $r['statut_validation'], $r['cv'],
            $r['annee_promo'], $r['id_formation'], $r['specialite'], $r['motif_inscription'],
            $r['id_entreprise'], $r['poste'], $r['id_gestionnaire_createur'], $r['date_inscription']
        );
    }
}