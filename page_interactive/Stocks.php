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
    <link rel = "stylesheet" href = "../css/Stocks.css?v=1.2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Inventaire</title>
</head>
<body class ="elms-sans-text">
<main>    
<h1> Liste du materiel : </h1>
    <?php while($row = mysqli_fetch_assoc($result)) { ?>
        <div class = "Appareil">
            <div class = "contenu"><?php echo "Categorie : {$row["Nom"]}"; ?> </div>
            <div class = "contenu"><?php echo "Nom Produit : {$row["Reference"]}"; ?>  </div>
            <div class = "contenu"><?php echo "Description : {$row["Description"]}"; ?>  </div>
            <div class = "contenu"><?php echo "Quantite disponible en stock : {$row["En_Stocks"]}"; ?> </div>
        </div>
    <?php }?>
    </main>  
    <footer>
                <p> Page <a href = "../page_interactive/Home.php"> d'acceuil</a> </p>
                <p> - Faire une <a href = "../page_interactive/Demande.php"> demande </a></p>
                <p> - Consulter <a href = "../page_interactive/Stocks.php"> les stocks </a></p>
    </footer>
</body>
</html>

<?php
mysqli_close($conn); 
} else {
    header("Location: connexion.php"); 
}
?>