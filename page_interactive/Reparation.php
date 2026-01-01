<?php

session_start();

if (!isset($_SESSION["logged"]) || $_SESSION["Responsable"] == false) {
    header("Location: connexion.php");
    exit();
}


include 'Connexion_DB.php'; 

$message = "";
$matricule = $_SESSION["Matricule"]; 

if (isset($_POST['reparer_selection'])) {
     // je récup l'id des exemplaires coché
    $update_reparation = $conn->prepare("INSERT INTO reparer(RE_Matricule, ID_Exemplaire, Date_Reparation) VALUES (?, ?, ?)"); 
    $update_emprunt_repair = $conn->prepare("UPDATE exemplaire SET Etat = 'utilisable', Disponibilite = 'disponible' WHERE ID_Exemplaire = ?"); 
    $update_emprunt_withdraw = $conn->prepare("UPDATE exemplaire SET Disponibilite = 'indisponible', Date_Retrait = ?, RE_Matricule_Retrait = ?  WHERE ID_Exemplaire = ?"); 
    
    $date_reparation = date('Y-m-d');  /*ajd est le jour auquel la réparation a été validée*/

    $compteur = 0;
    foreach($_POST['Reparation_ou_retrait'] as $id_exemplaire => $choix) { /* Modification importantes afin de permettre 
        un traitement alternatif entre réparer un équipement endommagé ou de le retirer des réserves*/
        
        $id_exemplaire = (int)$id_exemplaire; 
        if ($choix =="Reparer" ) {
            $update_reparation->bind_param("iis", $matricule, $id_exemplaire, $date_reparation); 
            $update_reparation->execute(); 
            $update_emprunt_repair->bind_param("i", $id_exemplaire); 
            $update_emprunt_repair->execute(); 
            $compteur++;
        } else if ($choix == "Retirer") {
            $update_emprunt_withdraw->bind_param("sii", $date_reparation, $matricule, $id_exemplaire); 
            $update_emprunt_withdraw->execute(); 
            $compteur++;
        }
    }
    
    
    $message = "Succès : $compteur exemplaire(s) ont été traité(s)";
    
}


$requete_sql_affichage = "SELECT exemplaire.ID_Exemplaire, modele.Reference, exemplaire.Etat 
        FROM exemplaire 
        JOIN modele ON exemplaire.ID_Modele = modele.ID_Modele 
        WHERE exemplaire.Etat = 'endommage' AND exemplaire.RE_Matricule_Retrait IS NULL";/* permet de n'afficher les pièces d'équipements endommagé qu'à la condition qu'elle n'ait pas encore été retirée*/

$resultat = mysqli_query($conn, $requete_sql_affichage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title> Responsable - Réparations</title>
    <link rel = "stylesheet" href = "../css/Reparation.css?v=1.3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class = "elms-sans-text">
    <main>
<div class="panneau-resp">
    <h1>Espace Réparation</h1>

    <?php 

    if ($message !== "") {
        echo '<div class="error">' . $message . '</div>';
    }

    if (mysqli_num_rows($resultat) > 0) {
        echo '<form method="POST">
                <table>
                    <thead>
                        <tr>
                            <th>Selection</th>
                            <th>ID</th>
                            <th>Modèle</th>
                            <th>État</th>
                        </tr>
                    </thead>
                    <tbody>';

        while ($row = mysqli_fetch_assoc($resultat)) { /* modification de la checkbox en un select de manière à pouvoir traiter le choix entre réparation et Retrait d'un équipement endommagé */
            echo '<tr>
                    <td><select name="Reparation_ou_retrait[' . $row['ID_Exemplaire'] . ']"> 
                    <option value="">Choisir</option>
                    <option value="Reparer">Réparer</option>
                    <option value="Retirer">Retirer</option>
                    </select><td>
                    
                    <td>#' . $row['ID_Exemplaire'] . '</td>
                    <td>' . htmlspecialchars($row['Reference']) . '</td>
                    <td style="color: #ff4d4d; font-weight: bold;"> Endommagé </td>
                  </tr>';
        }

        echo '    </tbody>
                </table>
                <button type="submit" name="reparer_selection" class="bouton-action">Valider les réparations</button>
              </form>';
    } else {
        echo '<p style="text-align: center; padding: 40px;">Tout le matériel est opérationnel !!</p>';
    }
    ?>

</div>
</main>
<footer>
        <p><a href = "../page_interactive/Home.php"> Page d'acceuil </a> </p>
                    <p><a href = "../page_interactive/Stocks.php"> - Consulter les stocks </a></p>
                    <p><a href = "../page_interactive/ajout_materiel.php"> - Ajouter materiel </a></p>
                    <p><a href = "../page_interactive/Reparation.php"> - Réparer materiel </a></p>
                    <p><a href = "../page_interactive/Validation_demandes.php"> - Gérer demandes </a></p>
                    <p><a href = "../page_interactive/outils_emprunts.php"> - Gérer retours </a></p>
                    <p><a href = "../page_interactive/Visualisation_projet_responsable.php"> - Visualiser projets </a></p>
        <?php if ($_SESSION["admin"] == true) { ?>
                    <p><a href = "../page_interactive/demande_resp.php"> - Gérer responsable </a></p>
        <?php } ?>
    </footer>
</body>
</html>