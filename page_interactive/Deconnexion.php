<?php
session_start();

session_destroy();
setcookie("PHPSESSID", "", time() - 3600, "/"); // je fais expirer le cookie sinon je peux malgré la déconnexion retourner sur des pages du site 

header("Location: connexion.php");
exit();
?>