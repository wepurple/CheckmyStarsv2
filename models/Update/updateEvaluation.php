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
    $Dossier_ID = $_POST['Dossier_ID'] ?? null;
    $textarea = $_POST['Commentaire'] ?? null;
    $Critere_ID = $_POST['Critere_ID'] ?? null;
    $Value = $_POST['Value'] ?? null;

    if (empty($Dossier_ID) || empty($Critere_ID)) {
        throw new Exception('Données manquantes');
    }

    $database = new Database();
    $db = $database->getConnection();
    
    try {
        $sql = "call Set_Evaluation(:Value, :Critere_ID, :Commentaire, :Dossier_ID,";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':Value', $Value, PDO::PARAM_BOOL);
        $stmt->bindParam(':Critere_ID', $Critere_ID, PDO::PARAM_INT);
        if (empty($textarea)) {
            $stmt->bindValue(':Commentaire', null, PDO::PARAM_NULL);
        }
        else {
            $stmt->bindParam(':Commentaire', $textarea, PDO::PARAM_STR);
        }
        $stmt->bindParam(':Dossier_ID', $Dossier_ID, PDO::PARAM_INT);
        $stmt->execute();
        

    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $db->rollBack();
        throw $e; }

} catch (Exception $e) {
    header("Content-Type : application/json");
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la mise à jour de l évaluation"
    ]);
}
?>
