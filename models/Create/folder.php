<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

require_once __DIR__ . '/../../includes/mariadb.php';

if (empty($_SESSION['Role']['Administrateur'] || $_SESSION['Role']['Inspecteur'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Accès refusé - Admin requis ou inspecteur']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données JSON invalides']);
    exit;
}

try 
{

    $db = (new Database())->getConnection();

    $stmt = $db->prepare("CALL Create_Dossier(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['NumRue'] ?? null,
        $data['NomRue'] ?? null,
        $data['Comp'] ?? null,
        $data['CP'] ?? null,
        $data['Ville'] ?? null,
        $data['Pays'] ?? null,

        $data['BiensNom'] ?? null,
        $data['BiensTel'] ?? null,
        $data['BiensEtoiles'] ?? null,
        $data['BiensDonneurID'] ?? null,
        $data['BiensType'] ?? null,
        $data['BiensUser'] ?? null,

        $data['EtoileDossier'] ?? null,
        $data['InspecteurID'] ?? null,
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    while ($stmt->nextRowset()) {}

    echo json_encode([
        'success' => true,
        'message' => 'Dossier créé avec succès',
    ]);

} 
catch (Exception $e) 
{
    error_log("Erreur création Dossier: " . $e->getMessage());

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}
?>