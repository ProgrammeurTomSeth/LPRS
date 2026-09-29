<?php

require_once __DIR__ . '/../repository/OffreRepository_Tom.php';
require_once __DIR__ . '/../modele/Offre_Tom.php';

class OffreTraitement_Tom{
    private $offreRepository;

    public function __construct(){
        $this->offreRepository = new OffreRepository_Tom();
    }

    public function creerOffre($titre, $description, $missions, $salaire, $type, $statut, $profil_cible, $date_publication, $ref_entreprise, $id_utilisateur_auteur) {
        $titre = trim($titre);
        $description = trim($description);
        $missions = trim($missions);
        $type = trim($type);
        $statut = trim($statut);
        $profil_cible = trim($profil_cible);
        $date_publication = trim($date_publication);
        if ($salaire !== null && $salaire !== '') {
            $salaire = trim($salaire);
        } else {
            $salaire = null;
        }
        if ($titre === '' || $description === '' || $missions === '' || $type === '' || $statut === '' || $profil_cible === '' || $ref_entreprise === '' || $id_utilisateur_auteur === '') {
            return false;
        }

        $typesAutorises = ['stage', 'alternance', 'cdd', 'cdi'];

        if (!in_array($type, $typesAutorises, true)) {
            return false;
        }

        $statutsAutorises = ['ouverte', 'cloturee'];

        if (!in_array($statut, $statutsAutorises, true)) {
            return false;
        }

        $profilsAutorises = ['tous', 'etudiant', 'alumni'];

        if (!in_array($profil_cible, $profilsAutorises, true)) {
            return false;
        }

        if ($salaire !== null) {
            if (!is_numeric($salaire) || $salaire < 0) {
                return false;
            }
            $salaire = (float)$salaire;
        }

        if (!ctype_digit((string)$ref_entreprise) || !ctype_digit((string)$id_utilisateur_auteur)) {
            return false;
        }

        if ($date_publication === '') {
            $date_publication = date('Y-m-d H:i:s');
        }

        $offre = new Offre_Tom(null, $titre, $description, $missions, $salaire, $type, $statut, $profil_cible, $date_publication, (int)$ref_entreprise, (int)$id_utilisateur_auteur);

        return $this->offreRepository->ajouter($offre);
    }

    public function recupererOffre($id_offre){
        if (!ctype_digit((string)$id_offre)) {
            return false;
        }
        return $this->offreRepository->trouverParId((int)$id_offre);
    }

    public function recupererToutesLesOffres(){
        return $this->offreRepository->trouverToutes();
    }
    public function recupererOffresOuvertes(){
        return $this->offreRepository->trouverParStatut('ouverte');
    }

    public function recupererOffresEntreprise($ref_entreprise){
        if (!ctype_digit((string)$ref_entreprise)) {
            return false;
        }
        return $this->offreRepository->trouverParEntreprise(
            (int)$ref_entreprise
        );
    }

    public function modifierOffre($id_offre, $titre, $description, $missions, $salaire, $type, $statut, $profil_cible, $date_publication, $ref_entreprise, $id_utilisateur_auteur) {
        if (!ctype_digit((string)$id_offre)) {
            return false;
        }
        $resultat = $this->validerDonnees($titre, $description, $missions, $salaire, $type, $statut, $profil_cible, $date_publication, $ref_entreprise, $id_utilisateur_auteur);
        if ($resultat === false) {
            return false;
        }
        $offre = new Offre_Tom((int)$id_offre, trim($titre), trim($description), trim($missions), $resultat['salaire'], trim($type), trim($statut), trim($profil_cible), trim($date_publication), (int)$ref_entreprise, (int)$id_utilisateur_auteur);
        return $this->offreRepository->modifier($offre);
    }

    public function cloturerOffre($id_offre){
        if (!ctype_digit((string)$id_offre)) {
            return false;
        }
        return $this->offreRepository->modifierStatut(
            (int)$id_offre,
            'cloturee'
        );
    }

    public function ouvrirOffre($id_offre){
        if (!ctype_digit((string)$id_offre)) {
            return false;
        }
        return $this->offreRepository->modifierStatut(
            (int)$id_offre,
            'ouverte'
        );
    }

    private function validerDonnees($titre, $description, $missions, $salaire, $type, $statut, $profil_cible, $date_publication, $ref_entreprise, $id_utilisateur_auteur) {
        $titre = trim($titre);
        $description = trim($description);
        $missions = trim($missions);
        $type = trim($type);
        $statut = trim($statut);
        $profil_cible = trim($profil_cible);
        $date_publication = trim($date_publication);
        if ($titre === '' || $description === '' || $missions === '' || $type === '' || $statut === '' || $profil_cible === '' || $ref_entreprise === '' || $id_utilisateur_auteur === '') {
            return false;
        }
        $typesAutorises = ['stage', 'alternance', 'cdd', 'cdi'];

        if (!in_array($type, $typesAutorises, true)) {
            return false;
        }

        $statutsAutorises = ['ouverte', 'cloturee'];

        if (!in_array($statut, $statutsAutorises, true)) {
            return false;
        }

        $profilsAutorises = ['tous', 'etudiant', 'alumni'];

        if (!in_array($profil_cible, $profilsAutorises, true)) {
            return false;
        }

        if ($salaire !== null && $salaire !== '') {
            if (!is_numeric($salaire) || $salaire < 0) {
                return false;
            }

            $salaire = (float)$salaire;
        } else {
            $salaire = null;
        }

        if (!ctype_digit((string)$ref_entreprise) || !ctype_digit((string)$id_utilisateur_auteur)) {
            return false;
        }

        return [
            'salaire' => $salaire
        ];
    }
}