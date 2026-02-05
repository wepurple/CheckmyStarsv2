<?php

session_start();

//renvoie un boolean indiquant si l'utilisateur se connecte pour la premire fois
if(isset($_SESSION['ID'])){
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    if($_SERVER['REQUEST_METHOD'] == 'GET'){
        include_once '../../includes/mariadb.php';
        include_once('../../models/user.php');

        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        $data = json_decode($_SESSION['ID']);
        $stmt = $user->getFirstLog($data);

        if($stmt->rowCount() > 0){
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            http_response_code(200);
            echo json_encode($result);
        }
    } else {
        http_response_code(405);
        echo json_encode(["message" => "La méthode n'est pas autorisée"]);
    }
}else{
    header('deco.php');
    die();
}