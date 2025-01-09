<?php
include_once("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$query = "
    SELECT 
        emprunts.id, 
        emprunts.date_emprunt, 
        livres.titre, 
        livres.auteur, 
        emprunts.statut,
        DATE_ADD(emprunts.date_emprunt, INTERVAL 30 DAY) AS date_limite
    FROM emprunts
    INNER JOIN livres ON emprunts.id_livre = livres.id
";

$stmt = $pdo->prepare($query);
$stmt->execute();
$emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestion des Emprunts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> <!-- Charte graphique -->
</head>
<body>

    <nav class="navbar navbar-expand-lg" style="background-color: #007BFF;">
        <div class="container">
            <a class="navbar-brand text-white" href="#"><img src="image/logo.png" alt="Librairie XYZ Logo"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-white" href="home.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="admin_loans.php">Gérer Emprunts</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Deconnexion</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1 class="mb-4">Gestion des Emprunts</h1>

        <?php if (!empty($emprunts)): ?>
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date d'Emprunt</th>
                        <th>Titre</th>
                        <th>Auteur</th>
                        <th>Statut</th>
                        <th>Date Limite de Retour</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emprunts as $emprunt): ?>
                        <?php 
                            $retard_class = (new DateTime($emprunt['date_limite']) < new DateTime()) ? 'retard' : '';
                        ?>
                        <tr class="<?= $retard_class ?>">
                            <td><?= $emprunt['id'] ?></td>
                            <td><?= $emprunt['date_emprunt'] ?></td>
                            <td><?= $emprunt['titre'] ?></td>
                            <td><?= $emprunt['auteur'] ?></td>
                            <td><?= $emprunt['statut'] ?></td>
                            <td><?= $emprunt['date_limite'] ?></td>
                            <td>
                                <?php if ($emprunt['statut'] != 'terminé' && $emprunt['statut'] != 'annulé'): ?>
                                    <form action="process_loan.php" method="POST">
                                        <input type="hidden" name="id_emprunt" value="<?= $emprunt['id'] ?>">
                                        <button type="submit" name="action" value="validate" class="btn btn-success btn-sm">Valider Retour</button>
                                        <button type="submit" name="action" value="cancel" class="btn btn-warning btn-sm">Annuler</button>
                                        <?php 
                                            if (new DateTime() > new DateTime($emprunt['date_limite'])): 
                                        ?>
                                            <button type="submit" name="action" value="send_email" class="btn btn-info btn-sm">Envoyer Email</button>
                                        <?php endif; ?>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Aucun emprunt en cours.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
