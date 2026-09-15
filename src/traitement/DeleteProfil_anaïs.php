<?php

class DeleteProfil_anaïs
{
    private $pdo;

    public function __construct() {
        $this->pdo = Bdd::getConnection();
    }
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM utilisateurs_prof WHERE id_utilisateur_prof = ?");
        return $stmt->execute([$id]);
    }
}