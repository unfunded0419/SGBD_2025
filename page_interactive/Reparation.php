<?php

session_start();

if (!isset($_SESSION["logged"]) || $_SESSION["Responsable"] == false) {
    header("Location: connexion.php");
    exit();
}


include 'Connexion_DB.php'; 

$message = "";


if (isset($_POST['reparer_selection']) && !empty($_POST['exemplaires_ids'])) {
    $ids = $_POST['exemplaires_ids']; // je récup l'id des exemplaires coché
    
    
    
    
    $compteur = 0;
    foreach ($ids as $id) {
        $requete_reparation = "UPDATE exemplaire SET Etat = 'utilisable' WHERE ID_Exemplaire = '$id'" ;
        mysqli_query($conn,$requete_reparation);
        $compteur++;
    }
    
    $message = "Succès : $compteur exemplaire(s) ont été remis en état 'utilisable !";
    
}


$requete_sql_affichage = "SELECT exemplaire.ID_Exemplaire, modele.Reference, exemplaire.Etat 
        FROM exemplaire 
        JOIN modele ON exemplaire.ID_Modele = modele.ID_Modele 
        WHERE exemplaire.Etat = 'endommage'";

$resultat = mysqli_query($conn, $requete_sql_affichage);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title> Responsable - Réparations</title>
    <link rel = "stylesheet" href = "../css/Reparation.css">
</head>
<body>
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

        while ($row = mysqli_fetch_assoc($resultat)) {
            echo '<tr>
                    <td><input type="checkbox" name="exemplaires_ids[]" value="' . $row['ID_Exemplaire'] . '"></td>
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
</body>
</html>