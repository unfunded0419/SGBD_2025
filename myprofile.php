<?php
session_start();
require '../db_connexion.php';   // fichier qui contient la connexion PDO ou MySQLi

// 1. Vérifier que l’utilisateur est connecté
if (!isset($_SESSION["logged"]) || $_SESSION["logged"] !== true) {
    header("Location: ../connexion.php");
    exit();
}

// 2. Email / utilisateur stocké dans la session
$login = $_SESSION["utilisateur"];

// 3. Récupération des infos de l'étudiant
$sql = "SELECT * FROM Etudiant WHERE E_mail = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$login]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    echo "Erreur : étudiant introuvable.";
    exit();
}

// 4. Récupération des emprunts en cours
$sql = "SELECT * FROM Emprunt WHERE E_Matricule = ? AND Date_retour IS NULL";
$stmt = $pdo->prepare($sql);
$stmt->execute([$etudiant["E_Matricule"]]);
$emprunts_en_cours = $stmt->fetchAll();

// 5. Récupération de l’historique
$sql = "SELECT * FROM Emprunt WHERE E_Matricule = ? AND Date_retour IS NOT NULL";
$stmt = $pdo->prepare($sql);
$stmt->execute([$etudiant["E_Matricule"]]);
$historique = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mon Profil</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>

<h1>Mon Profil</h1>

<section>
    <h2>Mes Informations</h2>
    <p><strong>Nom :</strong> <?= htmlspecialchars($etudiant["Nom"]) ?></p>
    <p><strong>Prénom :</strong> <?= htmlspecialchars($etudiant["Prenom"]) ?></p>
    <p><strong>Email :</strong> <?= htmlspecialchars($etudiant["E_mail"]) ?></p>
</section>

<section>
    <h2>Mes emprunts en cours</h2>
    <?php if (empty($emprunts_en_cours)): ?>
        <p>Aucun emprunt en cours.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID Emprunt</th>
                <th>ID Exemplaire</th>
                <th>Date début</th>
                <th>Date prévue de retour</th>
            </tr>
            <?php foreach ($emprunts_en_cours as $e): ?>
            <tr>
                <td><?= $e["ID_Emprunt"] ?></td>
                <td><?= $e["ID_Exemplaire"] ?></td>
                <td><?= $e["Date_debut"] ?></td>
                <td><?= $e["Date_fin_prevue"] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>

<section>
    <h2>Historique des emprunts</h2>
    <?php if (empty($historique)): ?>
        <p>Aucun emprunt passé.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID Emprunt</th>
                <th>ID Exemplaire</th>
                <th>Date début</th>
                <th>Retour réel</th>
            </tr>
            <?php foreach ($historique as $h): ?>
            <tr>
                <td><?= $h["ID_Emprunt"] ?></td>
                <td><?= $h["ID_Exemplaire"] ?></td>
                <td><?= $h["Date_debut"] ?></td>
                <td><?= $h["Date_retour"] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</section>

</body>
</html>
