<?php

class ModifierCv
{
    private PDO $bdd;

    public function __construct(PDO $bdd)
    {
        $this->bdd = $bdd;
    }

    //Retourne le rôle (en minuscules) d'un utilisateur, ou null s'il n'existe pas.

    public function getRole(int $idUtilisateur): ?string
    {
        $req = $this->bdd->prepare("
            SELECT role
            FROM utilisateur
            WHERE id_utilisateur = :id
        ");

        $req->execute([':id' => $idUtilisateur]);

        $role = $req->fetchColumn();

        return $role === false ? null : strtolower((string)$role);
    }


    //  Vérifie que l'utilisateur est un étudiant ou un alumni.

    public function peutAjouterCv(int $idUtilisateur): bool
    {
        $role = $this->getRole($idUtilisateur);

        return in_array($role, ['etudiant', 'alumni'], true);
    }

    // Retourne le nom du fichier CV actuel de l'utilisateur, ou null s'il n'en a pas.

    public function getCv(int $idUtilisateur): ?string
    {
        $req = $this->bdd->prepare("
            SELECT cv
            FROM utilisateur
            WHERE id_utilisateur = :id
        ");

        $req->execute([':id' => $idUtilisateur]);

        $cv = $req->fetchColumn();

        return ($cv === false || $cv === null || $cv === '') ? null : (string)$cv;
    }

    //  Met à jour le CV de l'utilisateur. Retourne true en cas de succès.

    public function modifierCv(int $idUtilisateur, string $nomCv): bool
    {
        $req = $this->bdd->prepare("
            UPDATE utilisateur
            SET cv = :cv
            WHERE id_utilisateur = :id
        ");

        return $req->execute([
            ':cv' => $nomCv,
            ':id' => $idUtilisateur,
        ]);
    }

}