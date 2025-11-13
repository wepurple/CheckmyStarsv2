<?php
function get_db(){
    // config.php
    $host = '172.20.33.6';
    $db   = 'checkmystars';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';

    $dsn = "MariaDB:host=$host;dbname=$db;charset=$charset"; //à voir où mettre la base de donnée sql

    try 
        {
        $pdo = new PDO($dsn, $user, $pass);
        } 
    catch (\PDOException $e) 
        {
        die('Erreur : ' . $e->getMessage());
        }
}