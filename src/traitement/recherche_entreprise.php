<?php

require_once __DIR__ . '/../repository/EntrepriseRepository_Tom.php';

class recherche_entreprise extends EntrepriseRepository_Tom
{
    private EntrepriseRepository_Tom $repo;

    public function __construct()
    {
        $this->repo = new EntrepriseRepository_Tom();
    }

    /**
     * Recherche une entreprise par son nom.
     * Retourne un tableau d'objets Entreprise_Tom.
     */
    public function rechercher(string $nom): array
    {
        // Si aucun nom → renvoie toutes les entreprises
        if (trim($nom) === '') {
            return $this->repo->getAllEntreprises();
        }

        // Sinon → recherche par nom
        return $this->repo->getEntreprisesParNom($nom);
    }
}
