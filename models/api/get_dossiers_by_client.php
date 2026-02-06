<?php
session_start();
require_once(__DIR__ . '/../../includes/mariadb.php');

// Vérifier si l'utilisateur est connecté
if(!isset($_SESSION['Role'])){
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

// Vérifier que l'ID client est fourni
if(!isset($_GET['client_id']) || empty($_GET['client_id'])){
    http_response_code(400);
    echo json_encode(['error' => 'ID client requis']);
    exit();
}

$client_id = intval($_GET['client_id']);

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (is_array($db)) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur de connexion à la base de données']);
        exit();
    }
    
    // Requête pour récupérer les dossiers du client
    $sql = "SELECT Dossier_ID, Dossier_Numero 
            FROM dossiers 
            WHERE Utilisateur_ID = :client_id 
            ORDER BY Dossier_Numero DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $dossiers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($dossiers);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}
?>
