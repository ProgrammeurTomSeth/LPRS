<?php

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/Offre_Tom.php';

class OffreRepository_Tom{
    private $connexionBdd;

    public function __construct(){
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    public function getOffre($id_offre){
        $req = $this->connexionBdd->prepare("SELECT * FROM offre WHERE id_offre = :id");
        $req->execute(array('id' => $id_offre));
        $ligne = $req->fetch();

        if ($ligne) {
            $offre = new Offre_Tom();
            $offre->setIdOffre($ligne['id_offre']);
            $offre->setTitre($ligne['titre']);
            $offre->setDescription($ligne['description']);
            $offre->setMissions($ligne['missions']);
            $offre->setSalaire($ligne['salaire']);
            $offre->setType($ligne['type']);
            $offre->setStatut($ligne['statut']);
            $offre->setProfilCible($ligne['profil_cible']);
            $offre->setDatePublication($ligne['date_publication']);
            $offre->setRefEntreprise($ligne['ref_entreprise']);
            $offre->setIdUtilisateurAuteur($ligne['id_utilisateur_auteur']);
            return $offre;
        }
        return null;
    }

    public function getOffresOuvertes(){
        $req = $this->connexionBdd->prepare("SELECT offre.*, entreprise.nom_entreprise, DATE_FORMAT(date_publication, '%d/%m/%Y') AS date_fr, FORMAT(salaire, 2, 'fr_FR') AS salaire_fr FROM offre LEFT JOIN entreprise ON entreprise.id_entreprise = offre.ref_entreprise WHERE statut = 'ouverte' ORDER BY date_publication DESC");
        $req->execute();
        return $req->fetchAll();
    }

    public function aDejaPostule($id_offre, $id_utilisateur){
        $req = $this->connexionBdd->prepare("SELECT COUNT(*) AS nb FROM candidature WHERE id_offre = :id_offre AND id_utilisateur = :id_utilisateur");
        $req->execute(array(
            'id_offre' => $id_offre,
            'id_utilisateur' => $id_utilisateur
        ));
        $ligne = $req->fetch();
        return $ligne['nb'] > 0;
    }

    public function ajouterCandidature($id_offre, $id_utilisateur, $motivation){
        $req = $this->connexionBdd->prepare("INSERT INTO candidature (id_utilisateur, id_offre, motivation) VALUES (:id_utilisateur, :id_offre, :motivation)");

        $req->execute(array(
            'id_utilisateur' => $id_utilisateur,
            'id_offre' => $id_offre,
            'motivation' => $motivation
        ));
    }

    public function ajouterOffre($offre){
        if (!$offre instanceof Offre_Tom) {
            throw new InvalidArgumentException("L'objet transmis doit être une instance de Offre_Tom.");
        }

        if (empty($offre->getTitre()) || empty($offre->getDescription()) || empty($offre->getMissions()) || empty($offre->getType()) || empty($offre->getProfilCible()) || empty($offre->getRefEntreprise()) || empty($offre->getIdUtilisateurAuteur())) {
            throw new InvalidArgumentException("Certains champs obligatoires de l'offre sont manquants.");
        }

        $requete = $this->connexionBdd->prepare("INSERT INTO offre (titre, description, missions, salaire, type, statut, profil_cible, date_publication, ref_entreprise, id_utilisateur_auteur) VALUES(:titre, :description, :missions, :salaire, :type, :statut, :profil_cible, NOW(), :ref_entreprise, :id_utilisateur_auteur)");
        $requete->execute(array(
            'titre' => $offre->getTitre(),
            'description' => $offre->getDescription(),
            'missions' => $offre->getMissions(),
            'salaire' => $offre->getSalaire(),
            'type' => $offre->getType(),
            'statut' => 'ouverte',
            'profil_cible' => $offre->getProfilCible(),
            'ref_entreprise' => $offre->getRefEntreprise(),
            'id_utilisateur_auteur' => $offre->getIdUtilisateurAuteur()
        ));
        return $this->connexionBdd->lastInsertId();
    }

    public function getMesOffresEntreprise($idEntreprise){
        $req = $this->connexionBdd->prepare("SELECT offre.*, entreprise.nom_entreprise, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, DATE_FORMAT(offre.date_publication, '%d/%m/%Y') AS date_fr, FORMAT(offre.salaire, 2, 'fr_FR') AS salaire_fr FROM offre LEFT JOIN entreprise ON entreprise.id_entreprise = offre.ref_entreprise LEFT JOIN utilisateur ON utilisateur.id_utilisateur = offre.id_utilisateur_auteur WHERE offre.ref_entreprise = :id_entreprise ORDER BY offre.date_publication DESC");
        $req->execute(array(
            'id_entreprise' => $idEntreprise));
        return $req->fetchAll();
    }

    public function modifierStatutOffreEntreprise($idOffre, $idEntreprise, $statut) {
        if (!in_array($statut, array('ouverte', 'cloturee'), true)) {
            throw new InvalidArgumentException("Statut invalide.");
        }

        $req = $this->connexionBdd->prepare("UPDATE offre SET statut = :statut WHERE id_offre = :id_offre AND ref_entreprise = :id_entreprise");

        $req->execute(array(
            'statut' => $statut,
            'id_offre' => $idOffre,
            'id_entreprise' => $idEntreprise));
        return $req->rowCount() > 0;
    }

    public function getCandidaturesOffreEntreprise($idOffre, $idEntreprise){
        $req = $this->connexionBdd->prepare("SELECT candidature.id_utilisateur, candidature.id_offre, candidature.motivation, candidature.date_candidature, candidature.statut_candidature, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.mail, utilisateur.cv FROM candidature INNER JOIN offre ON offre.id_offre = candidature.id_offre INNER JOIN utilisateur ON utilisateur.id_utilisateur = candidature.id_utilisateur WHERE candidature.id_offre = :id_offre AND offre.ref_entreprise = :id_entreprise AND candidature.statut_candidature = 'active' ORDER BY candidature.date_candidature DESC");

        $req->execute(array(
            'id_offre' => $idOffre,
            'id_entreprise' => $idEntreprise));
        return $req->fetchAll();
    }

    public function masquerCandidature($idOffre, $idUtilisateur, $idEntreprise) {
        $req = $this->connexionBdd->prepare("UPDATE candidature SET statut_candidature = 'masquee' WHERE id_offre = :id_offre AND id_utilisateur = :id_utilisateur AND statut_candidature = 'active' AND EXISTS (SELECT 1 FROM offre WHERE offre.id_offre = candidature.id_offre AND offre.ref_entreprise = :id_entreprise)");
        $req->execute(array(
            'id_offre' => $idOffre,
            'id_utilisateur' => $idUtilisateur,
            'id_entreprise' => $idEntreprise));

        return $req->rowCount() > 0;
    }
}