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
$reuse_result = mysqli_fetch_all($result, MYSQLI_ASSOC); 
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel = "stylesheet" href = "../css/Stocks.css?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Inventaire</title>
</head>
<body class ="elms-sans-text">
<main>    
<h1> Liste du materiel : </h1>
<h2>Filtrer les résultats suivant les catégories : </h2>
    <form action = "Stocks.php" method = "post">
    <?php foreach ($reuse_result as $row) { ?>
        <div class = "champ">
    <input type = "checkbox" name = "categories[]" value = "<?php echo htmlspecialchars($row["Nom"]);?>">  
    <p> <?php echo htmlspecialchars ($row["Nom"]); ?> </p>
        </div>  
    <?php }?>
    <div class = "boutton">
    <input type = "submit" name = "submit" value = "Trier">
    </div>
    </form>
<?php
$categorie = array_column($reuse_result,'Nom'); 
if (isset($_POST["submit"])) {
    if (isset($_POST["categories"])) {
        $categorie = $_POST["categories"];
    }
}
?>
<h2> Etats des stocks : </h2>
    <?php foreach($reuse_result as $row) { ?>
            <?php foreach($categorie as $finder) {
                if ($row["Nom"] == $finder) { ?>
            <div class = "Appareil">
                <div class = "contenu">Categorie : <?php echo htmlspecialchars($row["Nom"]); ?> </div>
                <div class = "contenu">Nom produit : <?php echo htmlspecialchars($row["Reference"]); ?>  </div>
                <div class = "contenu">Description : <?php echo htmlspecialchars($row["Description"]); ?>  </div>
                <div class = "contenu">Quantite disponible en stock : <?php echo ($row["En_Stocks"]); ?> </div>
            </div>
              <?php  }
            } ?>
    <?php }?>
    </main>  
    <footer>
                <p> Page <a href = "../page_interactive/Home.php"> d'acceuil</a> </p>
                <p> - Faire une <a href = "../page_interactive/Demande.php"> demande </a></p>
                <p> - Consulter <a href = "../page_interactive/Stocks.php"> les stocks </a></p>
                <p> - Suivre les <a href = "../page_interactive/Suivi_demande.php"> demandes </a></p>
    </footer>
</body>
</html>

<?php
mysqli_close($conn); 
} else {
    header("Location: connexion.php"); 
}
?>