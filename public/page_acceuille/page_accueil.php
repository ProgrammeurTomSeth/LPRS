<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/repository/EvenementRepository_ahmed.php';
require_once '../../src/repository/OffreRepository_Tom.php';

// --- Prochains événements : les 3 premiers événements publiés qui ne sont pas encore passés ---
$evenementRepository = new EvenementRepository_ahmed();
$prochainsEvenements = array();
foreach ($evenementRepository->getEvenementsPublies() as $evenement) {
    // getEvenementsPublies() renvoie des tableaux, triés du plus proche au plus lointain
    if (strtotime($evenement['date_evenement']) >= time() && count($prochainsEvenements) < 3) {
        $prochainsEvenements[] = $evenement;
    }
}

// --- Dernières offres : les 3 offres ouvertes les plus récentes ---
$offreRepository = new OffreRepository_Tom();
$dernieresOffres = array_slice($offreRepository->getOffresOuvertes(), 0, 3);

// Mois en français pour la pastille de date d'un événement
$mois = array(
    '01' => 'janv.', '02' => 'févr.', '03' => 'mars', '04' => 'avr.',
    '05' => 'mai', '06' => 'juin', '07' => 'juil.', '08' => 'août',
    '09' => 'sept.', '10' => 'oct.', '11' => 'nov.', '12' => 'déc.'
);

// Libellés affichés pour le type et le profil ciblé d'une offre
$types = array(
    'stage' => 'Stage',
    'alternance' => 'Alternance',
    'cdd' => 'CDD',
    'cdi' => 'CDI'
);
$profils = array(
    'tous' => 'Ouvert aux étudiants et aux alumni',
    'etudiant' => 'Ouvert aux étudiants',
    'alumni' => 'Ouvert aux alumni'
);

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'accueil';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lycée Robert Schuman</title>
    <meta name="description" content="Lycée Robert Schuman : le réseau des élèves, des anciens élèves et des entreprises partenaires. Offres, événements, annuaire et forum.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css?v=<?php echo filemtime(__DIR__ . '/../style.css'); ?>">
    <link rel="stylesheet" href="page_accueil.css?v=<?php echo filemtime(__DIR__ . '/page_accueil.css'); ?>">
</head>
<body>

<?php require_once '../commun/entete.php'; ?>

