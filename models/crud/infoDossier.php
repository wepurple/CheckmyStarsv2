<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if($_SERVER['REQUEST_METHOD'] == 'GET'){
    include_once '../../includes/mariadb.php';
    include_once '../crud/user.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $data = json_decode(file_get_contents("php://input"));
    $stmt = $user->afficherUtilisateur();

        if($stmt->rowCount() > 0){
        $tableauClient = [];
        $tableauClient['utilisateur'] = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            extract($row);
 
            $liste = [
                "Utilisateur_ID" => $Utilisateur_ID,
                "Utilisateur_Nom" => $Utilisateur_Nom,
                "Utilisateur_Prenom" => $Utilisateur_Prenom,
                "Utilisateur_Telephone" => $Utilisateur_Telephone,
                "Utilisateur_Mail" => $Utilisateur_Mail,
                "Utilisateurs_Societe" => $Utilisateurs_Societe,
                            ];
            $tableauClient['utilisateur'][] = $liste;
        }
        http_response_code(200);
        echo json_encode($tableauClient);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "La méthode n'est pas autorisée"]);
}

?>
