<?php
header('Content-Type: application/json');
require_once("../../includes/mariadb.php");

$email = $_GET['email'] ?? '';
$excludeId = $_GET['excludeId'] ?? null;

if (empty($email)) {
    echo json_encode(['exists' => false]);
    exit;
}

try {
    $pdo = (new Database())->getConnection();

    if ($excludeId) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE Utilisateur_Mail = :email AND Utilisateur_ID != :id");
        $stmt->execute(['email' => $email, 'id' => $excludeId]);
    } else {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE Utilisateur_Mail = :email");
        $stmt->execute(['email' => $email]);
    }
    
    $count = $stmt->fetchColumn();
    echo json_encode(['exists' => $count > 0]);
    
} catch (PDOException $e) {
    echo json_encode(['exists' => false, 'error' => $e->getMessage()]);
}
