<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include_once '../../includes/mariadb.php';
    include_once '../user.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $data = json_decode(file_get_contents("php://input"));
    $stmt = $user->infoDossier();

    $tableauClient = ['utilisateur' => []];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $tableauClient['utilisateur'][] = [
            "Utilisateur_ID" => $row["Utilisateur_ID"],
            "Utilisateur_Nom" => $row["Utilisateur_Nom"],
            "Utilisateur_Prenom" => $row["Utilisateur_Prenom"],
            "Utilisateur_Telephone" => $row["Utilisateur_Telephone"],
            "Utilisateur_Mail" => $row["Utilisateur_Mail"],
            "Societe_ID" => $row["Societe_ID"],
            "Societe_Nom" => $row["Societe_Nom"],
            "Nombre_Dossiers" => $row["Nombre_Dossiers"],
            "Status_Global" => $row["Status_Global"]
        ];
    }

    http_response_code(200);
    echo json_encode($tableauClient);

} else {
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée"]);
}