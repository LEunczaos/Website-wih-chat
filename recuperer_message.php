<?php
require_once 'connexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['connecte'])) {
    http_response_code(403);
    echo json_encode([]);
    exit();
}

$mon_id = $_SESSION['connecte']['id'];
$ami_id = isset($_GET['avec']) ? (int)$_GET['avec'] : 0;

if ($ami_id <= 0) {
    echo json_encode([]);
    exit();
}

$sql = "SELECT expediteur_id, message, date_envoi
        FROM messages
        WHERE (expediteur_id = :mon_id AND destinataire_id = :ami_id)
           OR (expediteur_id = :ami_id AND destinataire_id = :mon_id)
        ORDER BY date_envoi ASC";

$stmt = $conn->prepare($sql);
$stmt->execute([':mon_id' => $mon_id, ':ami_id' => $ami_id]);
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($messages);
