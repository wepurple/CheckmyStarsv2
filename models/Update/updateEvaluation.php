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


$json = file_get_contents('php://input');
$selection = json_decode($json, true);

foreach ($selection as $selection) {
    $stmt = $sql->prepare("call Set_Evaluation(:Critere_ID, :Value, :Commentaire, :Dossier_ID)");
    $stmt->execute([
        $selection['Critere_ID'],
        $selection['Checkbox'],
        $selection['Commentaire'],
        $selection['Dossier_ID']
    ])
};

echo json_encode(["status" => "success"]);

?>