<?php

require_once '../../src/traitement/SessionUtilisateur_ahmed.php';
require_once '../../src/bdd/bdd.php';
require_once '../../src/repository/EvenementRepository_ahmed.php';

// Messages possibles après une inscription (résultat envoyé dans l'URL par
// src/traitement/InscriptionEvenementTraitement_Tom.php)
$messages = array(
    'succes' => 'Votre inscription a bien été enregistrée.',
    'connexion' => 'Vous devez être connecté pour vous inscrire.',
    'deja_inscrit' => 'Vous êtes déjà inscrit à cet événement.',
    'complet' => 'Cet événement est complet.',
    'indisponible' => 'Cet événement est indisponible.',
    'erreur' => 'Une erreur est survenue. Veuillez réessayer.'
);

$message = "";
$resultat = "";
if (isset($_GET['resultat']) && isset($messages[$_GET['resultat']])) {
    $resultat = $_GET['resultat'];
    $message = $messages[$resultat];
}

$evenementRepository = new EvenementRepository_ahmed();
$evenements = $evenementRepository->getEvenementsPublies();

// Lien du menu à mettre en vert (voir ../commun/entete.php)
$pageActive = 'evenements';

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Liste des événements — Lycée Robert Schuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="liste_evenement_Tom.css">
</head>

<body>
<?php require_once '../commun/entete.php'; ?>

<main id="main" class="evenements-page">

    <section class="page-hero">
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Événements</p>

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

            <?php if ($message != "") { ?>
                <?php if ($resultat == 'succes') { ?>
                    <div class="form-status" role="status">
                        <?php echo $message; ?>
                    </div>
                <?php } else { ?>
                    <div class="form-status form-status--error" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php if (count($evenements) == 0) { ?>
                <div class="evenements-vide">
                    <h3>Aucun événement disponible</h3>
                    <p>
                        Il n'y a actuellement aucun événement publié.
                        Revenez consulter cette page ultérieurement.
                    </p>
                </div>
            <?php } else { ?>
                <div class="evenements-grille">
                    <?php foreach ($evenements as $evenement) { ?>
                        <?php
                        // Places restantes (null si le nombre de places n'est pas limité)
                        $placesRestantes = null;
                        $complet = false;
                        if ($evenement['nb_places'] != null) {
                            $placesRestantes = $evenement['nb_places'] - $evenement['nombre_inscrits'];
                            if ($placesRestantes <= 0) {
                                $placesRestantes = 0;
                                $complet = true;
                            }
                        }

                        $dejaInscrit = false;
                        if (estConnecte()) {
                            $dejaInscrit = $evenementRepository->estInscrit($evenement['id_evenement'], $_SESSION['id_utilisateur']);
                        }
                        ?>

                        <article class="evenement-carte">
                            <span class="evenement-carte__type">
                                <?php echo htmlspecialchars($evenement['type_evenement']); ?>
                            </span>

                            <h3>
                                <?php echo htmlspecialchars($evenement['titre']); ?>
                            </h3>

                            <p class="evenement-carte__description"><?php echo htmlspecialchars($evenement['description']); ?></p>

                            <dl class="evenement-infos">
                                <div class="evenement-info">
                                    <dt>Lieu</dt>
                                    <dd>
                                        <?php echo htmlspecialchars($evenement['lieu']); ?>
                                    </dd>
                                </div>

                                <div class="evenement-info">
                                    <dt>Date</dt>
                                    <dd>
                                        <?php echo $evenement['date_fr']; ?>
                                    </dd>
                                </div>

                                <?php if ($evenement['elements_requis'] != null) { ?>
                                    <div class="evenement-info">
                                        <dt>Éléments requis</dt>
                                        <dd>
                                            <?php echo htmlspecialchars($evenement['elements_requis']); ?>
                                        </dd>
                                    </div>
                                <?php } ?>
                            </dl>

                            <div class="evenement-places">
                                <span class="evenement-places__libelle">
                                    Places disponibles
                                </span>
                                <span class="evenement-places__nombre
                                    <?php if ($complet) { echo 'evenement-places__nombre--complet'; } ?>">
                                    <?php if ($evenement['nb_places'] == null) { ?>
                                        Non limitées
                                    <?php } else { ?>
                                        <?php echo $placesRestantes; ?>
                                        / <?php echo $evenement['nb_places']; ?>
                                    <?php } ?>
                                </span>
                            </div>

                            <div class="evenement-actions">
                                <?php if ($dejaInscrit) { ?>
                                    <span class="btn btn-outline" aria-disabled="true">
                                        Déjà inscrit
                                    </span>
                                <?php } elseif ($complet) { ?>
                                    <span class="btn btn-outline" aria-disabled="true">
                                        Événement complet
                                    </span>
                                <?php } else { ?>
                                    <form method="POST" action="../../src/traitement/InscriptionEvenementTraitement_Tom.php">
                                        <input type="hidden" name="id_evenement" value="<?php echo $evenement['id_evenement']; ?>">

                                        <button type="submit" class="btn btn-primary">
                                            S'inscrire
                                        </button>
                                    </form>
                                <?php } ?>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </section>

</main>

<?php require_once '../commun/pied_de_page.php'; ?>

<script src="liste_evenement_Tom.js"></script>

</body>
</html>
