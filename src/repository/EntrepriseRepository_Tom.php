<?php

require_once __DIR__ . '/../bdd/bdd.php';
require_once __DIR__ . '/../modele/Entreprise_Tom.php';

class EntrepriseRepository_Tom
{
    private $connexionbdd;

    public function __construct()
    {
        $this->connexionbdd = (new bdd())->getConnexionBdd();
    }

    public function getEntreprise($id_entreprise)
    {
        $sql = "SELECT * FROM entreprise WHERE id_entreprise = :id_entreprise";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_entreprise', $id_entreprise, PDO::PARAM_INT);
        $req->execute();

        $result = $req->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->createEntreprise($result);
    }

    public function getEntrepriseParSiteWeb($site_web)
    {
        $sql = "SELECT * FROM entreprise WHERE site_web = :site_web";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':site_web', $site_web);
        $req->execute();

        $result = $req->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->createEntreprise($result);
    }

    public function getAllEntreprises()
    {
        $sql = "SELECT * FROM entreprise ORDER BY nom_entreprise ASC";
        $req = $this->connexionbdd->prepare($sql);
        $req->execute();

        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        $entreprises = array();

        foreach ($results as $result) {
            $entreprises[] = $this->createEntreprise($result);
        }

        return $entreprises;
    }

    public function ajouterEntreprise(Entreprise_Tom $entreprise)
    {
        $sql = "INSERT INTO entreprise
                (nom_entreprise, activite, adresse, site_web)
                VALUES (:nom_entreprise, :activite, :adresse, :site_web)";

        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':nom_entreprise', $entreprise->getNomEntreprise());
        $req->bindValue(':activite', $entreprise->getActivite());
        $req->bindValue(':adresse', $entreprise->getAdresse());
        $req->bindValue(':site_web', $entreprise->getSiteWeb());
        $req->execute();

        $entreprise->setIdEntreprise($this->connexionbdd->lastInsertId());

        return $entreprise;
    }

    public function modifierEntreprise(Entreprise_Tom $entreprise)
    {
        $sql = "UPDATE entreprise
                SET nom_entreprise = :nom_entreprise,
                    activite = :activite,
                    adresse = :adresse,
                    site_web = :site_web
                WHERE id_entreprise = :id_entreprise";

        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':nom_entreprise', $entreprise->getNomEntreprise());
        $req->bindValue(':activite', $entreprise->getActivite());
        $req->bindValue(':adresse', $entreprise->getAdresse());
        $req->bindValue(':site_web', $entreprise->getSiteWeb());
        $req->bindValue(':id_entreprise', $entreprise->getIdEntreprise(), PDO::PARAM_INT);

        return $req->execute();
    }

    public function supprimerEntreprise($id_entreprise)
    {
        $sql = "DELETE FROM entreprise WHERE id_entreprise = :id_entreprise";
        $req = $this->connexionbdd->prepare($sql);
        $req->bindValue(':id_entreprise', $id_entreprise, PDO::PARAM_INT);

        return $req->execute();
    }

    private function createEntreprise($result)
    {
        return new Entreprise_Tom(
            $result['id_entreprise'],
            $result['nom_entreprise'],
            $result['activite'],
            $result['adresse'],
            $result['site_web']
        );
    }
}