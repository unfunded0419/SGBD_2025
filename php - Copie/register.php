<?php
session_start(); 
include "../page_html/register.html"; 
if (isset($_POST["submit"])) {
    if (strlen($_POST["Matricule"]) == 6) {
         
        $Matricule = filter_input(INPUT_POST, "Matricule", FILTER_VALIDATE_INT);
        if ($Matricule != null) { 
        include "Connexion_DB.php"; 
        $Nom = filter_input(INPUT_POST, "Nom", FILTER_SANITIZE_SPECIAL_CHARS); 
        $Prenom = filter_input(INPUT_POST, "Prenom", FILTER_SANITIZE_SPECIAL_CHARS); 
        $Email = filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL); 
        $password = $_POST["MDP"]; 
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO etudiant (E_Matricule, Nom, Prenom, E_mail, Mot_de_passe) VALUES ('$Matricule','$Nom', '$Prenom', '$Email', '$hash')"; 
        mysqli_query($conn, $sql);
        mysqli_close($conn);
        header ("Location: connexion.php");  
        } else {
            echo "Erreur rencontree au moment du traitement de votre matricule"; 
        }
    } else {
        echo "Un matricule est constitué d'exactement 6 chiffres"; 
    }
} 
?>