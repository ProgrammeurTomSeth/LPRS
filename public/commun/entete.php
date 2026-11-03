<?php
// En-tête commun à toutes les pages (logo, menu, boutons Connexion / Profil).
// La page qui l'inclut définit avant $pageActive pour mettre son lien en vert.

// Menu regroupé par catégorie pour ne pas surcharger la barre de navigation.
// Tableau associatif : titre de la catégorie => liens de la catégorie,
// chaque lien étant nom => [texte affiché, adresse, petite description].
// Une catégorie avec un seul lien (Accueil, Admin) s'affiche comme un lien simple.
$menu = array(
    'Accueil' => array(
        'accueil' => array('Accueil', '../page_acceuille/page_accueil.php', '')
    ),
    'Le lycée' => array(
        'formations' => array('Formations', '../page_formation/page_formation.php', 'Bacs et BTS du lycée'),
        'evenements' => array('Événements', '../evenement/liste_evenement_Tom.php', 'Rencontres et salons à venir')
    ),
    'Carrière' => array(
        'offres' => array('Offres', '../offre/liste_offres_Tom.php', 'Stages, alternances et emplois'),
        'entreprises' => array('Entreprises', '../page_entreprise/page_entreprise.php', 'Nos entreprises partenaires')
    ),
    'Communauté' => array(
        'annuaire' => array('Annuaire', '../annuaire/page_annuaire.php', 'Retrouver les anciens élèves'),
        'forum' => array('Forum', '../forum/forum.php', 'Discuter avec les membres')
    )
);

// Lien vers l'administration : seulement pour un gestionnaire connecté
if (estConnecte() && $_SESSION['role'] == 'gestionnaire') {
    $menu['Admin'] = array(
        'admin' => array('Admin', '../Page_Admin/page_admin.php', '')
    );
}
?>
<a class="skip-link" href="#main">Aller au contenu principal</a>

<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Schuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <?php foreach ($menu as $categorie => $liens) { ?>

                    <?php if (count($liens) == 1) { ?>
                        <!-- Catégorie à un seul lien : lien simple -->
                        <?php foreach ($liens as $nom => $lien) { ?>
                            <li><a href="<?php echo $lien[1]; ?>" <?php if ($nom == $pageActive) { echo 'aria-current="page"'; } ?>><?php echo $lien[0]; ?></a></li>
                        <?php } ?>

                    <?php } else { ?>
                        <!-- Catégorie à plusieurs liens : sous-menu qui s'ouvre au survol ou au clavier (voir style.css) -->
                        <li class="menu-groupe<?php if (isset($liens[$pageActive])) { echo ' menu-groupe--actif'; } ?>">
                            <button type="button" class="menu-groupe-titre" aria-haspopup="true"><?php echo $categorie; ?></button>
                            <ul class="sous-menu">
                                <?php foreach ($liens as $nom => $lien) { ?>
                                    <li>
                                        <a href="<?php echo $lien[1]; ?>" <?php if ($nom == $pageActive) { echo 'aria-current="page"'; } ?>>
                                            <?php echo $lien[0]; ?>
                                            <span class="sous-menu-description"><?php echo $lien[2]; ?></span>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

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
