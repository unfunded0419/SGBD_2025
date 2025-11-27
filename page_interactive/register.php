<?php
include 'Connexion_DB.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $Matricule = (int)$_POST["Matricule"];
    $Nom = $_POST["Nom"];
    $Prenom = $_POST["Prenom"];
    $Email = $_POST["Email"];
    $Mdp = $_POST["MDP"];

    
    
    $requetesqlResp = "SELECT * FROM responsable_des_equipements WHERE RE_Matricule = '$Matricule'";
    $resultatResp = mysqli_query($conn,$requetesqlResp);
    $requetesqlEtud = "SELECT * FROM etudiant WHERE E_Matricule = '$Matricule'";
    $resultatEtud = mysqli_query($conn,$requetesqlEtud);

    if(mysqli_num_rows($resultatResp) == 0 && mysqli_num_rows($resultatEtud) == 0 ){
        if(isset($_POST['responsable'])){
            $requetesql = "INSERT INTO responsable_des_equipements(RE_Matricule,Nom,Prenom,E_mail,Mot_de_passe)
                            VALUES ('$Matricule','$Nom','$Prenom','$Email','$Mdp')";
        }else{
            $requetesql = "INSERT INTO etudiant(E_Matricule,Nom,Prenom,E_mail,Mot_de_passe)
                            VALUES ('$Matricule','$Nom','$Prenom','$Email','$Mdp')";
            }

        mysqli_query($conn, $requetesql);
        header("Location: connexion.php");

        mysqli_close($conn);
        exit();

        }else{
        $erreur = "Matricule déjà enregistré";
        
                  

        }
    

    }
mysqli_close($conn);
include "../page_html/register.html"; 
?>