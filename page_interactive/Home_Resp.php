<?php

session_start();
if (!isset($_SESSION["connecté"]) || $_SESSION["role"] != "responsable") {
    header("Location: connexion.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title> en construction</title>
    <link rel="stylesheet" href="../page_html/Reparation.css">
</head>
<body>
<div class="panneau-resp">
    <a href="Reparation.php">Reparation</a>
    <h1>en construction</h1>
</div>
</body>
</html>