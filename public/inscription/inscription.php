<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/repository/Formation_ahmed.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';

// Messages et valeurs saisies laissés par InscriptionTraitement_ahmed.php
$erreurs = array();
$valeurs = array();
$succes = "";
if (!empty($_SESSION['inscription_erreurs'])) {
    $erreurs = $_SESSION['inscription_erreurs'];
    $valeurs = $_SESSION['inscription_valeurs'];
    $_SESSION['inscription_erreurs'] = array();
}
if (!empty($_SESSION['inscription_succes'])) {
    $succes = $_SESSION['inscription_succes'];
    $_SESSION['inscription_succes'] = "";
}

// Profils qu'un visiteur peut choisir (le gestionnaire est créé par un autre gestionnaire)
$roles = array(
    'etudiant' => 'Étudiant',
    'alumni' => 'Ancien élève (alumni)',
    'partenaire' => 'Partenaire entreprise',
    'professeur' => 'Professeur'
);
$typesFormation = array(
    'bac_pro' => 'Bac pro',
    'bac_techno' => 'Bac techno',
    'bts' => 'BTS'
);

$formationRepository = new Formation_ahmed();
$formations = $formationRepository->getAllFormations();
$entrepriseRepository = new EntrepriseRepository_Tom();
$entreprises = $entrepriseRepository->getAllEntreprises();

// Renvoie la valeur saisie avant l'erreur pour un champ, ou "" s'il n'y en a pas
function ancienneValeur($valeurs, $champ)
{
    if (!empty($valeurs[$champ])) {
        return $valeurs[$champ];
    }
    return "";
}

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'inscription';

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — Lycée Robert Schuman</title>
    <meta name="description" content="Créez votre compte sur la plateforme de le Lycée Robert Schuman : étudiants, anciens élèves et partenaires entreprise.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="inscription.css">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Inscription</p>
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

                <p class="register-aside-contact">
                    Une question ? <a href="../page_acceuille/page_accueil.php">Contactez-nous</a>
                    ou appelez le <a href="tel:0123456789">01 23 45 67 89</a>.
                </p>
            </aside>

            <form class="register-form" id="register-form" action="../../src/traitement/InscriptionTraitement_ahmed.php" method="POST">

                <?php if ($succes != "") { ?>
                    <p class="form-status form-status--success"><?php echo htmlspecialchars($succes); ?></p>
                <?php } ?>
                <?php if (count($erreurs) > 0) { ?>
                    <div class="form-status form-status--error" role="alert">
                        <ul>
                            <?php foreach ($erreurs as $erreur) { ?>
                                <li><?php echo htmlspecialchars($erreur); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <fieldset class="form-fieldset">
                    <legend>Profil</legend>

                    <div class="form-group">
                        <label for="role">Je suis <span class="required">*</span></label>
                        <select id="role" name="role" required>
                            <option value="" disabled <?php if (ancienneValeur($valeurs, 'role') == "") { echo 'selected'; } ?>>Choisir un profil</option>
                            <?php foreach ($roles as $valeur => $libelle) { ?>
                                <option value="<?php echo $valeur; ?>" <?php if (ancienneValeur($valeurs, 'role') == $valeur) { echo 'selected'; } ?>><?php echo $libelle; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>Informations personnelles</legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="prenom_utilisateur">Prénom <span class="required">*</span></label>
                            <input type="text" id="prenom_utilisateur" name="prenom_utilisateur" maxlength="100" autocomplete="given-name" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'prenom_utilisateur')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="nom_utilisateur">Nom <span class="required">*</span></label>
                            <input type="text" id="nom_utilisateur" name="nom_utilisateur" maxlength="100" autocomplete="family-name" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'nom_utilisateur')); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mail">Email <span class="required">*</span></label>
                            <input type="email" id="mail" name="mail" maxlength="255" autocomplete="email" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'mail')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" maxlength="20" autocomplete="tel" pattern="[0-9 +().\-]{6,20}" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'telephone')); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'date_naissance')); ?>">
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
                                <option value="" disabled <?php if (ancienneValeur($valeurs, 'id_formation') == "") { echo 'selected'; } ?>>Choisir une formation</option>
                                <?php foreach ($formations as $formation) { ?>
                                    <option value="<?php echo $formation['id_formation']; ?>" <?php if (ancienneValeur($valeurs, 'id_formation') == $formation['id_formation']) { echo 'selected'; } ?>><?php echo htmlspecialchars($formation['nom_formation']); ?> (<?php echo $typesFormation[$formation['type_formation']]; ?>)</option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="annee_promo">Année de promotion <span class="required" data-roles="alumni">*</span></label>
                            <input type="number" id="annee_promo" name="annee_promo" min="1950" max="<?php echo date('Y') + 5; ?>" placeholder="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'annee_promo')); ?>" data-required-roles="alumni">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="specialite">Spécialité</label>
                        <input type="text" id="specialite" name="specialite" maxlength="150" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'specialite')); ?>">
                    </div>
                </fieldset>

                <fieldset class="form-fieldset" data-roles="professeur" hidden>
                    <legend>Enseignement</legend>

                    <div class="form-group">
                        <label for="specialite_professeur">Matière enseignée / spécialité <span class="required">*</span></label>
                        <input type="text" id="specialite_professeur" name="specialite" maxlength="150" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'specialite')); ?>" required>
                        <span class="form-hint">Votre compte sera activé après vérification par un gestionnaire.</span>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset" data-roles="alumni partenaire" hidden>
                    <legend>Situation professionnelle</legend>

                    <div class="form-row">
                        <div class="form-group" data-roles="partenaire" hidden>
                            <label for="id_entreprise">Entreprise <span class="required">*</span></label>
                            <select id="id_entreprise" name="id_entreprise" required>
                                <option value="" disabled <?php if (ancienneValeur($valeurs, 'id_entreprise') == "") { echo 'selected'; } ?>>Choisir une entreprise</option>
                                <?php foreach ($entreprises as $entreprise) { ?>
                                    <option value="<?php echo $entreprise['id_entreprise']; ?>" <?php if (ancienneValeur($valeurs, 'id_entreprise') == $entreprise['id_entreprise']) { echo 'selected'; } ?>><?php echo htmlspecialchars($entreprise['nom_entreprise']); ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="poste">Poste <span class="required" data-roles="partenaire">*</span></label>
                            <input type="text" id="poste" name="poste" maxlength="150" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'poste')); ?>" data-required-roles="partenaire">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>Informations complémentaires</legend>

                    <div class="form-group">
                        <label for="motif_inscription">Motif de l'inscription (optionnel)</label>
                        <textarea id="motif_inscription" name="motif_inscription" rows="3" maxlength="255" placeholder="Pourquoi souhaitez-vous rejoindre la plateforme ?"><?php echo htmlspecialchars(ancienneValeur($valeurs, 'motif_inscription')); ?></textarea>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="consentement" name="consentement" value="1" required <?php if (ancienneValeur($valeurs, 'consentement') != "") { echo 'checked'; } ?>>
                        <label for="consentement">J'accepte que mes informations soient utilisées par le Lycée Robert Schuman dans le cadre du traitement de cette demande d'inscription. <span class="required">*</span></label>
                    </div>
                </fieldset>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-lg">Créer mon compte</button>
                </div>

            </form>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_inscription.js"></script>
</body>
</html>
