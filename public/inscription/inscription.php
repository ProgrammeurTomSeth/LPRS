<?php

require_once __DIR__ . '/../../src/traitement/InscriptionFormulaire_ahmed.php';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — École Lourdeault</title>
    <meta name="description" content="Créez votre compte sur la plateforme de l'École Lourdeault : étudiants, anciens élèves et partenaires entreprise.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style_inscription.css">
</head>
<body>

<a class="skip-link" href="#main">Aller au contenu principal</a>

<!-- ===== HEADER / NAV ===== -->
<header class="site-header">
    <div class="container header-inner">
        <a href="../page_accueil/page_accueil.php" class="logo">École<span>Lourdeault</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="../page_accueil/page_accueil.php">Programme</a></li>
                <li><a href="../page_accueil/page_accueil.php">Activités</a></li>
                <li><a href="../page_accueil/page_accueil.php">Contact</a></li>
            </ul>
        </nav>

        <a href="../page_accueil/page_accueil.php" class="btn btn-primary header-cta">Inscription Rapide</a>

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
            <p class="breadcrumb"><a href="../page_accueil/page_accueil.php">Accueil</a> / Inscription</p>
            <h1>Inscrivez vous</h1>
            <p>Remplissez le formulaire ci-dessous, votre compte sera activé après validation par un gestionnaire.</p>
        </div>
    </section>

    <!-- ===== FORMULAIRE D'INSCRIPTION ===== -->
    <section class="section register-section">
        <div class="container register-grid">

            <aside class="register-aside">
                <p class="eyebrow">Rejoindre l'école</p>
                <h2>Comment ça marche ?</h2>
                <p>Trois étapes simples pour vous inscrire</p>

                <div class="register-steps">
                    <div class="register-step">
                        <span class="register-step-num">1</span>
                        <div class="register-step-text">
                            <strong>Formulaire en ligne</strong>
                            <span>Choisissez votre profil et renseignez vos informations.</span>
                        </div>
                    </div>
                    <div class="register-step">
                        <span class="register-step-num">2</span>
                        <div class="register-step-text">
                            <strong>Validation</strong>
                            <span>Un gestionnaire vérifie et valide votre demande.</span>
                        </div>
                    </div>
                    <div class="register-step">
                        <span class="register-step-num">3</span>
                        <div class="register-step-text">
                            <strong>Accès à la plateforme</strong>
                            <span>Vous vous connectez et accédez aux offres, événements et au réseau de l'école.</span>
                        </div>
                    </div>
                </div>

                <p style="margin-top:28px;">
                    Une question ? <a href="../page_accueil/page_accueil.php" style="color:#fff; text-decoration:underline;">Contactez-nous</a>
                    ou appelez le <a href="tel:0123456789" style="color:#fff; text-decoration:underline;">01 23 45 67 89</a>.
                </p>
            </aside>

            <form class="register-form" id="register-form" action="../../src/traitement/InscriptionTraitement_ahmed.php" method="POST">

                <?php if ($succes): ?>
                    <p class="form-status form-status--success"><?= e($succes) ?></p>
                <?php endif; ?>
                <?php if (!empty($erreurs)): ?>
                    <div class="form-status form-status--error" role="alert">
                        <ul>
                            <?php foreach ($erreurs as $erreur): ?>
                                <li><?= e($erreur) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <fieldset class="form-fieldset">
                    <legend>Profil</legend>

                    <div class="form-group">
                        <label for="role">Je suis <span class="required">*</span></label>
                        <select id="role" name="role" required>
                            <option value="" disabled <?= ancienneValeur('role') === '' ? 'selected' : '' ?>>Choisir un profil</option>
                            <?php foreach ($roles as $valeur => $libelle): ?>
                                <option value="<?= e($valeur) ?>" <?= ancienneValeur('role') === $valeur ? 'selected' : '' ?>><?= e($libelle) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>Informations personnelles</legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom_utilisateur">Prénom <span class="required">*</span></label>
                            <input type="text" id="prenom_utilisateur" name="prenom_utilisateur" maxlength="100" autocomplete="given-name" value="<?= e(ancienneValeur('prenom_utilisateur')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nom_utilisateur">Nom <span class="required">*</span></label>
                            <input type="text" id="nom_utilisateur" name="nom_utilisateur" maxlength="100" autocomplete="family-name" value="<?= e(ancienneValeur('nom_utilisateur')) ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mail">Email <span class="required">*</span></label>
                            <input type="email" id="mail" name="mail" maxlength="255" autocomplete="email" value="<?= e(ancienneValeur('mail')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" maxlength="20" autocomplete="tel" pattern="[0-9 +().\-]{6,20}" value="<?= e(ancienneValeur('telephone')) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" max="<?= date('Y-m-d') ?>" value="<?= e(ancienneValeur('date_naissance')) ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mdp">Mot de passe <span class="required">*</span></label>
                            <input type="password" id="mdp" name="mdp" minlength="8" autocomplete="new-password" required>
                            <span class="form-hint">8 caractères minimum.</span>
                        </div>
                        <div class="form-group">
                            <label for="mdp_confirm">Confirmer le mot de passe <span class="required">*</span></label>
                            <input type="password" id="mdp_confirm" name="mdp_confirm" minlength="8" autocomplete="new-password" required>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset" data-roles="etudiant alumni" hidden>
                    <legend>Parcours scolaire</legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="id_formation">Formation <span class="required">*</span></label>
                            <select id="id_formation" name="id_formation" required>
                                <option value="" disabled <?= ancienneValeur('id_formation') === '' ? 'selected' : '' ?>>Choisir une formation</option>
                                <?php foreach ($formations as $formation): ?>
                                    <option value="<?= e($formation->getIdformation()) ?>" <?= ancienneValeur('id_formation') === (string) $formation->getIdformation() ? 'selected' : '' ?>><?= e($formation->getNom_formation()) ?> (<?= e($typesFormation[$formation->getType_formation()] ?? $formation->getType_formation()) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="annee_promo">Année de promotion <span class="required" data-roles="alumni">*</span></label>
                            <input type="number" id="annee_promo" name="annee_promo" min="1950" max="<?= date('Y') + 5 ?>" placeholder="<?= date('Y') ?>" value="<?= e(ancienneValeur('annee_promo')) ?>" data-required-roles="alumni">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="specialite">Spécialité</label>
                        <input type="text" id="specialite" name="specialite" maxlength="150" value="<?= e(ancienneValeur('specialite')) ?>">
                    </div>
                </fieldset>

                <fieldset class="form-fieldset" data-roles="professeur" hidden>
                    <legend>Enseignement</legend>

                    <div class="form-group">
                        <label for="specialite_professeur">Matière enseignée / spécialité <span class="required">*</span></label>
                        <input type="text" id="specialite_professeur" name="specialite" maxlength="150" value="<?= e(ancienneValeur('specialite')) ?>" required>
                        <span class="form-hint">Votre compte sera activé après vérification par un gestionnaire.</span>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset" data-roles="alumni partenaire" hidden>
                    <legend>Situation professionnelle</legend>

                    <div class="form-row">
                        <div class="form-group" data-roles="partenaire" hidden>
                            <label for="id_entreprise">Entreprise <span class="required">*</span></label>
                            <select id="id_entreprise" name="id_entreprise" required>
                                <option value="" disabled <?= ancienneValeur('id_entreprise') === '' ? 'selected' : '' ?>>Choisir une entreprise</option>
                                <?php foreach ($entreprises as $entreprise): ?>
                                    <option value="<?= e($entreprise->getIdEntreprise()) ?>" <?= ancienneValeur('id_entreprise') === (string) $entreprise->getIdEntreprise() ? 'selected' : '' ?>><?= e($entreprise->getNomEntreprise()) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="poste">Poste <span class="required" data-roles="partenaire">*</span></label>
                            <input type="text" id="poste" name="poste" maxlength="150" value="<?= e(ancienneValeur('poste')) ?>" data-required-roles="partenaire">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>Informations complémentaires</legend>

                    <div class="form-group">
                        <label for="motif_inscription">Motif de l'inscription (optionnel)</label>
                        <textarea id="motif_inscription" name="motif_inscription" rows="3" maxlength="255" placeholder="Pourquoi souhaitez-vous rejoindre la plateforme ?"><?= e(ancienneValeur('motif_inscription')) ?></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="consentement" name="consentement" value="1" required <?= ancienneValeur('consentement') !== '' ? 'checked' : '' ?>>
                        <label for="consentement">J'accepte que mes informations soient utilisées par l'École Lourdeault dans le cadre du traitement de cette demande d'inscription. <span class="required">*</span></label>
                    </div>
                </fieldset>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-lg">Créer mon compte</button>
                </div>

            </form>

        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_accueil/page_accueil.php" class="logo">École<span>Lourdeault</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_accueil/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 École Lourdeault. Tous droits réservés.</p>
    </div>
</footer>

<script src="script_inscription.js"></script>
</body>
</html>
