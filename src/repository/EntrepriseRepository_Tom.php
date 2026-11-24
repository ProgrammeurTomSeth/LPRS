
<?php

// Repository : toutes les requêtes SQL sur la table entreprise

class EntrepriseRepository_Tom
{
    private PDO $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Récupérer une entreprise par son identifiant
    public function getEntreprise($id_entreprise): ?Entreprise_Tom
    {
        $req = $this->connexionBdd->prepare("
            SELECT *
            FROM entreprise
            WHERE id_entreprise = :id
        ");

        $req->execute([
            'id' => $id_entreprise
        ]);

        $ligne = $req->fetch(PDO::FETCH_ASSOC);

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

    // Récupérer toutes les entreprises
    public function getAllEntreprises(): array
    {
        $req = $this->connexionBdd->prepare("
            SELECT *
            FROM entreprise
            ORDER BY nom_entreprise ASC
        ");

        $req->execute();

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Rechercher une entreprise par son nom
    public function getEntreprisesParNom($nom): array
    {
        $req = $this->connexionBdd->prepare("
            SELECT *
            FROM entreprise
            WHERE nom_entreprise LIKE :nom
            ORDER BY nom_entreprise ASC
        ");

        $req->execute([
            'nom' => '%' . $nom . '%'
        ]);

        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nettoyer et normaliser une adresse de site web
    public function nettoyerSiteWeb($siteWeb): string
    {
        $siteWeb = trim($siteWeb);
        $siteWeb = strtolower($siteWeb);

        $siteWeb = preg_replace('#^https?://#', '', $siteWeb);
        $siteWeb = preg_replace('#^www\.#', '', $siteWeb);

        $siteWeb = rtrim($siteWeb, '/');

        return $siteWeb;
    }

    // Rechercher une entreprise par son site web
    public function getEntrepriseParSiteWeb($siteWeb): ?Entreprise_Tom
    {
        $siteWeb = $this->nettoyerSiteWeb($siteWeb);

        $req = $this->connexionBdd->prepare("
            SELECT *
            FROM entreprise
        ");

        $req->execute();

        while ($ligne = $req->fetch(PDO::FETCH_ASSOC)) {

            $siteExistant = $this->nettoyerSiteWeb(
                $ligne['site_web']
            );

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
    public function ajouterEntreprise(
        $nom,
        $activite,
        $adresse,
        $siteWeb
    ): bool {

        // Nettoyer le site web
        $siteWeb = $this->nettoyerSiteWeb($siteWeb);

        // Vérifier si le site existe déjà
        if ($this->getEntrepriseParSiteWeb($siteWeb) !== null) {
            return false;
        }

        // Préparer l'insertion
        $req = $this->connexionBdd->prepare("
            INSERT INTO entreprise (
                nom_entreprise,
                activite,
                adresse,
                site_web
            )
            VALUES (
                :nom,
                :activite,
                :adresse,
                :siteWeb
            )
        ");

        // Exécuter l'insertion
        return $req->execute([
            'nom' => trim($nom),
            'activite' => trim($activite),
            'adresse' => trim($adresse),
            'siteWeb' => $siteWeb
        ]);
    }
}
