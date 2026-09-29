<?php

require_once __DIR__ . '/../repository/EntrepriseRepository_Tom.php';

class EntrepriseTraitement_Tom{
    private $entrepriseRepository;

    public function __construct(){
        $this->entrepriseRepository = new EntrepriseRepository_Tom();
    }

    public function créerEntreprise($nom_entreprise, $activité, $adresse, $site_web){
        $nom_entreprise = trim($nom_entreprise);
        $activité = trim($activité);
        $adresse = trim($adresse);
        $site_web = trim($site_web);
        if ($nom_entreprise === '' || $site_web === '') {
            return false;
        }
        if (!filter_var($site_web, FILTER_VALIDATE_URL)) {
            return false;
        }
        if ($this->entrepriseRepository->getEntrepriseParSiteWeb($site_web) !== null) {
            return false;
        }
        $entreprise = new Entreprise_Tom(
            null,
            $nom_entreprise,
            $activité !== '' ? $activité : null,
            $adresse !== '' ? $adresse : null,
            $site_web
        );
        return $this->entrepriseRepository->ajouterEntreprise($entreprise);
    }

    public function récupérerEntreprise($id_entreprise){
        return $this->entrepriseRepository->getEntreprise($id_entreprise);
    }

    public function récupérerEntrepriseParSiteWeb($site_web){
        return $this->entrepriseRepository->getEntrepriseParSiteWeb($site_web);
    }

    public function récupérerEntreprises(){
        return $this->entrepriseRepository->getAllEntreprises();
    }

    public function modifierEntreprise($id_entreprise, $nom_entreprise, $activité, $adresse, $site_web){
        $nom_entreprise = trim($nom_entreprise);
        $activité = trim($activité);
        $adresse = trim($adresse);
        $site_web = trim($site_web);
        if ($nom_entreprise === '' || $site_web === '') {
            return false;
        }
        if (!filter_var($site_web, FILTER_VALIDATE_URL)) {
            return false;
        }
        $entrepriseExistante = $this->entrepriseRepository->getEntrepriseParSiteWeb($site_web);
        if (
            $entrepriseExistante !== null
            && (int) $entrepriseExistante->getIdEntreprise() !== (int) $id_entreprise
        ) {
            return false;
        }
        $entreprise = new Entreprise_Tom(
            $id_entreprise,
            $nom_entreprise,
            $activité !== '' ? $activité : null,
            $adresse !== '' ? $adresse : null,
            $site_web
        );
        return $this->entrepriseRepository->modifierEntreprise($entreprise);
    }

    public function supprimerEntreprise($id_entreprise){
        return $this->entrepriseRepository->supprimerEntreprise($id_entreprise);
    }
}