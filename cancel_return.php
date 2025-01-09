<?php 
include_once("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_emprunt = $_POST['id_emprunt'];


    $query = "UPDATE emprunts SET statut = 'annulé' WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id_emprunt, PDO::PARAM_INT);
    $stmt->execute();

    header('Location: admin_loans.php?success=Retour annulé.');
    exit();
} else {
    header('Location: admin_loans.php');
    exit();
}
