<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Entreprises</title>
    <link rel="stylesheet" href="page_entreprises.css">
</head>

<body>
<aside class="sidebar">
    <h2>Dashboard</h2>
    <nav>
        <ul>
            <li><a href="#">Entreprises</a></li>
            <li><a href="#">Utilisateurs</a></li>
            <li><a href="#">Paramètres</a></li>
        </ul>
    </nav>
</aside>

<main class="main">

    <header class="main-header">
        <h1>Liste des entreprises</h1>
        <a href="#" class="btn-primary">+ Ajouter une entreprise</a>
    </header>

    <!-- Barre de recherche SANS JS -->
    <form method="GET" class="search-container">
        <input type="text" name="search" placeholder="Rechercher une entreprise..."
               value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit">Rechercher</button>
    </form>

    <section class="cards-grid">

        <!-- Exemple d'une carte entreprise (sera remplacé par ta boucle PHP) -->
        <div class="card">
            <h2>Nom de l'entreprise</h2>
            <p><strong>Activité :</strong> Informatique</p>
            <p><strong>Adresse :</strong> 1 rue du Code, Paris</p>
            <p><strong>Site web :</strong>
                <a href="https://exemple.com" target="_blank">https://exemple.com</a>
            </p>

            <div class="card-actions">
                <a href="#" class="btn-secondary">Voir</a>
                <a href="#" class="btn-secondary">Modifier</a>
                <a href="#" class="btn-danger">Supprimer</a>
            </div>
        </div>

    </section>
</main>
</body>
</html>
