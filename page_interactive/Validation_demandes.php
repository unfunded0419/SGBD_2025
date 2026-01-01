<?php
session_start(); 

if (!isset($_SESSION["logged"]) || $_SESSION["Responsable"] == false) {
    header("Location: connexion.php");
    exit();
}

$RE_Matricule = $_SESSION["Matricule"]; 
try{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $base = new PDO('mysql:host=localhost;dbname=db_iot','root',''); /*j'ai modifié le PDO pour qu'il fonctionne avec XAMPP je ne sais pas réellement si l'ancienne version était fonctionnelle*/ 
    
    $erreurs = [];

    if(!empty($_POST['statut'])){

        $func_old = $base->prepare("SELECT Statut FROM emprunt WHERE ID_Emprunt = :id");

        /*la modification de la requête qui suit afin de venir chercher l'ID modèle directement au niveau de l'attribut dans la table d'emprunt*/ 
        $func_modele = $base->prepare(" 
            SELECT e.ID_Modele_demande
            FROM emprunt e
            WHERE e.ID_Emprunt = :id
            LIMIT 1
        ");

        $func_ex_actuel = $base->prepare("
            SELECT ID_Exemplaire
            FROM concerner
            WHERE ID_Emprunt = :id
            LIMIT 1
        ");

        $func_ex_dispo = $base->prepare("
            SELECT ID_Exemplaire
            FROM exemplaire
            WHERE ID_Modele = :idModele
              AND Disponibilite = 'disponible'
            LIMIT 1
        ");

        $func_set_indispo = $base->prepare("
            UPDATE exemplaire
            SET Disponibilite = 'indisponible'
            WHERE ID_Exemplaire = :idEx
              AND Disponibilite = 'disponible'
        ");

        $func_set_dispo = $base->prepare("
            UPDATE exemplaire
            SET Disponibilite = 'disponible'
            WHERE ID_Exemplaire = :idEx
              AND Disponibilite = 'indisponible'
        ");

        /* Ai modifié la requete pour que le champ responsable soit mis à jour*/
        $func_update_statut = $base->prepare("
            UPDATE emprunt
            SET Statut = :statut, RE_Matricule = :RE_Mat 
            WHERE ID_Emprunt = :id
        ");

        $func_update_concerner = $base->prepare("
            UPDATE concerner
            SET ID_Exemplaire = :idEx
            WHERE ID_Emprunt = :id
            LIMIT 1
        ");
        /* Ai créé une nouvelle requête permettant de créer un instance dans la table concerner si elle n'existe pas au préalable*/
        $func_create_concerner = $base->prepare("
            INSERT INTO concerner(ID_Emprunt, ID_Exemplaire)
            VALUES (:id, :idEx)
        ");

        foreach($_POST['statut'] as $idem=>$nouveauStatut){

            $idem = (int)$idem;
            $nouveauStatut = trim($nouveauStatut);

            $func_old->execute([':id' => $idem]);
            $ancienStatut = $func_old->fetchColumn();
            if($ancienStatut === false) continue;

            if($ancienStatut === $nouveauStatut) continue;

            $base->beginTransaction();
            try{

                if($ancienStatut !== 'approuve' && $nouveauStatut === 'approuve'){

                    $func_modele->execute([':id' => $idem]);
                    $idModele = $func_modele->fetchColumn();
                    if(empty($idModele)){
                        $erreurs[] = "Emprunt $idem : modele introuvable";
                        $base->rollBack();
                        continue;
                    }

                    $func_ex_dispo->execute([':idModele' => $idModele]);
                    $idExDispo = $func_ex_dispo->fetchColumn();
                    if(empty($idExDispo)){
                        $erreurs[] = "Emprunt $idem : aucun exemplaire disponible";
                        $base->rollBack();
                        continue;
                    }

                    $func_set_indispo->execute([':idEx' => $idExDispo]);
                    if($func_set_indispo->rowCount() === 0){
                        $erreurs[] = "Emprunt $idem : exemplaire plus disponible";
                        $base->rollBack();
                        continue;
                    }

                    $func_ex_actuel->execute([':id' => $idem]);
                    if ($func_ex_actuel->rowCount() === 0) { /*si le champ concerner n'est pas encore créé il faut le créer*/
                    $func_create_concerner->execute(['idEx' => $idExDispo, ':id' => $idem]); 
                    } else {
                    $func_update_concerner->execute([':idEx' => $idExDispo, ':id' => $idem]);
                    }
                    $func_update_statut->execute([':statut' => 'approuve',':RE_Mat' => $RE_Matricule, ':id' => $idem]);

                    $base->commit();
                    continue;
                }

                if($ancienStatut === 'approuve' && $nouveauStatut !== 'approuve'){

                    $func_ex_actuel->execute([':id' => $idem]);
                    $idExActuel = $func_ex_actuel->fetchColumn();
                    if(!empty($idExActuel)){
                        $func_set_dispo->execute([':idEx' => $idExActuel]);
                    }

                    $func_update_statut->execute([':statut' => $nouveauStatut,':RE_Mat' => $RE_Matricule, ':id' => $idem]);
                    
                    $base->commit();
                    continue;
                }

                $func_update_statut->execute([':statut' => $nouveauStatut,':RE_Mat' => $RE_Matricule, ':id' => $idem]);
                $base->commit();

            }catch(Exception $e){
                if($base->inTransaction()) $base->rollBack();
                throw $e;
            }
        }
    }


    $filtre = $_GET['filtre'] ?? 'en_attente';
    
    if($filtre === 'en_attente'){
    $where = "WHERE emprunt.Statut = 'en_attente'";
    }
    elseif($filtre === 'approuve'){
    $where = "WHERE emprunt.Statut = 'approuve'";
    }
    elseif($filtre === 'refuse'){
    $where = "WHERE emprunt.Statut = 'refuse'";
    }
    elseif($filtre === 'toutes'){
    $where = "";
    } 

    $sql = "SELECT emprunt.ID_Emprunt, emprunt.Date_debut, emprunt.Date_fin_prevue, emprunt.Raison_Emprunt, emprunt.Statut,
    categorie.Nom AS nom_cate, modele.Reference AS nom_modele,
    etudiant.Nom AS nom_etu, etudiant.Prenom AS prenom_etu,
    responsable_des_equipements.Nom AS nom_re, responsable_des_equipements.Prenom AS prenom_re
    FROM emprunt JOIN modele ON emprunt.ID_Modele_demande = modele.ID_Modele
                                  JOIN categorie ON categorie.ID_Categorie = modele.ID_Categorie
                                  LEFT JOIN etudiant ON etudiant.E_Matricule = emprunt.E_Matricule
                                  LEFT JOIN responsable_des_equipements ON responsable_des_equipements.RE_Matricule = emprunt.RE_Matricule
                                  $where";
    $resultat = $base->query($sql);
}catch(Exception $e){
die('Erreur' .$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ACCUEIL DE DEMANDES</title>
    <link href='../css/Validation_demandes.css?v=1.5' rel='stylesheet'>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body class = "elms-sans-text">
    <main>
    <div class = "boite-login">
        <h1>ACCUEIL DE DEMANDES</h1></br>
        <?php if(!empty($erreurs)): ?>
            <div class="erreur">
                <?php foreach($erreurs as $m): ?>
                    <p><?= htmlspecialchars($m) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="filtre-container">
            <div class = "filtre-bouton">
                Filtre <span class="fleche">▾</span>
            </div>
            <div class="filtre-menu">
                <a href="Validation_demandes.php?filtre=toutes">Tous</a>
                <a href="Validation_demandes.php?filtre=en_attente">En attente</a>
                <a href="Validation_demandes.php?filtre=approuve">Validé</a>
                <a href="Validation_demandes.php?filtre=refuse">Décliné</a>
            </div>
        </div>

        <div class="liste-commandes">
            <form method="post" action="Validation_demandes.php">
                <?php while($ligne = $resultat->fetch(PDO::FETCH_ASSOC)):?>
                <?php
                $id = $ligne['ID_Emprunt'];
                $nomProduit = $ligne['nom_modele'];
                if(!empty($ligne['nom_etu'])){
                    $nomComplet = $ligne['nom_etu'] .' '. $ligne['prenom_etu'].', Etudiant';
                }
                elseif(!empty($ligne['nom_re'])){
                    $nomComplet = $ligne['nom_re'].' '.$ligne['prenom_re'].', Responsable';
                }
                else {
                    $nomComplet = 'Inconnu';
                    $typePersonne = ' ';
                }
                $dateDebut = $ligne['Date_debut'];
                $dateFin = $ligne['Date_fin_prevue'];
                $raison = $ligne['Raison_Emprunt'];
                $statut = $ligne['Statut'];
                ?>
                <div class="commande">
                    <p class="titre-produit">Produit : <span><?= htmlspecialchars($nomProduit) ?></span></p>
                    <p class="client">Client : <span><?= htmlspecialchars($nomComplet) ?></span></p>
                    <p class="dates">
                        Du : <span><?= htmlspecialchars($dateDebut) ?></span>
                        &nbsp;&nbsp;→&nbsp;&nbsp;
                        Au : <span><?= htmlspecialchars($dateFin) ?></span>
                    </p>
                    <div class="raison">Raison
                        <span class="fleche">▾</span>
                        <div class="raison-menu">
                            <p><?=htmlspecialchars($raison)?></p>
                        </div>
                    </div>
                    <select class="etat" name="statut[<?php echo $id ?>]">
                        <?php if($statut === 'en_attente'){?>
                        <option value="en_attente" selected="selected">En attente</option>
                        <?php }else{?>
                        <option value="en_attente" selected="selected">En attente</option><?php }?>

                        <?php if($statut === 'approuve'){?>
                        <option value="approuve" selected="selected">Accepté</option>
                        <?php }else{?>
                        <option value="approuve" >Accepté</option><?php }?>

                        <?php if($statut === 'refuse'){?>
                        <option value="refuse" selected="selected">Décliné</option>
                        <?php }else{?>
                        <option value="refuse">Décliné</option><?php }?>
                    </select>
                </div>
                <?php endwhile; ?>
            </div>
            <button type="submit" class="bouton-login">Valider les changements</button>
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