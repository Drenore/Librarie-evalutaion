<?php
require('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    // Récupérez les détails du livre depuis la base de données en utilisant $bookId
    $query = "SELECT * FROM livres WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(array(':id' => $bookId));

    if ($stmt->rowCount() == 1) {
        $book = $stmt->fetch();
    } else {
        // Livre non trouvé, gérer l'erreur ici
    }
} else {
    // ID de livre non spécifié dans l'URL, gérer l'erreur ici
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Détails du Livre</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .book-image {
            max-width: 30%;
            height: auto;
            display: block;
            margin: 0 auto;
            /* Pour centrer l'image */
        }
    </style>
</head>

<body>
    <header>
        <h1>Détails du Livre</h1>
    </header>
    <?php if (isset($_GET["success"])): ?>
        <div class="alert alert-success"><?= $_GET["success"] ?> </div>
    <?php elseif (isset($_GET["error"])) : ?>
        <div class="alert alert-success"><?= $_GET["error"] ?> </div>
    <?php else : ?>
    <?php endif; ?>

    <div class="container">
        <div class="details">
            <?php if (isset($book)) : ?>
                <h3><?= htmlspecialchars($book['titre']); ?></h3>

                <?php echo '<img class="book-image" src="' . htmlspecialchars($book['photo_url']) . '" alt="' . htmlspecialchars($book['titre']) . '">'; ?>
                <p>Auteur : <?= htmlspecialchars($book['auteur']); ?></p>
                <p>Année de publication : <?= htmlspecialchars($book['date_publication']); ?></p>
                <p>ISBN : <?= htmlspecialchars($book['isbn']); ?></p>
                <!-- Ajoutez l'URL de l'image ici -->
                <p>URL de l'image : <?= htmlspecialchars($book['photo_url']); ?></p>
                <!-- Autres détails du livre à afficher ici -->

            <?php else : ?>
                <p>Livre non trouvé</p>
            <?php endif; ?>
        </div>
        <div class="back-button">
            <button onclick="window.location.href = 'books.php'">Retour à la liste des livres</button>

            <?php
            // Ajoutez une vérification du rôle de l'utilisateur
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                // Si l'utilisateur est un administrateur, affichez les boutons "Modifier" et "Supprimer"
                echo '<button onclick="window.location.href = \'edit_book.php?book_id=' . $bookId . '\'">Modifier le livre</button>';
                echo '<button onclick="showDeleteConfirmation(' . $bookId . ')">Supprimer le livre</button>';
            }
            ?>
            <?php
            if (isset($book) && $book["statut"] === "disponible"):
            ?>
                <a href="loan-book.php?idbook=<?= $bookId ?>&userid=<?= $_SESSION['user_id'] ?>" class="btn btn-primary"> Emprunté ce livre </a>
            <?php else : ?>
                <div class="alert alert-primary"> Ce livre est déjà emprunté </div>
            <?php endif; ?>

        </div>

    </div>
</body>
<script>
    function showDeleteConfirmation(bookId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer ce livre ?")) {
            // Si l'utilisateur confirme la suppression, redirigez-le vers la page de suppression avec l'ID du livre.
            window.location.href = "delete_book.php?book_id=" + bookId;
        } else {
            // Si l'utilisateur annule la suppression, ne faites rien.
        }
    }
</script>

</html>