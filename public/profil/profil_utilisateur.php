<?php

require_once __DIR__ . '/../../src/traitement/ProfilFormulaire_ahmed.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="profil_utilisateur.css">
</head>
<body>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<!-- ===== HEADER / NAV ===== -->
<header class="site-header">
    <div class="container header-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="../page_acceuille/page_accueil.php">Programme</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Activités</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
            </ul>
        </nav>

        <div class="header-actions">
            <?php afficherActionsHeader('../'); ?>
        </div>

        <button class="nav-toggle" id="nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false" aria-controls="main-nav">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Mon profil</p>
            <h1>Mon profil</h1>
            <p>Retrouvez les informations de votre compte.</p>
        </div>
    </section>

    <!-- ===== PROFIL ===== -->
    <section class="section profil-section">
        <div class="container">
            <div class="profil-card">

                <?php if ($erreur): ?>
                    <p class="form-status form-status--error" role="alert"><?= e($erreur) ?></p>
                <?php else: ?>

                    <div class="profil-identite">
                        <div class="profil-avatar" aria-hidden="true">
                            <?= e(mb_strtoupper(mb_substr($utilisateur->getPrenom(), 0, 1) . mb_substr($utilisateur->getNom(), 0, 1))) ?>
                        </div>
                        <div>
                            <h2><?= e($utilisateur->getPrenom() . ' ' . $utilisateur->getNom()) ?></h2>
                            <span class="badge"><?= e($roles[$utilisateur->getRole()] ?? $utilisateur->getRole()) ?></span>
                        </div>
                    </div>

                    <dl class="profil-infos">
                        <?php foreach ($infos as $libelle => $valeur): ?>
                            <div>
                                <dt><?= e($libelle) ?></dt>
                                <dd><?= e($valeur) ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>

                    <div class="profil-actions">
                        <?php if ($utilisateur->getRole() !== 'gestionnaire'): ?>
                            <a href="../evenement/creation_evenement_Tom.php" class="btn btn-primary">Créer un événement</a>
                        <?php endif; ?>
                        <a href="../../src/traitement/DeconnexionTraitement_ahmed.php" class="btn btn-outline">Se déconnecter</a>
                    </div>

                <?php endif; ?>

            </div>

            <?php if (!$erreur): ?>

                <!-- ===== ÉVÉNEMENTS CRÉÉS ===== -->
                <div class="profil-card evenements-card">
                    <h2>Mes événements créés</h2>
                    <?php if (count($evenementsOrganises) === 0): ?>
                        <p class="evenements-vide">Vous n'avez créé aucun événement pour le moment.</p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="evenements-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Titre</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Lieu</th>
                                        <th scope="col">Places</th>
                                        <th scope="col">Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evenementsOrganises as $evenement): ?>
                                        <tr>
                                            <td><?= e($evenement['titre']) ?></td>
                                            <td><?= e($evenement['type_evenement']) ?></td>
                                            <td><?= e(date('d/m/Y H:i', strtotime($evenement['date_evenement']))) ?></td>
                                            <td><?= e($evenement['lieu']) ?></td>
                                            <td><?= e($evenement['nb_places'] ?? '—') ?></td>
                                            <td><span class="badge"><?= e($statutsEvenement[$evenement['statut']] ?? $evenement['statut']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ===== ÉVÉNEMENTS INSCRITS ===== -->
                <div class="profil-card evenements-card">
                    <h2>Mes inscriptions aux événements</h2>
                    <?php if (count($evenementsInscrits) === 0): ?>
                        <p class="evenements-vide">Vous n'êtes inscrit à aucun événement pour le moment.</p>
                    <?php else: ?>
                        <div class="table-wrapper">
                            <table class="evenements-table">
                                <thead>
                                    <tr>
                                        <th scope="col">Titre</th>
                                        <th scope="col">Type</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Lieu</th>
                                        <th scope="col">Inscrit le</th>
                                        <th scope="col">Inscription</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($evenementsInscrits as $evenement): ?>
                                        <tr>
                                            <td><?= e($evenement['titre']) ?></td>
                                            <td><?= e($evenement['type_evenement']) ?></td>
                                            <td><?= e(date('d/m/Y H:i', strtotime($evenement['date_evenement']))) ?></td>
                                            <td><?= e($evenement['lieu']) ?></td>
                                            <td><?= e(date('d/m/Y', strtotime($evenement['date_inscription']))) ?></td>
                                            <td><span class="badge"><?= e($statutsInscription[$evenement['statut_inscription']] ?? $evenement['statut_inscription']) ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

            <?php endif; ?>
        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 Lycée Robert Shuman. Tous droits réservés.</p>
    </div>
</footer>

<script>
    // Menu mobile
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }
</script>
</body>
</html>
