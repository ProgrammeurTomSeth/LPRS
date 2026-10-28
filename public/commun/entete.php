<?php
// En-tête commun à toutes les pages (logo, menu, boutons Connexion / Profil).
// La page qui l'inclut définit avant $pageActive pour mettre son lien en vert.

// Liens du menu : tableau associatif nom => [texte affiché, adresse]
$liensMenu = array(
    'accueil' => array('Accueil', '../page_acceuille/page_accueil.php'),
    'formations' => array('Formations', '../page_formation/page_formation.php'),
    'entreprises' => array('Entreprises', '../page_entreprise/page_entreprise.php'),
    'offres' => array('Offres', '../offre/liste_offres_Tom.php'),
    'evenements' => array('Événements', '../evenement/liste_evenement_Tom.php'),
    'annuaire' => array('Annuaire', '../annuaire/page_annuaire.php')
);

// Lien vers l'administration : seulement pour un gestionnaire connecté
if (estConnecte() && $_SESSION['role'] == 'gestionnaire') {
    $liensMenu['admin'] = array('Admin', '../Page_Admin/page_admin.php');
}
?>
<a class="skip-link" href="#main">Aller au contenu principal</a>

<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Schuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <?php foreach ($liensMenu as $nom => $lien) { ?>
                    <li><a href="<?php echo $lien[1]; ?>" <?php if ($nom == $pageActive) { echo 'aria-current="page"'; } ?>><?php echo $lien[0]; ?></a></li>
                <?php } ?>
            </ul>
        </nav>

        <div class="header-actions">
            <?php afficherActionsHeader(); ?>
        </div>

        <button class="nav-toggle" id="nav-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>
