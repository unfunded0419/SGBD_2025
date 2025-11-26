
<?php
session_start();
if ($_SESSION["logged"]) {

?>    
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.2">
            <title>Formulaire de Demande </title>
            <link rel = "stylesheet" href = "../css/Demande.css?v=1">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        </head>
        <body class = "elms-sans-text">
            <main>
                <?php include '../page_html/Demande.html';  ?>
            </main>
<?php
if (isset($_POST["submit"])) { /* code déguelasse vérifiant si l'utilisateur à bien remplit les champs pour les dates de manière logique , ensuite il vérifie si l'objet demandé se trouve dans la db et enfin seulement l'emprunt et la table concerner sont mise à jour*/
    $_Article = filter_input(INPUT_POST, "nom_article", FILTER_SANITIZE_SPECIAL_CHARS);  
    $today = date('Y-m-d'); 
        if($_POST["date_debut"] >= $today) {
            $_date_debut = $_POST["date_debut"]; 
                if($_POST["date_retour"] >= $_date_debut) {
                    $_date_retour = $_POST["date_retour"]; 
                    $_Raison = filter_input(INPUT_POST, "raison_emprunt", FILTER_SANITIZE_SPECIAL_CHARS);
                    include 'Connexion_DB.php';
                    $sql = "SELECT exemplaire.ID_modele as ID_modele FROM exemplaire join modele on exemplaire.ID_Modele = modele.ID_modele where modele.Reference = '$_Article'"; 
                    $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result)>0) {
                            $Matricule = $_SESSION["Matricule"]; 
                            $sql = "INSERT INTO emprunt(Date_debut, Date_fin_prevue, Raison_Emprunt, E_Matricule, Statut) VALUES ('$_date_debut', '$_date_retour', '$_Raison', '$Matricule', 'en_attente')"; /*il faut encore ajouter le projet auquel l'emprunt est associé*/ 
                                if(mysqli_query($conn, $sql)) {
                                    $temp = mysqli_fetch_row($result)[0]; /*permet de récupérer la première ligne du tableau retourner par mysqli_query*/ 
                                    $id_emprunt = mysqli_insert_id($conn);
                                    $sql = "INSERT INTO concerner(ID_Emprunt, ID_Exemplaire) VALUES ('$id_emprunt', '$temp')"; /*dans concerner on reprend l'ID du premier exemplaire correspondant à l'objet rechercher à titre informatif l'assignation définitive de l'exemplaire se fera du côté de l'administrateur*/ 
                                    mysqli_query($conn, $sql); 
                                }
                        } else {
                            $erreur = "L'article recherché n'existe pas !"; 
                        }
                    mysqli_close($conn);
                } else {
                    $erreur = "On ne peut pas finir un emprunt avant de l'avoir commencer !"; 
                }
        } else {
        $erreur = "L'emprunt sera autorisé au plus tôt aujourd'hui !";   
        }
}?>
            <?php if(!empty($erreur)) { ?>
                <div class = "erreur"> <p> <?php echo htmlspecialchars($erreur) ?> </p> </div>
            <?php } ?>
            <footer>
                    <p> Page <a href = "../page_interactive/Home.php"> d'acceuil </a> </p>
                    <p> - Faire <a href = "../page_interactive/Demande.php"> une demande </a></p>
                    <p> - Consulter <a href = "../page_interactive/Stocks.php"> les stocks </a></p>
            </footer>
        </body>
    </html>
<?php
} else {
    header("Location: connexion.php"); /*attention on doit écrire Location: absolument les espaces ne sont pas autorisé*/ 
}
?>