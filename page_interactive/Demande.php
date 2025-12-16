
<?php
session_start();
if (!isset($_SESSION["connecté"]) || $_SESSION["role"] != "etudiant" || !isset($_SESSION["id_projet"])) {
    header("Location: connexion.php");
    exit();
}



include("Connexion_DB.php");
    $id_projet = $_SESSION["id_projet"];
    $matricule = $_SESSION["matricule"];

    $requetesql_nom_projet = "SELECT * FROM projet WHERE projet.ID_Projet = '$id_projet' ";
    $resultat_nom_projet  = mysqli_query($conn,$requetesql_nom_projet);
    $ligne = mysqli_fetch_assoc($resultat_nom_projet);
    $nom_projet = $ligne['Nom']; //je récupère le nom du projet associé a l'id juste pour faire un truc propre dans le html

    $requetesql_liste_demandes = "SELECT * FROM emprunt
                                  WHERE emprunt.ID_Projet= '$id_projet' " ;
    $resultat_liste_demandes = mysqli_query($conn,$requetesql_liste_demandes);// je stock dans une variable toutes les demandes du projet pour les cout dans le .html
    $nbre_lignes_liste_demandes = mysqli_num_rows($resultat_liste_demandes); // utile aussi pour cout de le .html ( si pas de ligne je mets juste "aucune demande en cours")

if (isset($_POST["submit"])) {

    $statut = "en attente";

    $_Article = filter_input(INPUT_POST, "nom_article", FILTER_SANITIZE_SPECIAL_CHARS); 

    $_date_debut = $_POST["date_debut"]; 

    $_date_retour = $_POST["date_retour"]; 

    $_Raison = filter_input(INPUT_POST, "raison_emprunt", FILTER_SANITIZE_SPECIAL_CHARS);

    $_chercheur = "SELECT ID_Modele from modele WHERE Reference = '$_Article'"; 
    $resultat_id_Modele = mysqli_query($conn,$_chercheur);
    $ligne_modele = mysqli_fetch_assoc($resultat_id_Modele);
    $id_modele= $ligne_modele['ID_Modele']; // Pour récupérer l'id du modele concerné

    $requetesql_ajout_emprunt = "INSERT INTO emprunt (Date_debut, Date_fin_prevue, Raison_Emprunt, E_Matricule, ID_Projet, Statut,ID_Modele_demande) 
                                VALUES ('$_date_debut', '$_date_retour', '$_Raison','$matricule','$id_projet','$statut','$id_modele')";
    
    if(mysqli_query($conn, $requetesql_ajout_emprunt)) {
    }
    else {
        $erreur = "Modification de la base de donnée impossible"; 
    } 

}

include '../page_html/Demande.html'; 
?>