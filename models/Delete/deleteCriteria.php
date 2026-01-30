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
    
    // Démarrer une transaction pour garantir l'intégrité
    $db->beginTransaction();
    
    try {
        // Vérifier si le critère existe
        $checkSql = "SELECT COUNT(*) FROM criteres WHERE Critere_ID = :id";
        $checkStmt = $db->prepare($checkSql);
        $checkStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $checkStmt->execute();
        
        if ($checkStmt->fetchColumn() == 0) {
            throw new Exception('Critère introuvable');
        }
        
        // Étape 1 : Supprimer d'abord les enregistrements liés dans la table contient
        $deleteLinksSql = "DELETE FROM contient WHERE Critere_ID = :id";
        $deleteLinksStmt = $db->prepare($deleteLinksSql);
        $deleteLinksStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteLinksStmt->execute();
        
        $deletedLinks = $deleteLinksStmt->rowCount();
        
        // Étape 2 : Supprimer le critère
        $deleteCriteriaSql = "DELETE FROM criteres WHERE Critere_ID = :id";
        $deleteCriteriaStmt = $db->prepare($deleteCriteriaSql);
        $deleteCriteriaStmt->bindParam(':id', $id, PDO::PARAM_INT);
        $deleteCriteriaStmt->execute();
        
        // Valider la transaction
        $db->commit();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => "Critère supprimé avec succès (ainsi que $deletedLinks lien(s) associé(s))",
            'id' => $id,
            'deleted_links' => $deletedLinks
        ]);
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>
