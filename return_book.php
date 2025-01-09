<?php 
include_once("config.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_emprunt = $_POST['id_emprunt'];


    $query = "UPDATE emprunts SET statut = 'en attente de validation' WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id_emprunt, PDO::PARAM_INT);
    $stmt->execute();

  
    header('Location: loans.php?success=Le retour a été mis à jour en attente de validation.');
    exit();
} else {
    header('Location: loans.php');
    exit();
}
