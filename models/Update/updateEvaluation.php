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

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}
