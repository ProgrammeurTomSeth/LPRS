<?php

require_once __DIR__ . '/../../src/traitement/EntrepriseFormulaire_ahmed.php';

$entreprise = null;
$erreurChargement = null;
$idEntreprise = $_GET['id'] ?? '';
if (!ctype_digit((string) $idEntreprise)) {
    $erreurChargement = "Aucune entreprise sélectionnée.";
} else {
    try {
        $entreprise = (new EntrepriseRepository_Tom())->getEntreprise((int) $idEntreprise);
        if ($entreprise === null) {
            $erreurChargement = "Cette entreprise n'existe pas.";
        }
    } catch (PDOException $e) {
        $erreurChargement = "Impossible de charger l'entreprise pour le moment.";
    }
}
$estMonEntreprise = $entreprise !== null && $idEntrepriseUtilisateur === (int) $entreprise->getIdEntreprise();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($entreprise !== null ? $entreprise->getNomEntreprise() : 'Entreprise') ?> — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="profil_entreprise.css">
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
                <li><a href="page_entreprise.php">Entreprise</a></li>
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
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / <a href="page_entreprise.php">Entreprises</a> / <?= e($entreprise !== null ? $entreprise->getNomEntreprise() : 'Entreprise') ?></p>
            <h1><?= e($entreprise !== null ? $entreprise->getNomEntreprise() : 'Entreprise introuvable') ?></h1>
            <?php if ($entreprise !== null && $entreprise->getActivite()): ?>
                <p><?= e($entreprise->getActivite()) ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== FICHE ENTREPRISE ===== -->
    <section class="section entreprises-section">
        <div class="container">
            <div class="fiche-card">

                <?php if ($messageSucces): ?>
                    <p class="form-status form-status--success" role="status"><?= e($messageSucces) ?></p>
                <?php endif; ?>
                <?php if ($messageErreur): ?>
                    <p class="form-status form-status--error" role="alert"><?= e($messageErreur) ?></p>
                <?php endif; ?>

                <?php if ($erreurChargement): ?>
                    <p class="form-status form-status--error" role="alert"><?= e($erreurChargement) ?></p>
                    <a href="page_entreprise.php" class="btn btn-outline">Retour à la liste</a>
                <?php else: ?>

                    <div class="fiche-entete">
                        <h2><?= e($entreprise->getNomEntreprise()) ?></h2>
                        <?php if ($estMonEntreprise): ?>
                            <span class="badge">Votre entreprise</span>
                        <?php endif; ?>
                    </div>

                    <dl class="fiche-infos">
                        <div>
                            <dt>Activité</dt>
                            <dd><?= e($entreprise->getActivite() ?? 'Non renseignée') ?></dd>
                        </div>
                        <div>
                            <dt>Adresse</dt>
                            <dd><?= e($entreprise->getAdresse() ?? 'Non renseignée') ?></dd>
                        </div>
                        <div>
                            <dt>Site web</dt>
                            <dd><a href="<?= e($entreprise->getSiteWeb()) ?>" target="_blank" rel="noopener"><?= e($entreprise->getSiteWeb()) ?></a></dd>
                        </div>
                    </dl>

                    <!-- ===== RATTACHEMENT ===== -->
                    <div class="fiche-rattachement">
                        <?php if (!estConnecte()): ?>
                            <p><a href="../connexion/connexion.php">Connectez-vous</a> pour vous rattacher à cette entreprise.</p>
                        <?php elseif (!$peutSeRattacher): ?>
                            <p>Seuls les alumni et les partenaires peuvent se rattacher à une entreprise.</p>
                        <?php elseif ($estMonEntreprise): ?>
                            <p>Vous êtes rattaché à cette entreprise<?= $utilisateurConnecte->getPoste() ? ' en tant que <strong>' . e($utilisateurConnecte->getPoste()) . '</strong>' : '' ?>.</p>
                            <?php if ($utilisateurConnecte->getRole() !== 'partenaire'): ?>
                                <form action="../../src/traitement/rattachement_entreprise.php" method="post">
                                    <input type="hidden" name="id_entreprise" value="<?= (int) $entreprise->getIdEntreprise() ?>">
                                    <input type="hidden" name="action" value="detacher">
                                    <input type="hidden" name="retour" value="profil">
                                    <button type="submit" class="btn btn-danger">Me détacher de cette entreprise</button>
                                </form>
                            <?php endif; ?>
                        <?php else: ?>
                            <h3>Vous travaillez ici ?</h3>
                            <?php if ($idEntrepriseUtilisateur !== null): ?>
                                <p>Vous êtes actuellement rattaché à une autre entreprise. Ce rattachement la remplacera.</p>
                            <?php endif; ?>
                            <form action="../../src/traitement/rattachement_entreprise.php" method="post" class="rattachement-form">
                                <input type="hidden" name="id_entreprise" value="<?= (int) $entreprise->getIdEntreprise() ?>">
                                <input type="hidden" name="action" value="rattacher">
                                <input type="hidden" name="retour" value="profil">
                                <label for="poste">Votre poste <span class="optionnel">(facultatif)</span></label>
                                <input type="text" id="poste" name="poste" maxlength="150" value="<?= e($utilisateurConnecte->getPoste() ?? '') ?>" placeholder="Exemple : Développeur web">
                                <button type="submit" class="btn btn-primary">Me rattacher à cette entreprise</button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <div class="fiche-actions">
                        <a href="page_entreprise.php" class="btn btn-outline">Retour à la liste</a>
                    </div>

                <?php endif; ?>
            </div>
        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Schuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 Lycée Robert Schuman. Tous droits réservés.</p>
    </div>
</footer>

<script src="script_entreprise.js"></script>
</body>
</html>