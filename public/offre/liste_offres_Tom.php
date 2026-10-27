<?php

$message = '';
$typeMessage = '';
$offres = [];
$candidaturesUtilisateur = [];
$roleUtilisateur = '';
$idUtilisateur = 0;

require_once __DIR__ . '/../../src/traitement/ListeOffreTraitement_Tom.php';

function h($valeur){
    return htmlspecialchars((string) ($valeur ?? ''), ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres d'emploi et de stage — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="liste_offres_Tom.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">
            Lycée<span>Robert Shuman</span>
        </a>
        <nav class="main-nav" id="main-nav">
            <ul>
                <li>
                    <a href="../page_acceuille/page_accueil.php">Programme</a>
                </li>
                <li>
                    <a href="../evenement/liste_evenement_Tom.php">Événements</a>
                </li>
                <li>
                    <a href="liste_offres_Tom.php" aria-current="page">Offres</a>
                </li>
                <li>
                    <a href="../page_acceuille/page_accueil.php">Contact</a>
                </li>
            </ul>
        </nav>
        <div class="header-actions">
            <?php afficherActionsHeader('../'); ?>
        </div>
        <button class="nav-toggle" id="nav-toggle" type="button" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<main id="main">
    <section class="page-hero">
        <div class="container">
            <p class="breadcrumb">
                <a href="../page_acceuille/page_accueil.php">Accueil</a>
                / Offres
            </p>
            <h1>Offres d'emploi et de stage</h1>
            <p>
                Découvrez les opportunités proposées par les entreprises partenaires
                et postulez aux offres qui vous intéressent.
            </p>
        </div>
    </section>

    <section class="section offres-section">
        <div class="container">

            <h2>Les offres disponibles</h2>

            <p class="section-description">
                Retrouvez ici les offres actuellement ouvertes aux candidatures.
            </p>

            <?php if ($message !== ''): ?>
                <div class="message <?= $typeMessage === 'erreur' ? 'message-erreur' : 'message-succes' ?>" role="<?= $typeMessage === 'erreur' ? 'alert' : 'status' ?>">
                    <?= h($message) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($offres)): ?>
                <div class="offres-vides">
                    <h3>Aucune offre disponible</h3>
                    <p>
                        Il n'y a actuellement aucune offre ouverte.
                        Revenez consulter cette page ultérieurement.
                    </p>
                </div>
            <?php else: ?>
                <div class="offres-grille">
                    <?php foreach ($offres as $offre): ?>
                        <?php
                        $idOffre = (int) $offre['id_offre'];
                        $dejaPostule = in_array($idOffre, $candidaturesUtilisateur, true);
                        $profilCompatible = $offre['profil_cible'] === 'tous' || $offre['profil_cible'] === $roleUtilisateur;
                        $peutPostuler = estConnecte() && in_array($roleUtilisateur, ['etudiant', 'alumni'], true) && $profilCompatible;
                        ?>
                        <article class="offre-carte">
                            <div class="offre-entete">
                                <span class="offre-type">
                                    <?= h(ucfirst($offre['type'])) ?>
                                </span>
                                <span class="offre-statut">
                                    Ouverte
                                </span>
                            </div>
                            <h3><?= h($offre['titre']) ?></h3>
                            <p class="offre-entreprise">
                                <?= h($offre['nom_entreprise'] ?? 'Entreprise non renseignée') ?>
                            </p>
                            <div class="offre-description">
                                <?= nl2br(h($offre['description'])) ?>
                            </div>
                            <?php if (!empty($offre['missions'])): ?>
                                <div class="offre-missions">
                                    <h4>Missions</h4>
                                    <p>
                                        <?= nl2br(h($offre['missions'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                            <div class="offre-informations">
                                <p>
                                    <strong>Profil recherché :</strong>
                                    <?php
                                    $profils = [
                                            'tous' => 'Étudiants et alumni',
                                            'etudiant' => 'Étudiants',
                                            'alumni' => 'Alumni'
                                    ];
                                    echo h($profils[$offre['profil_cible']] ?? $offre['profil_cible']);
                                    ?>
                                </p>
                                <p>
                                    <strong>Salaire :</strong>
                                    <?php if ($offre['salaire'] !== null): ?>
                                        <?= h(number_format((float) $offre['salaire'], 2, ',', ' ')) ?> €
                                    <?php else: ?>
                                        Non renseigné
                                    <?php endif; ?>
                                </p>
                                <p>
                                    <strong>Publication :</strong>
                                    <?php
                                    $timestamp = strtotime($offre['date_publication']);
                                    echo $timestamp !== false
                                            ? h(date('d/m/Y', $timestamp))
                                            : 'Non renseignée';
                                    ?>
                                </p>
                            </div>
                            <div class="offre-candidature">
                                <?php if ($dejaPostule): ?>
                                    <p class="deja-postule">
                                        Vous avez déjà postulé à cette offre.
                                    </p>
                                <?php elseif (!$peutPostuler): ?>
                                    <?php if (!estConnecte()): ?>
                                        <p>
                                            Connectez-vous avec un compte étudiant
                                            ou alumni pour postuler.
                                        </p>
                                        <a class="bouton" href="../connexion/connexion.php">
                                            Se connecter
                                        </a>
                                    <?php elseif (!in_array($roleUtilisateur, ['etudiant', 'alumni'], true)): ?>
                                        <p>
                                            La candidature est réservée aux étudiants
                                            et aux alumni.
                                        </p>
                                    <?php else: ?>
                                        <p>
                                            Cette offre ne correspond pas à votre profil.
                                        </p>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <form method="POST" action="liste_offres_Tom.php" class="form-candidature">
                                        <input type="hidden" name="id_offre" value="<?= $idOffre ?>">
                                        <label for="motivation-<?= $idOffre ?>">
                                            Votre motivation
                                        </label>
                                        <textarea id="motivation-<?= $idOffre ?>" name="motivation" rows="4" maxlength="5000" required placeholder="Expliquez pourquoi cette offre vous intéresse..."></textarea>
                                        <button type="submit" class="bouton">
                                            Postuler
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer class="site-footer">
    <div class="container">
        <p>© 2026 Lycée Robert Shuman</p>
    </div>
</footer>

<script>
    const boutonMenu = document.getElementById('nav-toggle');
    const menu = document.getElementById('main-nav');

    if (boutonMenu && menu) {
        boutonMenu.addEventListener('click', function () {
            const ouvert = boutonMenu.getAttribute('aria-expanded') === 'true';

            boutonMenu.setAttribute('aria-expanded', String(!ouvert));
            menu.classList.toggle('is-open', !ouvert);
        });
    }
</script>
</body>
</html>