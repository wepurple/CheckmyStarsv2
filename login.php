<?php
// config.php
$host = '172.20.33.9';
$db   = 'secure_portal';   // à adapter
$user = 'root';            // à adapter
$pass = '';                // à adapter
$charset = 'utf8mb4';

$dsn = "MariaDB:host=$host;dbname=$db;charset=$charset"; //à voir où mettre la base de donnée sql

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