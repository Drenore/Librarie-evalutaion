<?php
include_once("config.php");

if (!isset($_SESSION['user_id']) && $_SESSION['role'] !== "admin") {
    header('Location: login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_emprunt = filter_input(INPUT_POST, 'id_emprunt', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($id_emprunt && $action) {
        $queryCheck = "SELECT DATE_ADD(date_emprunt, INTERVAL 30 DAY) AS date_limite, statut FROM emprunts WHERE id = :id";
        $stmtCheck = $pdo->prepare($queryCheck);
        $stmtCheck->execute([':id' => $id_emprunt]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $date_limite = new DateTime($result['date_limite']);
            $current_date = new DateTime();

            // Mise à jour du statut selon l'action
            if ($action == 'validate') {
                $queryUpdate = "UPDATE emprunts SET statut = 'terminé' WHERE id = :id";
                $stmtUpdate = $pdo->prepare($queryUpdate);
                $stmtUpdate->execute([':id' => $id_emprunt]);

            } elseif ($action == 'cancel') {
                $queryUpdate = "UPDATE emprunts SET statut = 'annulé' WHERE id = :id";
                $stmtUpdate = $pdo->prepare($queryUpdate);
                $stmtUpdate->execute([':id' => $id_emprunt]);

            } elseif ($action == 'send_email' && $current_date > $date_limite) {
                // Mettre à jour le statut en "en retard"
                $queryUpdate = "UPDATE emprunts SET statut = 'en retard' WHERE id = :id";
                $stmtUpdate = $pdo->prepare($queryUpdate);
                $stmtUpdate->execute([':id' => $id_emprunt]);

                // Envoi de l'email de notification
                $queryLivre = "SELECT livres.titre FROM emprunts INNER JOIN livres ON emprunts.id_livre = livres.id WHERE emprunts.id = :id";
                $stmtLivre = $pdo->prepare($queryLivre);
                $stmtLivre->execute([':id' => $id_emprunt]);
                $livre = $stmtLivre->fetch(PDO::FETCH_ASSOC);

                if ($livre) {
                    $subject = "Notification de Retard : \"{$livre['titre']}\"";
                    $message = "Le livre \"{$livre['titre']}\" a dépassé la date limite de retour.";
                    sendEmail('admin@librairie.xyz', $subject, $message);
                }
            }
        }

        // Redirection vers la page admin_loans
        header('Location: admin_loans.php');
        exit();
    }
}
?>
