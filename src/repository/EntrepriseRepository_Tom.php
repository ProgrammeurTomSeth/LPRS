<?php

// Repository : toutes les requêtes SQL sur la table entreprise
class EntrepriseRepository_Tom
{
    private $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Renvoie un objet Entreprise_Tom, ou null si l'id n'existe pas
    public function getEntreprise($id_entreprise)
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM entreprise WHERE id_entreprise = :id");
        $req->execute(array('id' => $id_entreprise));
        $ligne = $req->fetch();

        if ($ligne) {
            $entreprise = new Entreprise_Tom();
            $entreprise->setIdEntreprise($ligne['id_entreprise']);
            $entreprise->setNomEntreprise($ligne['nom_entreprise']);
            $entreprise->setActivite($ligne['activite']);
            $entreprise->setAdresse($ligne['adresse']);
            $entreprise->setSiteWeb($ligne['site_web']);
            return $entreprise;
        }
        return null;
    }

    // Renvoie le tableau de toutes les entreprises
    public function getAllEntreprises()
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM entreprise ORDER BY nom_entreprise ASC");
        $req->execute();
        return $req->fetchAll();
    }

    // Renvoie le tableau des entreprises dont le nom contient $nom
    public function getEntreprisesParNom($nom)
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM entreprise WHERE nom_entreprise LIKE :nom ORDER BY nom_entreprise ASC");
        $req->execute(array('nom' => '%' . $nom . '%'));
        return $req->fetchAll();
    }
}
