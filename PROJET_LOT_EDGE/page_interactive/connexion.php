<?php
session_start(); 
include '../page_html/connexion.html';  

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION["utilisateur"] = $_POST["utilisateur"]; 
    $_SESSION["logged"] = true; 
    $utilisateur = $_POST['utilisateur'] ?? '';
    $motdepasse = $_POST['motdepasse'] ?? '';

    echo "<h1>Informations saisies :</h1>";
    echo "<p>Utilisateur : " . htmlspecialchars($utilisateur) . "</p>";
    echo "<p>Mot de passe : " . htmlspecialchars($motdepasse) . "</p>";
    header("Location: Home.php");  
} else {
    echo "Aucune donnée reçue.";
}
?>
