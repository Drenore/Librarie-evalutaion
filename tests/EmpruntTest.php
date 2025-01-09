<?php

include_once("config.php");

try {


    $book_id = 5; 
    $stmt = $pdoTest->prepare("INSERT INTO emprunts (date_emprunt, id_utilisateur, id_livre) VALUES (NOW(), :user_id, :book_id)");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);

    $result = $stmt->execute();

    if ($result) {
        echo "Le livre a été emprunté avec succès.";
    } else {
        echo "Erreur lors de l'emprunt.";
    }

    $stmt = $pdo->prepare("SELECT * FROM emprunts WHERE id_utilisateur = :user_id AND id_livre = :book_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    $emprunt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($emprunt) {
        echo "Emprunt trouvé avec succès.";
    } else {
        echo "Emprunt non trouvé.";
    }
} catch (PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage();
}
