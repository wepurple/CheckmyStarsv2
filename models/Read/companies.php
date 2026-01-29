<?php
ob_clean();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../../includes/mariadb.php';

try {
    $db = (new Database())->getConnection();
    $stmt = $db->prepare("CALL Get_Companies()");
    $stmt->execute();
    
    $companies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    while ($stmt->nextRowset());
    
    echo json_encode($companies);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
