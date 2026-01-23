<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../includes/mariadb.php';

if (empty($_SESSION['Role']['Administrateur'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Accès refusé - Admin requis']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Méthode POST requise']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Données JSON invalides']);
    exit;
}

// Validation: ne pas utiliser empty() pour role_id (0 est valide)
$required_fields = ['nom', 'prenom', 'email', 'password', 'societe_id', 'role_id'];
foreach ($required_fields as $field) {
    if (!array_key_exists($field, $data)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Le champ '$field' est obligatoire"]);
        exit;
    }
    if (in_array($field, ['nom','prenom','email','password'], true) && trim((string)$data[$field]) === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => "Le champ '$field' est obligatoire"]);
        exit;
    }
}

try {
    $db = (new Database())->getConnection();

    // 15 paramètres (si ta procédure est bien dans cet ordre)
    $stmt = $db->prepare("CALL Create_User(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $data['num_rue'] ?? null,
        $data['nom_rue'] ?? null,
        $data['complement'] ?? null,
        $data['code_postal'] ?? null,
        $data['ville'] ?? null,
        $data['pays'] ?? null,

        $data['nom'],
        $data['prenom'],
        $data['civilite'] ?? 'Iel',

        $data['password'],      // MDP
        $data['email'],         // Mail

        $data['telephone'] ?? null,
        $data['signature'] ?? null,

        (int)$data['societe_id'],
        (int)$data['role_id'],
    ]);

    // Si la procédure fait un SELECT (voir section suivante), on peut fetch ici
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Nettoyage des result sets (utile quand une procédure renvoie plusieurs jeux de résultats)
    while ($stmt->nextRowset()) {} // nextRowset sert à avancer dans les jeux de résultats [web:15][web:12]

    echo json_encode([
        'success' => true,
        'message' => 'Utilisateur créé avec succès',
        'new_user_id' => $result['new_user_id'] ?? null
    ]);

} catch (Exception $e) {
    error_log("Erreur création utilisateur: " . $e->getMessage());

    $errorMsg = $e->getMessage();
    if (str_contains($errorMsg, 'Duplicate entry') && str_contains($errorMsg, 'Utilisateur_Mail')) {
        $errorMsg = "Un utilisateur avec cet email existe déjà";
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $errorMsg]);
}