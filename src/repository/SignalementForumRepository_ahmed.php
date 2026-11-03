<?php

// Repository : requêtes SQL sur la table signalement_forum
class SignalementForumRepository_ahmed
{
    private $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Enregistre un signalement (objet SignalementForum_ahmed)
    public function ajouterSignalement($signalement)
    {
        $req = $this->connexionBdd->prepare("INSERT INTO signalement_forum (id_forum, id_utilisateur, motif, commentaire) VALUES (:id_forum, :id_utilisateur, :motif, :commentaire)");
        $req->execute(array(
            'id_forum' => $signalement->getIdForum(),
            'id_utilisateur' => $signalement->getIdUtilisateur(),
            'motif' => $signalement->getMotif(),
            'commentaire' => $signalement->getCommentaire()
        ));
    }

    // Renvoie true si l'utilisateur a déjà signalé ce message
    public function aDejaSignale($id_forum, $id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT COUNT(*) AS nb FROM signalement_forum WHERE id_forum = :id_forum AND id_utilisateur = :id_utilisateur");
        $req->execute(array('id_forum' => $id_forum, 'id_utilisateur' => $id_utilisateur));
        $ligne = $req->fetch();
        return $ligne['nb'] > 0;
    }

    // Renvoie un TABLEAU simple des id des messages déjà signalés par l'utilisateur
    // (pour afficher « Signalé » au lieu du bouton dans la discussion)
    public function getIdsSignalesPar($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT id_forum FROM signalement_forum WHERE id_utilisateur = :id");
        $req->execute(array('id' => $id_utilisateur));

        $ids = array();
        foreach ($req->fetchAll() as $ligne) {
            $ids[] = $ligne['id_forum'];
        }
        return $ids;
    }

    // Nombre de messages différents qui ont au moins un signalement
    // (affiché sur le tableau de bord admin)
    public function compterMessagesSignales()
    {
        $req = $this->connexionBdd->prepare("SELECT COUNT(DISTINCT id_forum) AS nb FROM signalement_forum");
        $req->execute();
        $ligne = $req->fetch();
        return $ligne['nb'];
    }

    // Tous les signalements, avec le message signalé, son auteur, celui qui signale
    // et le post de la discussion. Renvoie des TABLEAUX (fetchAll).
    // id_post : COALESCE renvoie la première valeur non nulle :
    //   - réponse à un commentaire -> le parent de son parent (le post)
    //   - commentaire              -> son parent (le post)
    //   - post                     -> lui-même
    public function getSignalements()
    {
        $req = $this->connexionBdd->prepare("SELECT signalement_forum.id_signalement, signalement_forum.motif, signalement_forum.commentaire, DATE_FORMAT(signalement_forum.date_signalement, '%d/%m/%Y à %H:%i') AS date_signalement_fr, message.id_forum, message.titre, message.text, message.canal, message.id_forum_parent, DATE_FORMAT(message.date_heure, '%d/%m/%Y à %H:%i') AS date_message_fr, auteur.prenom_utilisateur AS prenom_auteur, auteur.nom_utilisateur AS nom_auteur, auteur.role AS role_auteur, signaleur.prenom_utilisateur AS prenom_signaleur, signaleur.nom_utilisateur AS nom_signaleur, COALESCE(parent.id_forum_parent, message.id_forum_parent, message.id_forum) AS id_post, post.titre AS titre_post FROM signalement_forum INNER JOIN forum AS message ON message.id_forum = signalement_forum.id_forum INNER JOIN utilisateur AS auteur ON auteur.id_utilisateur = message.id_utilisateur INNER JOIN utilisateur AS signaleur ON signaleur.id_utilisateur = signalement_forum.id_utilisateur LEFT JOIN forum AS parent ON parent.id_forum = message.id_forum_parent LEFT JOIN forum AS post ON post.id_forum = COALESCE(parent.id_forum_parent, message.id_forum_parent, message.id_forum) ORDER BY signalement_forum.date_signalement DESC, signalement_forum.id_signalement DESC");
        $req->execute();
        return $req->fetchAll();
    }

    // Supprime tous les signalements d'un message (le gestionnaire garde le message)
    public function supprimerSignalementsDuMessage($id_forum)
    {
        $req = $this->connexionBdd->prepare("DELETE FROM signalement_forum WHERE id_forum = :id");
        $req->execute(array('id' => $id_forum));
    }
}
