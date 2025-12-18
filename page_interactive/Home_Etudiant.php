<?php
session_start();

if ($_SESSION["logged"] == false || $_SESSION["Responsable"] == true) {
    header("Location: connexion.php");
    exit();
}

include 'Connexion_DB.php';
$matricule = $_SESSION['Matricule'];
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
    }else{
        $erreur = "Nom de projet déjà utilisé";
    }
    
    }

if (isset($_POST['quitter'])){
    $projet_a_quitter = (int)$_POST['id_projet'];

    $requetesql_quitter = "DELETE from participer WHERE E_Matricule = '$matricule' AND ID_Projet = '$projet_a_quitter'";
    mysqli_query($conn,$requetesql_quitter);
}
?>

<html>
    <head>
        <title> Page d'accueil Etudiant </title>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "../css/Home_Etudiant.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    </head>
    <body>
        <h1> Page de l'étudiant #<?php echo $_SESSION["Matricule"]; ?>  </h1> <br>
        
    <form action="../page_interactive/Home_Etudiant.php" method="post">
        

        <hr>
        <h3>Créer un projet</h3> <br>

        <?php 
            if (!empty($erreur)) {
            echo "<p class='erreur'>$erreur</p>";
            }
        ?>
        <label>Nom du projet :</label>
        <input type="text" name="nom_projet" title="Nom du projet" required>
        <button type="submit" name="creer" >Créer</button>
        <hr>
    </form>
    
        <h3>Liste de vos projets : </h3> <br>
        <?php
        if($nbre_lignes_projet == 0){
         echo "<h4>Aucun projets en cours</h4>";
        }else{
            while($ligne = mysqli_fetch_assoc($resultat_projet)){
                echo "<h4>";
                echo "Nom : " . $ligne['Nom']. "<br>" . " id projet : ". $ligne['ID_Projet'] . "<br>";
                echo '<form action="../page_interactive/Home_Etudiant.php" method="post">';
                echo '<input type="hidden" name="id_projet" value="' . $ligne['ID_Projet'] . '">';
                echo '<button type="submit" name="quitter">Quitter le projet</button>';
                echo '</form>';
            }

        } 
        
        
        ?>
    </body>
</html>

