<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include_once '../../includes/mariadb.php';
    include_once '../crud/User.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->Nom) && !empty($data->Prenom) && !empty($data->Civilite) && !empty($data->Telephone) && !empty($data->Email) && !empty($data->Adresse) && !empty($data->Complement) && !empty($data->CodePostal) && !empty($data->Ville) && !empty($data->Pays) && !empty($data->Societe) && !empty($data->Role) && !empty($data->Login) && !empty($data->MotDePasse)) {
        
        $user->Nom = $data->Nom;
        $user->Prenom = $data->Prenom;
        $user->Civilite = $data->Civilite;
        $user->Telephone = $data->Telephone;
        $user->Email = $data->Email;
        $user->Adresse = $data->Adresse;
        $user->Complement = $data->Complement;
        $user->CodePostal = $data->CodePostal;
        $user->Ville = $data->Ville;
        $user->Pays = $data->Pays;
        $user->Societe = $data->Societe;
        $user->Role = $data->Role;
        $user->Login = $data->Login;
        $user->MotDePasse = $data->MotDePasse;

        if($user->creer()){
            http_response_code(201);
            echo json_encode(array("message" => "Utilisateur créé avec succès."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Impossible de créer l'utilisateur."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("message" => "La methode n'est pas autorisé."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "La methode n'est pas autorisé."));
}