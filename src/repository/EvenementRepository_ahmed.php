<?php

// Repository : requêtes SQL sur les tables evenement, organise_evenement et inscription_evenement
class EvenementRepository_ahmed
{
    private $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Renvoie un objet evenement_anais, ou null si l'id n'existe pas
    public function getEvenement($id_evenement)
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM evenement WHERE id_evenement = :id");
        $req->execute(array('id' => $id_evenement));
        $ligne = $req->fetch();

        if ($ligne) {
            $evenement = new evenement_anais();
            $evenement->setIdEvenement($ligne['id_evenement']);
            $evenement->setTypeEvenement($ligne['type_evenement']);
            $evenement->setTitre($ligne['titre']);
            $evenement->setDescription($ligne['description']);
            $evenement->setLieu($ligne['lieu']);
            $evenement->setElementsRequis($ligne['elements_requis']);
            $evenement->setNbPlaces($ligne['nb_places']);
            $evenement->setDateEvenement($ligne['date_evenement']);
            $evenement->setStatut($ligne['statut']);
            return $evenement;
        }
        return null;
    }

    // Événements publiés, avec la date en français et le nombre d'inscrits
    public function getEvenementsPublies()
    {
        $req = $this->connexionBdd->prepare("SELECT *, DATE_FORMAT(date_evenement, '%d/%m/%Y à %Hh%i') AS date_fr, (SELECT COUNT(*) FROM inscription_evenement WHERE inscription_evenement.id_evenement = evenement.id_evenement AND statut_inscription = 'acceptee') AS nombre_inscrits FROM evenement WHERE statut = 'publie' ORDER BY date_evenement ASC");
        $req->execute();
        return $req->fetchAll();
    }

    // Événements dont l'utilisateur fait partie des organisateurs
    public function getEvenementsOrganises($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT evenement.*, DATE_FORMAT(date_evenement, '%d/%m/%Y %H:%i') AS date_fr FROM evenement INNER JOIN organise_evenement ON organise_evenement.id_evenement = evenement.id_evenement WHERE organise_evenement.id_utilisateur = :id ORDER BY date_evenement DESC");
        $req->execute(array('id' => $id_utilisateur));
        return $req->fetchAll();
    }

    // Événements auxquels l'utilisateur est inscrit
    public function getEvenementsInscrits($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT evenement.*, DATE_FORMAT(date_evenement, '%d/%m/%Y %H:%i') AS date_fr, DATE_FORMAT(inscription_evenement.date_inscription, '%d/%m/%Y') AS date_inscription_fr, statut_inscription FROM evenement INNER JOIN inscription_evenement ON inscription_evenement.id_evenement = evenement.id_evenement WHERE inscription_evenement.id_utilisateur = :id ORDER BY date_evenement DESC");
        $req->execute(array('id' => $id_utilisateur));
        return $req->fetchAll();
    }

    // Renvoie true si l'utilisateur est déjà inscrit à l'événement
    public function estInscrit($id_evenement, $id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT COUNT(*) AS nb FROM inscription_evenement WHERE id_evenement = :id_evenement AND id_utilisateur = :id_utilisateur");
        $req->execute(array('id_evenement' => $id_evenement, 'id_utilisateur' => $id_utilisateur));
        $ligne = $req->fetch();
        return $ligne['nb'] > 0;
    }

    // Nombre d'inscriptions acceptées pour un événement
    public function compterInscrits($id_evenement)
    {
        $req = $this->connexionBdd->prepare("SELECT COUNT(*) AS nb FROM inscription_evenement WHERE id_evenement = :id AND statut_inscription = 'acceptee'");
        $req->execute(array('id' => $id_evenement));
        $ligne = $req->fetch();
        return $ligne['nb'];
    }

    public function inscrire($id_evenement, $id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("INSERT INTO inscription_evenement (id_utilisateur, id_evenement, statut_inscription) VALUES (:id_utilisateur, :id_evenement, 'acceptee')");
        $req->execute(array('id_utilisateur' => $id_utilisateur, 'id_evenement' => $id_evenement));
    }

    // Ajoute l'événement et renvoie son id
    public function ajouterEvenement($evenement)
    {
        $req = $this->connexionBdd->prepare("INSERT INTO evenement (type_evenement, titre, description, lieu, elements_requis, nb_places, date_evenement, statut) VALUES (:type, :titre, :description, :lieu, :elements_requis, :nb_places, NOW(), :statut)");
        $req->execute(array(
            'type' => $evenement->getTypeEvenement(),
            'titre' => $evenement->getTitre(),
            'description' => $evenement->getDescription(),
            'lieu' => $evenement->getLieu(),
            'elements_requis' => $evenement->getElementsRequis(),
            'nb_places' => $evenement->getNbPlaces(),
            'statut' => $evenement->getStatut()
        ));
        return $this->connexionBdd->lastInsertId();
    }

    public function ajouterOrganisateur($id_evenement, $id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("INSERT INTO organise_evenement (id_utilisateur, id_evenement) VALUES (:id_utilisateur, :id_evenement)");
        $req->execute(array('id_utilisateur' => $id_utilisateur, 'id_evenement' => $id_evenement));
    }
}
