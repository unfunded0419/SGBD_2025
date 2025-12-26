<?php
    session_start(); 
    $erreur = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_Matricule = $_POST["Matricule"]; 
        $_MDP = $_POST["MDP"];  
        if (preg_match('/^\d{6}$/', $_Matricule) ) { /*permet de vérifier si $_Matricule est une chaîne de 6 entier // = limiteur du motf, ^ = début chaîne, $ = fin chaîne, \d = char est un entier, {6} = \d répété six fois */ 
            include 'Connexion_DB.php'; 
            if ($_POST["Responsable_Etudiant"] == "Responsable") {
            $sql = "SELECT * FROM responsable_des_equipements WHERE responsable_des_equipements.RE_Matricule = '$_Matricule' AND responsable_des_equipements.Statut = 'approuve'"; 
            $result = mysqli_query($conn, $sql); 
            if (mysqli_num_rows($result)>0){
               $row = mysqli_fetch_assoc ($result); 
               if (password_verify($_MDP, $row["Mot_de_passe"])) {
                            $_SESSION["Nom"] = $row["Nom"]; 
                            $_SESSION["Prenom"] = $row["Prenom"]; 
                            $_SESSION["Email"] = $row["E_mail"]; 
                            $_SESSION["Matricule"] = $row["RE_Matricule"];   
                            $_SESSION["logged"] = true;
                            $_SESSION["Responsable"] = true; 
                            if ($row["admin"] == 1) {
                                $_SESSION["admin"] = true; 
                            } else {
                                $_SESSION["admin"] = false; 
                            }
                            mysqli_close($conn);  
                            header("Location: Home.php");  
                            exit(); 
               } else {
                $erreur = "Mot de passe invalide"; 
               }
            } else {
                mysqli_close($conn);   
                $erreur = "Utilisateur non enregistré dans la DB ou compte inactif";  
            }
        } else {
            $sql = "SELECT * FROM etudiant WHERE etudiant.E_Matricule = '$_Matricule'"; 
            $result = mysqli_query($conn, $sql); 
                if (mysqli_num_rows($result)>0) {
                    if($row = mysqli_fetch_assoc($result)) {
                        if (password_verify($_MDP, $row["Mot_de_passe"])) {/*nécessaire d'utiliser cette condition car $result retourne un array de donnée et la fonction empty ne vérifie que si une variable est vide pas le tableau*/ 
                            $_SESSION["Nom"] = $row["Nom"]; 
                            $_SESSION["Prenom"] = $row["Prenom"]; 
                            $_SESSION["Email"] = $row["E_mail"]; 
                            $_SESSION["Matricule"] = $row["E_Matricule"];   
                            $_SESSION["logged"] = true;
                            $_SESSION["Responsable"] = false; 
                            mysqli_close($conn);  
                            header("Location: Home_Etudiant.php");  
                            exit(); 
                        }
                        else {
                            mysqli_close($conn);  
                            $erreur = "Mot de passe invalide";
                        }   
                    }
                }else {
                    mysqli_close($conn);   
                    $erreur = "Utilisateur non enregistré dans la DB"; 
                } 
            } 
        } else { 
            $erreur = "Un matricule contient 6 chiffres";
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Connexion - Université de Mons </title>
        <link rel="stylesheet" href="../css/connexion.css?v=1.0">
        <link href='https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
    <div class="boite-login">
        <?php if(empty($erreur)) {?>
        <form action="connexion.php" method="post">
            <h1>Login</h1>
        <div class="entree-login">
            <input type="number" name="Matricule" placeholder="Matricule" required>
            <i class='bx  bxs-user'    ></i> 
        </div> 
        <div class="entree-login">
            <input type="password" name="MDP" placeholder="Mot de passe" required>
            <i class='bx  bxs-lock'    ></i> 
        </div>
        <div class="radio">
            <label for = "Etudiant"> Etudiant </label>
            <input type = "radio" id = "Etudiant" name="Responsable_Etudiant" value = "Etudiant" checked>
            <label for = "Responsable"> Responsable </label>
            <input type = "radio" id = "Responsable" name="Responsable_Etudiant" value = "Responsable">
        </div> 
            <button type="submit" class="bouton-login">Connexion</button>

        <div class="register">
            <p> Pas encore de compte ? <a href="register.php">S'enregistrer </a> </p>
        </div>
        <?php } else {?>
            <div class = "error">
        <p> <?php echo $erreur; ?> </p>
            </div>
        <?php } ?>
        </form>
        </div>     
    </body>
</html> 
