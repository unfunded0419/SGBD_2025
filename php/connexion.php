<?php
$utilisateur = $_POST['utilisateur'];
$motdepasse = $_POST['motdepasse'];
try{
    $base = new PDO('mysql:host=localhost;port=8888;dbname=db_IOT','root', 'root')
    echo "Connexion réussie à la base de données <br>";
}catch(Exception $e){
die('Erreur : '.$e->getMessage());
}
?>