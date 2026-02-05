<?php
/**
 * Utilisable par n'importe quel utilisateur connecté pour la première fois
 * Accepte un paramètres via la méthode post
 *      new : nouveau mot de passe
 * Attribue un nouveau mot de passe à un utilisateur
 * 
 * By Pedro
 */
session_start();

try{
    if(isset($_SESSION['ID']) && $_SESSION['first_log']){
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
            $regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/";

            if(isset($data->new)){
                if(preg_match($regex, $data->new)){
                    try{
                        $user->editPassword($id, password_hash($data->new, PASSWORD_DEFAULT));
                        $_SESSION['first_log'] = false;
                        http_response_code(200);
                        echo json_encode(["response" => "Mot de passe modifié avec succès"]);
                    }catch (Exception $e){
                        http_response_code(400);
                        echo json_encode(["response" => "Erreur MariaDB"]);
                    }
                }else{//si le mot de passe ne respecte pas le regex
                    http_response_code(403);
                    echo json_encode(["response" => "Nouveau mot de passe trop faible"]);
                }
            }else{
                http_response_code(401);
                echo json_encode(["response" => "Formulaire incomplet"]);
            }

            /*
            http_response_code(200);
            echo json_encode($result);*/


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