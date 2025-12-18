<?php
include 'Connexion_DB.php'; 
$sql = "INSERT INTO exemplaire (Etat, Disponibilite, ID_Modele) VALUES ('utilisable', 'disponible', '2')"; 
mysqli_query($conn, $sql); 
mysqli_close($conn); 
?>
