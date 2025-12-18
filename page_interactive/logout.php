<?php
// 1. On démarre la session (obligatoire pour pouvoir y toucher)
session_start();

// 2. On détruit toutes les variables de session (vider le tableau $_SESSION)
$_SESSION = array();

// 3. On détruit la session sur le serveur
session_destroy();

// 4. On redirige l'utilisateur vers la page de connexion
header("Location: connexion.php");
exit();
?>