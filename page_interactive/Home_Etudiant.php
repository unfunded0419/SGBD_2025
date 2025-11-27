<?php
session_start();

if (!isset($_SESSION["connecté"]) || $_SESSION["role"] != "etudiant") {
    header("Location: connexion.php");
    exit();
}

include 'Connexion_DB.php';
$matricule = $_SESSION['matricule'];
$requetesql_listeprojet = "SELECT * FROM projet
                           JOIN participer ON projet.ID_Projet = participer.ID_Projet
                           WHERE participer.E_Matricule = '$matricule' " ;
$resultat_projet = mysqli_query($conn,$requetesql_listeprojet);// je stock dans une variable tous les projets aux quels l'étudiant participe pour les cout dans le .html
$nbre_lignes_projet = mysqli_num_rows($resultat_projet);

if (isset($_POST['creer'])) {

    $nomprojet = $_POST["nom_projet"];
    $requetesql_check = "SELECT * FROM projet WHERE Nom = '$nomprojet'";
    $resultat = mysqli_query($conn,$requetesql_check);

    if(mysqli_num_rows($resultat) == 0){  
        $requetesql = "INSERT INTO projet(Nom) VALUES ('$nomprojet')";
        mysqli_query($conn,$requetesql);
        $id = mysqli_insert_id($conn); // je récupére la valeur de  l'autoincrement que la db à fait 
        $requetesql_participer = "INSERT INTO participer(ID_Projet,E_Matricule) VALUES ('$id','$matricule')";
        mysqli_query($conn,$requetesql_participer);

        header("Location: Home_Etudiant.php");
    }else{
        $erreur = "Nom de projet déjà utilisé";
    }
    
    }

if (isset($_POST['quitter'])){
    $projet_a_quitter = (int)$_POST['id_projet'];

    $requetesql_quitter = "DELETE from participer WHERE E_Matricule = '$matricule' AND ID_Projet = '$projet_a_quitter'";
    mysqli_query($conn,$requetesql_quitter);
    header("Location: Home_Etudiant.php");


}
include '../page_html/Home_Etudiant.html';


?>


