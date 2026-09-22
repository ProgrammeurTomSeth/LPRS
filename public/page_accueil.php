<?php

?>
<!DOCTYPE html>
<html lang="fr" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Site école</title>
    <link href="accueil.css" rel="stylesheet">
</head>
<body>

<header class="header">

    <a href="#" class="logo">
        MonSite
    </a>

    <!-- Checkbox invisible qui sert à ouvrir/fermer le menu -->
    <input type="checkbox" id="nav-toggle" class="nav-toggle">

    <!-- Bouton hamburger -->
    <label for="nav-toggle" class="nav-toggle-label">
        <span></span>
    </label>

    <nav class="navbar">
        <ul>
            <li>
                <a href="#">Accueil</a>
            </li>

            <li>
                <a href="#">À propos</a>
            </li>

            <li>
                <a href="#">Services</a>
            </li>

            <li>
                <a href="#">Contact</a>
            </li>
        </ul>
    </nav>

</header>

<!-- ================= FOOTER ================= -->
<footer class="text-white py-4 mt-5">
    <div class="container text-center">
        <p class="mb-2 fs-5">
            © 2025 – Tous droits réservés Anaïs Kriegel--Grapain - Malik Ben Mechichi -
            Ahmed Ben Mechichi - Tom Treillon
        </p>
    </div>
</footer>
</body>
</html>