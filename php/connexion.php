<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $utilisateur = $_POST['utilisateur'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    echo "<h1>Informations saisies :</h1>";
    echo "<p>Utilisateur : " . htmlspecialchars($utilisateur) . "</p>";
    echo "<p>Mot de passe : " . htmlspecialchars($motdepasse) . "</p>";
} else {
    echo "Aucune donnée reçue.";
}

?>