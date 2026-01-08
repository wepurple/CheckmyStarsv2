<?php
error_reporting(0);
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Gérer les requêtes OPTIONS pour CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include_once '../../includes/mariadb.php';
    include_once '../crud/user.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $data = json_decode(file_get_contents("php://input"));

    // Debug logging
    error_log("Données reçues: " . print_r($data, true));

    if (!empty($data->Nom) && !empty($data->Prenom) && !empty($data->Civilite) && !empty($data->Telephone) && !empty($data->Email) && !empty($data->AdresseNum) && !empty($data->AdresseNom) && !empty($data->CodePostal) && !empty($data->Ville) && !empty($data->Pays) && !empty($data->Societe) && !empty($data->MotPasse)) {
        
        $user->Nom = $data->Nom;
        $user->Prenom = $data->Prenom;
        $user->Civilite = $data->Civilite;
        $user->Telephone = $data->Telephone;
        $user->Email = $data->Email;
        $user->AdresseNum = $data->AdresseNum;
        $user->AdresseNom = $data->AdresseNom;
        $user->Complement = $data->Complement;
        $user->CodePostal = $data->CodePostal;
        $user->Ville = $data->Ville;
        $user->Pays = $data->Pays;
        $user->Societe = $data->Societe;
        $user->MotPasse = $data->MotPasse;
        
        $idAdresse = $user->creerAdresse();
        $user->idAdresse = $idAdresse;

        if ($idAdresse) {
            if($user->creer()){
                http_response_code(201);
                echo json_encode(array("success" => true, "message" => "Utilisateur créé avec succès."));
            } else {
                http_response_code(500);
                echo json_encode(array("success" => false, "message" => "Erreur lors de la création de l'utilisateur."));
            }
        } else {
            http_response_code(503);
            echo json_encode(array("success" => false, "message" => "Impossible de créer l'adresse."));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("success" => false, "message" => "Erreur, des données obligatoires sont manquantes."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "La méthode n'est pas autorisée."));
}
/*
{
    "Nom" : "tg",
    "Prenom" : "tg",
    "Civilite" : "Monsieur",
    "Telephone" : "0791919191",
    "Email" : "tg.tg.tg",
    "AdresseNum" : "14",
    "AdresseNom" : "rue du bazouzou",
    "Complement" : "12",
    "CodePostal" : "45000",
    "Ville" : "Orléans",
    "Pays": "France",
    "Societe" : "TerenceInc",
    "MotPasse" : "tg"
}
 */
