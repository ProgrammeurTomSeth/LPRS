<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Utilisateur_Malik.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';

// Recherche par nom (formulaire en GET) : sans recherche, on affiche toutes les entreprises
$recherche = "";
if (isset($_GET['search'])) {
    $recherche = $_GET['search'];
}

$entrepriseRepository = new EntrepriseRepository_Tom();
if ($recherche == "") {
    $entreprises = $entrepriseRepository->getAllEntreprises();
} else {
    $entreprises = $entrepriseRepository->getEntreprisesParNom($recherche);
}

// Seuls les alumni et les partenaires connectés peuvent se rattacher à une entreprise
$peutSeRattacher = false;
$idEntrepriseUtilisateur = null;
if (estConnecte()) {
    $utilisateurRepository = new UtilisateurRepository_malik();
    $utilisateurConnecte = $utilisateurRepository->getUtilisateurParId($_SESSION['id_utilisateur']);
    if ($utilisateurConnecte != null) {
        $idEntrepriseUtilisateur = $utilisateurConnecte->getIdEntreprise();
        if ($utilisateurConnecte->getRole() == 'alumni' || $utilisateurConnecte->getRole() == 'partenaire') {
            $peutSeRattacher = true;
        }
    }
}

// Messages laissés par src/traitement/rattachement_entreprise.php
$messageSucces = "";
$messageErreur = "";
if (!empty($_SESSION['entreprise_message'])) {
    $messageSucces = $_SESSION['entreprise_message'];
    $_SESSION['entreprise_message'] = "";
}
if (!empty($_SESSION['entreprise_erreur'])) {
    $messageErreur = $_SESSION['entreprise_erreur'];
    $_SESSION['entreprise_erreur'] = "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'entreprises';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entreprises — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="page_entreprise.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Entreprises</p>
            <h1>Liste des entreprises</h1>
            <p>Retrouvez les entreprises partenaires du lycée.</p>
        </div>
    </section>

    <!-- ===== ENTREPRISES ===== -->
    <section class="section entreprises-section">
        <div class="container">

            <div class="toolbar">
                <!-- Barre de recherche SANS JS -->
                <form method="GET" class="search-container">
                    <input type="text" name="search" placeholder="Rechercher une entreprise..." aria-label="Rechercher une entreprise"
                           value="<?php echo htmlspecialchars($recherche); ?>">
                    <button type="submit" class="btn btn-primary">Rechercher</button>
                </form>
            </div>

            <?php if ($messageSucces != "") { ?>
                <p class="form-status form-status--success" role="status"><?php echo htmlspecialchars($messageSucces); ?></p>
            <?php } ?>
            <?php if ($messageErreur != "") { ?>
                <p class="form-status form-status--error" role="alert"><?php echo htmlspecialchars($messageErreur); ?></p>
            <?php } ?>

            <?php if (count($entreprises) == 0) { ?>
                <p class="liste-vide">
                    <?php if ($recherche != "") { ?>
                        Aucune entreprise ne correspond à « <?php echo htmlspecialchars($recherche); ?> ».
                        <a href="page_entreprise.php">Voir toutes les entreprises</a>
                    <?php } else { ?>
                        Aucune entreprise n'est enregistrée pour le moment.
                    <?php } ?>
                </p>
            <?php } else { ?>
                <div class="cards-grid">
                    <?php foreach ($entreprises as $entreprise) { ?>
                        <?php $estMonEntreprise = ($idEntrepriseUtilisateur == $entreprise['id_entreprise']); ?>
                        <article class="card<?php if ($estMonEntreprise) { echo ' card--mon-entreprise'; } ?>">
                            <h3><?php echo htmlspecialchars($entreprise['nom_entreprise']); ?></h3>
                            <?php if ($estMonEntreprise) { ?>
                                <span class="badge">Votre entreprise</span>
                            <?php } ?>
                            <p><strong>Activité :</strong> <?php if ($entreprise['activite'] != null) { echo htmlspecialchars($entreprise['activite']); } else { echo 'Non renseignée'; } ?></p>
                            <p><strong>Adresse :</strong> <?php if ($entreprise['adresse'] != null) { echo htmlspecialchars($entreprise['adresse']); } else { echo 'Non renseignée'; } ?></p>
                            <p><strong>Site web :</strong>
                                <a href="<?php echo htmlspecialchars($entreprise['site_web']); ?>" target="_blank" rel="noopener"><?php echo htmlspecialchars($entreprise['site_web']); ?></a>
                            </p>

                            <div class="card-actions">
                                <a href="profil_entreprise.php?id=<?php echo $entreprise['id_entreprise']; ?>" class="btn btn-sm btn-outline">Voir</a>
                                <?php if ($peutSeRattacher && !$estMonEntreprise) { ?>
                                    <form action="../../src/traitement/rattachement_entreprise.php" method="post">
                                        <input type="hidden" name="id_entreprise" value="<?php echo $entreprise['id_entreprise']; ?>">
                                        <input type="hidden" name="action" value="rattacher">
                                        <button type="submit" class="btn btn-sm btn-primary">Me rattacher</button>
                                    </form>
                                <?php } ?>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_entreprise.js"></script>
</body>
</html>
