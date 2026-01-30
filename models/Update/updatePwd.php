<?php
/**
 * Utilisable par n'importe quel utilisateur connecté
 * Accepte deux paramètres vie la méthode post
 *      old : ancien mot de passe
 *      new : nouveau mot de passe
 * Compare l'ancien mot de passe avec le hash actuel, puis hash le nouveau mot de passe s'il est assez robuste avant de l'attribuer à l'utilisateur
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
            include_once '../user.php';

            $database = new Database();
            $db = $database->getConnection();
            $user = new User($db);
            $id = $_SESSION['ID'];
            $stmt = $user->getPassword($id);

            if($stmt->rowCount() == 1){//si un mdp est associé à l'identifiant de la personne connectée (si ce n'est pas le cas c'est inquiétant)
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();
                $data = json_decode(file_get_contents("php://input"));
                $regex = "/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{12,}$/";

                if(isset($data->new) && isset($data->old)){
                    if(password_verify($data->old, $result[0]["pwd"])){
                        if(preg_match($regex, $data->new)){
                            $user->editPassword($id, password_hash($data->new, PASSWORD_DEFAULT));
                            http_response_code(200);
                            echo json_encode(["response" => "Mot de passe modifié avec succès"]);
                        }else{//si le mot de passe ne respecte pas le regex
                            http_response_code(403);
                            echo json_encode(["response" => "Nouveau mot de passe trop faible"]);
                        }
                    }else{//si l'ancien mot de passe n'est pas valide
                        http_response_code(401);
                        echo json_encode(["response" => "Ancien mot de passe incorrect"]);
                    }
                }else{
                    http_response_code(401);
                    echo json_encode(["response" => "Formulaire incomplet"]);
                }

                /*
                http_response_code(200);
                echo json_encode($result);*/
            }else{
                http_response_code(401);
                echo json_encode(["response" => "Mot de passe introuvable"]);
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