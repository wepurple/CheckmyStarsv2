<?php
session_start();

// Vérification de l'authentification
if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Inclure la connexion à la base de données
require_once('../../includes/mariadb.php');

?>