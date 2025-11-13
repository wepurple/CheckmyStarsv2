<?php
// config.php
$host = 'localhost';
$db   = 'secure_portal';   // à adapter
$user = 'root';            // à adapter
$pass = '';                // à adapter
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset"; //a voir ou mettre la base de donnée sql

$options = [
   PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // exceptions
   PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
   PDO::ATTR_EMULATE_PREPARES   => false,                 // vraies requêtes préparées
];

try 
    {
    $pdo = new PDO($dsn, $user, $pass, $options);
    } 
catch (\PDOException $e) 
    {
    die('Erreur : ' . $e->getMessage());
    }

