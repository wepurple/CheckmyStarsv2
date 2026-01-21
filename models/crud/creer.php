<?php /*
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
*/
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include_once '../../includes/mariadb.php';
    include_once '../crud/user.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $data = json_decode(file_get_contents("php://input"));

    // Debug logging
    error_log("Données reçues: " . print_r($data, true));

    if (!empty($data->AdresseNum) && !empty($data->AdresseNom) && !empty($data->Complement) && !empty($data->CodePostal) && !empty($data->Ville) && !empty($data->Pays) && !empty($data->Nom) && !empty($data->Prenom) && !empty($data->Telephone) && !empty($data->Email) && !empty($data->Societe) && !empty($data->Civilite)) {
        
        //adresse
        $user->AdresseNum = htmlspecialchars(strip_tags($data->AdresseNum));
        $user->AdresseNom = htmlspecialchars(strip_tags($data->AdresseNom));
        $user->Complement = htmlspecialchars(strip_tags($data->Complement));
        $user->CodePostal = htmlspecialchars(strip_tags($data->CodePostal));
        $user->Ville = htmlspecialchars(strip_tags($data->Ville));
        $user->Pays = htmlspecialchars(strip_tags($data->Pays));

        //le reste
        $user->Nom = htmlspecialchars(strip_tags($data->Nom));
        $user->Prenom = htmlspecialchars(strip_tags($data->Prenom));
        $user->Telephone = htmlspecialchars(strip_tags($data->Telephone));
        $user->Email = htmlspecialchars(strip_tags($data->Email));
        $user->Societe = htmlspecialchars(strip_tags($data->Societe));
        $user->Civilite = htmlspecialchars(strip_tags($data->Civilite));

        //les trucs qui se remplissent différemment
        $user->Signature = "Null";
        $user->MotPasse = password_hash("pass123", PASSWORD_DEFAULT);

        if($user->creer()){
            http_response_code(200);
            echo json_encode(["message" => "Ajout effectué"]);
            
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Erreur lors de l'ajout"]);
        }

    } else {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Erreur, des données obligatoires sont manquantes."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "La méthode n'est pas autorisée."));
}
?>