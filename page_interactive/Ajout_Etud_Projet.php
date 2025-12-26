<?php
session_start();

if ($_SESSION["logged"] == false || $_SESSION["Responsable"] == true || empty($_SESSION["ID_Projet"])) {
    header("Location: connexion.php");
    exit();
}

include 'Connexion_DB.php';
$erreur = ""; 
$idprojet = $_SESSION["ID_Projet"]; 
$matricule = $_SESSION["Matricule"]; 
$sql = "SELECT projet.Nom as Nom_projet  FROM projet WHERE projet.ID_Projet = '$idprojet'"; 
$result = mysqli_query($conn, $sql); 
$reuse_result = mysqli_fetch_all($result, MYSQLI_ASSOC); 

if (isset($_POST['ajouter'])) {

    $matricule_a_ajouter = $_POST["Matricule"];
    $projet = $_POST["ID_Projet"]; 
    $detective = "SELECT etudiant.E_Matricule FROM etudiant WHERE '$matricule_a_ajouter' = etudiant.E_Matricule"; 
    $detection = mysqli_query($conn, $detective);  
    if (mysqli_num_rows($detection) == 0) {
        $erreur = "L'étudiant de matricule {$matricule_a_ajouter} n'existe pas"; 
    } else {
    $par = "INSERT INTO participer(ID_Projet, E_Matricule) VALUES ('$projet', '$matricule_a_ajouter')"; 
    try {
    mysqli_query($conn, $par);
    } catch (mysqli_sql_exception) {
        $erreur = "L'étudiant de matricule {$matricule_a_ajouter} participe déjà au projet";
    } 
    }
    } 
?>
<html>
    <head>
        <title> Ajout Etudiants </title>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "../css/Ajout_Etud_Projet.css?v=1.4">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    </head>
    <body>
        
        <?php if (empty($erreur)) { ?>    
            <main>
                <h1>Ajout d'étudiants aux projets que vous suivez utilisateur : <?php echo " {$_SESSION["Nom"]} {$_SESSION["Prenom"]}"?></h1> 
    <?php  foreach ($reuse_result as $row) { ?>
        <div class = "projet">
        <?php
        $sql2 = "SELECT Etudiant.E_Matricule as Etudiant_matricule, Etudiant.Prenom as Prenom, Etudiant.Nom as Nom
        FROM participer, etudiant WHERE '$idprojet' = participer.ID_Projet AND participer.E_Matricule = etudiant.E_Matricule"; 
        $participants = mysqli_query($conn, $sql2); 
        ?>
            <h2>Nom du projet : <?php echo $row["Nom_projet"]?> </h2> 
            <form action="../page_interactive/Ajout_Etud_Projet.php" method="post">
                <hr> 
                <h3>Ajout d'un étudiant</h3> <br>

                <?php 
                    if (!empty($erreur)) {
                    echo "<p class='erreur'> $erreur </p>";
                    }
                ?>
                <label for = "Matricule">Matricule :</label>
                <input type="text" id = "Matricule" name="Matricule" pattern = "\d{6}" title="matricule (6 chiffres)" required>
                <input type = "hidden" name = "ID_Projet" value = "<?php echo $idprojet; ?>">
                <button type="submit" name="ajouter" >Ajouter</button>
                <hr>
            </form>
            <div class = "participant">
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
            </div>
            </div>
            <?php }  ?> 
            </main> 
            <footer>
                 <p><a href = "../page_interactive/Home.php"> Page d'acceuil </a> </p>
                    <p><a href = "../page_interactive/Demande.php"> - Faire une demande </a></p>
                    <p><a href = "../page_interactive/Stocks.php"> - Consulter les stocks </a></p>
                    <p><a href = "../page_interactive/Suivi_demande.php"> - Suivre les demandes </a></p>
                    <p><a href = "../page_interactive/Ajout_Etud_Projet.php"> - Ajouter des étudiants au projet</a></p>
                    <p><a href = "../page_interactive/Home_Etudiant.php"> - Projets suivi </a> </p>
            </footer>
            <?php } else { ?>
            <div class = "erreur">
        <p><?php echo $erreur; ?> </p>
    </div> 
    <?php }?>
    </body>
</html>