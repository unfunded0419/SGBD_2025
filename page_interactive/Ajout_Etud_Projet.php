<?php
session_start();

if ($_SESSION["logged"] == false || $_SESSION["Responsable"] == true) {
    header("Location: connexion.php");
    exit();
}

include 'Connexion_DB.php';
$erreur = ""; 

$matricule = $_SESSION["Matricule"]; 
$sql = "SELECT projet.ID_Projet as ID_Projet, projet.Nom as Nom_projet  FROM projet JOIN participer WHERE '$matricule' = participer.E_Matricule AND projet.ID_Projet = participer.ID_Projet"; 
$result = mysqli_query($conn, $sql); 
if (mysqli_num_rows($result) != 0) {
$reuse_result = mysqli_fetch_all($result, MYSQLI_ASSOC); 
} else {
    $erreur = "Veuillez vous inscrire à un projet pour commencer"; 
    mysqli_close($conn);     
}
if (isset($_POST['ajouter'])) {

    $matricule_a_ajouter = $_POST["Matricule"];
    $projet = $_POST["ID_Projet"]; 
    $detective = "SELECT etudiant.E_Matricule FROM etudiant WHERE '$matricule_a_ajouter' = etudiant.E_Matricule"; 
    $detection = mysqli_query($conn, $detective);  
    if (mysqli_num_rows($detection) == 0) {
        $erreur = "L'etudiant de matricule $matricule_a_ajouter n'existe pas"; 
    } else {
    $par = "INSERT INTO participer(ID_Projet, E_Matricule) VALUES ('$projet', '$matricule_a_ajouter')"; 
    mysqli_query($conn, $par); 
    }
    } 
?>
<html>
    <head>
        <title> Ajout Etudiants </title>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "../css/Ajout_Etud_Projet.css?v=1.2">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        
    </head>
    <body>
        <?php if (empty($erreur)) { ?>    
    <?php  foreach ($reuse_result as $row) { ?>
        <?php
        $idprojet = $row["ID_Projet"]; 
        $sql2 = "SELECT Etudiant.E_Matricule as Etudiant_matricule, Etudiant.Prenom as Prenom, Etudiant.Nom as Nom
        FROM participer, etudiant WHERE '$idprojet' = participer.ID_Projet AND participer.E_Matricule = etudiant.E_Matricule"; 
        $participants = mysqli_query($conn, $sql2); 
        ?>
        
            <form action="../page_interactive/Ajout_Etud_Projet.php" method="post">
                <hr>
                <h2>Nom du projet : <?php echo $row["Nom_projet"]?> </h2> 
                <h3>Ajout d'un étudiant</h3> <br>

                <?php 
                    if (!empty($erreur)) {
                    echo "<p class='erreur'> $erreur </p>";
                    }
                ?>
                <label for = "Matricule">Matricule :</label>
                <input type="text" id = "Matricule" name="Matricule" pattern = "\d{6}" title="matricule (6 chiffres)" required>
                <input type = "hidden" name = "ID_Projet" value = "<?php echo $row["ID_Projet"]; ?>">
                <button type="submit" name="ajouter" >Ajouter</button>
                <hr>
            </form>
            <h4>
            <h3>Participants : </h3> <br>

            <?php
            if(mysqli_num_rows($participants) == 0){
            echo "<h4>Aucun participants</h4>";
            }else{
                while($ligne = mysqli_fetch_assoc($participants)){
                    echo "<h4>";
                    echo "Prénom : " . $ligne['Prenom']. "<br>" . " Nom : ". $ligne['Nom'] . "<br>" . " Matricule : " . $ligne['Etudiant_matricule'];
                    echo "</h4>";
                }
            } 
            ?>
            </h4>
            <?php } } else { ?>
            <div class = "erreur">
        <p><?php echo $erreur; ?> </p>
    </div> 
    <?php }?>
    </body>
</html>