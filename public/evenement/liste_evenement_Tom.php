<?php

$message = '';
$typeMessage = '';
$evenements = [];
$idUtilisateur = 0;

require_once __DIR__ . '/../../src/traitement/ListeEvenementsTraitement_Tom.php';

function h($valeur){
    return htmlspecialchars((string) ($valeur ?? ''), ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des événements - Lycée Robert Schuman</title>
    <link rel="stylesheet" href="liste_evenement_Tom.css">
</head>

<body>
<main class="evenements-page">

    <section class="evenements-hero">
        <div class="evenements-hero__contenu">
            <span class="evenements-hero__surtitre">
                Lycée Robert Schuman
            </span>
            <h1>Les événements</h1>
            <p>
                Découvrez les événements proposés par le lycée
                et inscrivez-vous aux activités qui vous intéressent.
            </p>
        </div>
    </section>

    <section class="evenements-section">
        <div class="evenements-container">
            <h2 class="evenements-titre-section">
                Événements disponibles
            </h2>
            <p class="evenements-description">
                Retrouvez ici les événements publiés et leurs
                informations pratiques.
            </p>
            <?php if (!empty($message)): ?>
                <div class="evenements-message <?= h($typeMessage === 'succes' ? 'evenements-message--succes' : 'evenements-message--erreur') ?>" role="status">
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
                        <?php
                        $idEvenement = (int) $evenement['id_evenement'];
                        $nombreInscrits = (int) ($evenement['nombre_inscrits'] ?? 0);
                        $nombrePlaces = $evenement['nb_places'] !== null ? (int) $evenement['nb_places'] : null;
                        $placesRestantes = $nombrePlaces !== null ? max(0, $nombrePlaces - $nombreInscrits) : null;
                        $dejaInscrit = (int) ($evenement['deja_inscrit'] ?? 0) === 1;
                        $complet = $placesRestantes !== null && $placesRestantes <= 0;
                        $dateEvenement = !empty($evenement['date_evenement']) ? strtotime($evenement['date_evenement']) : false;
                        ?>

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
                                        <?= h($evenement['lieu'] ?: 'Non renseigné') ?>
                                    </dd>
                                </div>

                                <div class="evenement-info">
                                    <dt>Date</dt>
                                    <dd>
                                        <?php if ($dateEvenement !== false): ?>
                                            <?= h(date('d/m/Y à H\hi', $dateEvenement)) ?>
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
                                    <span class="btn btn-outline"
                                          aria-disabled="true">
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

</body>
</html>