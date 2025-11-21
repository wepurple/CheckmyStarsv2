<?php
    // Connection au serveur
    class Database {
        private $host = "localhost";
        private $connexion;
        private $dns = 'mysql:host=localhost;port=3307;dbname=checkmystars';
        private $utilisateur = 'root';
        private $motDePasse = 'password';

        public function getConnection() {
            $this->connexion = null;

            try {
                $this->connexion = new PDO($this->dns, $this->utilisateur, $this->motDePasse);
                $this->connexion->exec("USE checkmystars");
                $this->connexion->query("SET NAMES utf8");
            } catch (PDOException $e) {
                $this->connexion = (array('0'=>false, '1' => mb_convert_encoding($e->getMessage(), "UTF-8")));
            }
            return $this->connexion;
        }
    }
?>