<?php

// Repository : requêtes SQL sur la table forum
// Une ligne de la table forum peut être :
//   - un post            : id_forum_parent = NULL (avec un titre)
//   - un commentaire     : id_forum_parent = id du post
//   - une réponse à un commentaire : id_forum_parent = id du commentaire
class ForumRepository_ahmed
{
    private $connexionBdd;

    public function __construct()
    {
        $bdd = new Bdd();
        $this->connexionBdd = $bdd->getConnexionBdd();
    }

    // Renvoie un objet Forum_ahmed (post, commentaire ou réponse), ou null si l'id n'existe pas
    // (utilisé par les traitements pour vérifier les droits)
    public function getMessage($id_forum)
    {
        $req = $this->connexionBdd->prepare("SELECT * FROM forum WHERE id_forum = :id");
        $req->execute(array('id' => $id_forum));
        $ligne = $req->fetch();

        if ($ligne) {
            $message = new Forum_ahmed();
            $message->setIdForum($ligne['id_forum']);
            $message->setTitre($ligne['titre']);
            $message->setText($ligne['text']);
            $message->setCanal($ligne['canal']);
            $message->setDateHeure($ligne['date_heure']);
            $message->setIdUtilisateur($ligne['id_utilisateur']);
            $message->setIdForumParent($ligne['id_forum_parent']);
            return $message;
        }
        return null;
    }

    // Renvoie l'id du post (la discussion) qui contient le message, ou null.
    // COALESCE renvoie la première valeur non nulle :
    //   - réponse à un commentaire -> le parent de son parent (le post)
    //   - commentaire              -> son parent (le post)
    //   - post                     -> lui-même
    public function getIdPost($id_forum)
    {
        $req = $this->connexionBdd->prepare("SELECT COALESCE(parent.id_forum_parent, forum.id_forum_parent, forum.id_forum) AS id_post FROM forum LEFT JOIN forum AS parent ON parent.id_forum = forum.id_forum_parent WHERE forum.id_forum = :id");
        $req->execute(array('id' => $id_forum));
        $ligne = $req->fetch();

        if ($ligne) {
            return $ligne['id_post'];
        }
        return null;
    }

    // Posts d'un canal, du plus récent au plus ancien.
    // Renvoie des TABLEAUX (fetchAll) avec l'auteur, la date, les minutes écoulées
    // et le nombre de réponses (commentaires + réponses aux commentaires).
    public function getPostsParCanal($canal)
    {
        $req = $this->connexionBdd->prepare("SELECT forum.id_forum, forum.titre, forum.text, forum.canal, forum.id_utilisateur, DATE_FORMAT(forum.date_heure, '%d/%m/%Y') AS date_fr, TIMESTAMPDIFF(MINUTE, forum.date_heure, NOW()) AS minutes, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.role, (SELECT COUNT(*) FROM forum AS commentaire WHERE commentaire.id_forum_parent = forum.id_forum) + (SELECT COUNT(*) FROM forum AS reponse INNER JOIN forum AS commentaire ON commentaire.id_forum = reponse.id_forum_parent WHERE commentaire.id_forum_parent = forum.id_forum) AS nb_reponses FROM forum INNER JOIN utilisateur ON utilisateur.id_utilisateur = forum.id_utilisateur WHERE forum.canal = :canal AND forum.id_forum_parent IS NULL ORDER BY forum.date_heure DESC, forum.id_forum DESC");
        $req->execute(array('canal' => $canal));
        return $req->fetchAll();
    }

    // Section "mes posts" : les posts écrits par l'utilisateur, ceux qu'il a commentés
    // et ceux où il a répondu à un commentaire. Mêmes colonnes que getPostsParCanal().
    public function getMesPosts($id_utilisateur)
    {
        $req = $this->connexionBdd->prepare("SELECT forum.id_forum, forum.titre, forum.text, forum.canal, forum.id_utilisateur, DATE_FORMAT(forum.date_heure, '%d/%m/%Y') AS date_fr, TIMESTAMPDIFF(MINUTE, forum.date_heure, NOW()) AS minutes, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.role, (SELECT COUNT(*) FROM forum AS commentaire WHERE commentaire.id_forum_parent = forum.id_forum) + (SELECT COUNT(*) FROM forum AS reponse INNER JOIN forum AS commentaire ON commentaire.id_forum = reponse.id_forum_parent WHERE commentaire.id_forum_parent = forum.id_forum) AS nb_reponses FROM forum INNER JOIN utilisateur ON utilisateur.id_utilisateur = forum.id_utilisateur WHERE forum.id_forum_parent IS NULL AND (forum.id_utilisateur = :id_auteur OR forum.id_forum IN (SELECT commentaire.id_forum_parent FROM forum AS commentaire WHERE commentaire.id_utilisateur = :id_commentateur) OR forum.id_forum IN (SELECT commentaire.id_forum_parent FROM forum AS commentaire INNER JOIN forum AS reponse ON reponse.id_forum_parent = commentaire.id_forum WHERE reponse.id_utilisateur = :id_repondant)) ORDER BY forum.date_heure DESC, forum.id_forum DESC");
        $req->execute(array('id_auteur' => $id_utilisateur, 'id_commentateur' => $id_utilisateur, 'id_repondant' => $id_utilisateur));
        return $req->fetchAll();
    }

