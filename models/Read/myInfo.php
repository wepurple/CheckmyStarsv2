<?php
/**
 * Utilisable par n'importe quel utilisateur connecté
 * Renvoie la liste de ses informations
 * 
 * By Pedro
 */
session_start();

if(isset($_SESSION['ID'])){//renvoie les informations relatives à un utilisateur à partir de l'id stocké dans la variable de session
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
        $stmt = $user->afficherUtilisateur($data);

            if($stmt->rowCount() > 0){
            $tableauClient = [];
            $tableauClient['utilisateur'] = [];
            while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                extract($row);

                $liste = [
                    "Utilisateur_ID" => $Utilisateur_ID,
                    "Utilisateur_Nom" => $Utilisateur_Nom,
                    "Utilisateur_Prenom" => $Utilisateur_Prenom,
                    "Utilisateur_Civilite" => $Utilisateur_Civilite,
                    "Utilisateur_Telephone" => $Utilisateur_Telephone,
                    "Utilisateur_Mail" => $Utilisateur_Mail,
                    "Utilisateur_Signature" => $Utilisateur_Signature,
                    "AdressePostale_NumeroRue" => $AdressePostale_NumeroRue,
                    "AdressePostale_Complement" => $AdressePostale_Complement,
                    "AdressePostale_CodePostal" => $AdressePostale_CodePostal,
                    "Utilisateur_Societe" => $Societe_ID,
                    "AdressePostale_NomRue" => $AdressePostale_NomRue,
                    "AdressePostale_Ville" => $AdressePostale_Ville,
                    "AdressePostale_Pays" => $AdressePostale_Pays,
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
}else{
    header('deco.php');
    die();
}