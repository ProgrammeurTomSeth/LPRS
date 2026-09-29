<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Formulaire d'inscritpion</title>
    <link href="form_inscription.css" rel="stylesheet">
</head>
<body>
<form action="../src/traitement/traitement_inscription.php" method="POST" enctype="multipart/form-data">

    <label>Nom :</label>
    <input type="text" name="nom" required>

    <label>Prénom :</label>
    <input type="text" name="prenom" required>

    <label>Email :</label>
    <input type="email" name="email" required>

    <label>Mot de passe :</label>
    <input type="password" name="mdp" required>

    <label>Téléphone :</label>
    <input type="text" name="telephone">


    <button type="submit">Créer le compte</button>
</form>

</body>
</html>
