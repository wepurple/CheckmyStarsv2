<?php
class Database {
    private $host = "localhost";
    private $connexion;
    private $dns = "mysql:host=localhost;port=3307;dbname=checkmystars3;charset=utf8mb4";
    private $utilisateur = "root";
    private $motDePasse = "password";

    public function getConnection() {
        $this->connexion = null;

        try {
            $this->connexion = new PDO(
                $this->dns,
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