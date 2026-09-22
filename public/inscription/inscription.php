<?php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription — École Lourdeault</title>
    <meta name="description" content="Inscrivez votre enfant à l'École Lourdeault : remplissez le formulaire d'inscription en ligne, notre équipe vous recontacte sous 48h.">
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
        <a href="../page_acceuille/page_accueil.php" class="logo">École<span>Lourdeault</span></a>

        <nav class="main-nav" id="main-nav">
            <ul>
                <li><a href="../page_acceuille/page_accueil.php">Programme</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Activités</a></li>
                <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
            </ul>
        </nav>

        <a href="../page_acceuille/page_accueil.php" class="btn btn-primary header-cta">Inscription Rapide</a>

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
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Inscription</p>
            <h1>Inscrivez votre enfant</h1>
            <p>Remplissez le formulaire ci-dessous, notre équipe vous recontacte sous 48h pour finaliser l'inscription.</p>
        </div>
    </section>

    <!-- ===== FORMULAIRE D'INSCRIPTION ===== -->
    <section class="section register-section">
        <div class="container register-grid">

            <aside class="register-aside">
                <p class="eyebrow">Rejoindre l'école</p>
                <h2>Comment ça marche ?</h2>
                <p>Trois étapes simples pour inscrire votre enfant à l'École Lourdeault.</p>

                <div class="register-steps">
                    <div class="register-step">
                        <span class="register-step-num">1</span>
                        <div class="register-step-text">
                            <strong>Formulaire en ligne</strong>
                            <span>Renseignez les informations de l'élève et du responsable légal.</span>
                        </div>
                    </div>
                    <div class="register-step">
                        <span class="register-step-num">2</span>
                        <div class="register-step-text">
                            <strong>Prise de contact</strong>
                            <span>Notre équipe vous appelle sous 48h pour confirmer les disponibilités.</span>
                        </div>
                    </div>
                    <div class="register-step">
                        <span class="register-step-num">3</span>
                        <div class="register-step-text">
                            <strong>Dossier finalisé</strong>
                            <span>Vous complétez le dossier administratif et la place est réservée.</span>
                        </div>
                    </div>
                </div>

                <p style="margin-top:28px;">
                    Une question ? <a href="../page_acceuille/page_accueil.php" style="color:#fff; text-decoration:underline;">Contactez-nous</a>
                    ou appelez le <a href="tel:0123456789" style="color:#fff; text-decoration:underline;">01 23 45 67 89</a>.
                </p>
            </aside>

            <form class="register-form" id="register-form" novalidate>

                <fieldset class="form-fieldset">
                    <legend>Informations sur l'élève</legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="eleve-prenom">Prénom <span class="required">*</span></label>
                            <input type="text" id="eleve-prenom" name="eleve-prenom" autocomplete="given-name" required>
                        </div>
                        <div class="form-group">
                            <label for="eleve-nom">Nom <span class="required">*</span></label>
                            <input type="text" id="eleve-nom" name="eleve-nom" autocomplete="family-name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="eleve-naissance">Date de naissance <span class="required">*</span></label>
                            <input type="date" id="eleve-naissance" name="eleve-naissance" required>
                        </div>
                        <div class="form-group">
                            <label for="eleve-niveau">Niveau souhaité <span class="required">*</span></label>
                            <select id="eleve-niveau" name="eleve-niveau" required>
                                <option value="" disabled selected>Choisir un niveau</option>
                                <option value="maternelle">Maternelle</option>
                                <option value="cp">CP</option>
                                <option value="ce1">CE1</option>
                                <option value="ce2">CE2</option>
                                <option value="cm1">CM1</option>
                                <option value="cm2">CM2</option>
                                <option value="6e">6ᵉ</option>
                                <option value="5e">5ᵉ</option>
                                <option value="4e">4ᵉ</option>
                                <option value="3e">3ᵉ</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>Responsable légal</legend>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="parent-prenom">Prénom <span class="required">*</span></label>
                            <input type="text" id="parent-prenom" name="parent-prenom" autocomplete="given-name" required>
                        </div>
                        <div class="form-group">
                            <label for="parent-nom">Nom <span class="required">*</span></label>
                            <input type="text" id="parent-nom" name="parent-nom" autocomplete="family-name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="parent-email">Email <span class="required">*</span></label>
                            <input type="email" id="parent-email" name="parent-email" autocomplete="email" required>
                        </div>
                        <div class="form-group">
                            <label for="parent-telephone">Téléphone <span class="required">*</span></label>
                            <input type="tel" id="parent-telephone" name="parent-telephone" autocomplete="tel" pattern="[0-9 +().-]{6,}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="parent-adresse">Adresse postale</label>
                        <input type="text" id="parent-adresse" name="parent-adresse" autocomplete="street-address" placeholder="Numéro, rue, ville, code postal">
                    </div>
                </fieldset>

                <fieldset class="form-fieldset">
                    <legend>Informations complémentaires</legend>

                    <div class="form-group">
                        <label for="message">Message (optionnel)</label>
                        <textarea id="message" name="message" rows="4" placeholder="Besoins particuliers, questions, disponibilités..."></textarea>
                        <span class="form-hint">Ces informations nous aident à mieux préparer l'accueil de votre enfant.</span>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="consentement" name="consentement" required>
                        <label for="consentement">J'accepte que mes informations soient utilisées par l'École Lourdeault dans le cadre du traitement de cette demande d'inscription. <span class="required">*</span></label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" id="newsletter" name="newsletter">
                        <label for="newsletter">Je souhaite recevoir les actualités et événements de l'école par email.</label>
                    </div>
                </fieldset>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary btn-lg">Envoyer ma demande d'inscription</button>
                    <p class="form-status" id="register-status" hidden></p>
                </div>

            </form>

        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">École<span>Lourdeault</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 École Lourdeault. Tous droits réservés.</p>
    </div>
</footer>

<script src="script_inscription.js"></script>
</body>
</html>
