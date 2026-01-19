<?php

require_once 'connexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['connecte'])) {
    echo json_encode(['success' => false, 'error' => 'Non connecté']);
    exit;
}

$expediteur = $_SESSION['connecte']['id'];
$destinataire = isset($_POST['destinataire_id']) ? (int)$_POST['destinataire_id'] : 0;
$message = trim($_POST['message'] ?? '');

if ($destinataire <= 0 || $message === '') {
    echo json_encode(['success' => false, 'error' => 'Veuillez remplir tous les champs']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO messages (expediteur_id, destinataire_id, message, date_envoi) VALUES (?, ?, ?, NOW())");
$success = $stmt->execute([$expediteur, $destinataire, $message]);

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'envoi']);
}
