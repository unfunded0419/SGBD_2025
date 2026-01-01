<?php
session_start();
if (!$_SESSION["logged"] || !$_SESSION["Responsable"]) {
    header("Location: connexion.php"); /*attention on doit écrire Location: absolument les espaces ne sont pas autorisé*/ 
} 

include 'Connexion_DB.php';

$today = date("Y-m-d"); // Plus lisible : 01/01/2026 au lieu de 2026-01-01
$projet_choisi = $_POST["projet_choisi"] ?? null; 
// UNE SEULE REQUÊTE bien pensée
$sql = "SELECT 
    projet.ID_Projet as ID_Projet,
    projet.Nom AS Nom_projet,
    cours.Nom AS Cours_nom,
    etudiant.E_Matricule AS EMatricule,
    etudiant.Nom AS Nom_etudiant,
    etudiant.Prenom AS Prenom_etudiant,
    emprunt.ID_Emprunt,
    emprunt.ID_Projet AS emprunt_ID_Projet,
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
    AND emprunt.Date_retour IS NULL 
    AND emprunt.Statut = 'approuve'
LEFT JOIN concerner ON emprunt.ID_Emprunt = concerner.ID_Emprunt
LEFT JOIN exemplaire ON concerner.ID_Exemplaire = exemplaire.ID_Exemplaire
LEFT JOIN modele ON exemplaire.ID_Modele = modele.ID_Modele
ORDER BY EMatricule, emprunt.Date_debut";


$stmt = mysqli_prepare($conn, $sql);
$stmt->execute(); 
$result = mysqli_stmt_get_result($stmt);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Aucun résultat trouvé pour ce projet.");
}

// On récupère toutes les lignes
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);

$projets = [];

foreach ($rows as $row) {
    if ($row["Nom_projet"] == $projet_choisi || $projet_choisi == null) {
    $idprojet = $row["ID_Projet"];

    // 1. Créer le projet s'il n'existe pas encore
    if (!isset($projets[$idprojet])) {
        $projets[$idprojet] = [
            'nom_proj'  => $row["Nom_projet"],
            'nom_cours' => $row["Cours_nom"],
            'etudiants' => []  // Tableau qui contiendra les étudiants par matricule
        ];
    }

    $matricule = $row['EMatricule'];

    // 2. Créer l'étudiant s'il n'existe pas encore dans ce projet
    if (!isset($projets[$idprojet]['etudiants'][$matricule])) {
        $projets[$idprojet]['etudiants'][$matricule] = [
            'nom'      => $row['Nom_etudiant'],
            'prenom'   => $row['Prenom_etudiant'],
            'matricule'=> $matricule,
            'emprunts' => []
        ];
    }

    // 3. Ajouter l'emprunt s'il existe (seulement si on a un ID_Emprunt)
    if ($row['ID_Emprunt'] && $idprojet == $row['emprunt_ID_Projet']) {
        $projets[$idprojet]['etudiants'][$matricule]['emprunts'][] = [
            'id_emprunt'    => $row['ID_Emprunt'],
            'date_debut'    => $row['Date_debut'],
            'date_fin'      => $row['Date_fin'],
            'id_exemplaire' => $row['ID_Exemplaire'],
            'reference'     => $row['Reference']
        ];
    }
}
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisation du projet</title>
    <link rel="stylesheet" href="../css/Visualisation_projet_responsable.css?v=1.4">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class = "elms-sans-text">
    <main>
    <div class = "titre">     
    <h1>Visualisation globale des projets actifs - <?php echo htmlspecialchars($today); ?></h1>
    </div>

        <form action = "../page_interactive/Visualisation_projet_responsable.php" method = "post">
        <h2>Choisir un projet en particulier : </h2>
        <select name = "projet_choisi"> 
        <option value = "">Tous</option>
        <?php foreach ($projets as $traqueur) { ?>
        <option value = "<?php echo htmlspecialchars($traqueur["nom_proj"]) ?>"><?php echo htmlspecialchars($traqueur["nom_proj"]) ?></option>
        <?php } ?>
        </select>
            <input type = "submit"  name = "filtre" value = "Filtrer">
        </form>

        <div class = "liste_projet">
        <?php foreach ($projets as $projet) { ?>


        <div class = "projet">
            <h2>Projet : <?php echo htmlspecialchars($projet['nom_proj']); ?></h2>
            <div class = "cours">
            <p>Cours :</strong> <?php echo htmlspecialchars($projet['nom_cours']); ?></p>
            </div>

        <h2>Liste des participants :</h2>
        
        <?php foreach ($projet['etudiants'] as $etudiant) { ?>
            <div class="etudiant-card">
                <p>Etudiant : <?php echo htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']); ?></p>
                <p>Matricule :</strong> <?php echo htmlspecialchars($etudiant['matricule']); ?></p>

                <?php if (!empty($etudiant['emprunts'])) { ?>
                    <p>Matériel emprunté en cours :</p>
                        <?php foreach ($etudiant['emprunts'] as $emprunt) { ?>
                            <div class = "equipement">  
                                Référence : <?php echo htmlspecialchars($emprunt['reference']); ?> 
                                (Exemplaire n°<?php echo htmlspecialchars($emprunt['id_exemplaire']); ?>) <br>
                                Emprunté le : <?php echo date("Y-m-d", strtotime($emprunt['date_debut'])); ?><br>
                                À rendre avant le : <?php echo date("Y-m-d", strtotime($emprunt['date_fin'])); ?>
                                </div>  
                        <?php } ?>
                <?php } else {?>
                    <p class="no-emprunt"><em>Aucun emprunt en cours</em></p>
                <?php } ?>
            </div>
        <?php } ?>
        </div>
    <?php } ?>
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