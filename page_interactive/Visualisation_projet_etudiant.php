<?php

session_start();
if (!$_SESSION["logged"] || $_SESSION["Responsable"] || empty($_SESSION["ID_Projet"])) {
    header("Location: connexion.php"); /*attention on doit écrire Location: absolument les espaces ne sont pas autorisé*/ 
} 

include 'Connexion_DB.php';

$idprojet = $_SESSION["ID_Projet"];
$today = date("Y-m-d"); // Plus lisible : 01/01/2026 au lieu de 2026-01-01

// UNE SEULE REQUÊTE bien pensée
$sql = "SELECT 
    projet.Nom AS Nom_projet,
    cours.Nom AS Cours_nom,
    etudiant.E_Matricule AS EMatricule,
    etudiant.Nom AS Nom_etudiant,
    etudiant.Prenom AS Prenom_etudiant,
    emprunt.ID_Emprunt,
    emprunt.Date_debut,
    emprunt.Date_fin_prevue AS Date_fin,
    exemplaire.ID_Exemplaire,
    modele.Reference
FROM projet
JOIN lier ON projet.ID_Projet = lier.ID_Projet
JOIN cours ON lier.ID_Cours = cours.ID_Cours
JOIN participer ON projet.ID_Projet = participer.ID_Projet
JOIN etudiant ON participer.E_Matricule = etudiant.E_Matricule
LEFT JOIN emprunt ON etudiant.E_Matricule = emprunt.E_Matricule 
    AND emprunt.ID_Projet = ? 
    AND emprunt.Date_retour IS NULL 
    AND emprunt.Statut = 'approuve'
LEFT JOIN concerner ON emprunt.ID_Emprunt = concerner.ID_Emprunt
LEFT JOIN exemplaire ON concerner.ID_Exemplaire = exemplaire.ID_Exemplaire
LEFT JOIN modele ON exemplaire.ID_Modele = modele.ID_Modele
WHERE projet.ID_Projet = ?
ORDER BY EMatricule, emprunt.Date_debut";


$stmt = mysqli_prepare($conn, $sql);
$stmt->bind_param("ii", $idprojet, $idprojet);
$stmt->execute(); 
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Aucun résultat trouvé pour ce projet.");
}

// On récupère toutes les lignes
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

// On groupe par étudiant pour faciliter l'affichage
$etudiants = [];
foreach ($rows as $row) {
    $matricule = $row['EMatricule'];
    if (!isset($etudiants[$matricule])) {
        $etudiants[$matricule] = [
            'nom' => $row['Nom_etudiant'],
            'prenom' => $row['Prenom_etudiant'],
            'matricule' => $matricule,
            'emprunts' => []
        ];
    }
    // Ajouter l'emprunt s'il existe
    if ($row['ID_Emprunt']) {
        $etudiants[$matricule]['emprunts'][] = [
            'id_emprunt' => $row['ID_Emprunt'],
            'date_debut' => $row['Date_debut'],
            'date_fin' => $row['Date_fin'],
            'id_exemplaire' => $row['ID_Exemplaire'],
            'reference' => $row['Reference']
        ];
    }
}

// Récupérer nom projet et cours (ils sont identiques pour tous)
$nom_projet = $rows[0]['Nom_projet'] ?? 'Projet inconnu';
$cours_nom = $rows[0]['Cours_nom'] ?? 'Cours inconnu';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisation du projet</title>
    <link rel="stylesheet" href="../css/Visualisation_projet_etudiant.css?v=1.6">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class = "elms-sans-text">
    <main>
    <div class = "titre">  
    <h1>Visualisation globale du projet - <?php echo htmlspecialchars($today); ?></h1>
    </div>  
        <div class = "projet">
            <h2>Projet : <?php echo htmlspecialchars($nom_projet); ?></h2>
            <div class = "cours">
            <p>Cours : <?php echo htmlspecialchars($cours_nom); ?></p>
            </div>
        <h2>Liste des participants : </h2>

        <?php foreach ($etudiants as $etudiant){ ?>
            <div class="etudiant-card">
                <p>Etudiant : <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?></p>
                <p>Matricule : <?php echo htmlspecialchars($etudiant['matricule']); ?></p>

                <?php if (!empty($etudiant['emprunts'])){ ?>
                    <p>Matériel emprunté en cours :</p>
                        <?php foreach ($etudiant['emprunts'] as $emprunt){ ?>
                              <div class = "equipement">
                                Référence : <?php echo htmlspecialchars($emprunt['reference']); ?> 
                                (Exemplaire n°<?php echo htmlspecialchars($emprunt['id_exemplaire']); ?>)<br>
                                Emprunté le : <?php echo date("Y-m-d", strtotime($emprunt['date_debut'])); ?><br>
                                À rendre avant le : <?php echo date("Y-m-d", strtotime($emprunt['date_fin'])); ?>
                              </div>
                        <?php } ?>
                <?php } else { ?>
                    <p class="equipement">Aucun emprunt en cours</p>
                <?php } ?>
            </div>
        <?php } ?>
        </div>
    </main>
    <footer>
                    <p><a href = "../page_interactive/Home.php"> Page d'acceuil </a> </p>
                    <p><a href = "../page_interactive/Demande.php"> - Faire une demande </a></p>
                    <p><a href = "../page_interactive/Stocks.php"> - Consulter les stocks </a></p>
                    <p><a href = "../page_interactive/Suivi_demande.php"> - Suivre les demandes </a></p>
                    <p><a href = "../page_interactive/Ajout_Etud_Projet.php"> - Ajouter des étudiants au projet</a></p>
                    <p><a href = "../page_interactive/Home_Etudiant.php"> - Changer de projet  </a> </p>
                    <p><a href = "../page_interactive/Visualisation_projet_etudiant.php"> - Etat du projet </a> </p>     
    </footer>
</body>
</html>