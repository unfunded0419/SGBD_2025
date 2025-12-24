<?php
session_start();

// 1. VÉRIFICATION SÉCURITÉ
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true || $_SESSION['role'] !== 'admin') {
    header("Location: Home.php"); 
    exit();
}

include 'Connexion_DB.php';
   
// 2. REQUÊTE SQL
$sql = "SELECT * FROM responsable_des_equipements WHERE Statut = 'En attente'";
$result = mysqli_query($conn, $sql);
$nombre_demandes = mysqli_num_rows($result);
?>

<!DOCTYPE html>
<html lang="fr"> 
<head>
    <meta charset="UTF-8">
    <title>Validation des Responsables</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <?php/* ne pas dézoomer si on est sur smartphone, sinon tout paraitra rikiki */?>
    <link rel="stylesheet" href="../css/demande_resp.css">
</head>
<body>
    <main class="table">
        <section class="table_header">
            <h1>Demandes de responsables en attente</h1>
        </section>
        <?php if ($nombre_demandes > 0) : ?>
            <section class="table_body">
                <table>
                
                    <thead>
                        <tr> <?php/* les noms des colonnes*/?>
                            <th>Matricule</th>
                            <th>Identité</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($ligne = mysqli_fetch_assoc($result)) : ?>
                            <tr> <?php/* les données des colonnes*/?>
                                <td><?= htmlspecialchars($ligne['RE_Matricule']) ?></td> 
                                <td>
                                    <?= htmlspecialchars($ligne['Nom']) ?> <?= htmlspecialchars($ligne['Prenom']) ?>
                                </td>
                                <td><?= htmlspecialchars($ligne['E_mail']) ?></td>     
                                <td>
                                <?php /* Pour "Valider", comme pour "Refuser", on envoie l'information à travers la variable "action",
                                on prend le matricule de la personne qui fait la demande
                                on part dans le code gestion_demande.php pour UPDATE */?>
                                    <a href="gestion_statut.php?matricule=<?= $ligne['RE_Matricule'] ?>&action=Accepté" class="btn-glass">
                                        Valider 
                                    </a>
                                
                                    <a href="gestion_statut.php?matricule=<?= $ligne['RE_Matricule'] ?>&action=Refusé" class="btn-glass">
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
        -Page<a href = "../page_interactive/Home.php">d'acceuil </a>
        -Gestion des<a href = #>demandes étudiantes </a>
        -Ajout de<a href = "../page_interactive/ajout_materiel.php">matériel</a>
    </footer>
</body>
</html>
