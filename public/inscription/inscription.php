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
// Phrase affichée sous chaque profil, pour aider à choisir
$descriptionsRoles = array(
    'etudiant' => 'Je suis actuellement élève au lycée.',
    'alumni' => "J'ai été élève au lycée.",
    'partenaire' => 'Je représente une entreprise.',
    'professeur' => "J'enseigne au lycée."
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
    <meta name="description" content="Créez votre compte sur la plateforme du Lycée Robert Schuman : étudiants, anciens élèves et partenaires entreprise.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="inscription.css?v=<?php echo filemtime(__DIR__ . '/inscription.css'); ?>">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>


<main id="main">

    <!-- ===== PAGE HERO ===== -->
    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Inscription</p>
            <h1>Inscrivez-vous</h1>
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
                            <span>Un gestionnaire vérifie votre demande. Vous recevez un email dès qu'elle est acceptée.</span>
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
                    Une question ? <a href="../page_acceuille/page_accueil.php#contact">Contactez-nous</a>.
                </p>
            </aside>

            <!-- enctype="multipart/form-data" : obligatoire pour envoyer un fichier (le CV) -->
            <form class="register-form" id="register-form" action="../../src/traitement/InscriptionTraitement_ahmed.php" method="POST" enctype="multipart/form-data">

                <?php if ($succes != "") { ?>
                    <p class="form-status form-status--success" id="inscription-message" tabindex="-1"><?php echo htmlspecialchars($succes); ?></p>
                <?php } ?>
                <?php if (count($erreurs) > 0) { ?>
                    <!-- tabindex="-1" : le script place le curseur sur ce bloc pour que l'erreur soit vue tout de suite -->
                    <div class="form-status form-status--error" id="inscription-message" role="alert" tabindex="-1">
                        <p>Votre inscription n'a pas pu être enregistrée :</p>
                        <ul>
                            <?php foreach ($erreurs as $erreur) { ?>
                                <li><?php echo htmlspecialchars($erreur); ?></li>
                            <?php } ?>
                        </ul>
                    </div>
                <?php } ?>

                <p class="form-legende">Les champs marqués d'un <span class="required">*</span> sont obligatoires.</p>

                <!-- ===== ÉTAPE : PROFIL (cartes cliquables, ce sont des boutons radio) ===== -->
                <fieldset class="form-fieldset">
                    <legend>Vous êtes <span class="required">*</span></legend>

                    <div class="profil-choix">
                        <?php foreach ($roles as $valeur => $libelle) { ?>
                            <!-- Le bouton radio est placé avant sa carte : le CSS colore la carte quand il est coché -->
                            <input type="radio" class="profil-radio" id="role_<?php echo $valeur; ?>" name="role" value="<?php echo $valeur; ?>" required <?php if (ancienneValeur($valeurs, 'role') == $valeur) { echo 'checked'; } ?>>
                            <label class="profil-carte" for="role_<?php echo $valeur; ?>">
                                <?php if ($valeur == 'etudiant') { ?>
                                    <svg class="profil-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5z"></path><path d="M6 12v5c3 2 9 2 12 0v-5"></path></svg>
                                <?php } elseif ($valeur == 'alumni') { ?>
                                    <svg class="profil-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"></circle><path d="M2 21c0-4 3-6 7-6s7 2 7 6"></path><path d="M17 4a4 4 0 0 1 0 8"></path><path d="M22 21c0-3-1-5-4-6"></path></svg>
                                <?php } elseif ($valeur == 'partenaire') { ?>
                                    <svg class="profil-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"></rect><path d="M9 8h1M14 8h1M9 12h1M14 12h1M10 21v-4h4v4"></path></svg>
                                <?php } else { ?>
                                    <svg class="profil-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v14"></path><path d="M2 19h20"></path><path d="M8 8h8M8 12h5"></path></svg>
                                <?php } ?>
                                <span class="profil-titre"><?php echo $libelle; ?></span>
                                <span class="profil-description"><?php echo $descriptionsRoles[$valeur]; ?></span>
                            </label>
                        <?php } ?>
                    </div>

                    <!-- Message affiché tant qu'aucun profil n'est choisi (géré par script_inscription.js) -->
                    <p class="profil-attente" id="profil-attente" hidden>Choisissez votre profil pour afficher la suite du formulaire.</p>
                </fieldset>

                <!-- ===== ÉTAPE : INFORMATIONS PERSONNELLES ===== -->
                <fieldset class="form-fieldset" data-apres-profil>
                    <legend>Vos informations</legend>

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
                            <input type="email" id="mail" name="mail" maxlength="255" autocomplete="email" placeholder="prenom.nom@exemple.fr" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'mail')); ?>" required>
                            <span class="form-hint">Sert à vous connecter et à recevoir la réponse du gestionnaire.</span>
                        </div>
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" maxlength="20" autocomplete="tel" placeholder="06 12 34 56 78" pattern="[0-9 +().\-]{6,20}" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'telephone')); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="date_naissance">Date de naissance</label>
                            <input type="date" id="date_naissance" name="date_naissance" max="<?php echo date('Y-m-d'); ?>" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'date_naissance')); ?>">
                        </div>
                    </div>
                </fieldset>

                <!-- ===== ÉTAPE : MOT DE PASSE ===== -->
                <fieldset class="form-fieldset" data-apres-profil>
                    <legend>Votre mot de passe</legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mdp">Mot de passe <span class="required">*</span></label>
                            <div class="mdp-champ">
                                <input type="password" id="mdp" name="mdp" minlength="8" autocomplete="new-password" aria-describedby="mdp-regle" required>
                                <button type="button" class="mdp-afficher" data-cible="mdp" aria-pressed="false">Afficher</button>
                            </div>
                            <span class="form-hint" id="mdp-regle">8 caractères minimum.</span>
                        </div>
                        <div class="form-group">
                            <label for="mdp_confirm">Confirmer le mot de passe <span class="required">*</span></label>
                            <div class="mdp-champ">
                                <input type="password" id="mdp_confirm" name="mdp_confirm" minlength="8" autocomplete="new-password" aria-describedby="mdp-identique" required>
                                <button type="button" class="mdp-afficher" data-cible="mdp_confirm" aria-pressed="false">Afficher</button>
                            </div>
                            <span class="form-hint" id="mdp-identique" aria-live="polite"></span>
                        </div>
                    </div>
                </fieldset>

                <!-- ===== ÉTAPE : PARCOURS (étudiant et alumni) ===== -->
                <fieldset class="form-fieldset" data-roles="etudiant alumni" hidden>
                    <legend>Votre parcours au lycée</legend>

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
                            <span class="form-hint">Année d'obtention du diplôme.</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="specialite">Option ou spécialité</label>
                        <input type="text" id="specialite" name="specialite" maxlength="150" placeholder="Exemple : SLAM, SISR" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'specialite')); ?>">
                    </div>
                </fieldset>

                <!-- ===== ÉTAPE : CV (étudiant obligatoire, alumni facultatif) ===== -->
                <fieldset class="form-fieldset" data-roles="etudiant alumni" hidden>
                    <legend>Votre CV</legend>

                    <div class="form-group">
                        <span class="form-label">CV au format PDF <span class="required" data-roles="etudiant">*</span><span class="form-facultatif" data-roles="alumni">(facultatif)</span></span>
                        <!-- Le vrai champ fichier est caché visuellement : on clique sur la zone (son label) -->
                        <input type="file" class="fichier-input" id="cv" name="cv" accept="application/pdf,.pdf" data-required-roles="etudiant" aria-describedby="cv-nom cv-regle">
                        <label class="fichier-zone" for="cv">
                            <svg class="fichier-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><path d="M14 3v6h6"></path><path d="M12 18v-6M9 15l3-3 3 3"></path></svg>
                            <span class="fichier-texte">
                                <span class="fichier-action">Choisir un fichier PDF</span>
                                <span class="fichier-nom" id="cv-nom">Aucun fichier choisi</span>
                            </span>
                        </label>
                        <span class="form-hint" id="cv-regle">PDF uniquement, 2 Mo maximum.</span>
                    </div>
                </fieldset>

                <!-- ===== ÉTAPE : ENSEIGNEMENT (professeur) ===== -->
                <fieldset class="form-fieldset" data-roles="professeur" hidden>
                    <legend>Votre enseignement</legend>

                    <div class="form-group">
                        <label for="specialite_professeur">Matière enseignée / spécialité <span class="required">*</span></label>
                        <input type="text" id="specialite_professeur" name="specialite" maxlength="150" placeholder="Exemple : Mathématiques" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'specialite')); ?>" required>
                    </div>
                </fieldset>

                <!-- ===== ÉTAPE : ENTREPRISE (partenaire, et poste facultatif pour un alumni) ===== -->
                <fieldset class="form-fieldset" data-roles="alumni partenaire" hidden>
                    <legend>Votre situation professionnelle</legend>

                    <div class="form-row">
                        <div class="form-group" data-roles="partenaire" hidden>
                            <label for="id_entreprise">Entreprise <span class="required">*</span></label>
                            <select id="id_entreprise" name="id_entreprise" required>
                                <option value="" disabled <?php if (ancienneValeur($valeurs, 'id_entreprise') == "") { echo 'selected'; } ?>>Choisir une entreprise</option>
                                <?php foreach ($entreprises as $entreprise) { ?>
                                    <option value="<?php echo $entreprise['id_entreprise']; ?>" <?php if (ancienneValeur($valeurs, 'id_entreprise') == $entreprise['id_entreprise']) { echo 'selected'; } ?>><?php echo htmlspecialchars($entreprise['nom_entreprise']); ?></option>
                                <?php } ?>
                            </select>
                            <span class="form-hint">Votre entreprise n'est pas dans la liste ? <a href="../page_acceuille/page_accueil.php#contact">Contactez-nous</a>.</span>
                        </div>
                        <div class="form-group">
                            <label for="poste">Poste <span class="required" data-roles="partenaire">*</span><span class="form-facultatif" data-roles="alumni">(facultatif)</span></label>
                            <input type="text" id="poste" name="poste" maxlength="150" placeholder="Exemple : Responsable recrutement" value="<?php echo htmlspecialchars(ancienneValeur($valeurs, 'poste')); ?>" data-required-roles="partenaire">
                        </div>
                    </div>

                    <div class="form-group" data-roles="partenaire" hidden>
                        <label for="motif_inscription">Motif de l'inscription <span class="required">*</span></label>
                        <textarea id="motif_inscription" name="motif_inscription" rows="3" maxlength="255" placeholder="Exemple : proposer des stages à vos étudiants" aria-describedby="motif-compteur" required><?php echo htmlspecialchars(ancienneValeur($valeurs, 'motif_inscription')); ?></textarea>
                        <span class="form-hint form-compteur" id="motif-compteur">0 / 255 caractères</span>
                    </div>
                </fieldset>

                <!-- ===== VALIDATION ===== -->
                <div class="form-validation" data-apres-profil>
                    <div class="form-check">
                        <input type="checkbox" id="consentement" name="consentement" value="1" required <?php if (ancienneValeur($valeurs, 'consentement') != "") { echo 'checked'; } ?>>
                        <label for="consentement">J'accepte que mes informations soient utilisées par le Lycée Robert Schuman dans le cadre du traitement de cette demande d'inscription. <span class="required">*</span></label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">Valider l'inscription</button>
                    <p class="form-connexion">Vous avez déjà un compte ? <a href="../connexion/connexion.php">Se connecter</a></p>
                </div>

            </form>

        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script_inscription.js?v=<?php echo filemtime(__DIR__ . '/script_inscription.js'); ?>"></script>
</body>
</html>
