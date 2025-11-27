<?php
if (!isset($_SESSION["connecté"]) || $_SESSION["role"] != "responsable") {
    header("Location: connexion.php");
    exit();
}
?>