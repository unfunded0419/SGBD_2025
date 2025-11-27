<?php
session_start(); 
if ($_SESSION["logged"]) {
    include "Connexion_DB.php"; 
    $Matricule = $_SESSION["Matricule"];  
    $sql = "SELECT emprunt.ID_Emprunt as ID_Emprunt, emprunt.Date_debut as Date_debut, emprunt.Date_fin_prevue as Date_fin_prevue,
    emprunt.Raison_Emprunt as Raison, emprunt.Statut as Statut, modele.Reference as Reference
    FROM emprunt
    JOIN concerner ON emprunt.ID_Emprunt = concerner.ID_Emprunt
    JOIN exemplaire ON concerner.ID_Exemplaire = exemplaire.ID_Exemplaire
    JOIN modele ON exemplaire.ID_Modele = modele.ID_Modele
    WHERE emprunt.E_Matricule = '$Matricule' "; 
    $results = mysqli_query($conn, $sql);
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivre les demandes </title>
    <link rel = "stylesheet" href = "../css/Suivi_demande.css?v=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class = "elms-sans-text">
    <main>
    <div class = "Titre">    
    <h1>Historique des demandes</h1>
    </div>
        <?php if (mysqli_num_rows($results) > 0) {
            foreach ($results as $result) { ?>
            <div class = "Demande">
            <div class = "ligne"> ID Emprunt : <?php echo htmlspecialchars($result["ID_Emprunt"]); ?> </div>
            <div class = "ligne"> Reference de l'article : <?php echo htmlspecialchars($result["Reference"]); ?> </div>
            <div class = "ligne"> Date debut : <?php echo htmlspecialchars($result["Date_debut"]); ?> </div>
            <div class = "ligne"> Date fin prevue : <?php echo htmlspecialchars($result["Date_fin_prevue"]); ?> </div>
            <div class = "ligne"> Raison : <?php echo htmlspecialchars($result["Raison"]); ?> </div>
            <div class = "ligne"> Statut : <?php echo htmlspecialchars($result["Statut"]); ?> </div>  
            </div>
        <?php   }
         } else { ?>
        <div class = "Nouveau"> <?php   echo "Vous n'avez pas encore soumis de demande !";  ?> </div>
        <?php } ?>
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
    header("Location : connexion.php");
    exit();  
}

?>