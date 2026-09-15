<?php

class EntrepriseRepository_Tom{
    public function __construct(private int $id_entreprise, private string $nom_entreprise, private string $activite, private string $adresse, private string $site_web){}
    public function getIdEntreprise(): int {
        return $this->id_entreprise;
    }
    public function getNomEntreprise(): string {
        return $this->nom_entreprise;
    }
    public function getActivite(): string {
        return $this->activite;
    }
    public function getAdresse(): string {
        return $this->adresse;
    }
    public function getSiteWeb(): string {
        return $this->site_web;
    }
}