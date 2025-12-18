<?php
session_start();
// Vérification de sécurité
if (!isset($_SESSION['logged']) || $_SESSION['logged'] !== true) {
    header("Location: connexion.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Page d'accueil</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> <?php/* ne pas dézoomer si on est sur smartphone, sinon tout paraitra rikiki */?>
        <link rel="stylesheet" href="../css/Home.css?v=1.3"> <?php /* on fait appel à notre fichier css */?>
        
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Elms+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    </head>
    
    <body class="elms-sans-text">
        
        <div class="glass-container">
            
            <h1>Gestion Équipements</h1>
            <h2>Université de Mons - Lots et Edge</h2>

            <div class="menu-options">
                
                <a href="../page_interactive/Demande.php" class="btn-glass">
                    Faire une demande
                </a>
                
                <a href="../page_interactive/Stocks.php" class="btn-glass">
                    Consulter les stocks
                </a>
                
                <a href="../page_interactive/Suivi_demande.php" class="btn-glass">
                    Suivi des demandes
                </a>

                <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'resp' || $_SESSION['role'] === 'admin')) : ?>
                    <a href="#" class="btn-glass">
                        Gestion des demandes étduiantes (code Noa)
                    </a>
                <?php endif; ?> 

                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
                    <a href="../page_interactive/demande_resp.php" class="btn-glass btn-admin">
                        Gestion des comptes Responsables
                    </a>
                <?php endif; ?>

                <a href="logout.php" class="btn-glass" style="margin-top: 10px; background: rgba(200,50,50,0.3);">
                    Déconnexion
                </a>

            </div>
        </div>

    </body>
</html>