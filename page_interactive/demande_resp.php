<?php
session_start();

// 1. VÉRIFICATION SÉCURITÉ
if ($_SESSION['logged'] == false || $_SESSION["Responsable"] == false || $_SESSION['admin'] == false) {
    header("Location: connexion.php"); 
    exit();
}

include 'Connexion_DB.php';
   
// 2. REQUÊTE SQL
$sql = "SELECT * FROM responsable_des_equipements WHERE Statut = 'en_attente'";
$result = mysqli_query($conn, $sql);
$nombre_demandes = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="fr"> 
<head>
    <meta charset="UTF-8">
    <title>Validation des Responsables</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <?php /* ne pas dézoomer si on est sur smartphone, sinon tout paraitra rikiki */?>
    <link rel="stylesheet" href="../css/demande_resp.css?v=1.3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class = "elms-sans-text">
    <main class="table">
        <section class="table_header">
            <h1>Demandes de responsables en attente</h1>
        </section>
        <?php if ($nombre_demandes > 0) : ?>
            <section class="table_body">
                <table>
                
                    <thead>
                        <tr> <?php /* les noms des colonnes*/?>
                            <th>Matricule</th>
                            <th>Identité</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($ligne = mysqli_fetch_assoc($result)) : ?>
                            <tr> <?php /* les données des colonnes*/?>
                                <td><?= htmlspecialchars($ligne['RE_Matricule']) ?></td> 
                                <td>
                                    <?= htmlspecialchars($ligne['Nom']) ?> <?= htmlspecialchars($ligne['Prenom']) ?>
                                </td>
                                <td><?= htmlspecialchars($ligne['E_mail']) ?></td>     
                                <td>
                                    <?php /* Pour "Valider", comme pour "Refuser", on envoie l'information à travers la variable "action",
                                    on prend le matricule de la personne qui fait la demande
                                    on part dans le code gestion_demande.php pour UPDATE */?>
                                    <a href="gestion_statut.php?matricule=<?= $ligne['RE_Matricule'] ?>&action=Accepte" class="btn-glass">
                                        Valider
                                    </a>
                                    
                                    &nbsp; <a href="gestion_statut.php?matricule=<?= $ligne['RE_Matricule'] ?>&action=Refuse" class="btn-glass">
                                        Refuser
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </section>
        <?php else: /* s'il n'y a pas de demande en attente, on affiche ceci */?>
            <p>Aucune demande à traiter pour le moment.</p>
        <?php endif; ?>
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
