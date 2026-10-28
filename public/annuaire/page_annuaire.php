

<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/repository/UtilisateurRepository_malik.php';

$utilisateurRepository = new UtilisateurRepository_malik();

// Récupération de tous les alumni validés
$alumni = $utilisateurRepository->getAnnuaireAlumni();

// Recherche par nom ou prénom
$nom = trim($_GET['nom'] ?? '');

if ($nom !== '') {
    $alumni = array_filter($alumni, function ($a) use ($nom) {
        return stripos($a['nom_utilisateur'] ?? '', $nom) !== false
                || stripos($a['prenom_utilisateur'] ?? '', $nom) !== false;
    });

    // Réindexation du tableau
    $alumni = array_values($alumni);
}

$pageActive = 'annuaire';

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Annuaire des anciens élèves — Lycée Robert Schuman</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
            href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap"
            rel="stylesheet"
    >

    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="annuaire.css">
</head>

<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- En-tête de la page -->
    <section class="page-hero">
        <div class="hero-overlay"></div>

        <div class="container hero-content">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                / Annuaire
            </p>

            <h1>Annuaire des anciens élèves</h1>

            <p>Retrouvez les alumni du lycée et gardez le contact.</p>
        </div>
    </section>

    <!-- Annuaire -->
    <section class="section annuaire-section">

        <div class="container">

            <!-- Barre de recherche par nom ou prénom -->
            <form method="GET" action="page_annuaire.php" class="annuaire-recherche">

                <input
                        type="search"
                        id="rechercheNom"
                        name="nom"
                        placeholder="Rechercher par nom ou prénom..."
                        >


                <button type="submit">Rechercher</button>

            </form>

            <div class="annuaire-card">

                <?php if (empty($alumni)) { ?>

                    <p class="liste-vide">
                        Aucun alumni ne correspond à votre recherche.
                    </p>

                <?php } else { ?>

                    <table class="annuaire-table">

                        <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                        </tr>
                        </thead>

                        <tbody>

                        <?php foreach ($alumni as $a) { ?>

                            <tr>
                                <td data-label="Nom">
                                    <?= htmlspecialchars(
                                            $a['nom_utilisateur'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                    ) ?>
                                </td>

                                <td data-label="Prénom">
                                    <?= htmlspecialchars(
                                            $a['prenom_utilisateur'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                    ) ?>
                                </td>

                                <td data-label="Email">
                                    <?= htmlspecialchars(
                                            $a['mail'] ?? '',
                                            ENT_QUOTES,
                                            'UTF-8'
                                    ) ?>
                                </td>

                                <td data-label="Téléphone">
                                    <?= !empty($a['telephone'])
                                            ? htmlspecialchars(
                                                    $a['telephone'],
                                                    ENT_QUOTES,
                                                    'UTF-8'
                                            )
                                            : '—'
                                    ?>
                                </td>
                            </tr>

                        <?php } ?>

                        </tbody>

                    </table>

                <?php } ?>

            </div>

        </div>

    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_annuaire.js"></script>

</body>
</html>
