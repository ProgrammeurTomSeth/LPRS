
<?php

require_once '../../src/bdd/bdd.php';
require_once '../../src/modele/Entreprise_Tom.php';
require_once '../../src/repository/EntrepriseRepository_Tom.php';

$message = '';
$lienEntreprise = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nom = trim($_POST['nom'] ?? '');
    $activite = trim($_POST['activite'] ?? '');
    $adresse = trim($_POST['adresse'] ?? '');
    $siteWeb = trim($_POST['siteWeb'] ?? '');

    if ($nom && $activite && $adresse && $siteWeb) {

        $repository = new EntrepriseRepository_Tom();

        // Vérifier si le site web existe déjà
        $entrepriseExistante = $repository->getEntrepriseParSiteWeb($siteWeb);

        if ($entrepriseExistante !== null) {

            $idEntreprise = $entrepriseExistante->getIdEntreprise();

            $message = "Cette entreprise existe déjà.";

            $lienEntreprise = "../page_entreprise/profil_entreprise.php?id="
                . urlencode($idEntreprise);

        } else {

            // Créer l'entreprise
            $resultat = $repository->ajouterEntreprise(
                $nom,
                $activite,
                $adresse,
                $siteWeb
            );

            if ($resultat) {
                header("Location: ../profil/profil_utilisateur.php");
                exit;
            } else {
                $message = "Erreur lors de la création de l'entreprise.";
            }
        }

    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<?php if (!empty($message)) { ?>
    <div class="message">
        <p><?php echo htmlspecialchars($message); ?></p>

        <?php if (!empty($lienEntreprise)) { ?>
            <a href="<?php echo htmlspecialchars($lienEntreprise); ?>">
                Voir la fiche de cette entreprise
            </a>
        <?php } ?>
    </div>
<?php } ?>

