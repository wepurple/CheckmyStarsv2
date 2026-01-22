<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/mariadb.php';

if (!isset($_SESSION['RoleAdministrateur']) || !$_SESSION['RoleAdministrateur']) {
  http_response_code(403);
  echo json_encode(['success' => false, 'error' => 'Accès refusé']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['success' => false, 'error' => 'Méthode POST requise']);
  exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userId = (int)($data['id'] ?? 0);

if ($userId <= 0) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => 'ID utilisateur invalide']);
  exit;
}

try {
  $db = (new Database())->getConnection();

  $stmt = $db->prepare("CALL DeleteUserSafe(?)");
  $stmt->execute([$userId]);

  while ($stmt->nextRowset()) {}

  echo json_encode(['success' => true]);
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
