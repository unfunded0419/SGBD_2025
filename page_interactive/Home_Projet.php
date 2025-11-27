<?php
session_start(); 
if (!isset($_SESSION["connecté"]) || $_SESSION["role"] != "etudiant") {
    header("Location: connexion.php");
    exit();
}

// je stock l'id du projet sur lequel j'ai cliqué (et que j'ai transféré via l'URL) dans le cookie de connexion
// Comme ça sur les page suivante je pourrai utiliser l'id comme un paramètre ( créer des demandes, ajouter des participants au projet etc etc ...)
if($_GET['id_projet']){
$_SESSION['id_projet'] = (int)$_GET['id_projet']; 

}else{
header("Location: Home_Etudiant.php");
}

include '../page_html/Home_Projet.html';
?>