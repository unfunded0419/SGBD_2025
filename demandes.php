<?php
try{
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    $base = new PDO('mysql:host=localhost;port=8888;dbname=db_IOT','root','root');

    $erreurs = [];

    if(!empty($_POST['statut'])){

        $func_old = $base->prepare("SELECT Statut FROM emprunt WHERE ID_Emprunt = :id");

        $func_modele = $base->prepare("
            SELECT ex.ID_Modele
            FROM concerner c
            JOIN exemplaire ex ON ex.ID_Exemplaire = c.ID_Exemplaire
            WHERE c.ID_Emprunt = :id
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

        $func_update_statut = $base->prepare("
            UPDATE emprunt
            SET Statut = :statut
            WHERE ID_Emprunt = :id
        ");

        $func_update_concerner = $base->prepare("
            UPDATE concerner
            SET ID_Exemplaire = :idEx
            WHERE ID_Emprunt = :id
            LIMIT 1
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

                if($ancienStatut !== 'valide' && $nouveauStatut === 'valide'){

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

                    $func_update_concerner->execute([':idEx' => $idExDispo, ':id' => $idem]);
                    $func_update_statut->execute([':statut' => 'valide', ':id' => $idem]);

                    $base->commit();
                    continue;
                }

                if($ancienStatut === 'valide' && $nouveauStatut !== 'valide'){

                    $func_ex_actuel->execute([':id' => $idem]);
                    $idExActuel = $func_ex_actuel->fetchColumn();
                    if(!empty($idExActuel)){
                        $func_set_dispo->execute([':idEx' => $idExActuel]);
                    }

                    $func_update_statut->execute([':statut' => $nouveauStatut, ':id' => $idem]);

                    $base->commit();
                    continue;
                }

                $func_update_statut->execute([':statut' => $nouveauStatut, ':id' => $idem]);
                $base->commit();

            }catch(Exception $e){
                if($base->inTransaction()) $base->rollBack();
                throw $e;
            }
        }
    }


    $filtre = $_GET['filtre'] ?? 'en_attente';
    if($filtre === 'en_attente'){
    $where = "WHERE statut = 'en attente'";
    }
    elseif($filtre === 'valide'){
    $where = "WHERE statut = 'valide'";
    }
    elseif($filtre === 'decline'){
    $where = "WHERE statut = 'decline'";
    }
    elseif($filtre === 'toutes'){
    $where = "";
    }

    $sql = "SELECT emprunt.ID_Emprunt, emprunt.Date_debut, emprunt.Date_fin_prevue, emprunt.Raison_Emprunt, emprunt.Statut,
    categorie.Nom AS nom_cate, modele.Reference AS nom_modele,
    etudiant.Nom AS nom_etu, etudiant.Prenom AS prenom_etu,
    responsable_des_equipements.Nom AS nom_re, responsable_des_equipements.Prenom AS prenom_re
    FROM emprunt JOIN concerner ON concerner.ID_Emprunt = emprunt.ID_Emprunt
                                  JOIN exemplaire ON exemplaire.ID_Exemplaire = concerner.ID_Exemplaire
                                  JOIN modele ON modele.ID_Modele = exemplaire.ID_Modele
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
    <link href='demandes_style.css' rel='stylesheet'>
    <link href='https://cdn.boxicons.com/3.0.3/fonts/basic/boxicons.min.css' rel='stylesheet'>
</head>
<body>
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
                <a href="demandes.php?filtre=toutes">Tous</a>
                <a href="demandes.php?filtre=en_attente">En attente</a>
                <a href="demandes.php?filtre=valide">Validé</a>
                <a href="demandes.php?filtre=decline">Décliné</a>
            </div>
        </div>

        <div class="liste-commandes">
            <form method="post" action="demandes.php">
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
                        <?php if($statut === 'en attente'){?>
                        <option value="en attente" selected="selected">En attente</option>
                        <?php }else{?>
                        <option value="en attente" selected="selected">En attente</option><?php }?>

                        <?php if($statut === 'valide'){?>
                        <option value="valide" selected="selected">Accepté</option>
                        <?php }else{?>
                        <option value="valide" >Accepté</option><?php }?>

                        <?php if($statut === 'decline'){?>
                        <option value="decline" selected="selected">Décliné</option>
                        <?php }else{?>
                        <option value="decline">Décliné</option><?php }?>
                    </select>
                </div>
                <?php endwhile; ?>
            </div>
            <button type="submit" class="bouton-login">Valider les changements</button>
        </form>
    </div>
</body>
</html>