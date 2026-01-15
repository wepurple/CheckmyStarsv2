<?php
// Désactive l'affichage des erreurs (elles vont dans les logs)
ini_set('display_errors', 0);
error_reporting(E_ALL);

session_start();

// Vérifie les droits
if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

// Chemin vers ta connexion DB (adapte-le)
require_once __DIR__ . '/../../includes/db.php';

header('Content-Type: application/json');

$star = isset($_GET['star']) ? (int)$_GET['star'] : 1;

try {
    $pdo = getConnection();
    
    // TA requête SQL qui fonctionnait
    $sql = "
        SELECT DISTINCT c.Critere_ID, c.Critere_description, c.Critere_statut, c.Critere_points
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
    
} catch(Exception $e) {
    error_log("getCriteriaByEtoile.php erreur: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur']);
}