
<?php
session_start();
if ($_SESSION["logged"]) {
include '../page_html/Demande.html'; 
include("Connexion_DB.php");
if (isset($_POST["submit"])) {
    $_Article = filter_input(INPUT_POST, "nom_article", FILTER_SANITIZE_SPECIAL_CHARS);  
    $_date_debut = $_POST["date_debut"]; 
    $_date_retour = $_POST["date_retour"]; 
    $_Raison = filter_input(INPUT_POST, "raison_emprunt", FILTER_SANITIZE_SPECIAL_CHARS); 
    $sql = "INSERT INTO emprunt (Date_debut, Date_fin_prevue, Raison_Emprunt) VALUES ('$_date_debut', '$_date_retour', '$_Raison')";  
    if(mysqli_query($conn, $sql)) {
        $id_emprunt = mysqli_insert_id($conn); /*retourne l'id de l'emprunt qui vient d'être créé à la ligne précédente*/
    }
    else {
        echo "Modification de la base de donnée impossible"; 
    } 
    $_chercheur = "SELECT ID_Modele from modele WHERE Reference = '$_Article'"; 
    $id_Modele = mysqli_query($conn,$_chercheur); 
}
} else {
    header("Location : connexion.php"); 
}
?>