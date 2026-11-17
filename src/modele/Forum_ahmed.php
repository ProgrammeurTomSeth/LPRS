<?php

// Repository : toutes les requêtes SQL sur la table formation
class Formation_ahmed{
    private $connexionBdd;

    public function __construct(){
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Renvoie un objet formation_anais, ou null si l'id n'existe pas
    public function getFormation($id_formation){
        $req = $this->connexionBdd->prepare(
            "SELECT * FROM formation WHERE id_formation = :id"
        );

        $req->execute(array('id' => $id_formation));
        $ligne = $req->fetch();

        if ($ligne) {
            $formation = new formation_anais();
            $formation->setIdFormation($ligne['id_formation']);
            $formation->setNomFormation($ligne['nom_formation']);
            $formation->setTypeFormation($ligne['type_formation']);
            $formation->setDescription($ligne['description']);

            return $formation;
        }

        return null;
    }

    // Renvoie le tableau de toutes les formations
    public function getAllFormations(){
        $req = $this->connexionBdd->prepare(
            "SELECT * FROM formation ORDER BY nom_formation ASC"
        );

        $req->execute();

        return $req->fetchAll();
    }

    // Vérifie si un nom de formation existe déjà
    // L'identifiant permet d'exclure la formation en cours de modification
    public function nomFormationExiste($nom_formation, $id_formation = null){
        $sql = "SELECT COUNT(*) FROM formation WHERE LOWER(nom_formation) = LOWER(:nom)";

        if ($id_formation !== null) {
            $sql .= " AND id_formation != :id";
        }

        $req = $this->connexionBdd->prepare($sql);

        $parametres = array('nom' => trim($nom_formation));

        if ($id_formation !== null) {
            $parametres['id'] = $id_formation;
        }

        $req->execute($parametres);

        return $req->fetchColumn() > 0;
    }

    // Ajoute une formation
    public function ajouterFormation($formation){
        $req = $this->connexionBdd->prepare(
            "INSERT INTO formation (nom_formation, type_formation, description)
             VALUES (:nom, :type, :description)"
        );

        return $req->execute(array(
            'nom' => trim($formation->getNomFormation()),
            'type' => $formation->getTypeFormation(),
            'description' => $formation->getDescription()
        ));
    }

    // Modifie une formation existante
    public function modifierFormation($formation){
        $req = $this->connexionBdd->prepare(
            "UPDATE formation
             SET nom_formation = :nom,
                 type_formation = :type,
                 description = :description
             WHERE id_formation = :id"
        );

        return $req->execute(array(
            'nom' => trim($formation->getNomFormation()),
            'type' => $formation->getTypeFormation(),
            'description' => $formation->getDescription(),
            'id' => $formation->getIdFormation()
        ));
    }

    // Supprime une formation.
    // La clé étrangère de utilisateur met automatiquement id_formation à NULL.
    public function supprimerFormation($id_formation){
        $req = $this->connexionBdd->prepare(
            "DELETE FROM formation WHERE id_formation = :id"
        );

        return $req->execute(array('id' => $id_formation));
    }
}
