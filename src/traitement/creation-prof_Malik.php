<?php
class creation_prof_Malik {
    private $pdo;

    public function __construct() {
        $this->pdo = Bdd::getConnection();
    }
    public function add(Utilisateur_prof $u) {
        $stmt = $this->pdo->prepare("INSERT INTO utilisateurs_prof 
            (nom, prenom, email, mdp, telephone, adresse, date_naissance)
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $u->getNom(),
            $u->getPrenom(),
            $u->getEmail(),
            password_hash($u->getMdp(), PASSWORD_BCRYPT),
            $u->getTelephone(),
            $u->getAdresse(),
            $u->getDateNaissance()
        ]);
        $u->setIdUtilisateur_prof($this->pdo->lastInsertId());
        return $u->getIdUtilisateur_prof();
    }
}