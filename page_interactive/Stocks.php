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
    <link rel = "stylesheet" href = "../css/Stocks.css?v=1.2">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <title>Inventaire</title>
</head>
<body class ="elms-sans-text">
<main>    
    <h1> Liste du materiel : </h1>
    <form action = "Stocks.php" method = "post">
        <h2>Filtrer les résultats suivant les catégories : </h2>
        <div class = "champ"> 
        <select name = "categorie">
            <option value = "">Aucun</option> 
        <?php foreach ($reuse_result as $row) { ?>
            <option value = "<?php echo htmlspecialchars($row["Nom"]);?>"> <?php echo htmlspecialchars ($row["Nom"]); ?> </option>  
    <?php }?>
    </select>
    </div>
    <div class = "boutton">
    <input type = "submit" name = "submit" value = "Trier">
    </div>
    </form>
<?php
if (isset($_POST["submit"])) {
        $categorie = $_POST["categorie"];
}
?>
    <div class = "catalogue">
    <h2> Etats des stocks : </h2> <?php /*duplication nécessaire suite à l'utilisation d'un select*/  ?>
    <?php if (empty($categorie)) { ?>
         <?php foreach($reuse_result as $row) { ?>
            <div class = "Appareil">
                <div class = "contenu">Categorie : <?php echo htmlspecialchars($row["Nom"]); ?> </div>
                <div class = "contenu">Nom produit : <?php echo htmlspecialchars($row["Reference"]); ?>  </div>
                <div class = "contenu">Description : <?php echo htmlspecialchars($row["Description"]); ?>  </div>
                <div class = "contenu">Quantite disponible en stock : <?php echo ($row["En_Stocks"]); ?> </div>
            </div>
    <?php }?>
    <?php } else { ?>
    <?php foreach($reuse_result as $row) { ?>
            <?php   if ($row["Nom"] == $categorie) { ?>
            <div class = "Appareil">
                <div class = "contenu">Categorie : <?php echo htmlspecialchars($row["Nom"]); ?> </div>
                <div class = "contenu">Nom produit : <?php echo htmlspecialchars($row["Reference"]); ?>  </div>
                <div class = "contenu">Description : <?php echo htmlspecialchars($row["Description"]); ?>  </div>
                <div class = "contenu">Quantite disponible en stock : <?php echo ($row["En_Stocks"]); ?> </div>
            </div>
              <?php  } ?>
    <?php } }?>
    </div>
    </main>  
    <footer>
                 <p><a href = "../page_interactive/Home.php"> Page d'acceuil </a> </p>
                    <p><a href = "../page_interactive/Demande.php"> - Faire une demande </a></p>
                    <p><a href = "../page_interactive/Stocks.php"> - Consulter les stocks </a></p>
                    <p><a href = "../page_interactive/Suivi_demande.php"> - Suivre les demandes </a></p>
                    <p><a href = "../page_interactive/Ajout_Etud_Projet.php"> - Ajouter des étudiants au projet</a></p>
                    <p><a href = "../page_interactive/Home_Etudiant.php"> - Projets suivi </a> </p>
    </footer>
</body>
</html>

<?php
mysqli_close($conn); 
} else {
    header("Location: connexion.php"); 
}
?>