<main id="main">

    <!-- ===== BANNIÈRE : à quoi sert le site + les deux actions principales ===== -->
    <section class="hero accueil-hero">
        <div class="hero-overlay"></div>
        <div class="container accueil-hero-grid">
            <div class="accueil-hero-texte">
                <h1>Le réseau des élèves, des anciens et des entreprises du lycée</h1>
                <div class="accueil-hero-actions">
                    <a href="../offre/liste_offres_Tom.php" class="btn btn-primary btn-lg">Voir les offres</a>
                    <?php if (estConnecte()) { ?>
                        <a href="../profil/profil_utilisateur.php" class="btn btn-outline btn-lg">Mon profil</a>
                    <?php } else { ?>
                        <a href="../inscription/inscription.php" class="btn btn-outline btn-lg">Rejoindre le réseau</a>
                    <?php } ?>
                </div>
                <?php if (!estConnecte()) { ?>
                    <p class="accueil-hero-note">Inscription gratuite, validée par un gestionnaire du lycée.</p>
                <?php } ?>
            </div>

            <ul class="accueil-chiffres">
                <li>
                    <span class="chiffre-nombre">1920</span>
                    <span class="chiffre-libelle">Année de création</span>
                </li>
                <li>
                    <span class="chiffre-nombre">450</span>
                    <span class="chiffre-libelle">Élèves</span>
                </li>
                <li>
                    <span class="chiffre-nombre">8</span>
                    <span class="chiffre-libelle">Formations</span>
                </li>
            </ul>
        </div>
    </section>

    <!-- ===== ACCÈS DIRECTS : une carte par profil ===== -->
    <section class="section acces">
        <div class="container">
            <h2>Que voulez-vous faire ?</h2>
            <p class="acces-intro">Un accès direct selon votre profil.</p>

            <div class="acces-grid">
                <a href="../offre/liste_offres_Tom.php" class="acces-card">
                    <svg class="acces-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 10 12 5 2 10l10 5 10-5z"></path><path d="M6 12v5c3 2 9 2 12 0v-5"></path></svg>
                    <span class="acces-profil">Étudiant</span>
                    <span class="acces-titre">Trouver un stage ou une alternance</span>
                    <span class="acces-lien">Voir les offres →</span>
                </a>
                <a href="../annuaire/page_annuaire.php" class="acces-card">
                    <svg class="acces-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="8" r="4"></circle><path d="M2 21c0-4 3-6 7-6s7 2 7 6"></path><path d="M17 4a4 4 0 0 1 0 8"></path><path d="M22 21c0-3-1-5-4-6"></path></svg>
                    <span class="acces-profil">Ancien élève</span>
                    <span class="acces-titre">Retrouver sa promotion</span>
                    <span class="acces-lien">Ouvrir l'annuaire →</span>
                </a>
                <a href="../inscription/inscription.php" class="acces-card">
                    <svg class="acces-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"></rect><path d="M9 8h1M14 8h1M9 12h1M14 12h1M10 21v-4h4v4"></path></svg>
                    <span class="acces-profil">Entreprise</span>
                    <span class="acces-titre">Publier une offre et recruter</span>
                    <span class="acces-lien">Créer un compte partenaire →</span>
                </a>
                <a href="../evenement/liste_evenement_Tom.php" class="acces-card">
                    <svg class="acces-icone" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M3 10h18M8 3v4M16 3v4"></path></svg>
                    <span class="acces-profil">Professeur</span>
                    <span class="acces-titre">Proposer un événement</span>
                    <span class="acces-lien">Voir les événements →</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ===== ACTUALITÉS : prochains événements et dernières offres, lus dans la base ===== -->
    <section class="section actualites" id="evenements">
        <div class="container actualites-grid">

            <div class="actualites-colonne">
                <div class="actualites-entete">
                    <h2>Prochains événements</h2>
                    <a href="../evenement/liste_evenement_Tom.php">Tout voir</a>
                </div>

                <?php if (count($prochainsEvenements) == 0) { ?>
                    <p class="actualite-vide">Aucun événement à venir pour le moment.</p>
                <?php } ?>

                <?php foreach ($prochainsEvenements as $evenement) { ?>
                    <?php $date = strtotime($evenement['date_evenement']); ?>
                    <a href="../evenement/liste_evenement_Tom.php" class="actualite-card">
                        <span class="actualite-date">
                            <span class="actualite-jour"><?php echo date('d', $date); ?></span>
                            <span class="actualite-mois"><?php echo $mois[date('m', $date)]; ?></span>
                        </span>
                        <span class="actualite-texte">
                            <span class="actualite-titre"><?php echo htmlspecialchars($evenement['titre']); ?></span>
                            <span class="actualite-detail"><?php echo date('H\hi', $date); ?> · <?php echo htmlspecialchars($evenement['lieu']); ?></span>
                        </span>
                        <?php if ($evenement['nb_places'] != null) { ?>
                            <?php $placesRestantes = $evenement['nb_places'] - $evenement['nombre_inscrits']; ?>
                            <?php if ($placesRestantes > 0) { ?>
                                <span class="pastille pastille--verte"><?php echo $placesRestantes; ?> places</span>
                            <?php } else { ?>
                                <span class="pastille">Complet</span>
                            <?php } ?>
                        <?php } ?>
                    </a>
                <?php } ?>
            </div>

            <div class="actualites-colonne">
                <div class="actualites-entete">
                    <h2>Dernières offres</h2>
                    <a href="../offre/liste_offres_Tom.php">Tout voir</a>
                </div>

                <?php if (count($dernieresOffres) == 0) { ?>
                    <p class="actualite-vide">Aucune offre ouverte pour le moment.</p>
                <?php } ?>

                <?php foreach ($dernieresOffres as $offre) { ?>
                    <a href="../offre/liste_offres_Tom.php" class="actualite-card">
                        <span class="actualite-texte">
                            <span class="actualite-titre"><?php echo htmlspecialchars($offre['titre']); ?></span>
                            <span class="actualite-detail"><?php echo htmlspecialchars((string) $offre['nom_entreprise']); ?> · <?php echo $profils[$offre['profil_cible']]; ?></span>
                        </span>
                        <span class="pastille pastille--bleue"><?php echo $types[$offre['type']]; ?></span>
                    </a>
                <?php } ?>
            </div>

        </div>
    </section>

    <!-- ===== CONTACT ===== -->
    <section class="section contact" id="contact">
        <div class="container contact-grid">
            <div class="contact-info">
                <h2>Une question ?</h2>
                <p>Écrivez aux gestionnaires de la plateforme. Votre message leur est transmis et vous recevez une réponse par email.</p>
                <ul>
                    <li><strong>Téléphone :</strong> à compléter</li>
                    <li><strong>Email :</strong> à compléter</li>
                    <li><strong>Adresse :</strong> à compléter, Dugny</li>
                </ul>
            </div>

            <form class="contact-form" id="contact-form">
                <label for="name">Nom</label>
                <input type="text" id="name" name="name" autocomplete="name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" autocomplete="email" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="4" required></textarea>

                <button type="submit" class="btn btn-primary btn-lg btn-block">Envoyer le message</button>
                <p class="form-status form-status--success" id="form-status" role="status" hidden></p>
            </form>
        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="script.js"></script>
</body>
</html>
