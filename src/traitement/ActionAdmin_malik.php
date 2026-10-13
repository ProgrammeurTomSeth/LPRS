<?php

require_once __DIR__ . '/../repository/AdminRepository_Malik.php';

class ActionAdmin_malik
{
    private $repo;

    public function __construct()
    {
        $this->repo = new AdminRepository_Malik();
    }

    public function getTous()
    {
        return $this->repo->getAllUtilisateurs();
    }

    public function getUn($id)
    {
        return $this->repo->getUtilisateur($id);
    }

    public function compterEnAttente()
    {
        return $this->repo->compterEnAttente();
    }

    // Ajout par un gestionnaire : compte directement validé, mot de passe haché
    public function ajouter($nom, $prenom, $mail, $mdp, $telephone, $role, $idGestionnaire)
    {
        if ($nom === '' || $prenom === '' || $mail === '' || strlen($mdp) < 8) {
            return false;
        }
        if ($this->repo->getUtilisateurParMail($mail) !== null) {
            return false; // email déjà utilisé
        }
        $u = new Utilisateur_Malik(
            null, $nom, $prenom, $mail, password_hash($mdp, PASSWORD_DEFAULT),
            $telephone !== '' ? $telephone : null, null, $role, 'valide',
            null, null, null, null, null, null, null, $idGestionnaire, null
        );
        $this->repo->ajouterUtilisateur($u);
        return true;
    }

    // Modifie seulement les champs du formulaire, les autres gardent leur valeur
    public function modifier($id, $nom, $prenom, $mail, $telephone, $role)
    {
        $e = $this->repo->getUtilisateur($id);
        if ($e === null || $nom === '' || $prenom === '' || $mail === '') {
            return false;
        }
        $autre = $this->repo->getUtilisateurParMail($mail);
        if ($autre !== null && (int) $autre->getIdUtilisateur() !== (int) $id) {
            return false; // email pris par quelqu'un d'autre
        }
        $u = new Utilisateur_Malik(
            $id, $nom, $prenom, $mail, $e->getMdp(),
            $telephone !== '' ? $telephone : null, $e->getDateNaissance(), $role, $e->getStatutValidation(),
            $e->getCv(), $e->getAnneePromo(), $e->getIdFormation(), $e->getSpecialite(), $e->getMotifInscription(),
            $e->getIdEntreprise(), $e->getPoste(), $e->getIdGestionnaireCreateur(), $e->getDateInscription()
        );
        $this->repo->modifierUtilisateur($u);
        return true;
    }

    public function valider($id)
    {
        return $this->repo->validerUtilisateur($id);
    }

    public function refuser($id)
    {
        return $this->repo->refuserUtilisateur($id);
    }

    public function supprimer($id)
    {
        return $this->repo->supprimerUtilisateur($id);
    }
}