<?php

$message = '';
$typeMessage = '';
$evenements = [];
$idUtilisateur = 0;

require_once __DIR__ . '/../../src/traitement/ListeEvenementsTraitement_Tom.php';
require_once __DIR__ . '/../../src/traitement/SessionUtilisateur_ahmed.php';

function h($valeur){
    return htmlspecialchars((string) ($valeur ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Liste des événements — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="liste_evenement_Tom.css">
</head>

<body>
<a class="skip-link" href="#main">Aller au contenu principal</a>

<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>
        <nav class="main-nav" id="main-nav">
            <ul>
                <li>
                    <a href="../page_acceuille/page_accueil.php">Programme</a>
                </li>
                <li>
                    <a href="liste_evenement_Tom.php">Événements</a>
                </li>
                <li>
                    <a href="../offre/liste_offres_Tom.php">Offres</a>
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

<main id="main" class="evenements-page">

    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a>/ Événements</p>

            <h1>Les événements</h1>

            <p>Découvrez les événements proposés par le lycée et inscrivez-vous aux activités qui vous intéressent.</p>
        </div>
    </section>

    <section class="section evenements-section">
        <div class="container">

            <h2 class="evenements-titre-section">
                Événements disponibles
            </h2>

            <p class="evenements-description">Retrouvez les événements publiés et consultez leurs informations pratiques.</p>

            <?php if (!empty($message)): ?>
                <div class="form-status <?= $typeMessage === 'erreur' ? 'form-status--error' : '' ?>" role="<?= $typeMessage === 'erreur' ? 'alert' : 'status' ?>">
                    <?= h($message) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($evenements)): ?>
                <div class="evenements-vide">
                    <h3>Aucun événement disponible</h3>
                    <p>
                        Il n'y a actuellement aucun événement publié.
                        Revenez consulter cette page ultérieurement.
                    </p>
                </div>
            <?php else: ?>
                <div class="evenements-grille">
                    <?php foreach ($evenements as $evenement): ?>
                        <?php $idEvenement = (int) $evenement['id_evenement'];$nombreInscrits = (int) ($evenement['nombre_inscrits'] ?? 0);
                        $nombrePlaces = (isset($evenement['nb_places']) && $evenement['nb_places'] !== '') ? (int) $evenement['nb_places'] : null;
                        $placesRestantes = $nombrePlaces !== null ? max(0, $nombrePlaces - $nombreInscrits) : null;
                        $dejaInscrit = (int) ($evenement['deja_inscrit'] ?? 0) === 1;
                        $complet = $placesRestantes !== null && $placesRestantes <= 0;
                        $timestamp = !empty($evenement['date_evenement']) ? strtotime($evenement['date_evenement']) : false; ?>

                        <article class="evenement-carte">
                            <span class="evenement-carte__type">
                                <?= h($evenement['type_evenement']) ?>
                            </span>

                            <h3>
                                <?= h($evenement['titre']) ?>
                            </h3>

                            <p class="evenement-carte__description">
                                <?= nl2br(h($evenement['description'])) ?>
                            </p>

                            <dl class="evenement-infos">
                                <div class="evenement-info">
                                    <dt>Lieu</dt>
                                    <dd>
                                        <?= h(!empty($evenement['lieu']) ? $evenement['lieu'] : 'Non renseigné') ?>
                                    </dd>
                                </div>

                                <div class="evenement-info">
                                    <dt>Date</dt>
                                    <dd>
                                        <?php if ($timestamp !== false): ?>
                                            <?= h(date('d/m/Y à H\hi', $timestamp)) ?>
                                        <?php else: ?>
                                            Non renseignée
                                        <?php endif; ?>
                                    </dd>
                                </div>

                                <?php if (!empty($evenement['elements_requis'])): ?>
                                    <div class="evenement-info">
                                        <dt>Éléments requis</dt>
                                        <dd>
                                            <?= nl2br(h($evenement['elements_requis'])) ?>
                                        </dd>
                                    </div>
                                <?php endif; ?>
                            </dl>

                            <div class="evenement-places">
                                <span class="evenement-places__libelle">
                                    Places disponibles
                                </span>
                                <span class="evenement-places__nombre
                                    <?= $complet ? 'evenement-places__nombre--complet' : '' ?>">
                                    <?php if ($nombrePlaces === null): ?>
                                        Non limitées
                                    <?php else: ?>
                                        <?= h($placesRestantes) ?>
                                        / <?= h($nombrePlaces) ?>
                                    <?php endif; ?>
                                </span>
                            </div>

                            <div class="evenement-actions">
                                <?php if ($dejaInscrit): ?>
                                    <span class="btn btn-outline" aria-disabled="true">
                                        Déjà inscrit
                                    </span>
                                <?php elseif ($complet): ?>
                                    <span class="btn btn-outline" aria-disabled="true">
                                        Événement complet
                                    </span>
                                <?php else: ?>
                                    <form method="POST" action="../../src/traitement/InscriptionEvenementTraitement_Tom.php">
                                        <input type="hidden" name="id_evenement" value="<?= h($idEvenement) ?>">

                                        <button type="submit" class="btn btn-primary">
                                            S'inscrire
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
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">
            Lycée<span>Robert Shuman</span>
        </a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li>
                <a href="../page_acceuille/page_accueil.php">
                    Contact
                </a>
            </li>
        </ul>

        <p class="footer-copy">
            © 2026 Lycée Robert Shuman. Tous droits réservés.
        </p>
    </div>
</footer>

<script>
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
            navToggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
        });
    }
</script>

</body>
</html>