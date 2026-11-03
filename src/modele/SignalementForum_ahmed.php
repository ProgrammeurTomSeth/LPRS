<?php

// Modèle : représente une ligne de la table signalement_forum
// (un utilisateur signale un message du forum au gestionnaire)
class SignalementForum_ahmed
{
    private $id_signalement;
    private $id_forum;
    private $id_utilisateur;
    private $motif;
    private $commentaire;
    private $date_signalement;

    public function getIdSignalement()
    {
        return $this->id_signalement;
    }

    public function setIdSignalement($id_signalement)
    {
        $this->id_signalement = $id_signalement;
    }

    public function getIdForum()
    {
        return $this->id_forum;
    }

    public function setIdForum($id_forum)
    {
        $this->id_forum = $id_forum;
    }

    public function getIdUtilisateur()
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur($id_utilisateur)
    {
        $this->id_utilisateur = $id_utilisateur;
    }

    public function getMotif()
    {
        return $this->motif;
    }

    public function setMotif($motif)
    {
        $this->motif = $motif;
    }

    public function getCommentaire()
    {
        return $this->commentaire;
    }

    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;
    }

    public function getDateSignalement()
    {
        return $this->date_signalement;
    }

    public function setDateSignalement($date_signalement)
    {
        $this->date_signalement = $date_signalement;
    }
}
