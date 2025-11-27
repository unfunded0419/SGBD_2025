<?php
session_start(); // me permet de créer un cookie pour plus tard, dans lequel je peux charger des données réutilisable plus tard
include 'Connexion_DB.php';
 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["utilisateur"];
    $mdp   = $_POST["motdepasse"]; 
    
    $requetesqlResp = "SELECT * FROM responsable_des_equipements WHERE E_mail = '$email'";
    $requetesqlEtud = "SELECT * FROM etudiant WHERE E_mail = '$email'";

    $resultatResp = mysqli_query($conn,$requetesqlResp);
    $resultatEtud = mysqli_query($conn,$requetesqlEtud);

    if (mysqli_num_rows($resultatResp) >0){

        $donnee_reponsable = mysqli_fetch_assoc($resultatResp) ;

        if($mdp == $donnee_reponsable["Mot_de_passe"]){

            $_SESSION["utilisateur"] = $donnee_reponsable["E_mail"];
            $_SESSION["role"] = "responsable";
            $_SESSION["matricule"] = $donnee_responsable["RE_Matricule"];
            $_SESSION["connecté"] = true; // pour empécher les users d'accéder à n'importe quelle page sans être connecté 
            header("Location: Home_Resp.php");

            mysqli_close($conn);
            exit();
        }

    } elseif(mysqli_num_rows($resultatEtud) >0){

        $donnee_etudiant = mysqli_fetch_assoc($resultatEtud) ;

        if($mdp == $donnee_etudiant["Mot_de_passe"]){

            $_SESSION["utilisateur"] = $donnee_etudiant["E_mail"];
            $_SESSION["role"] = "etudiant";
            $_SESSION["matricule"] = $donnee_etudiant["E_Matricule"];
            $_SESSION["connecté"] = true; 
            header("Location: Home_Etudiant.php");

            mysqli_close($conn);
            exit();
        }


    }

    $erreur = "Email ou mot de passe incorrect !";


    }

mysqli_close($conn);
include '../page_html/connexion.html';
?>
