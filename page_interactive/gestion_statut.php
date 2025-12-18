<?php
session_start();
include 'Connexion_DB.php';

// 1. SÉCURITÉ : Seul l'admin peut faire ça
if (!isset($_SESSION['logged']) || $_SESSION['role'] !== 'admin') {
    die("Accès interdit.");
}

// 2. VÉRIFICATION DES PARAMÈTRES URL
if (isset($_GET['matricule']) && isset($_GET['action'])) {
    
    $matricule = $_GET['matricule'];
    $action = $_GET['action']; // Sera 'Accepte' ou 'Refuse'

    // Petite sécurité pour être sûr que l'action est valide
    if ($action === 'Accepte' || $action === 'Refuse') {
        
        // Si c'est "Accepte", on met "Accepté" (avec accent comme dans ta DB)
        // Si c'est "Refuse", on met "Refusé"
        $nouveau_statut = ($action === 'Accepte') ? 'Accepté' : 'Refusé';

        // 3. UPDATE DANS LA BASE DE DONNÉES
        $stmt = $conn->prepare("UPDATE responsable_des_equipements SET Statut = ? WHERE RE_Matricule = ?");
        $stmt->bind_param("si", $nouveau_statut, $matricule);
        
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