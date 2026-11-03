<?php
// Barre latérale commune aux pages du forum (comme ../commun/entete.php pour le menu).
// La page qui l'inclut définit avant : $roleConnecte et $canalActif
// ($canalActif = un canal, 'mes_posts', ou "" si aucun lien n'est à mettre en vert).
// Elle doit aussi avoir inclus ForumRegles_ahmed.php ($canauxForum).

require_once '../../src/repository/EvenementRepository_ahmed.php';

// Catégories : les canaux autorisés pour ce rôle, puis "Mes posts"
$categoriesForum = array();
foreach ($canauxForum as $canal => $libelle) {
    if (peutAccederCanal($canal, $roleConnecte)) {
        $categoriesForum[$canal] = $libelle;
    }
}
$categoriesForum['mes_posts'] = 'Mes posts';

// Bouton "Nouveau sujet" : on pré-sélectionne le canal en cours s'il est autorisé
$lienNouveauSujet = 'nouveau_sujet.php';
if (peutCreerPost($canalActif, $roleConnecte)) {
    $lienNouveauSujet = 'nouveau_sujet.php?canal=' . $canalActif;
}

// "À ne pas manquer" : le prochain événement publié.
// getEvenementsPublies() renvoie des TABLEAUX triés par date (le plus proche d'abord),
// on garde le premier qui n'est pas encore passé.
$evenementRepository = new EvenementRepository_ahmed();
$prochainEvenement = null;
foreach ($evenementRepository->getEvenementsPublies() as $evenement) {
    if ($prochainEvenement == null && strtotime($evenement['date_evenement']) >= time()) {
        $prochainEvenement = $evenement;
    }
}
?>
<aside class="forum-sidebar" aria-label="Navigation du forum">

    <?php if (peutCreerSujet($roleConnecte)) { ?>
        <a href="<?php echo $lienNouveauSujet; ?>" class="btn btn-primary btn-block">+ Nouveau sujet</a>
    <?php } ?>

    <nav class="sidebar-carte" aria-label="Catégories du forum">
        <h2>Catégories</h2>
        <ul class="sidebar-categories">
            <?php foreach ($categoriesForum as $valeur => $libelle) { ?>
                <li><a href="forum.php?canal=<?php echo $valeur; ?>" <?php if ($valeur == $canalActif) { echo 'aria-current="page"'; } ?>><?php echo $libelle; ?></a></li>
            <?php } ?>
        </ul>
    </nav>

    <?php if ($prochainEvenement != null) { ?>
        <a href="../evenement/liste_evenement_Tom.php" class="sidebar-carte sidebar-evenement">
            <div class="sidebar-evenement-image" aria-hidden="true"></div>
            <div class="sidebar-evenement-texte">
                <p class="sidebar-surtitre">À ne pas manquer</p>
                <h3><?php echo htmlspecialchars($prochainEvenement['titre']); ?></h3>
                <p><?php echo $prochainEvenement['date_fr']; ?> · <?php echo htmlspecialchars($prochainEvenement['lieu']); ?></p>
            </div>
        </a>
    <?php } ?>

    <div class="sidebar-carte sidebar-regles">
        <h2>Règles du forum</h2>
        <p>Restez bienveillant·e et respectueux·se.</p>
        <p>Pas de données personnelles (téléphone, adresse).</p>
        <p>Les messages sont relus par l'équipe de modération.</p>
        <p>Un message pose problème ? Utilisez le bouton <strong>Signaler</strong> sous ce message.</p>
    </div>

</aside>
