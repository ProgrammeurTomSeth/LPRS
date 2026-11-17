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
    public function getAllEntreprises():array
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM entreprise ORDER BY nom_entreprise ASC");
        $req->execute();
        return $req->fetchAll();
    }

    // Renvoie le tableau des entreprises dont le nom contient $nom
    public function getEntreprisesParNom($nom): array
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM entreprise WHERE nom_entreprise LIKE :nom ORDER BY nom_entreprise ASC");
        $req->execute(array('nom' => '%' . $nom . '%'));
        return $req->fetchAll();
    }

    // Nettoyer et normaliser une adresse de site web
    public function nettoyerSiteWeb($siteWeb): string
    {
        // Supprimer les espaces au début et à la fin
        $siteWeb = trim($siteWeb);

        // Mettre en minuscules
        $siteWeb = strtolower($siteWeb);

        // Supprimer http:// ou https://
        $siteWeb = preg_replace('#^https?://#', '', $siteWeb);

        // Supprimer www.
        $siteWeb = preg_replace('#^www\.#', '', $siteWeb);

        // Supprimer les / à la fin
        $siteWeb = rtrim($siteWeb, '/');

        return $siteWeb;
    }

    // Rechercher une entreprise à partir de son site web
    public function getEntrepriseParSiteWeb($siteWeb): ?Entreprise_Tom
    {
        $siteWeb = $this->nettoyerSiteWeb($siteWeb);

        $req = $this->connexionBdd->prepare("
        SELECT * FROM entreprise
    ");

        $req->execute();

        while ($ligne = $req->fetch()) {

            $siteExistant = $this->nettoyerSiteWeb($ligne['site_web']);

            if ($siteExistant === $siteWeb) {

                $entreprise = new Entreprise_Tom();
                $entreprise->setIdEntreprise($ligne['id_entreprise']);
                $entreprise->setNomEntreprise($ligne['nom_entreprise']);
                $entreprise->setActivite($ligne['activite']);
                $entreprise->setAdresse($ligne['adresse']);
                $entreprise->setSiteWeb($ligne['site_web']);

                return $entreprise;
            }
        }

        return null;
    }
    // Ajouter une nouvelle entreprise
    public function ajouterEntreprise($nom, $activite, $adresse, $siteWeb)
    {
        $siteWeb = $this->nettoyerSiteWeb($siteWeb);

        // Vérifier si l'entreprise existe déjà avec ce site web
        if ($this->getEntrepriseParSiteWeb($siteWeb) !== null) {
            return false;
        }

        $req = $this->connexionBdd->prepare("
        INSERT INTO entreprise
        (nom_entreprise, activite, adresse, site_web)
        VALUES (:nom, :activite, :adresse, :siteWeb)
    ");

        return $req->execute([
            'nom' => $nom,
            'activite' => $activite,
            'adresse' => $adresse,
            'siteWeb' => $siteWeb
        ]);
    }
}
