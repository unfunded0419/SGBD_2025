<?php
try{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $base = new PDO('mysql:host=localhost;port=8888;dbname=db_IOT','root','root');

    if(!empty($_POST['rendu'])){
        $stmt = $base->prepare("UPDATE exemplaire SET Disponibilite = :dispo, Etat= :etat WHERE ID_Exemplaire = :id");
        foreach($_POST['rendu'] as $idex=>$v){
            $isDommage = isset($_POST['dommage'][$idex]);
            if($isDommage){
                $dispo = 'indisponible';
                $dom = 'endommage';
            }else{
                $dispo = 'disponible';
                $dom = 'utilisable';
            }
            $stmt->execute([
                ':dispo' => $dispo,
                ':etat'  => $dom,
                ':id'    => $idex
            ]);
        }
    }

    $action = $_POST['action'] ?? null;

    if ($action === 'search') {
        $mat = trim($_POST['recherche'] ?? '');
        header('Location: outils_emprunts.php?filtre=' . urlencode($mat));
        exit;
    }

    $filtre = trim($_GET['filtre'] ?? '');

    if ($filtre !== '') {
        $ajout = " AND (etudiant.E_Matricule = $filtre OR responsable_des_equipements.RE_Matricule = $filtre)";
    }else{
        $ajout='';
    }

    $sql = "SELECT emprunt.Date_debut, emprunt.Date_fin_prevue,
            categorie.Nom AS nom_cate, modele.Reference AS nom_modele,
            exemplaire.Disponibilite, exemplaire.ID_Exemplaire AS id_ex,
            etudiant.E_Matricule AS e_mat, responsable_des_equipements.RE_Matricule as re_mat
            FROM emprunt JOIN concerner ON concerner.ID_Emprunt = emprunt.ID_Emprunt
                                                   JOIN exemplaire ON exemplaire.ID_Exemplaire = concerner.ID_Exemplaire
                                                   JOIN modele ON modele.ID_Modele = exemplaire.ID_Modele
                                                   JOIN categorie ON categorie.ID_Categorie = modele.ID_Categorie
                                                   LEFT JOIN etudiant ON etudiant.E_Matricule = emprunt.E_Matricule
                                                   LEFT JOIN responsable_des_equipements ON responsable_des_equipements.RE_Matricule = emprunt.RE_Matricule
            WHERE exemplaire.Disponibilite = 'indisponible' AND exemplaire.Etat = 'utilisable' $ajout";
    $resultat = $base->query($sql);
}catch(Exception $e){
    die('Erreur' .$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OUTILS EMPRUNTÉS</title>
    <link href="outils_empruntes_style.css" rel="stylesheet">
    <link href="https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <div class="boite-login">
        <h1>OUTILS EMPRUNTÉS</h1>
        <form method="post" action="outils_emprunts.php">
        <div class="container">
            <div class="entree-login">
                <input type="text" name="recherche" placeholder="Recherche">
            </div>
            <button type="submit" class="bouton-login" name="action" value="search"><i class='bx  bxs-search'    ></i></button>
        </div>
        </form>
        <div class="liste-commandes">
            <form method="post" action="outils_emprunts.php">
                <?php while($ligne = $resultat->fetch(PDO::FETCH_ASSOC)):?>
                <?php
                $idex = $ligne['id_ex'];
                $nomProduit = $ligne['nom_modele'];
                if(!empty($ligne['e_mat'])){
                    $matricule = $ligne['e_mat'];
                }
                elseif(!empty($ligne['re_mat'])){
                    $matricule = $ligne['re_mat'];
                }
                $dateDebut = $ligne['Date_debut'];
                $dateFin = $ligne['Date_fin_prevue'];
                $today = new DateTime();
                $dat_fin_num = new DateTime($dateFin);
                $retard = $dat_fin_num->diff($today)->days;
                ?>
                <div class="commande">
                    <p class="titre-produit">Produit : <span><?= htmlspecialchars($nomProduit) ?></span></p>
                    <p class="matricule">Matricule : <span><?= htmlspecialchars($matricule)?></span></p>
                    <p class="dates">
                       Du : <span><?= htmlspecialchars($dateDebut) ?></span>
                       &nbsp;&nbsp;→&nbsp;&nbsp;
                       Au : <span><?= htmlspecialchars($dateFin) ?></span>
                    </p>
                    <p>Compte a rebours :
                        <?php if($today>$dat_fin_num){?>
                            <span style="color: red;"><?= $retard?> </span></p>
                        <?php }elseif($today<$dat_fin_num){ ?>
                            <span style="color: green;"><?= $retard?> </span></p>
                        <?php }; ?>
                    <label class="check"><span>Endommagé</span><input type="checkbox" name="dommage[<?php echo $idex ?>]"></input></label>
                    <label class="check"><span>Rendu</span><input type="checkbox" name="rendu[<?php echo $idex ?>]"></input></label>
                </div>
                <?php endwhile; ?>
                </div>
                <button type="submit" class="bouton-login" value="update">Valider les changements</button>
            </form>
    </div>
</body>
</html>
