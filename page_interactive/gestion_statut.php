<?php
session_start();
include 'Connexion_DB.php';

// 1. SÉCURITÉ : Seul l'admin peut faire ça
if ($_SESSION['logged'] == false || $_SESSION['admin'] == false || $_SESSION["Responsable"] == false) {
    die("Accès interdit.");
}

// 2. VÉRIFICATION DES PARAMÈTRES URL
if (isset($_GET['matricule']) && isset($_GET['action'])) {
    
    $matricule = $_GET['matricule'];
    $action = $_GET['action']; // Sera 'Accepte' ou 'Refuse'

    // Petite sécurité pour être sûr que l'action est valide
    if ($action === 'Accepte' || $action === 'Refuse') {
        
        $nouveau_statut = ($action === 'Accepte') ? 'approuve' : 'refuse';

        // 3. UPDATE DANS LA BASE DE DONNÉES
        if ($nouveau_statut == "refuse") {
           $stmt = $conn->prepare("DELETE FROM responsable_des_equipements WHERE RE_Matricule = ?");
           $stmt->bind_param("i", $matricule);  // RE_Matricule est probablement un entier
        } else {
            $stmt = $conn->prepare("UPDATE responsable_des_equipements SET Statut = ? WHERE RE_Matricule = ?");
            $stmt->bind_param("si", $nouveau_statut, $matricule);  // "s" pour string (Statut), "i" pour integer (matricule)
        }
        // bind_param() permet d'injecter mes variables dans les "?" 
        // "ss" car le statut et le matricule (qui est en réalité une adresse mail) sont des string (varchar dans la db) et donc "s" pour chaque variable
        if ($stmt->execute()) {
            // Succès : on retourne à la liste
            header("Location: demande_resp.php");
            exit();
        } else {
            echo "Erreur lors de la mise à jour.";
        }
    }
} else {
    echo "Paramètres manquants.";
}
?>