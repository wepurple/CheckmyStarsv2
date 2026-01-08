<?php
session_start();
include_once __DIR__ . '/../includes/mariadb.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$star = isset($_GET['star']) ? (int)$_GET['star'] : 0;
if ($star < 1 || $star > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid star']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
if (!($db instanceof PDO)) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

$typeHebergement = 2;

$sql = "
    SELECT DISTINCT
        c.Critere_ID   AS id,
        c.Critere_description AS description,
        c.Critere_statut AS statut,
        c.Critere_points AS points
    FROM listescriteres_etoiles lce
    JOIN contient co ON co.ListesCriteres_ID = lce.ListesCriteres_ID
    JOIN criteres c ON c.Critere_ID = co.Critere_ID
    WHERE lce.type_hebergement_id = :type
      AND lce.etoile = :star
    ORDER BY c.Critere_ID
";

$stmt = $db->prepare($sql);
$stmt->execute(['type' => $typeHebergement, 'star' => $star]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
