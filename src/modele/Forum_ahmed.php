<?php

// Modèle : représente une ligne de la table forum
// Une ligne est soit un post (id_forum_parent = null, avec un titre),
// soit une réponse à un post (id_forum_parent = id du post, sans titre)
class Forum_ahmed
{
    private $id_forum;
    private $titre;
    private $text;
    private $canal;
    private $date_heure;
    private $id_utilisateur;
    private $id_forum_parent;

    public function getIdForum()
    {
        return $this->id_forum;
    }

    public function setIdForum($id_forum)
    {
        $this->id_forum = $id_forum;
    }

    public function getTitre()
    {
        return $this->titre;
    }

    public function setTitre($titre)
    {
        $this->titre = $titre;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;
    }

    public function getCanal()
    {
        return $this->canal;
    }

    public function setCanal($canal)
    {
        $this->canal = $canal;
    }

    public function getDateHeure()
    {
        return $this->date_heure;
    }

    public function setDateHeure($date_heure)
    {
        $this->date_heure = $date_heure;
    }

    public function getIdUtilisateur()
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur($id_utilisateur)
    {
        $this->id_utilisateur = $id_utilisateur;
    }

    public function getIdForumParent()
    {
        return $this->id_forum_parent;
    }

    public function setIdForumParent($id_forum_parent)
    {
        $this->id_forum_parent = $id_forum_parent;
    }

    // Renvoie true si la ligne est un post principal (et pas une réponse)
    public function estUnPost()
    {
        return $this->id_forum_parent == null;
    }
}
