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

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupérer les données
    $id = $_POST['id'] ?? null;
    $description = $_POST['description'] ?? null;
    $statut = $_POST['statut'] ?? null;
    $points = $_POST['points'] ?? null;
    
    // Validation
    if (empty($id) || empty($description) || empty($statut)) {
        throw new Exception('Données manquantes');
    }
    
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    // Préparer la requête de mise à jour
    $sql = "UPDATE criteres 
            SET Critere_description = :description,
                Critere_statut = :statut,
                Critere_points = :points
            WHERE Critere_ID = :id";
    
    $stmt = $db->prepare($sql);
    
    // Bind des paramètres
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':description', $description, PDO::PARAM_STR);
    $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
    
    // Gérer les points qui peuvent être NULL
    if (empty($points)) {
        $stmt->bindValue(':points', null, PDO::PARAM_NULL);
    } else {
        $stmt->bindParam(':points', $points, PDO::PARAM_INT);
    }
    
    // Exécuter la requête
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Critère modifié avec succès',
            'id' => $id
        ]);
    } else {
        throw new Exception('Échec de la mise à jour');
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>
