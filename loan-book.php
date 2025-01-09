<?php
include_once("config.php");

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET["userid"]) && isset($_GET["idbook"])) {
    $id_user = htmlspecialchars($_GET["userid"]);
    $id_book = htmlspecialchars($_GET["idbook"]);

    if (is_numeric($id_user) && is_numeric($id_book)) {
        $id_user = (int) $id_user;
        $id_book = (int) $id_book;
        $status = 'en cours';

        $query = "INSERT INTO emprunts(id_utilisateur, id_livre, statut) VALUES(:id_utilisateur, :id_livre, :statut)";

        $stmt = $pdo->prepare($query);

        $stmt->bindParam(':id_utilisateur', $id_user, PDO::PARAM_INT);
        $stmt->bindParam(':id_livre', $id_book, PDO::PARAM_INT);
        $stmt->bindParam(':statut', $status, PDO::PARAM_STR);

        if ($stmt->execute()) {
         
            $query = "UPDATE livres SET statut = 'emprunté' WHERE id = :id";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id_book, PDO::PARAM_INT);
            if ($stmt->execute()) {
                header('Location: book_details.php?id=' . $id_book . '&success=L\'emprunt a été enregistré.');
            }
        } else {
            header('Location: book_details.php?id=' . $id_book . '&error=Erreur lors de l\'emprunt.');
        }
    } else {
        header('Location: book_details.php?id=' . $id_book . '&error=l\'emprunt du livre ne fonctionne pas');
    }
} else {
    echo "Paramètres manquants.";
    exit;
}
?>
