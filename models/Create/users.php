<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/mariadb.php';

if (!isset($_SESSION['Role']['Administrateur']) || !$_SESSION['Role']['Administrateur']) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Accès refusé - Admin requis']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode POST requise']);
    exit;
}

$input = file_get_contents('php://input');
error_log("Données reçues: " . $input);
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données JSON invalides']);
    exit;
}

$required_fields = ['nom', 'prenom', 'email', 'password', 'societe_id', 'role_id'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Le champ '$field' est obligatoire"]);
        exit;
    }
}

try {
    $db = (new Database())->getConnection();
    
    $stmt = $db->prepare("CALL CreateUser(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $data['nom'] ?? '',
        $data['prenom'] ?? '',
        $data['civilite'] ?? 'Iel',
        $data['email'] ?? '',
        $data['password'] ?? '',
        $data['societe_id'] ?? null,
        $data['role_id'] ?? 0,
        $data['telephone'] ?? '',
        $data['num_rue'] ?? '',
        $data['nom_rue'] ?? '',
        $data['complement'] ?? '',
        $data['code_postal'] ?? '',
        $data['ville'] ?? '',
        $data['pays'] ?? ''
    ]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    while ($stmt->nextRowset()) {}
    
    echo json_encode([
        'success' => true, 
        'message' => 'Utilisateur créé avec succès',
        'new_user_id' => $result['new_user_id'] ?? null
    ]);
    
} catch (Exception $e) {
    error_log("Erreur création utilisateur: " . $e->getMessage());
    
    if (strpos($e->getMessage(), 'Duplicate entry') !== false && strpos($e->getMessage(), 'Utilisateur_Mail') !== false) {
        $errorMsg = "Un utilisateur avec cet email existe déjà";
    } else {
        $errorMsg = $e->getMessage();
    }
    
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}