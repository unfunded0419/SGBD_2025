<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $base = new PDO('mysql:host=localhost;port=8888;dbname=db_IOT','root','root');

    $sql = "SELECT
                emprunt.ID_Emprunt,
                modele.Reference AS modele,
                etudiant.Nom AS nom_etu,
                etudiant.Prenom AS prenom_etu,
                responsable_des_equipements.Nom AS nom_re,
                responsable_des_equipements.Prenom AS prenom_re,
                emprunt.Statut
            FROM emprunt
            JOIN concerner ON concerner.ID_Emprunt = emprunt.ID_Emprunt
            JOIN exemplaire ON exemplaire.ID_Exemplaire = concerner.ID_Exemplaire
            JOIN modele ON modele.ID_Modele = exemplaire.ID_Modele
            LEFT JOIN etudiant ON etudiant.E_Matricule = emprunt.E_Matricule
            LEFT JOIN responsable_des_equipements ON responsable_des_equipements.RE_Matricule = emprunt.RE_Matricule";

    $resultat = $base->query($sql);

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>TEST WHILE PHP</title>
</head>
<body>

<h1>Liste d'emprunts (TEST)</h1>

<?php while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)): ?>

    <?php
    // Déterminer le nom du client
    if (!empty($ligne['nom_etu'])) {
        $client = $ligne['prenom_etu'] . ' ' . $ligne['nom_etu'] . " (Étudiant)";
    } elseif (!empty($ligne['nom_re'])) {
        $client = $ligne['prenom_re'] . ' ' . $ligne['nom_re'] . " (Responsable)";
    } else {
        $client = "Inconnu";
    }
    ?>

    <div style="padding: 15px; margin: 10px; background: #eee; border-radius: 10px">
        <p><strong>Produit :</strong> <?= htmlspecialchars($ligne['modele']) ?></p>
        <p><strong>Client :</strong> <?= htmlspecialchars($client) ?></p>
        <p><strong>Statut :</strong> <?= htmlspecialchars($ligne['Statut']) ?></p>
    </div>

<?php endwhile; ?>

</body>
</html>
