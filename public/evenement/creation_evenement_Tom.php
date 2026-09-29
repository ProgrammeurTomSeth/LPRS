<?php

require_once __DIR__ . '/../../src/traitement/EvenementTraitement_Tom.php';

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
    >

    <title>Créer un événement</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f5f5f5;
        }

        .conteneur {
            max-width: 800px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 25px;
        }

        .champ {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        button {
            padding: 10px 18px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .bouton-principal {
            background-color: #1d4ed8;
            color: white;
        }

        .resultats {
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
        }

        .utilisateur {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .utilisateur:last-child {
            border-bottom: none;
        }

        .organisateur {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 5px;
            padding: 8px 10px;
            background-color: #eee;
            border-radius: 5px;
        }

        .message {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #d1fae5;
        }

        .erreur {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #fee2e2;
        }

        .info {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #e0f2fe;
        }

        .aucun-resultat {
            padding: 10px;
            color: #666;
        }

    </style>

</head>

<body>

<div class="conteneur">

    <h1>Créer un événement</h1>


    <?php if ($message !== ""): ?>

        <div class="message">
            <?= htmlspecialchars($message) ?>
        </div>

    <?php endif; ?>


    <?php if ($erreur !== ""): ?>

        <div class="erreur">
            <?= htmlspecialchars($erreur) ?>
        </div>

    <?php endif; ?>


    <?php if ($message === "" && $erreur === ""): ?>

        <div class="info">

            Le créateur est automatiquement ajouté
            comme organisateur.

            Si le créateur est étudiant, un professeur
            doit faire partie des organisateurs pour
            que l'événement soit publié.

        </div>


        <form method="POST">

            <div class="champ">

                <label for="type_evenement">
                    Type d'événement *
                </label>

                <input
                        type="text"
                        id="type_evenement"
                        name="type_evenement"
                        required
                        placeholder="Exemple : Conférence, rencontre, atelier..."
                >

            </div>


            <div class="champ">

                <label for="titre">
                    Titre *
                </label>

                <input
                        type="text"
                        id="titre"
                        name="titre"
                        required
                >

            </div>


            <div class="champ">

                <label for="description">
                    Description *
                </label>

                <textarea
                        id="description"
                        name="description"
                        required
                ></textarea>

            </div>


            <div class="champ">

                <label for="lieu">
                    Lieu / adresse *
                </label>

                <input
                        type="text"
                        id="lieu"
                        name="lieu"
                        required
                        placeholder="Exemple : Lycée Robert Schuman, Dugny"
                >

            </div>


            <div class="champ">

                <label for="elements_requis">
                    Éléments requis
                </label>

                <input
                        type="text"
                        id="elements_requis"
                        name="elements_requis"
                        placeholder="Facultatif"
                >

            </div>


            <div class="champ">

                <label for="nb_places">
                    Nombre de places
                </label>

                <input
                        type="number"
                        id="nb_places"
                        name="nb_places"
                        min="1"
                        placeholder="Facultatif"
                >

            </div>


            <div class="champ">

                <label for="recherche_utilisateur">
                    Ajouter des co-organisateurs
                </label>

                <input
                        type="text"
                        id="recherche_utilisateur"
                        placeholder="Rechercher par nom, prénom ou email"
                        autocomplete="off"
                >

                <div
                        id="resultats_recherche"
                        class="resultats"
                ></div>

            </div>


            <div class="champ">

                <label>
                    Co-organisateurs sélectionnés
                </label>

                <div
                        id="organisateurs_selectionnes"
                ></div>

            </div>


            <div class="champ">

                <button
                        type="submit"
                        class="bouton-principal"
                >
                    Créer l'événement
                </button>

            </div>

        </form>

    <?php endif; ?>

</div>


<script>

    const rechercheInput =
        document.getElementById(
            'recherche_utilisateur'
        );


    const resultats =
        document.getElementById(
            'resultats_recherche'
        );


    const selection =
        document.getElementById(
            'organisateurs_selectionnes'
        );


    const utilisateursSelectionnes =
        new Map();


    if (rechercheInput) {

        rechercheInput.addEventListener(
            'input',
            function () {

                const recherche =
                    this.value.trim();


                if (recherche.length < 2) {

                    resultats.innerHTML = '';

                    return;
                }


                fetch(
                    '../../src/traitement/EvenementTraitement_Tom.php?recherche=' +
                    encodeURIComponent(recherche)
                )

                    .then(
                        response => response.json()
                    )

                    .then(
                        utilisateurs => {

                            resultats.innerHTML = '';


                            if (
                                utilisateurs.length === 0
                            ) {

                                resultats.innerHTML =
                                    '<div class="aucun-resultat">' +
                                    'Aucun utilisateur trouvé.' +
                                    '</div>';

                                return;
                            }


                            utilisateurs.forEach(
                                utilisateur => {

                                    if (
                                        utilisateursSelectionnes.has(
                                            String(
                                                utilisateur.id_utilisateur
                                            )
                                        )
                                    ) {

                                        return;
                                    }


                                    const div =
                                        document.createElement(
                                            'div'
                                        );


                                    div.className =
                                        'utilisateur';


                                    div.innerHTML =
                                        '<strong>' +
                                        escapeHtml(
                                            utilisateur.prenom_utilisateur +
                                            ' ' +
                                            utilisateur.nom_utilisateur
                                        ) +
                                        '</strong>' +
                                        '<br>' +
                                        escapeHtml(
                                            utilisateur.mail
                                        ) +
                                        ' - ' +
                                        escapeHtml(
                                            utilisateur.role
                                        ) +
                                        ' ' +
                                        '<button type="button">' +
                                        'Ajouter' +
                                        '</button>';


                                    div.querySelector(
                                        'button'
                                    ).addEventListener(
                                        'click',
                                        function () {

                                            ajouterOrganisateur(
                                                utilisateur
                                            );


                                            resultats.innerHTML =
                                                '';


                                            rechercheInput.value =
                                                '';
                                        }
                                    );


                                    resultats.appendChild(
                                        div
                                    );
                                }
                            );
                        }
                    )

                    .catch(
                        () => {

                            resultats.innerHTML =
                                '<div class="aucun-resultat">' +
                                'Erreur lors de la recherche.' +
                                '</div>';
                        }
                    );
            }
        );
    }


    function ajouterOrganisateur(
        utilisateur
    ) {

        const id =
            String(
                utilisateur.id_utilisateur
            );


        if (
            utilisateursSelectionnes.has(id)
        ) {

            return;
        }


        utilisateursSelectionnes.set(
            id,
            utilisateur
        );


        afficherOrganisateurs();
    }


    function supprimerOrganisateur(id) {

        utilisateursSelectionnes.delete(
            String(id)
        );


        afficherOrganisateurs();
    }


    function afficherOrganisateurs() {

        selection.innerHTML = '';


        utilisateursSelectionnes.forEach(
            utilisateur => {

                const div =
                    document.createElement(
                        'div'
                    );


                div.className =
                    'organisateur';


                div.innerHTML =
                    escapeHtml(
                        utilisateur.prenom_utilisateur +
                        ' ' +
                        utilisateur.nom_utilisateur
                    ) +
                    ' (' +
                    escapeHtml(
                        utilisateur.role
                    ) +
                    ')' +
                    ' ' +
                    '<button type="button">X</button>' +
                    '<input type="hidden" ' +
                    'name="coorganisateurs[]" ' +
                    'value="' +
                    escapeHtml(
                        utilisateur.id_utilisateur
                    ) +
                    '">';


                div.querySelector(
                    'button'
                ).addEventListener(
                    'click',
                    function () {

                        supprimerOrganisateur(
                            utilisateur.id_utilisateur
                        );
                    }
                );


                selection.appendChild(
                    div
                );
            }
        );
    }

    function escapeHtml(texte) {
        const div = document.createElement('div');
        div.textContent = texte;
        return div.innerHTML;
    }
</script>
</body>
</html>