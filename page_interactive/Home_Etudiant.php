<?php
session_start();

if (!isset($_SESSION["logged"]) || $_SESSION["Responsable"] == true) {
    header("Location: connexion.php");
    exit();
}

include 'Connexion_DB.php';
$matricule = $_SESSION['Matricule'];
$requetesql_listeprojet = "SELECT * FROM projet
                           JOIN participer ON projet.ID_Projet = participer.ID_Projet
                           WHERE participer.E_Matricule = '$matricule' " ;
$resultat_projet = mysqli_query($conn,$requetesql_listeprojet);// je stock dans une variable tous les projets aux quels l'étudiant participe pour les cout dans le .html
$nbre_lignes_projet = mysqli_num_rows($resultat_projet);

if (isset($_POST['creer'])) {

    $creer_cours = $conn->prepare("INSERT INTO cours(Nom) VALUES (?)"); 
    $chercher_cours = $conn->prepare("SELECT cours.ID_Cours as ID_Cours From cours Where cours.Nom = ? LIMIT 1"); 
    $lier_insertion = $conn->prepare("INSERT INTO lier(ID_Cours,ID_Projet) VALUES (?,?)"); 

    $nomprojet = filter_input(INPUT_POST, "nom_projet", FILTER_SANITIZE_SPECIAL_CHARS);
    $nomcours = filter_input(INPUT_POST, "cours", FILTER_SANITIZE_SPECIAL_CHARS);

    $requetesql_check = "SELECT * FROM projet WHERE Nom = '$nomprojet'";
    $resultat = mysqli_query($conn,$requetesql_check);
    $chercher_cours->bind_param("s", $nomcours); 
    $chercher_cours->execute(); 
    $cours = $chercher_cours->get_result();
    $row = $cours->fetch_assoc(); 
    $id_cours = $row["ID_Cours"]; 

    if(mysqli_num_rows($resultat) == 0 && $cours->num_rows == 0 ){  /* Nouveau code permettant de créer le cours
         auquel le projet est rattaché s'il n'existe pas encore dans la db plus création du projet  */
        $creer_cours->bind_param("s", $nomcours); 
        $creer_cours->execute(); 
        $id_cours = mysqli_insert_id($conn); /* je récupère l'id du cours nouvellement créé */
        $requetesql = "INSERT INTO projet(Nom) VALUES ('$nomprojet')";
        mysqli_query($conn,$requetesql);
        $id_projet = mysqli_insert_id($conn); // je récupére la valeur de  l'autoincrement que la db à fait 
        $requetesql_participer = "INSERT INTO participer(ID_Projet,E_Matricule) VALUES ('$id_projet','$matricule')";
        mysqli_query($conn,$requetesql_participer);
        $lier_insertion->bind_param("ii", $id_cours, $id_projet); 
        $lier_insertion->execute(); 
        header("Location: Home_Etudiant.php");
    }else if (mysqli_num_rows($resultat) == 0){ /* Code initial permettant d'ajouter un projet pour un cours existant déjà */
        $requetesql = "INSERT INTO projet(Nom) VALUES ('$nomprojet')";
        mysqli_query($conn,$requetesql);
        $id_projet = mysqli_insert_id($conn); // je récupére la valeur de  l'autoincrement que la db à fait 
        $requetesql_participer = "INSERT INTO participer(ID_Projet,E_Matricule) VALUES ('$id_projet','$matricule')";
        mysqli_query($conn,$requetesql_participer);
        $lier_insertion->bind_param("ii", $id_cours, $id_projet); 
        $lier_insertion->execute(); 
        header("Location: Home_Etudiant.php");
        
    } else {
        $erreur = "Nom de projet déjà utilisé";
    }
    
    }

if (isset($_POST['quitter'])){
    $projet_a_quitter = (int)$_POST['id_projet'];

    $requetesql_quitter = "DELETE from participer WHERE E_Matricule = '$matricule' AND ID_Projet = '$projet_a_quitter'";
    mysqli_query($conn,$requetesql_quitter);
    header("Location: Home_Etudiant.php");
}
?>
<html>
    <head>
        <title> Page d'accueil Etudiant </title>
        <meta charset = "utf-8">
        <link rel = "stylesheet" href = "../css/Home_Etudiant.css?v=1.3">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    </head>
    <body class="elms-sans-text">
    <main>
        <h1>Page de l'étudiant #<?php echo $_SESSION["Matricule"]; ?></h1>

        <?php if (!empty($erreur)): ?>
            <p class="erreur"><?php echo htmlspecialchars($erreur); ?></p>
        <?php else: ?>
            <h3><a href="../page_interactive/Deconnexion.php">Déconnexion</a></h3>

            <form action="../page_interactive/Home_Etudiant.php" method="post">
                <hr>
                <h3>Créer un projet</h3><br>
                <label for = "nom_projet">Nom du projet :</label>
                <input type="text" id = "nom_projet" name="nom_projet" title="Nom du projet" required>
                <label for = "cours">Cours associé au projet :</label>
                <input type ="text" id = "cours" name="cours" required>
                <button type="submit" name="creer">Créer</button>
                <hr>
            </form>

            <h3>Liste de vos projets :</h3><br>
            <div class="liste">
                <?php
                if ($nbre_lignes_projet == 0) {
                    echo "<h4>Aucun projet en cours</h4>";
                } else {
                    while ($ligne = mysqli_fetch_assoc($resultat_projet)) {
                        echo "<h4>";
                        echo "Nom : " . htmlspecialchars($ligne['Nom']) . "<br>";
                        echo "id projet : " . $ligne['ID_Projet'] . "<br>";
                        echo '<a id="acceder" href="Home_Projet.php?id_projet=' . $ligne['ID_Projet'] . '">Accéder</a><br>';
                        echo '<form action="../page_interactive/Home_Etudiant.php" method="post" style="display:inline;">';
                        echo '<input type="hidden" name="id_projet" value="' . $ligne['ID_Projet'] . '">';
                        echo '<button type="submit" name="quitter">Quitter le projet</button>';
                        echo '</form>';
                        echo "</h4>";
                    }
                }
                ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>



