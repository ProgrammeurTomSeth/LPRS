<?php

// Repository : requêtes SQL sur les tables offre et candidature
class OffreRepository_Tom
{
    private $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Renvoie un objet Offre_Tom, ou null si l'id n'existe pas
    public function getOffre($id_offre)
    {
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

    // Offres ouvertes, avec le nom de l'entreprise, la date et le salaire mis en forme
    public function getOffresOuvertes()
    {
        $req = $this->connexionBdd->prepare("SELECT offre.*, entreprise.nom_entreprise, DATE_FORMAT(date_publication, '%d/%m/%Y') AS date_fr, FORMAT(salaire, 2, 'fr_FR') AS salaire_fr FROM offre LEFT JOIN entreprise ON entreprise.id_entreprise = offre.ref_entreprise WHERE statut = 'ouverte' ORDER BY date_publication DESC");
        $req->execute();
        return $req->fetchAll();
    }

    // Renvoie true si l'utilisateur a déjà postulé à l'offre
    public function aDejaPostule($id_offre, $id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT COUNT(*) AS nb FROM candidature WHERE id_offre = :id_offre AND id_utilisateur = :id_utilisateur");
        $req->execute(array('id_offre' => $id_offre, 'id_utilisateur' => $id_utilisateur));
        $ligne = $req->fetch();
        return $ligne['nb'] > 0;
    }

    public function ajouterCandidature($id_offre, $id_utilisateur, $motivation)
    {
        $req = $this->connexionBdd->prepare("INSERT INTO candidature (id_utilisateur, id_offre, motivation) VALUES (:id_utilisateur, :id_offre, :motivation)");
        $req->execute(array('id_utilisateur' => $id_utilisateur, 'id_offre' => $id_offre, 'motivation' => $motivation));
    }
}