    // Un post avec son auteur. Renvoie un TABLEAU, ou null
    // si l'id n'existe pas ou si ce n'est pas un post.
    public function getPost($id_forum)
    {
        $req = $this->connexionBdd->prepare("SELECT forum.id_forum, forum.titre, forum.text, forum.canal, forum.id_utilisateur, DATE_FORMAT(forum.date_heure, '%d/%m/%Y') AS date_fr, TIMESTAMPDIFF(MINUTE, forum.date_heure, NOW()) AS minutes, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.role FROM forum INNER JOIN utilisateur ON utilisateur.id_utilisateur = forum.id_utilisateur WHERE forum.id_forum = :id AND forum.id_forum_parent IS NULL");
        $req->execute(array('id' => $id_forum));
        $ligne = $req->fetch();

        if ($ligne) {
            return $ligne;
        }
        return null;
    }

    // Commentaires d'un post. $tri = 'recents' (plus récents d'abord)
    // ou 'anciens' (ordre de la discussion). Renvoie des TABLEAUX.
    public function getCommentaires($id_post, $tri)
    {
        if ($tri == 'recents') {
            $req = $this->connexionBdd->prepare("SELECT forum.id_forum, forum.text, forum.id_utilisateur, DATE_FORMAT(forum.date_heure, '%d/%m/%Y') AS date_fr, TIMESTAMPDIFF(MINUTE, forum.date_heure, NOW()) AS minutes, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.role FROM forum INNER JOIN utilisateur ON utilisateur.id_utilisateur = forum.id_utilisateur WHERE forum.id_forum_parent = :id_post ORDER BY forum.date_heure DESC, forum.id_forum DESC");
        } else {
            $req = $this->connexionBdd->prepare("SELECT forum.id_forum, forum.text, forum.id_utilisateur, DATE_FORMAT(forum.date_heure, '%d/%m/%Y') AS date_fr, TIMESTAMPDIFF(MINUTE, forum.date_heure, NOW()) AS minutes, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.role FROM forum INNER JOIN utilisateur ON utilisateur.id_utilisateur = forum.id_utilisateur WHERE forum.id_forum_parent = :id_post ORDER BY forum.date_heure ASC, forum.id_forum ASC");
        }
        $req->execute(array('id_post' => $id_post));
        return $req->fetchAll();
    }

    // Toutes les réponses aux commentaires d'un post, de la plus ancienne à la plus récente.
    // Renvoie des TABLEAUX ; id_forum_parent indique le commentaire auquel elles répondent.
    public function getReponsesAuxCommentaires($id_post)
    {
        $req = $this->connexionBdd->prepare("SELECT reponse.id_forum, reponse.text, reponse.id_utilisateur, reponse.id_forum_parent, DATE_FORMAT(reponse.date_heure, '%d/%m/%Y') AS date_fr, TIMESTAMPDIFF(MINUTE, reponse.date_heure, NOW()) AS minutes, utilisateur.nom_utilisateur, utilisateur.prenom_utilisateur, utilisateur.role FROM forum AS reponse INNER JOIN forum AS commentaire ON commentaire.id_forum = reponse.id_forum_parent INNER JOIN utilisateur ON utilisateur.id_utilisateur = reponse.id_utilisateur WHERE commentaire.id_forum_parent = :id_post ORDER BY reponse.date_heure ASC, reponse.id_forum ASC");
        $req->execute(array('id_post' => $id_post));
        return $req->fetchAll();
    }

    // Enregistre un post, un commentaire ou une réponse (objet Forum_ahmed) et renvoie son id
    public function ajouterMessage($message)
    {
        $req = $this->connexionBdd->prepare("INSERT INTO forum (titre, text, canal, id_utilisateur, id_forum_parent) VALUES (:titre, :text, :canal, :id_utilisateur, :id_forum_parent)");
        $req->execute(array(
            'titre' => $message->getTitre(),
            'text' => $message->getText(),
            'canal' => $message->getCanal(),
            'id_utilisateur' => $message->getIdUtilisateur(),
            'id_forum_parent' => $message->getIdForumParent()
        ));
        return $this->connexionBdd->lastInsertId();
    }

    // Supprime un message (réservé au gestionnaire).
    // Grâce aux clés étrangères ON DELETE CASCADE, ses réponses
    // et leurs signalements sont supprimés aussi.
    public function supprimerMessage($id_forum)
    {
        $req = $this->connexionBdd->prepare("DELETE FROM forum WHERE id_forum = :id");
        $req->execute(array('id' => $id_forum));
    }
}
