<?php
session_start();

// 1. SÉCURITÉ : Vérifier si l'utilisateur est connecté
if ($_SESSION["logged"] == false || $_SESSION["Responsable"] == false ) {
    header("Location: connexion.php");
    exit();
}

include 'Connexion_DB.php';

$message = "";
$erreur = "";

// 2. TRAITEMENT DU FORMULAIRE
if (isset($_POST["submit"])) {

    // RECUPERATION DU MATRICULE DU RESPONSABLE
    $matricule_responsable = $_SESSION["Matricule"]; 
    echo $_SESSION["Matricule"]; 
    if (empty($matricule_responsable)) {
        $erreur = "Erreur : Impossible de retrouver votre identifiant de session.";
    } else {
        
        // Démarrage de la transaction (tout ou rien)
        mysqli_begin_transaction($conn);

        try {
            // --- A. GESTION DE LA CATEGORIE ---
            $id_cat_final = null;

            // Si le champ "Nouveau nom" est rempli, on crée une nouvelle catégorie
            if (!empty($_POST['new_cat_nom'])) {
                $nom = mysqli_real_escape_string($conn, $_POST['new_cat_nom']);
                
                // Insertion (valeur critique par défaut à 5)
                $sql = "INSERT INTO categorie (Nom, Valeur_critique) VALUES ('$nom', 5)";
                if (!mysqli_query($conn, $sql)) throw new Exception("Erreur lors de la création de la catégorie.");
                
                $id_cat_final = mysqli_insert_id($conn); // On récupère l'ID créé
            } else {
                // Sinon, on vérifie si une catégorie existante a été choisie
                if (empty($_POST['cat_select'])) throw new Exception("Veuillez choisir une catégorie ou en créer une nouvelle.");
                $id_cat_final = intval($_POST['cat_select']);
            }

            // --- B. GESTION DU MODELE ---
            $id_mod_final = null;

            // Si le champ "Nouvelle référence" est rempli, on crée un nouveau modèle
            if (!empty($_POST['new_mod_ref'])) {
                $ref = mysqli_real_escape_string($conn, $_POST['new_mod_ref']);
                $desc = mysqli_real_escape_string($conn, $_POST['new_mod_desc']);
                
                // On lie ce modèle à la catégorie déterminée juste avant ($id_cat_final)
                $sql = "INSERT INTO modele (Reference, Description, ID_Categorie) VALUES ('$ref', '$desc', $id_cat_final)";
                if (!mysqli_query($conn, $sql)) throw new Exception("Erreur lors de la création du modèle.");
                
                $id_mod_final = mysqli_insert_id($conn);
            } else {
                // Sinon, on prend le modèle de la liste
                if (empty($_POST['mod_select'])) throw new Exception("Veuillez choisir un modèle ou en créer un nouveau.");
                $id_mod_final = intval($_POST['mod_select']);
            }

            // --- C. AJOUT DES EXEMPLAIRES ---
            $quantite = intval($_POST['Quantité']);
            $date_ajout = date('Y-m-d');

            // On boucle pour créer le nombre d'exemplaires demandés
            for ($i = 0; $i < $quantite; $i++) {
                $sql = "INSERT INTO exemplaire (Etat, Disponibilite, ID_Modele, Date_ajout, RE_Matricule_Ajout) 
                        VALUES ('utilisable', 'disponible', $id_mod_final, '$date_ajout', '$matricule_responsable')";
                if (!mysqli_query($conn, $sql)) throw new Exception("Erreur lors de l'ajout de l'exemplaire n°".($i+1));
            }

            // Si on arrive ici sans erreur, on valide tout !
            mysqli_commit($conn);
            $message = "Succès ! $quantite exemplaire(s) ajouté(s) à la base de données.";

        } catch (Exception $e) {
            // En cas d'erreur, on annule tout ce qui a été fait
            mysqli_rollback($conn);
            $erreur = $e->getMessage();
        }
    }
}

// 3. RECUPERATION DES DONNEES POUR LES LISTES DEROULANTES
$liste_categories = mysqli_query($conn, "SELECT * FROM categorie");
$liste_modeles = mysqli_query($conn, "SELECT * FROM modele");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajout Matériel</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../css/ajout_materiel.css">
</head>
<body>
    <div class="boite-register">
        <h1>Ajouter du Matériel</h1>

        <?php if ($message): ?>
            <p class="message-ok"><?= $message // si ça fonctionne bien, on le dit?></p>
        <?php endif; ?>
        
        <?php if ($erreur): ?>
            <p class="message-ko"><?= $erreur // si ça fonctionne pas, on le dit?></p>
        <?php endif; ?>

        <form action="" method="post">
            
            <div class="option-bloc">
                <div class="titre-section">1. Choix de la Catégorie</div>
                
                <label>Option A : Choisir dans la liste existante</label>
                <select name="cat_select">      <?php //select pour avoir la boite ?>
                    <?php while($row = mysqli_fetch_assoc($liste_categories)):  // $row récupère les données par élément ?>
                        <option value="<?= $row['ID_Categorie'] ?>"><?= $row['Nom'] ?></option> <?php // option pour indiquer un choix ?>
                    <?php endwhile; ?>
                </select>

                <div class="separation">- OU -</div>

                <label>Option B : Créer une nouvelle catégorie</label>
                <input type="text" name="new_cat_nom" placeholder="Nom de la nouvelle catégorie...">
            </div>

            <div class="option-bloc">
                <div class="titre-section">2. Choix du Modèle</div>

                <label>Option A : Choisir dans la liste existante</label>
                <select name="mod_select">
                    <?php while($row = mysqli_fetch_assoc($liste_modeles)): ?>
                        <option value="<?= $row['ID_Modele'] ?>">
                            <?= $row['Reference'] ?> (<?= substr($row['Description'], 0, 20) ?>...)
                        </option>
                    <?php endwhile; ?>
                </select>

                <div class="separation">- OU -</div>

                <label>Option B : Créer un nouveau modèle</label>
                <input type="text" name="new_mod_ref" placeholder="Référence (ex: CABLE-USB-C)">
                <input type="text" name="new_mod_desc" placeholder="Courte description...">
            </div>

            <div class="option-bloc">
                <div class="titre-section">3. Quantité à ajouter</div>
                <input type="number" name="Quantité" value="1" min="1" required>
            </div>

            <button type="submit" name="submit" class="submit">Enregistrer</button>
        </form>
    </div>
</body>
</html>