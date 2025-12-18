<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if($_SERVER['REQUEST_METHOD'] == 'DELETE'){
    include_once '../../includes/mariadb.php';
    include_once '../crud/user.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $donnees = json_decode(file_get_contents("php://input"));
    $stmt = $user->afficherUtilisateur();

    if(!empty($donnees->IdPersonne)){
        $user->IdPersonne = $donnees->IdPersonne;

        if($user->supprimerUtilisateur()){
            http_response_code(200);
            echo json_encode(["message" => "La suppression a été effectuée"]);
            
        } else {
            http_response_code(503);
            echo json_encode(["message" => "La suppression n'a pas été effectuée"]);
        }
    }
    else{
        http_response_code(503);
        echo json_encode(["message" => "Données invalide"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée"]);
}
?>