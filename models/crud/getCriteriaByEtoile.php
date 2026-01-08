<?php
session_start();

if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    http_response_code(403);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

include("../../includes/mariadb.php");

$database = new Database();
$db = $database->getConnection();

$star = isset($_GET['star']) ? (int)$_GET['star'] : 0;

if ($star < 1 || $star > 5) {
    http_response_code(400);
    echo json_encode(['error' => 'Etoile invalide']);
    exit;
}

$sql = "
    SELECT DISTINCT c.Critere_ID, c.Critere_description, c.Critere_statut, c.Critere_points
    FROM listescriteres_etoiles lce
    JOIN contient co ON co.ListesCriteres_ID = lce.ListesCriteres_ID
    JOIN criteres c ON c.Critere_ID = co.Critere_ID
    WHERE lce.type_hebergement_id = 2
      AND lce.etoile = :star
    ORDER BY c.Critere_ID
";

$stmt = $db->prepare($sql);
$stmt->execute(['star' => $star]);

$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($data);
