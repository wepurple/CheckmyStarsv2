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
                echo "Erreur de connexion : " . $e->getMessage();
            }
            return $this->connexion;
        }
    }
?>