<?php
session_start();
require_once __DIR__ . '/../bdd/bdd.php';
$bdd = new bdd();
$pdo = $bdd->getConnexionBdd();
$message = "";
$erreur = "";

if (isset($_SESSION['id_utilisateur']) && isset($_SESSION['role'])) {
    $idUtilisateurConnecte = (int) $_SESSION['id_utilisateur'];
    $roleUtilisateurConnecte = $_SESSION['role'];
} else {
    $idUtilisateurConnecte = 2;
    $roleUtilisateurConnecte = 'etudiant';
}

if ($roleUtilisateurConnecte === 'gestionnaire') {
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
    $req->bindValue(':id_utilisateur', $idUtilisateurConnecte, PDO::PARAM_INT);
    $req->execute();
    $utilisateurs = $req->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($utilisateurs, JSON_UNESCAPED_UNICODE);
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
    if (
        $erreur === "" &&
        $nbPlaces !== ''
    ) {

        if (
            !ctype_digit($nbPlaces) ||
            (int) $nbPlaces <= 0
        ) {

            $erreur =
                "Le nombre de places doit être un nombre entier positif.";
        }
    }


    if ($nbPlaces === '') {

        $nbPlaces = null;

    } else {

        $nbPlaces = (int) $nbPlaces;
    }


    /*
     * Vérification des co-organisateurs.
     */
    $coorganisateursValides = [];


    foreach (
        $coorganisateurs as $idUtilisateur
    ) {

        if (
            ctype_digit(
                (string) $idUtilisateur
            )
        ) {

            $idUtilisateur =
                (int) $idUtilisateur;


            if (
                $idUtilisateur !==
                $idUtilisateurConnecte
                &&
                !in_array(
                    $idUtilisateur,
                    $coorganisateursValides,
                    true
                )
            ) {

                $coorganisateursValides[] =
                    $idUtilisateur;
            }
        }
    }


    /*
     * Vérification que les co-organisateurs
     * sont des utilisateurs valides.
     */
    if (
        $erreur === "" &&
        count($coorganisateursValides) > 0
    ) {

        $placeholders = [];


        foreach (
            $coorganisateursValides as
            $index => $idUtilisateur
        ) {

            $placeholders[] =
                ':id' . $index;
        }


        $sqlVerification = "
            SELECT
                id_utilisateur
            FROM utilisateur
            WHERE
                id_utilisateur IN (
                    " .
            implode(
                ',',
                $placeholders
            )
            . "
                )
                AND role <> 'gestionnaire'
                AND statut_validation = 'valide'
        ";


        $reqVerification =
            $pdo->prepare(
                $sqlVerification
            );


        foreach (
            $coorganisateursValides as
            $index => $idUtilisateur
        ) {

            $reqVerification->bindValue(
                ':id' . $index,
                $idUtilisateur,
                PDO::PARAM_INT
            );
        }


        $reqVerification->execute();


        $utilisateursAutorises =
            $reqVerification->fetchAll(
                PDO::FETCH_COLUMN
            );


        $coorganisateursValides =
            array_map(
                'intval',
                $utilisateursAutorises
            );
    }


    /*
     * Tous les organisateurs :
     *
     * - créateur
     * - co-organisateurs
     */
    $tousLesOrganisateurs =
        $coorganisateursValides;


    $tousLesOrganisateurs[] =
        $idUtilisateurConnecte;


    $tousLesOrganisateurs =
        array_unique(
            $tousLesOrganisateurs
        );


    /*
     * Recherche d'un professeur parmi
     * les organisateurs.
     */
    $professeurPresent = false;


    if (
        count($tousLesOrganisateurs) > 0
    ) {

        $placeholders = [];


        foreach (
            $tousLesOrganisateurs as
            $index => $idUtilisateur
        ) {

            $placeholders[] =
                ':prof' . $index;
        }


        $sqlProfesseur = "
            SELECT COUNT(*)
            FROM utilisateur
            WHERE
                id_utilisateur IN (
                    " .
            implode(
                ',',
                $placeholders
            )
            . "
                )
                AND role = 'professeur'
                AND statut_validation = 'valide'
        ";


        $reqProfesseur =
            $pdo->prepare(
                $sqlProfesseur
            );


        foreach (
            $tousLesOrganisateurs as
            $index => $idUtilisateur
        ) {

            $reqProfesseur->bindValue(
                ':prof' . $index,
                $idUtilisateur,
                PDO::PARAM_INT
            );
        }


        $reqProfesseur->execute();


        $professeurPresent =
            (
                (int)
                $reqProfesseur->fetchColumn()
                > 0
            );
    }


    /*
     * Détermination du statut.
     *
     * Étudiant sans professeur :
     * brouillon.
     *
     * Étudiant avec professeur :
     * publié.
     */
    if (
        $roleUtilisateurConnecte ===
        'etudiant'
    ) {

        if ($professeurPresent) {

            $statut = 'publie';

        } else {

            $statut = 'brouillon';
        }

    } else {

        $statut = 'publie';
    }


    /*
     * Insertion dans la base de données.
     */
    if ($erreur === "") {

        try {

            $pdo->beginTransaction();


            /*
             * Création de l'événement.
             */
            $sqlEvenement = "
                INSERT INTO evenement (
                    type_evenement,
                    titre,
                    description,
                    lieu,
                    elements_requis,
                    nb_places,
                    date_evenement,
                    statut
                )
                VALUES (
                    :type_evenement,
                    :titre,
                    :description,
                    :lieu,
                    :elements_requis,
                    :nb_places,
                    NOW(),
                    :statut
                )
            ";


            $reqEvenement =
                $pdo->prepare(
                    $sqlEvenement
                );


            $reqEvenement->bindValue(
                ':type_evenement',
                $typeEvenement
            );


            $reqEvenement->bindValue(
                ':titre',
                $titre
            );


            $reqEvenement->bindValue(
                ':description',
                $description
            );
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
            $reqOrganisateur->bindValue(':id_utilisateur', $idUtilisateurConnecte, PDO::PARAM_INT);
            $reqOrganisateur->bindValue(':id_evenement', $idEvenement, PDO::PARAM_INT);
            $reqOrganisateur->execute();
            foreach ($coorganisateursValides as $idCoorganisateur) {
                $reqCoorganisateur = $pdo->prepare($sqlOrganisateur);
                $reqCoorganisateur->bindValue(':id_utilisateur', $idCoorganisateur, PDO::PARAM_INT);
                $reqCoorganisateur->bindValue(':id_evenement', $idEvenement, PDO::PARAM_INT);
                $reqCoorganisateur->execute();
            }
            $pdo->commit();
            if ($statut === 'publie') {
                $message = "L'événement a été créé et publié.";
            } else {
                $message = "L'événement a été créé en brouillon. " . "Il sera publié lorsqu'un professeur " . "sera ajouté aux organisateurs.";
            }
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $erreur =
                "Une erreur est survenue lors de la création " .
                "de l'événement : " .
                $e->getMessage();
        }
    }
}