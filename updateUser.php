<?php
/**
 * Utilisable par n'importe quel utilisateur connecté
 * Accepte plusieurs paramètres via la méthode post pour mettre à jour les informations d'un utilisateur
 * 
 * By Pedro
 */
session_start();
try{
    if(isset($_SESSION['ID'])){//renvoie les informations relatives à un utilisateur à partir de l'id stocké dans la variable de session
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            include_once '../../includes/mariadb.php';
            include_once '../crud/user.php';
            $data = json_decode(file_get_contents("php://input"));

            if(isset($data->)){
                $database = new Database();
                $db = $database->getConnection();
                $user = new User($db);
                $id = $_SESSION['ID'];
            }else{
                http_response_code(405);
                echo json_encode(["response" => "Formulaire incomplet"]);
            }

            
        } else {
            http_response_code(405);
            echo json_encode(["response" => "La méthode n'est pas autorisée"]);
        }
    }else{
        header('deco.php');
        die();
    }
} catch (Exception $e){
    http_response_code(406);
    echo json_encode(["response" => "Erreur : ".$e]);
}