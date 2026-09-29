<?php
session_start();
require_once __DIR__ . '/../../src/bdd/bdd.php';
$bdd = new bdd();
$pdo = $bdd->getConnexionBdd();
$message = "";
$erreur = "";

if (!isset($_SESSION['id_utilisateur']) || !isset($_SESSION['role'])) {
    $erreur = "Vous devez être connecté pour créer un événement.";
}

if ($erreur === "" && $_SESSION['role'] === 'gestionnaire') {
    $erreur = "Les gestionnaires ne peuvent pas créer d'événement.";
}

if (isset($_GET['recherche']) && $erreur === "") {
    header('Content-Type: application/json; charset=utf-8');
    $recherche = trim($_GET['recherche']);
    if ($recherche === '') {
        echo json_encode([]);
        exit;
    }
    $sql = "SELECT id_utilisateur, nom_utilisateur, prenom_utilisateur, mail, role FROM utilisateur WHERE (nom_utilisateur LIKE :recherche OR prenom_utilisateur LIKE :recherche OR mail LIKE :recherche) AND role <> 'gestionnaire' AND id_utilisateur <> :id_utilisateur AND statut_validation = 'valide' ORDER BY nom_utilisateur, prenom_utilisateur LIMIT 10";
    $req = $pdo->prepare($sql);
    $rechercheSql = '%' . $recherche . '%';
    $req->bindValue(':recherche', $rechercheSql);
    $req->bindValue(':id_utilisateur', $_SESSION['id_utilisateur'], PDO::PARAM_INT);
    $req->execute();
    $utilisateurs = $req->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(
        $utilisateurs,
        JSON_UNESCAPED_UNICODE
    );
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $erreur === "") {
    $typeEvenement = trim($_POST['type_evenement'] ?? '');
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $lieu = trim($_POST['lieu'] ?? '');
    $elementsRequis = trim($_POST['elements_requis'] ?? '');
    $nbPlaces = trim($_POST['nb_places'] ?? '');
    $coorganisateurs = $_POST['coorganisateurs'] ?? [];
    if (!is_array($coorganisateurs)) {
        $coorganisateurs = [];
    }

    if ($typeEvenement === '' || $titre === '' || $description === '' || $lieu === '') {
        $erreur = "Veuillez remplir tous les champs obligatoires.";
    }

    if ($erreur === "" && $nbPlaces !== '') {
        if (!ctype_digit($nbPlaces) || (int)$nbPlaces <= 0) {
            $erreur = "Le nombre de places doit être un nombre entier positif.";
        }
    }

    if ($nbPlaces === '') {
        $nbPlaces = null;
    } else {
        $nbPlaces = (int)$nbPlaces;
    }

    $coorganisateursValides = [];

    foreach ($coorganisateurs as $idUtilisateur) {
        if (ctype_digit((string)$idUtilisateur)) {
            $idUtilisateur = (int)$idUtilisateur;
            if ($idUtilisateur !== (int)$_SESSION['id_utilisateur'] && !in_array($idUtilisateur, $coorganisateursValides, true)) {
                $coorganisateursValides[] = $idUtilisateur;
            }
        }
    }

    if ($erreur === "" && count($coorganisateursValides) > 0) {
        $placeholders = [];

        foreach ($coorganisateursValides as $index => $idUtilisateur) {
            $placeholders[] = ':id' . $index;
        }

        $sqlVerification = "SELECT id_utilisateur FROM utilisateur WHERE id_utilisateur IN (" .  implode(',', $placeholders) . ") AND role <> 'gestionnaire' AND statut_validation = 'valide'";

        $reqVerification = $pdo->prepare($sqlVerification);

        foreach ($coorganisateursValides as $index => $idUtilisateur) {
            $reqVerification->bindValue(':id' . $index, $idUtilisateur, PDO::PARAM_INT);
        }

        $reqVerification->execute();

        $utilisateursAutorises = $reqVerification->fetchAll(PDO::FETCH_COLUMN);

        $coorganisateursValides = array_map('intval', $utilisateursAutorises);
    }

    $tousLesOrganisateurs = $coorganisateursValides;

    $tousLesOrganisateurs[] = (int)$_SESSION['id_utilisateur'];

    $tousLesOrganisateurs = array_unique($tousLesOrganisateurs);

    $professeurPresent = false;

    if (count($tousLesOrganisateurs) > 0) {$placeholders = []; foreach ($tousLesOrganisateurs as $index => $idUtilisateur) {
            $placeholders[] = ':prof' . $index;
        }

        $sqlProfesseur = "SELECT COUNT(*) FROM utilisateur WHERE id_utilisateur IN (" . implode(',', $placeholders) . ") AND role = 'professeur' AND statut_validation = 'valide'";

        $reqProfesseur = $pdo->prepare($sqlProfesseur);

        foreach ($tousLesOrganisateurs as $index => $idUtilisateur) {
            $reqProfesseur->bindValue(':prof' . $index, $idUtilisateur, PDO::PARAM_INT);
        }

        $reqProfesseur->execute();

        $professeurPresent = ((int)$reqProfesseur->fetchColumn() > 0);
    }

    if ($_SESSION['role'] === 'etudiant') {
        if ($professeurPresent) {
            $statut = 'publie';
        } else {
            $statut = 'brouillon';
        }
    } else {
        $statut = 'publie';
    }

    if ($erreur === "") {
        try {
            $pdo->beginTransaction();
            $sqlEvenement = "INSERT INTO evenement (type_evenement, titre, description, lieu, elements_requis, nb_places, date_evenement, statut) VALUES (:type_evenement, :titre, :description, :lieu, :elements_requis, :nb_places, NOW(), :statut)";
            $reqEvenement = $pdo->prepare($sqlEvenement);
            $reqEvenement->bindValue(':type_evenement', $typeEvenement);
            $reqEvenement->bindValue(':titre', $titre);
            $reqEvenement->bindValue(':description', $description);
            $reqEvenement->bindValue(':lieu', $lieu);
            if ($elementsRequis === '') {
                $reqEvenement->bindValue(':elements_requis', null, PDO::PARAM_NULL);
            } else {
                $reqEvenement->bindValue(':elements_requis', $elementsRequis);
            }

            if ($nbPlaces === null) {
                $reqEvenement->bindValue(':nb_places', null, PDO::PARAM_NULL);
            } else {
                $reqEvenement->bindValue(':nb_places', $nbPlaces, PDO::PARAM_INT);
            }

            $reqEvenement->bindValue(':statut', $statut);
            $reqEvenement->execute();
            $idEvenement = $pdo->lastInsertId();

            $sqlOrganisateur = "INSERT INTO organise_evenement (id_utilisateur, id_evenement) VALUES (:id_utilisateur, :id_evenement)";
            $reqOrganisateur = $pdo->prepare($sqlOrganisateur);
            $reqOrganisateur->bindValue(':id_utilisateur', $_SESSION['id_utilisateur'], PDO::PARAM_INT);
            $reqOrganisateur->bindValue(':id_evenement', $idEvenement, PDO::PARAM_INT);
            $reqOrganisateur->execute();
            if (count($coorganisateursValides) > 0) {
                foreach ($coorganisateursValides as $idCoorganisateur) {
                    $reqCoorganisateur = $pdo->prepare($sqlOrganisateur);
                    $reqCoorganisateur->bindValue(':id_utilisateur', $idCoorganisateur, PDO::PARAM_INT);
                    $reqCoorganisateur->bindValue(':id_evenement', $idEvenement, PDO::PARAM_INT);
                    $reqCoorganisateur->execute();
                }
            }
            $pdo->commit();
            if ($statut === 'publie') {
                $message = "L'événement a été créé et publié.";
            } else {
                $message = "L'événement a été créé en brouillon. " . "Il sera publié lorsqu'un professeur sera ajouté " . "aux organisateurs.";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erreur = "Une erreur est survenue lors de la création de l'événement.";
        }
    }
}
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

    <?php if ($erreur === "" && $message === ""): ?>

        <div class="info">
            Le créateur est automatiquement ajouté comme organisateur. Si vous êtes étudiant, un professeur doit faire partie des organisateurs pour que l'événement soit publié.
        </div>
        <form method="POST">
            <div class="champ">
                <label for="type_evenement">
                    Type d'événement *
                </label>
                <input type="text" id="type_evenement" name="type_evenement" required placeholder="Exemple : Conférence, rencontre, atelier...">
            </div>
            <div class="champ">
                <label for="titre">
                    Titre *
                </label>

                <input type="text" id="titre" name="titre" required>
            </div>

            <div class="champ">
                <label for="description">
                    Description *
                </label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <div class="champ">
                <label for="lieu">
                    Lieu / adresse *
                </label>
                <input type="text" id="lieu" name="lieu" required placeholder="Exemple : Lycée Robert Schuman, Dugny">
            </div>

            <div class="champ">
                <label for="elements_requis">
                    Éléments requis
                </label>
                <input type="text" id="elements_requis" name="elements_requis" placeholder="Facultatif">
            </div>

            <div class="champ">
                <label for="nb_places">
                    Nombre de places
                </label>
                <input type="number" id="nb_places" name="nb_places" min="1" placeholder="Facultatif">
            </div>

            <div class="champ">
                <label for="recherche_utilisateur">
                    Ajouter des co-organisateurs
                </label>
                <input type="text" id="recherche_utilisateur" placeholder="Rechercher par nom, prénom ou email" autocomplete="off">
                <div id="resultats_recherche" class="resultats"></div>
            </div>

            <div class="champ">
                <label>
                    Co-organisateurs sélectionnés
                </label>
                <div id="organisateurs_selectionnes"></div>
            </div>

            <div class="champ">
                <button type="submit" class="bouton-principal">
                    Créer l'événement
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>

    const rechercheInput = document.getElementById('recherche_utilisateur');
    const resultats = document.getElementById('resultats_recherche');
    const selection = document.getElementById('organisateurs_selectionnes');
    const utilisateursSelectionnes = new Map();

    if (rechercheInput) {
        rechercheInput.addEventListener('input',
            function () {

                const recherche = this.value.trim();

                if (recherche.length < 2) {
                    resultats.innerHTML = '';
                    return;
                }

                fetch('creer_evenement.php?recherche=' + encodeURIComponent(recherche))
                    .then(response => response.json())
                    .then(utilisateurs => {
                        resultats.innerHTML = '';
                        if (utilisateurs.length === 0) {
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
                                        String(utilisateur.id_utilisateur)
                                    )
                                ) {
                                    return;
                                }
                                const div = document.createElement('div')
                                div.className = 'utilisateur';
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

                                div.querySelector('button')
                                    .addEventListener(
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

                                resultats.appendChild(div);
                            }
                        );
                    })
                    .catch(() => {

                        resultats.innerHTML =
                            '<div class="aucun-resultat">' +
                            'Erreur lors de la recherche.' +
                            '</div>';
                    });
            }
        );
    }

    function ajouterOrganisateur(utilisateur) {
        const id = String(utilisateur.id_utilisateur);
        if (utilisateursSelectionnes.has(id)) {
            return;
        }
        utilisateursSelectionnes.set(id, utilisateur);
        afficherOrganisateurs();
    }

    function supprimerOrganisateur(id) {
        utilisateursSelectionnes.delete(String(id));
        afficherOrganisateurs();
    }

    function afficherOrganisateurs() {
        selection.innerHTML = '';
        utilisateursSelectionnes.forEach(
            utilisateur => {
                const div = document.createElement('div');
                div.className = 'organisateur';
                div.innerHTML = escapeHtml(
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
                div.querySelector('button').addEventListener(
                        'click',
                        function () {
                            supprimerOrganisateur(
                                utilisateur.id_utilisateur
                            );
                        }
                    );
                selection.appendChild(div);
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