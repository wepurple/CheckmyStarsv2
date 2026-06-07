<?php
ini_set('display_errors', 0);
error_reporting(0);

class Database {
    private $connexion;
    private $host;
    private $port;
    private $database;
    private $utilisateur;
    private $motDePasse;

    public function __construct() {
        $this->host = getenv('DB_HOST') ?: '127.0.0.1';
        $this->port = getenv('DB_PORT') ?: '8889';
        $this->database = getenv('DB_NAME') ?: 'checkmystars3';
        $this->utilisateur = getenv('DB_USER') ?: 'root';
        $this->motDePasse = getenv('DB_PASSWORD') ?: 'root';
    }

    public function getConnection() {
        $this->connexion = null;
        $dns = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            $this->host,
            $this->port,
            $this->database
        );

        try {
            $this->connexion = new PDO(
                $dns,
                $this->utilisateur,
                $this->motDePasse,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            $this->connexion = [
                0 => false,
                1 => mb_convert_encoding($e->getMessage(), "UTF-8")
            ];
        }

        return $this->connexion;
    }
}
?>