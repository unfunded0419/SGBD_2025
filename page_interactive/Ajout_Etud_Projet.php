<?php
session_start();

if (!isset($_SESSION["connecté"]) || $_SESSION["role"] != "etudiant" ||  !isset($_SESSION["id_projet"]) ) {
    header("Location: connexion.php");
    exit();
}

include 'Connexion_DB.php';
$idprojet = (int)$_SESSION['id_projet'];
$requetesql_nom = "SELECT Nom FROM projet WHERE ID_Projet ='$idprojet'";
$resultat_nom = mysqli_query($conn,$requetesql_nom);
$nomprojet = mysqli_fetch_assoc( $resultat_nom);

$requetesql_participants = "SELECT * FROM participer 
                            JOIN etudiant ON participer.E_Matricule = etudiant.E_Matricule
                            WHERE ID_Projet = '$idprojet' ";
$resultat_participants = mysqli_query($conn,$requetesql_participants);
$nbre_lignes_participants = mysqli_num_rows($resultat_participants);

if (isset($_POST['ajouter'])) {

    $matricule_a_ajouter = $_POST["matricule"];

    $requetesql_check_inscrit = "SELECT * FROM etudiant WHERE E_Matricule = '$matricule_a_ajouter'";
    $resultat_check_inscrit = mysqli_query($conn,$requetesql_check_inscrit);

    $requetesql_check_projet = "SELECT * FROM participer WHERE E_Matricule = '$matricule_a_ajouter' AND ID_Projet = '$idprojet'";
    $resultat_check_projet = mysqli_query($conn,$requetesql_check_projet);

    if(mysqli_num_rows($resultat_check_inscrit) != 0){ 
        if(mysqli_num_rows($resultat_check_projet) == 0){
        $requetesql = "INSERT INTO participer(ID_Projet,E_Matricule) VALUES ('$idprojet','$matricule_a_ajouter')";
        mysqli_query($conn,$requetesql);
        
        header("Location: Ajout_Etud_Projet.php");
        }else{
            $erreur = "L'étudiant participe déjà au projet";
        }

    }else{
        $erreur = "Aucun étudiant correspondant à ce matricule";
    }
    
    }

include '../page_html/Ajout_Etud_Projet.html';


?>


