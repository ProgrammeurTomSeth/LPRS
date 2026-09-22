<?php
class createUserAnais {
    private PDO $bdd;

    public function __construct() {
        $this->bdd= (new bdd)->getConnexionBdd();
    }
    public function add(Utilisateur_Malik $u) {
        $stmt = $this->bdd->prepare("INSERT INTO utilisateur 
            (id_utilisateur,nom, prenom, email, mdp, telephone, date_naissance, role, statut_validation, cv, annee_promo, id_formation, specilaite, motif_inscription, id_entreprise, poste, id_gestionnaire_utilisateur, date_inscription )
            VALUES (?, ?, ?, ?, ?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([

            $u->getIdUtilisateur(),
            $u->getNom(),
            $u->getPrenom(),
            $u->getEmail(),
            $u->getMdp(),
            password_hash($u->getMdp(), PASSWORD_BCRYPT),
            $u->getTelephone(),
            $u->getDateNaissance(),
            $u->getRole(),
            $u->getStatutValidation(),
            $u->getCv(),
            $u->getAnneePromo(),
            $u->getPoste(),
            $u->getIdEntreprise(),
            $u->getPoste(),
            $u->getIdGestionnaireCreateur(),
            $u->getDateInscription()

        ]);
        $u->setIdUtilisateur($this->bdd->lastInsertId());
        return $u->getIdUtilisateur();
    }
}