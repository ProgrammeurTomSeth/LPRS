<?php
session_start();

// Pas connecté, ou pas gestionnaire : retour à la connexion
if (!isset($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'gestionnaire') {
    header('Location: ../connexion/connexion.php');
    exit;
}

require_once __DIR__ . '/../../src/traitement/ActionAdmin_malik.php';

class ActionAdmin_malik{
    private $repo;

    public function __construct()
    {
        $this->repo = new AdminRepository_Malik();
    }

    public function getTous()
    {
        return $this->repo->getAllUtilisateurs();
    }

    public function getUn($id)
    {
        return $this->repo->getUtilisateur($id);
    }

    public function compterEnAttente()
    {
        return $this->repo->compterEnAttente();
    }

    // Ajout par un gestionnaire : compte directement validé, mot de passe haché
    public function ajouter($nom, $prenom, $mail, $mdp, $telephone, $role, $idGestionnaire)
    {
        if ($nom === '' || $prenom === '' || $mail === '' || strlen($mdp) < 8) {
            return false;
        }
        if ($this->repo->getUtilisateurParMail($mail) !== null) {
            return false; // email déjà utilisé
        }
        $u = new Utilisateur_Malik(
            null, $nom, $prenom, $mail, password_hash($mdp, PASSWORD_DEFAULT),
            $telephone !== '' ? $telephone : null, null, $role, 'valide',
            null, null, null, null, null, null, null, $idGestionnaire, null
        );
        $this->repo->ajouterUtilisateur($u);
        return true;
    }

    // Modifie les champs du formulaire, les autres gardent leur valeur
    public function modifier($id, $nom, $prenom, $mail, $telephone, $role, $statut)
    {
        $e = $this->repo->getUtilisateur($id);
        if ($e === null || $nom === '' || $prenom === '' || $mail === '') {
            return false;
        }
        // Seuls les 3 statuts connus par la connexion sont acceptés
        if ($statut !== 'valide' && $statut !== 'refuse' && $statut !== 'en_attente') {
            return false;
        }
        $autre = $this->repo->getUtilisateurParMail($mail);
        if ($autre !== null && (int) $autre->getIdUtilisateur() !== (int) $id) {
            return false; // email pris par quelqu'un d'autre
        }
        $u = new Utilisateur_Malik(
            $id, $nom, $prenom, $mail, $e->getMdp(),
            $telephone !== '' ? $telephone : null, $e->getDateNaissance(), $role, $statut,
            $e->getCv(), $e->getAnneePromo(), $e->getIdFormation(), $e->getSpecialite(), $e->getMotifInscription(),
            $e->getIdEntreprise(), $e->getPoste(), $e->getIdGestionnaireCreateur(), $e->getDateInscription()
        );
        $this->repo->modifierUtilisateur($u);
        return true;
    }

    public function supprimer($id)
    {
        return $this->repo->supprimerUtilisateur($id);
    }

    // Traite les boutons Ajouter / Enregistrer / Supprimer, puis recharge la page
    public function traiterFormulaire()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        $action = $_POST['action'] ?? '';
        $id = (int) ($_POST['id_utilisateur'] ?? 0);

        if ($action === 'ajouter') {
            $this->ajouter(trim($_POST['nom']), trim($_POST['prenom']), trim($_POST['mail']), $_POST['mdp'], trim($_POST['telephone']), $_POST['role'], null);
        } elseif ($action === 'modifier') {
            $this->modifier($id, trim($_POST['nom']), trim($_POST['prenom']), trim($_POST['mail']), trim($_POST['telephone']), $_POST['role'], $_POST['statut']);
        } elseif ($action === 'supprimer') {
            $this->supprimer($id);
        }

        header('Location: page_admin.php');
        exit;
    }

    // Renvoie l'utilisateur à modifier si on a cliqué sur Modifier (?modifier=ID), sinon null
    public function getUtilisateurAModifier()
    {
        if (isset($_GET['modifier'])) {
            return $this->getUn((int) $_GET['modifier']);
        }
        return null;
    }
}

// Prépare les données affichées par public/Page_Admin/page_admin.php
$admin = new ActionAdmin_malik();
$admin->traiterFormulaire();

$utilisateurs = $admin->getTous();
$u = $admin->getUtilisateurAModifier();

header('Location: ../../public/connexion/connexion.php');