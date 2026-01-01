<?php
session_start(); 
try{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $base = new PDO('mysql:host=localhost;dbname=db_iot','root',''); /*j'ai modifié le PDO pour qu'il fonctionne avec XAMPP je ne sais pas réellement si l'ancienne version était fonctionnelle*/ 

    if(!empty($_POST['rendu'])){ 
        $stmt = $base->prepare("UPDATE exemplaire SET Disponibilite = :dispo, Etat= :etat WHERE ID_Exemplaire = :id");
        $shearsh = $base->prepare("SELECT exemplaire.ID_Exemplaire as ID_Exemplaire FROM concerner 
            join exemplaire on concerner.ID_Exemplaire = exemplaire.ID_Exemplaire WHERE concerner.ID_Emprunt = :idemp Limit 1");
       
        foreach($_POST['rendu'] as $idex=>$v){ /* modification du code afin de tenir compte du fait que idex désigne désormais l'id de l'emprunt et plus celui de l'exemplaire*/
            $shearsh->execute([':idemp' => $idex]); /* je vais d'abord  rechercher l'id de l'exemplaire acutellement associé à l'emprunt*/
            $id_exemplaire = $shearsh->fetchColumn(); 
            
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
                ':id'    => $id_exemplaire
            ]); 
        }
    }
 /* mise à jour de la table emprunt afin de retourner la date de retour ainsi que s'il y a eu ou pas des dégats*/
    if(!empty($_POST['rendu'])){
        $today = date('Y-m-d'); 
        $stmt = $base->prepare("UPDATE emprunt SET Date_retour = :date_retour,  Retard = :retard, Degradation_materiel = :degat WHERE ID_Emprunt = :id");
        $shearsh = $base->prepare("SELECT emprunt.Date_fin_prevue as Date_initiale FROM emprunt WHERE emprunt.ID_Emprunt = :idemp Limit 1"); 
        foreach($_POST['rendu'] as $idex=>$v){
            $shearsh->execute([':idemp' => $idex]); 
            $date_prevue = $shearsh->fetchColumn(); 

            $retard = ($today > $date_prevue) ? true : false;

            $isDommage = isset($_POST['dommage'][$idex]);
            if($isDommage){
                $dom = true;
            }else{
                $dom = false;
            }

            $stmt->execute([
                 ':id' => $idex,
                 ':degat' => $dom,
                 ':retard' => $retard,
                 ':date_retour' => $today
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

    $sql = "SELECT emprunt.Date_debut, emprunt.Date_fin_prevue, emprunt.ID_Emprunt as ID_Emprunt, 
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
    <link href="../css/outils_empruntes_style.css?v=1.3" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class = "elms-sans-text">
    <main>
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
                $idex = $ligne['ID_Emprunt']; /* modification afin de retourner l'emprunt et non l'exemplaire sinon très chiant de trouver l'emprunt à partir de l'exemplaire*/
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
