<?php

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/Offre_Tom.php';

class CreationOffreRepository{
    private $connexionBdd;

    public function __construct(){
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    public function ajouterOffre($offre){
        if (!$offre instanceof Offre_Tom) {
            throw new InvalidArgumentException("L'objet transmis doit être une instance de Offre_Tom.");
        }

        if (empty($offre->getTitre()) || empty($offre->getDescription()) || empty($offre->getMissions()) || empty($offre->getType()) || empty($offre->getProfilCible()) || empty($offre->getRefEntreprise()) || empty($offre->getIdUtilisateurAuteur())) {
            throw new InvalidArgumentException("Certains champs obligatoires de l'offre sont manquants.");
        }

        $requete = $this->connexionBdd->prepare("INSERT INTO offre (titre, description, missions, salaire, type, statut, profil_cible, date_publication, ref_entreprise, id_utilisateur_auteur) VALUES (:titre, :description, :missions, :salaire, :type, :statut, :profil_cible, NOW(), :ref_entreprise, :id_utilisateur_auteur)");

        $requete->execute([
            'titre' => $offre->getTitre(),
            'description' => $offre->getDescription(),
            'missions' => $offre->getMissions(),
            'salaire' => $offre->getSalaire(),
            'type' => $offre->getType(),
            'statut' => 'ouverte',
            'profil_cible' => $offre->getProfilCible(),
            'ref_entreprise' => $offre->getRefEntreprise(),
            'id_utilisateur_auteur' => $offre->getIdUtilisateurAuteur()
        ]);

        return $this->connexionBdd->lastInsertId();
    }
}