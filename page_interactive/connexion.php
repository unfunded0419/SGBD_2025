<?php
session_start(); 
include '../page_html/connexion.html';  

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION["utilisateur"] = filter_input(INPUT_POST, "utilisateur", FILTER_SANITIZE_SPECIAL_CHARS);  
    $_SESSION["logged"] = true; 
    header("Location: Home.php");  
}
?>
