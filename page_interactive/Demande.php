<?php
session_start();
// je me suis permis de prendre tout ton HTML et je l'ai mit dans le .HTML associé (pour simplifier la tache pour le css sinon je comprenais plus rien)
if (!isset($_SESSION["connecté"]) || $_SESSION["role"] != "etudiant" || !isset($_SESSION["id_projet"])) {
    header("Location: connexion.php");
    exit();
}

include("Connexion_DB.php");
$id_projet = $_SESSION["id_projet"];
$matricule = $_SESSION["matricule"];

//je récupère des infos pour le .html (pour faire un truc clean) 
$requetesql_nom_projet = "SELECT * FROM projet WHERE projet.ID_Projet = '$id_projet' ";
$resultat_nom_projet  = mysqli_query($conn,$requetesql_nom_projet);
$ligne = mysqli_fetch_assoc($resultat_nom_projet);
$nom_projet = $ligne['Nom']; 

$requetesql_liste_demandes = "SELECT * FROM emprunt WHERE emprunt.ID_Projet= '$id_projet' " ;
$resultat_liste_demandes = mysqli_query($conn,$requetesql_liste_demandes);
$nbre_lignes_liste_demandes = mysqli_num_rows($resultat_liste_demandes); 
if (isset($_POST["submit"])) { /* code déguelasse vérifiant si l'utilisateur à bien remplit les champs pour les dates de manière logique , ensuite il vérifie si l'objet demandé se trouve dans la db et enfin seulement l'emprunt et la table concerner sont mise à jour*/
    $_Article = filter_input(INPUT_POST, "nom_article", FILTER_SANITIZE_SPECIAL_CHARS);  
    $today = date('Y-m-d'); 
        if($_POST["date_debut"] >= $today) {
            $_date_debut = $_POST["date_debut"]; 
                if($_POST["date_retour"] >= $_date_debut) {
                    $_date_retour = $_POST["date_retour"]; 
                    $_Raison = filter_input(INPUT_POST, "raison_emprunt", FILTER_SANITIZE_SPECIAL_CHARS);
                    
                    // j'ai corrigé le truc dont on parlait j'ai donc ajouté un champ dans la db, ça permet de ne pas passer par concerner
                    $sql = "SELECT ID_Modele from modele WHERE Reference = '$_Article'"; 
                    $result = mysqli_query($conn, $sql);
                    
                        if(mysqli_num_rows($result)>0) {
                            $ligne_modele = mysqli_fetch_assoc($result);
                            $id_modele = $ligne_modele['ID_Modele'];
                            
                            
                            $sql = "INSERT INTO emprunt(Date_debut, Date_fin_prevue, Raison_Emprunt, E_Matricule, ID_Projet, Statut, ID_Modele_demande) VALUES ('$_date_debut', '$_date_retour', '$_Raison', '$matricule', '$id_projet', 'en attente', '$id_modele')"; /*il faut encore ajouter le projet auquel l'emprunt est associé*/ 
                        if(mysqli_query($conn, $sql)) {
                                    
                                }
                        } else {
                            $erreur = "L'article recherché n'existe pas !"; 
                        }
                    
                } else {
                    $erreur = "On ne peut pas finir un emprunt avant de l'avoir commencer !"; 
                }
        } else {
        $erreur = "L'emprunt sera autorisé au plus tôt aujourd'hui !";   
        }
    }
include '../page_html/Demande.html';
mysqli_close($conn);
?>
