<?php

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/Offre_Tom.php';

class OffreRepository_Tom{
    private $connexionbdd;

    public function __construct(){
        $this->connexionbdd = (new bdd())->getConnexionBdd();
    }

    public function ajouter(Offre_Tom $offre){
        $sql = "INSERT INTO offre(titre, description, missions, salaire, type, statut, profil_cible, date_publication, ref_entreprise, id_utilisateur_auteur) VALUES (:titre, :description, :missions, :salaire, :type, :statut, :profil_cible, :date_publication, :ref_entreprise, :id_utilisateur_auteur)";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':titre', $offre->getTitre());
        $req->bindValue(':description', $offre->getDescription());
        $req->bindValue(':missions', $offre->getMissions());
        $req->bindValue(':salaire', $offre->getSalaire());
        $req->bindValue(':type', $offre->getType());
        $req->bindValue(':statut', $offre->getStatut());
        $req->bindValue(':profil_cible', $offre->getProfilCible());
        $req->bindValue(':date_publication', $offre->getDatePublication());
        $req->bindValue(':ref_entreprise', $offre->getRefEntreprise(), PDO::PARAM_INT);
        $req->bindValue(':id_utilisateur_auteur', $offre->getIdUtilisateurAuteur(), PDO::PARAM_INT);
        if (!$req->execute()) {
            return false;
        }
        $offre->setIdOffre($this->connexionbdd->lastInsertId());
        return $offre;
    }

    public function trouverParId($id_offre){
        $sql = "SELECT * FROM offre WHERE id_offre = :id_offre";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_offre', $id_offre, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }
        return $this->creerObjetOffre($result);
    }

    public function trouverToutes(){
        $sql = "SELECT * FROM offre ORDER BY date_publication DESC";
        $req = $this->connexionbdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $offres = [];
        foreach ($results as $result) {
            $offres[] = $this->creerObjetOffre($result);
        }
        return $offres;
    }

    public function trouverParStatut($statut){
        $sql = "SELECT * FROM offre WHERE statut = :statut ORDER BY date_publication DESC";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':statut', $statut);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $offres = [];
        foreach ($results as $result) {
            $offres[] = $this->creerObjetOffre($result);
        }
        return $offres;
    }

    public function trouverParEntreprise($ref_entreprise){
        $sql = "SELECT * FROM offre WHERE ref_entreprise = :ref_entreprise ORDER BY date_publication DESC";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':ref_entreprise', $ref_entreprise, PDO::PARAM_INT);
        $req->execute();
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $offres = [];
        foreach ($results as $result) {
            $offres[] = $this->creerObjetOffre($result);
        }
        return $offres;
    }

    public function modifier(Offre_Tom $offre){
        $sql = "UPDATE offre SET titre = :titre, description = :description, missions = :missions, salaire = :salaire, type = :type, statut = :statut, profil_cible = :profil_cible, date_publication = :date_publication, ref_entreprise = :ref_entreprise, id_utilisateur_auteur = :id_utilisateur_auteur WHERE id_offre = :id_offre";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':titre', $offre->getTitre());
        $req->bindValue(':description', $offre->getDescription());
        $req->bindValue(':missions', $offre->getMissions());
        $req->bindValue(':salaire', $offre->getSalaire());
        $req->bindValue(':type', $offre->getType());
        $req->bindValue(':statut', $offre->getStatut());
        $req->bindValue(':profil_cible', $offre->getProfilCible());
        $req->bindValue(':date_publication', $offre->getDatePublication());
        $req->bindValue(':ref_entreprise', $offre->getRefEntreprise(), PDO::PARAM_INT);
        $req->bindValue(':id_utilisateur_auteur', $offre->getIdUtilisateurAuteur(), PDO::PARAM_INT);
        $req->bindValue(':id_offre', $offre->getIdOffre(), PDO::PARAM_INT);
        return $req->execute();
    }

    public function modifierStatut($id_offre, $statut){
        $sql = "UPDATE offre SET statut = :statut WHERE id_offre = :id_offre";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':statut', $statut);
        $req->bindValue(':id_offre', $id_offre, PDO::PARAM_INT);
        return $req->execute();
    }

    private function creerObjetOffre($result){
        return new Offre_Tom($result['id_offre'], $result['titre'], $result['description'], $result['missions'], $result['salaire'], $result['type'], $result['statut'], $result['profil_cible'], $result['date_publication'], $result['ref_entreprise'], $result['id_utilisateur_auteur']);
    }
}