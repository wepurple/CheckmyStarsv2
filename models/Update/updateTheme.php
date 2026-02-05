<?php
/**
 * Utilisable par n'importe quel utilisateur connecté
 * Accepte un paramètres via la méthode post
 *      theme : nouveau theme
 * Change le theme préféré de l'utilisateur
 * 
 * By Pedro
 */
session_start();

try{
    if(isset($_SESSION['ID'])){
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Max-Age: 3600");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            include_once '../../includes/mariadb.php';
            include_once '../user.php';

            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);
            $id = $_SESSION['ID'];

            $data = json_decode(file_get_contents("php://input"));
            $theme = strip_tags(htmlspecialchars($data->theme));

            try{
                $user->updateTheme($id, $theme);
                $_SESSION['Theme'] = $theme;

                http_response_code(200);
                echo json_encode(["response" => "Thème modifié avec succès"]);
            }catch (Exception $e){
                http_response_code(400);
                echo json_encode(["response" => "Erreur MariaDB"]);
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