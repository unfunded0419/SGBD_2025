
<?php
session_start();
if (!$_SESSION["logged"] || $_SESSION["Responsable"] || empty($_SESSION["ID_Projet"])) {
    header("Location: connexion.php"); /*attention on doit écrire Location: absolument les espaces ne sont pas autorisé*/ 
} 
include 'Connexion_DB.php';
$erreur = ""; 
$matricule = $_SESSION["Matricule"]; 
$idprojet = $_SESSION["ID_Projet"]; 
$proj = "SELECT projet.Nom as Nom_proj FROM projet WHERE projet.ID_Projet = '$idprojet'"; 
$projets = mysqli_query($conn, $proj); 

if (isset($_POST["submit"])) { /* code déguelasse vérifiant si l'utilisateur à bien remplit les champs pour les dates de manière logique , ensuite il vérifie si l'objet demandé se trouve dans la db et enfin seulement l'emprunt et la table concerner sont mise à jour*/
    $_Article = filter_input(INPUT_POST, "nom_article", FILTER_SANITIZE_SPECIAL_CHARS);  
    $today = date('Y-m-d'); 
        if($_POST["date_debut"] >= $today) {
            $_date_debut = $_POST["date_debut"]; 
                if($_POST["date_retour"] >= $_date_debut) {
                    $_date_retour = $_POST["date_retour"]; 
                    $_Raison = filter_input(INPUT_POST, "raison_emprunt", FILTER_SANITIZE_SPECIAL_CHARS);
                    $sql = "SELECT modele.ID_modele as ID_modele FROM modele WHERE modele.Reference = '$_Article'"; 
                    $result = mysqli_query($conn, $sql);
                        if(mysqli_num_rows($result)>0) {
                            foreach ($result as $row) {
                            $modele = $row["ID_modele"]; 
                            $Matricule = $_SESSION["Matricule"]; 
                            $sql = "INSERT INTO emprunt(Date_debut, Date_fin_prevue, Raison_Emprunt, E_Matricule, Statut, ID_Projet, ID_Modele_demande) VALUES ('$_date_debut', '$_date_retour', '$_Raison', '$Matricule', 'en_attente','$idprojet','$modele')"; /*il faut encore ajouter le projet auquel l'emprunt est associé*/ 
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
}
?>   
<!DOCTYPE html>
    <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Formulaire de Demande </title>
            <link rel = "stylesheet" href = "../css/Demande.css?v=1.5">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        </head>
        <body class = "elms-sans-text">
            <?php if(empty($erreur)) { ?>
            <main>
                 <h1> Vous souhaitez soumettre une nouvelle demande ! </h1> <br>
            <form action = "../page_interactive/Demande.php" method = "post">
                <div class = "survey">
                    <h2> Remplisser le formulaire suivant : </h2>
                    </div>
                    <div class = "survey">
                        <label for = "nom_article"> Nom de l'article  : </label>
                        <input type = "text" id = "nom_article" name = "nom_article" required = "required">
                    </div>
                    <div class = "survey">
                        <label for = "date_debut"> Date début  : </label>
                        <input type = "date" id = "date_debut" name = "date_debut" required = "required">
                    </div>
                    <div class = "survey">
                        <label for = "date_retour"> Date retour : </label>
                        <input type = "date" id = "date_retour" name = "date_retour" required = "required">
                    </div>
                    <div class = "Projet">
                        <div class = "debut">
                        <p>Projet concerné : </p>
                        </div> 
                        <?php foreach ($projets as $projet) {?>
                            <div class = "fin">
                            <p> <?php echo $projet["Nom_proj"] ?> </p>
                            </div> 
                    <?php } ?>
                    </div>
                    <div class = "survey">
                        <label for = "raison_emprunt"> Raison derrière l'emprunt : </label>
                        <textarea id = "raison_emprunt" name = "raison_emprunt" required = "required"></textarea>
                    </div>    
                    <div class = "soumettre">
                        <input type = "submit" name = "submit" value = "Valider"> 
                    </div>
            </form>
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
        <div class = "erreur"> <p> <?php echo htmlspecialchars($erreur) ?> </p> </div>
        <?php } ?>
        </body>
    </html>
