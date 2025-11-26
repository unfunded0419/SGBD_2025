<?php
session_start(); 
if ($_SESSION["logged"]) {
include 'Connexion_DB.php'; 
$sql = "SELECT DISTINCT categorie.Nom as Nom, modele.Reference as Reference, modele.Description as Description, SUM(CASE WHEN exemplaire.Etat = 'utilisable' AND exemplaire.Disponibilite = 'disponible' THEN 1 ELSE 0 END) as En_Stocks
FROM modele
JOIN categorie ON categorie.ID_Categorie = modele.ID_Categorie
LEFT JOIN exemplaire ON exemplaire.ID_Modele = modele.ID_Modele /*fonctionnement des join comme si on travaillait avec des compositions de fonctions*/
GROUP BY categorie.Nom, modele.Reference
Order BY categorie.Nom"; 
$result = mysqli_query($conn, $sql); 

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href = "Stocks.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Inventaire</title>
</head>
<body class ="elms-sans-text">
    <h1> Liste du materiel : </h1>
    <div>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class = "Appareil">
        <?php echo "Categorie : {$row["Nom"]} <br>"; ?> 
        <?php echo "Nom Produit : {$row["Reference"]} <br>"; ?>  
        <?php echo "Description : {$row["Description"]} <br>"; ?> 
        <?php echo "Quantite disponible en stock : {$row["En_Stocks"]}"; ?> 
        </div> <br>
    <?php }?>
    </div>
    <footer>
                <p> Revenir à la page <a href = "../page_interactive/Home.php"> d'acceuil</a> </p>
                <p> Allez sur la page <a href = "../page_interactive/Demande.php"> de demande </a></p>
                <p> Allez sur la page de consultation <a href = "../page_interactive/Stocks.php"> des stocks </a></p>
    </footer>
</body>
</html>

<?php
mysqli_close($conn); 
} else {
    header("Location : connexion.php"); 
}
?>