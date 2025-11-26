<?php
session_start();
 if ($_SESSION["logged"]) {
include '../page_html/Home.html';
 } else {
    header("Location : connexion.php"); 
 } 
?>