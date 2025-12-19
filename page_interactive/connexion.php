<?php
session_start();
include 'Connexion_DB.php'; 
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricule = $_POST['Matricule']; 
    $password = $_POST['MDP'];

    // --- ÉTAPE 1 : VÉRIFIER DANS LA TABLE RESPONSABLE (Version MySQLi) ---
    // On prépare la requête (sécurité)
    $stmt = $conn->prepare("SELECT * FROM responsable_des_equipements WHERE RE_Matricule = ?");
    $stmt->bind_param("i", $matricule); // "i" veut dire integer (entier)
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $j=0;

    if ($admin && password_verify($password, $admin['Mot_de_passe'])) 
    {
        session_regenerate_id(true); 
        $_SESSION['Matricule'] = $admin['RE_Matricule'];
        $_SESSION['pseudo'] = $admin['Nom'];

        if ($admin['admin'] == 1) {
            $_SESSION['role'] = 'admin'; 
            $_SESSION['logged'] = true;
            header('Location: Home.php');
            exit();
        } elseif ($admin['Statut'] == "Accepté") {
            $_SESSION['role'] = 'resp'; 
            $_SESSION['logged'] = true;
            header('Location: Home.php');
            exit();
        } else{
            $attente = "Validation en attente.";
            $j=1;
        }
        
    }
    
    // --- ÉTAPE 2 : VÉRIFIER DANS LA TABLE ETUDIANT ---
    else {
        // On ferme la requête précédente
        $stmt->close();

        $stmt = $conn->prepare("SELECT * FROM etudiant WHERE E_Matricule = ?");
        $stmt->bind_param("i", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['Mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['Matricule'] = $user['E_Matricule'];
            $_SESSION['pseudo'] = $user['Nom'];
            $_SESSION['role'] = 'membre'; 
            $_SESSION['logged'] = true;

            header('Location: Home.php');
            exit();
        } else {
            if($j===0)
            {
                $erreur = "Identifiants incorrects.";
            }
        }
        $stmt->close();
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
            <input type="number" name="Matricule" placeholder="Matricule" required>
            <i class='bx  bxs-user'    ></i> 
        </div> 
            <?php if(!empty($erreur)) {?>
            <p> <?php echo $erreur; ?>
            <?php } ?>
            <?php if(!empty($attente)) {?>
            <p> <?php echo $attente; ?>
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
