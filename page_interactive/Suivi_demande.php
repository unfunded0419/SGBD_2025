<?php
session_start(); 
if ($_SESSION["logged"]) {
    include "Connexion_DB.php"; 
    $id_projet = $_SESSION["id_projet"];  
    // l'affichage de toutes les demandes en fonction du projet ne nécessite plus les join (affichage vide sinon)
    $sql = "SELECT emprunt.ID_Emprunt as ID_Emprunt, emprunt.Date_debut as Date_debut, emprunt.Date_fin_prevue as Date_fin_prevue,
    emprunt.Raison_Emprunt as Raison, emprunt.Statut as Statut, emprunt.ID_Modele_demande as Reference
    FROM emprunt WHERE emprunt.ID_Projet = '$id_projet' "; // au lieu de mettre la condition sur E_matricule je la mets sur l'id du projet sur lequel on a cliqué précédemment (sinon on avait un probleme avec les étudiants dans plusieurs projets)
    $results = mysqli_query($conn, $sql);
    
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivre les demandes </title>
    <link rel = "stylesheet" href = "../page_html/Suivi_demande.css?v=1">
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
            while ($ligne_results = mysqli_fetch_assoc($results) ) { // le for each ne m'affichait qu'une seule demande, en le remplaçant par le while et cette condition j'ai plus de problèmes 
             // code degueu pour trouver le nom du modèle associé à l'id_modele_demande (j'avoue j'aurais pu faire un effort mais il est 1:17 du matin)
            
            $ref = $ligne_results['Reference'];

            $sql = "SELECT * from modele where modele.ID_Modele = '$ref'";

            $result_modele = mysqli_query($conn, $sql);
            $ligne_modele = mysqli_fetch_assoc($result_modele);
            $modele = $ligne_modele['Reference']; ?>


            <div class = "Demande">
            <div class = "ligne"> ID Emprunt : <?php echo htmlspecialchars($ligne_results["ID_Emprunt"]); ?> </div>
            <div class = "ligne"> Reference de l'article : <?php echo htmlspecialchars($modele); ?> </div>
            <div class = "ligne"> Date debut : <?php echo htmlspecialchars($ligne_results["Date_debut"]); ?> </div>
            <div class = "ligne"> Date fin prevue : <?php echo htmlspecialchars($ligne_results["Date_fin_prevue"]); ?> </div>
            <div class = "ligne"> Raison : <?php echo htmlspecialchars($ligne_results["Raison"]); ?> </div>
            <div class = "ligne"> Statut : <?php echo htmlspecialchars($ligne_results["Statut"]); ?> </div> 
            <br> 
            </div>
        <?php   }
         } else { ?>
        <div class = "Nouveau"> <?php   echo "Vous n'avez pas encore soumis de demande !";  ?> </div>
        <?php } ?>
    </main>
    <footer>
                <p> Page <a href = "../page_interactive/Home_Projet.php"> d'acceuil</a> </p>
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