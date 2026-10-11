<?php
session_start();
require_once __DIR__ . '/../../src/traitement/SessionUtilisateur_ahmed.php';
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement — Lycée Robert Shuman</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="creation_evenement_Tom.css">
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
            <p class="breadcrumb"><a href="../page_acceuille/page_accueil.php">Accueil</a> / Créer un événement</p>
            <h1>Créer un événement</h1>
            <p>Organisez une conférence, une rencontre ou un atelier avec la communauté du lycée.</p>
        </div>
    </section>

    <!-- ===== FORMULAIRE ===== -->
    <section class="section form-section">
        <div class="container">
            <div class="form-card">

                <h2>Nouvel événement</h2>

                <?php if ($message !== ""): ?>
                    <div class="alert alert--success">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <?php if ($erreur !== ""): ?>
                    <div class="alert alert--error" role="alert">
                        <?= htmlspecialchars($erreur) ?>
                    </div>
                <?php endif; ?>

                <?php if ($erreur === "" && $message === ""): ?>

                    <div class="alert alert--info">
                        Le créateur est automatiquement ajouté comme organisateur. Si vous êtes étudiant, un professeur doit faire partie des organisateurs pour que l'événement soit publié.
                    </div>
                    <form method="POST">
                        <div class="form-group">
                            <label for="type_evenement">Type d'événement *</label>
                            <input type="text" id="type_evenement" name="type_evenement" required placeholder="Exemple : Conférence, rencontre, atelier...">
                        </div>

                        <div class="form-group">
                            <label for="titre">Titre *</label>
                            <input type="text" id="titre" name="titre" required>
                        </div>

                        <div class="form-group">
                            <label for="description">Description *</label>
                            <textarea id="description" name="description" required></textarea>
                        </div>

                        <div class="form-group">
                            <label for="lieu">Lieu / adresse *</label>
                            <input type="text" id="lieu" name="lieu" required placeholder="Exemple : Lycée Robert Schuman, Dugny">
                        </div>

                        <div class="form-group">
                            <label for="elements_requis">Éléments requis</label>
                            <input type="text" id="elements_requis" name="elements_requis" placeholder="Facultatif">
                        </div>

                        <div class="form-group">
                            <label for="nb_places">Nombre de places</label>
                            <input type="number" id="nb_places" name="nb_places" min="1" placeholder="Facultatif">
                        </div>

                        <div class="form-group">
                            <label for="recherche_utilisateur">Ajouter des co-organisateurs</label>
                            <input type="text" id="recherche_utilisateur" placeholder="Rechercher par nom, prénom ou email" autocomplete="off">
                            <div id="resultats_recherche" class="resultats"></div>
                        </div>

                        <div class="form-group">
                            <label>Co-organisateurs sélectionnés</label>
                            <div id="organisateurs_selectionnes"></div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">Créer l'événement</button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </section>

</main>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">
    <div class="container footer-inner">
        <a href="../page_acceuille/page_accueil.php" class="logo">Lycée<span>Robert Shuman</span></a>
        <ul class="footer-links">
            <li><a href="#">Politique</a></li>
            <li><a href="#">Mentions légales</a></li>
            <li><a href="#">FAQ</a></li>
            <li><a href="../page_acceuille/page_accueil.php">Contact</a></li>
        </ul>
        <p class="footer-copy">© 2026 Lycée Robert Shuman. Tous droits réservés.</p>
    </div>
</footer>

<script>
    // Menu mobile
    const navToggle = document.getElementById('nav-toggle');
    const mainNav = document.getElementById('main-nav');

    if (navToggle && mainNav) {
        navToggle.addEventListener('click', () => {
            const isOpen = mainNav.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', String(isOpen));
        });
    }
</script>

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

                fetch('creation_evenement_Tom.php?recherche=' + encodeURIComponent(recherche))
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