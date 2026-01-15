<?php
session_start();

// Vérification de l'authentification
if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Inclure la connexion à la base de données
require_once('../../includes/mariadb.php');

try {
    // Récupérer l'ID depuis l'URL
    $id = $_GET['id'] ?? null;
    
    // Validation
    if (empty($id)) {
        throw new Exception('ID manquant');
    }
    
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    // Vérifier si le critère existe
    $checkSql = "SELECT COUNT(*) FROM criteres WHERE Critere_ID = :id";
    $checkStmt = $db->prepare($checkSql);
    $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    
    if ($checkStmt->fetchColumn() == 0) {
        throw new Exception('Critère introuvable');
    }
    
    // Supprimer le critère
    $sql = "DELETE FROM criteres WHERE Critere_ID = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Critère supprimé avec succès',
            'id' => $id
        ]);
    } else {
        throw new Exception('Échec de la suppression');
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>
