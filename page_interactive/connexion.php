<?php
    session_start(); 
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["Email"])) {
        $_SESSION["Email"] = filter_input(INPUT_POST, "Email", FILTER_SANITIZE_EMAIL);  
        $_SESSION["logged"] = true; 
        header("Location: Home.php");  
        exit(); 
        } else {
        $erreur = "Adresse mail non reconnue dans la DB";
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Connexion - Université de Mons </title>
        <link rel="stylesheet" href="../css/connexion.css?v=1.2">
        <link href='https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css' rel='stylesheet'>
    </head>
    <body>
    <div class="boite-login">
        <form action="connexion.php" method="post">
            <h1>Login</h1>

        <div class="entree-login">
            <input type="email" name="Email" placeholder="Email" required>
            <i class='bx  bxs-user'    ></i> 
        </div> 
            <?php if(!empty($erreur)) {?>
            <p> <?php echo $erreur; ?>
            <?php } ?>
        <div class="entree-login">
            <input type="password" name="MDP" placeholder="Mot de passe" required>
            <i class='bx  bxs-lock'    ></i> 
        </div>

            <button type="submit" class="bouton-login">Connexion</button>

        <div class="register">
            <p> Pas encore de compte ? <a href="register.php">S'enregistrer </a> </p>
        </div>
        </form>
        </div>     
    </body>
</html> 
