<?php
session_start(); 
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
        if ($_POST["Etudiant_Responsable"] == "Etudiant") {
        $sql = "INSERT INTO etudiant (E_Matricule, Nom, Prenom, E_mail, Mot_de_passe) VALUES ('$Matricule','$Nom', '$Prenom', '$Email', '$hash')"; 
        mysqli_query($conn, $sql);
        mysqli_close($conn);
        header ("Location: connexion.php");  
        } else {
        $sql = "INSERT INTO responsable_des_equipements (RE_Matricule, Nom, Prenom, E_mail, Mot_de_passe, Statut) VALUES ('$Matricule','$Nom', '$Prenom', '$Email', '$hash', 'En attente')"; 
        mysqli_query($conn, $sql);
        mysqli_close($conn);
        header ("Location: connexion.php");  
        }
        } else {
            $erreur_caractere = "Erreur rencontree au moment du traitement de votre matricule"; 
        }
    } else {
       $erreur_nombre = "Un matricule est constitué d'exactement 6 chiffres"; 
    }
} 
/*session_start(); 
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
} */
?>
<!DOCTYPE html>
    <html>
        <head>
            <title> Inscription </title>
            <meta charset = "utf-8">
            <link rel = "stylesheet" href = "../css/register.css?v=1.1">
             <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        </head>
        <body class = "elms-sans-text">
            <form action = "../page_interactive/register.php" method = "post">  
                <div class = "align">
                    <h1> Remplissez le formulaire suivant </h1>
                </div>
                <div class = "align">
                <label for = "Matricule"> Matricule  : </label>
                <input type = "number"  id = "Matricule" name = "Matricule" required = "required">
                </div>
                <?php if(!empty($erreur_nombre)) { ?>
                  <p> <?php echo $erreur_nombre ?> </p> 
                <?php } ?>
                <?php if(!empty($erreur_caractere)) { ?>
                    <p> <?php echo $erreur_caractere ?> </p> 
                <?php } ?>
                <div class = "align">
                <label for = "Nom"> Nom : </label>
                <input type = "text" id ="Nom" name = "Nom" required = "required">
                </div>
                <div class = "align">
                <label for = "Prenom"> Prenom : </label>
                <input type = "text" id ="Prenom" name ="Prenom" required = "required">
                </div>
                <div class = "align"> 
                <label for = "Email"> Email : </label>
                <input type ="email" id = "Email" name = "Email" required ="required">
                </div>
                <div class = "align">
                <label for = "MDP"> Mot de Passe : </label>
                <input type = "password" id ="MDP" name = "MDP" required="required">
                </div>
                <div class = "choix">
                    <div class = "choi">
                <input type = "radio" id = "Etudiant" name = "Etudiant_Responsable" value="Etudiant" checked>
                <label for = "Etudiant"> Etudiant </label> 
                </div>
                    <div class = "choi">
                <input type = "radio" id = "Responsable" name = "Etudiant_Responsable" value="Responsable"> 
                <label for  = "Responsable">Responsable</label>
                </div>
                </div>
                <div class = "bouton">
                <input type ="submit" name = "submit" value = "submit">
                </div>
            </form>
        </body>
    </html>

