<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/mariadb.php';

if (!isset($_SESSION['Role']['Administrateur']) || !$_SESSION['Role']['Administrateur']) {
    error_log("ACCÈS REFUSÉ - Pas admin");
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Accès refusé - Pas administrateur']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode POST requise']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
error_log("Data decoded: " . print_r($data, true));

$userId = (int)($data['id'] ?? 0);
error_log("User ID: " . $userId);

if ($userId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID utilisateur invalide: ' . $userId]);
    exit;
}

try {
    error_log("Connexion DB et appel procédure pour ID: " . $userId);
    $db = (new Database())->getConnection();
    
    $stmt = $db->prepare("CALL DeleteUserSafe(?)");
    $stmt->execute([$userId]);

    while ($stmt->nextRowset()) {}

    error_log("Suppression réussie pour ID: " . $userId);
    echo json_encode(['success' => true, 'message' => 'Utilisateur supprimé']);
} catch (Exception $e) {
    error_log("ERREUR: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}