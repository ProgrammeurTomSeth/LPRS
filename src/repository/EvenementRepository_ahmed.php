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

    // Événements dont l'utilisateur fait partie des organisateurs (seulement si sa participation est acceptée)
    // nb_reponses_attendues = nombre de professeurs qui n'ont pas encore répondu à l'invitation
    public function getEvenementsOrganises($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT evenement.*, DATE_FORMAT(date_evenement, '%d/%m/%Y %H:%i') AS date_fr, (SELECT COUNT(*) FROM organise_evenement AS autre WHERE autre.id_evenement = evenement.id_evenement AND autre.statut_participation = 'en_attente') AS nb_reponses_attendues FROM evenement INNER JOIN organise_evenement ON organise_evenement.id_evenement = evenement.id_evenement WHERE organise_evenement.id_utilisateur = :id AND organise_evenement.statut_participation = 'acceptee' ORDER BY date_evenement DESC");
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

    // $statut_participation : 'acceptee' (par défaut) ou 'en_attente' si l'organisateur doit d'abord accepter
    public function ajouterOrganisateur($id_evenement, $id_utilisateur, $statut_participation = 'acceptee')
    {
        $req = $this->connexionBdd->prepare("INSERT INTO organise_evenement (id_utilisateur, id_evenement, statut_participation) VALUES (:id_utilisateur, :id_evenement, :statut)");
        $req->execute(array('id_utilisateur' => $id_utilisateur, 'id_evenement' => $id_evenement, 'statut' => $statut_participation));
    }

    // Invitations auxquelles l'utilisateur (un professeur) n'a pas encore répondu
    // createurs = noms des organisateurs qui ont déjà accepté (l'étudiant qui a créé l'événement)
    public function getInvitationsEnAttente($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT evenement.*, DATE_FORMAT(date_evenement, '%d/%m/%Y %H:%i') AS date_fr, (SELECT GROUP_CONCAT(CONCAT(utilisateur.prenom_utilisateur, ' ', utilisateur.nom_utilisateur) SEPARATOR ', ') FROM organise_evenement AS autre INNER JOIN utilisateur ON utilisateur.id_utilisateur = autre.id_utilisateur WHERE autre.id_evenement = evenement.id_evenement AND autre.statut_participation = 'acceptee') AS createurs FROM evenement INNER JOIN organise_evenement ON organise_evenement.id_evenement = evenement.id_evenement WHERE organise_evenement.id_utilisateur = :id AND organise_evenement.statut_participation = 'en_attente' ORDER BY date_evenement ASC");
        $req->execute(array('id' => $id_utilisateur));
        return $req->fetchAll();
    }

    // Enregistre la réponse ('acceptee' ou 'refusee') d'un professeur à une invitation
    // Renvoie true si une invitation en attente a bien été trouvée et modifiée
    public function repondreInvitation($id_evenement, $id_utilisateur, $reponse)
    {
        $req = $this->connexionBdd->prepare("UPDATE organise_evenement SET statut_participation = :reponse WHERE id_evenement = :id_evenement AND id_utilisateur = :id_utilisateur AND statut_participation = 'en_attente'");
        $req->execute(array('reponse' => $reponse, 'id_evenement' => $id_evenement, 'id_utilisateur' => $id_utilisateur));
        return $req->rowCount() > 0;
    }

    // Nombre de professeurs organisateurs qui n'ont pas refusé (acceptés ou encore en attente)
    public function compterProfesseursRestants($id_evenement)
    {
        $req = $this->connexionBdd->prepare("SELECT COUNT(*) AS nb FROM organise_evenement INNER JOIN utilisateur ON utilisateur.id_utilisateur = organise_evenement.id_utilisateur WHERE organise_evenement.id_evenement = :id AND utilisateur.role = 'professeur' AND organise_evenement.statut_participation <> 'refusee'");
        $req->execute(array('id' => $id_evenement));
        $ligne = $req->fetch();
        return $ligne['nb'];
    }

    // Récupère les événements qui attendent la validation de l'admin
    // (on ne garde que ceux dont tous les professeurs invités ont déjà répondu)
    public function getEvenementsEnAttente()
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM evenement WHERE statut = 'en_attente' AND NOT EXISTS (SELECT * FROM organise_evenement WHERE organise_evenement.id_evenement = evenement.id_evenement AND organise_evenement.statut_participation = 'en_attente')");
        $req->execute();
        return $req->fetchAll();
    }

    // Passe un événement en "publie"
    public function publierEvenement($id_evenement)
    {
        $req = $this->connexionBdd->prepare("UPDATE evenement SET statut = 'publie' WHERE id_evenement = :id");
        $req->execute(array('id' => $id_evenement));
    }

    // Passe un événement en "refuse"
    public function refuserEvenement($id_evenement)
    {
        $req = $this->connexionBdd->prepare("UPDATE evenement SET statut = 'refuse' WHERE id_evenement = :id");
        $req->execute(array('id' => $id_evenement));
    }
}
