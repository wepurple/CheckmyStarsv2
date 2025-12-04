<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Respond to CORS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    include_once '../../includes/mariadb.php';
    include_once '../crud/user.php';

    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    $data = json_decode(file_get_contents("php://input"));

    $errors = array();

    $required = ['Nom','Prenom','Civilite','Telephone','Email','AdresseNum','AdresseNom','CodePostal','Ville','Pays','Societe','MotPasse'];
    foreach ($required as $field) {
        if (empty($data->{$field}) && $data->{$field} !== "0") {
            $errors[$field] = 'Champ requis.';
        }
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(array('message' => 'Validation failed', 'errors' => $errors));
        exit;
    }

    if (!preg_match('/^[\p{L} \-\']+$/u', $data->Nom)) {
        $errors['Nom'] = 'Nom invalide.';
    }
    if (!preg_match('/^[\p{L} \-\']+$/u', $data->Prenom)) {
        $errors['Prenom'] = 'Prenom invalide.';
    }

    $allowedCivilite = ['Monsieur','Madame'];
    if (!in_array($data->Civilite, $allowedCivilite)) {
        $errors['Civilite'] = 'Civilité invalide.';
    }

    if (!preg_match('/^[0-9]{10}$/', $data->Telephone)) {
        $errors['Telephone'] = 'Telephone invalide (10 chiffres).';
    }

    if (!filter_var($data->Email, FILTER_VALIDATE_EMAIL)) {
        $errors['Email'] = 'Adresse email invalide.';
    }

    if (!preg_match('/^[0-9]+$/', $data->AdresseNum)) {
        $errors['AdresseNum'] = 'Numero de rue invalide.';
    }

    if (empty(trim($data->AdresseNom))) {
        $errors['AdresseNom'] = 'Nom de rue requis.';
    }

    if (!preg_match('/^[0-9]{5}$/', $data->CodePostal)) {
        $errors['CodePostal'] = 'Code postal invalide (5 chiffres).';
    }

    if (empty(trim($data->Ville))) {
        $errors['Ville'] = 'Ville requise.';
    }

    if (empty(trim($data->Pays))) {
        $errors['Pays'] = 'Pays requis.';
    }

    if (empty(trim($data->Societe))) {
        $errors['Societe'] = 'Societe requise.';
    }

    if (strlen($data->MotPasse) < 8) {
        $errors['MotPasse'] = 'Mot de passe trop court (8 caractères minimum).';
    }

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(array('message' => 'Validation failed', 'errors' => $errors));
        exit;
    }

    try {
        $sqlCheck = "SELECT COUNT(*) FROM utilisateurs WHERE Utilisateur_Mail = ?";
        $stmtCheck = $db->prepare($sqlCheck);
        $stmtCheck->execute([$data->Email]);
        $count = $stmtCheck->fetchColumn();
        if ($count > 0) {
            http_response_code(409);
            echo json_encode(array('message' => 'Adresse email déjà utilisée.'));
            exit;
        }
    } catch (Exception $e) {

    }

    $user->Nom = $data->Nom;
    $user->Prenom = $data->Prenom;
    $user->Civilite = $data->Civilite;
    $user->Telephone = $data->Telephone;
    $user->Email = $data->Email;
    $user->AdresseNum = $data->AdresseNum;
    $user->AdresseNom = $data->AdresseNom;
    $user->Complement = isset($data->Complement) ? $data->Complement : null;
    $user->CodePostal = $data->CodePostal;
    $user->Ville = $data->Ville;
    $user->Pays = $data->Pays;
    $user->Societe = $data->Societe;
    $user->MotPasse = password_hash($data->MotPasse, PASSWORD_DEFAULT);
    
    $idAdresse = $user->creerAdresse();
    $user->idAdresse = $idAdresse;

    if ($idAdresse) {
        if($user->creer()){
            http_response_code(201);
            echo json_encode(array("message" => "Utilisateur créé avec succès."));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Impossible de créer l'utilisateur."));
        }
    } else {
        http_response_code(503);
        echo json_encode(array("message" => "Impossible de créer l'adresse."));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "La methode n'est pas autorisé."));
}

/*
http://172.20.33.6/checkmystars/models/crud/creer.php
{
    "Nom" : "tg",
    "Prenom" : "tg",
    "Civilite" : "Monsieur",
    "Telephone" : "0791919191",
    "Email" : "inspesdect.dupont@example.com",
    "AdresseNum" : "14",
    "AdresseNom" : "rue du bazouzou",
    "Complement" : "12",
    "CodePostal" : "45000",
    "Ville" : "Orléans",
    "Pays": "France",
    "Societe" : "TerenceInc",
    "MotPasse" : "mdr"
}
 */