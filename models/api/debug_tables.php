<?php
require_once __DIR__ . '/helpers.php';

try {
    $pdo = getDbConnection();
    
    // Vérifier la structure de la table devis
    $stmt = $pdo->query("SHOW COLUMNS FROM devis");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Colonnes de la table 'devis':</h2>";
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    // Vérifier la structure de la table devis_client
    $stmt = $pdo->query("SHOW COLUMNS FROM devis_client");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Colonnes de la table 'devis_client':</h2>";
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage();
}
?>
