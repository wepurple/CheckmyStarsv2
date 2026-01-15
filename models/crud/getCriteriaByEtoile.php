<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    http_response_code(403);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

require_once '../../includes/db.php'; // ← Adapte le chemin

$star = isset($_GET['star']) ? (int)$_GET['star'] : 1;

try {
    $pdo = getConnection();
    
    $sql = "
        SELECT DISTINCT 
            c.Critere_ID,
            c.Critere_description,
            c.Critere_statut,
            c.Critere_points
        FROM listescriteres_etoiles lce
        JOIN contient co ON co.ListesCriteres_ID = lce.ListesCriteres_ID
        JOIN criteres c ON c.Critere_ID = co.Critere_ID
        WHERE lce.type_hebergement_id = 2 
          AND lce.etoile = :star
        ORDER BY c.Critere_ID
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['star' => $star]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($data);
    
} catch(PDOException $e) {
    http_response_code(500);
    error_log("Erreur SQL: " . $e->getMessage());
    echo json_encode(['error' => 'Erreur serveur']);
}