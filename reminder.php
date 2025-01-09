<?php
include_once("config.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] === 'admin') {
    header('Location: login.php');
    exit();
}

// Durée maximale d'emprunt
define('DUREE_EMPRUNT', 30);

// Récupérer les emprunts en retard
$query = "
    SELECT 
        emprunts.id, 
        emprunts.date_emprunt, 
        utilisateurs.email
    FROM emprunts
    INNER JOIN utilisateurs ON emprunts.id_utilisateur = utilisateurs.id
    WHERE emprunts.statut = 'en retard' AND DATEDIFF(NOW(), emprunts.date_emprunt) > " . DUREE_EMPRUNT;
    
$stmt = $pdo->prepare($query);
$stmt->execute();
$emprunts_rappeled = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($emprunts_rappeled as $emprunt) {
    $email = $emprunt['email'];
    $subject = "Rappel de retour en retard pour le livre ID: " . $emprunt['id'];
    $message = "Bonjour,\n\nVotre emprunt pour le livre avec l'ID " . $emprunt['id'] . " est en retard. Merci de le retourner au plus vite.\n\nCordialement,\nL'équipe.";
    sendEmail($email, $subject, $message);
}

?>
