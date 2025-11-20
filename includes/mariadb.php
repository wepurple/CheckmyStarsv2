<?php
    // Connection au serveur
class Database {
    private $host = "localhost";

    private $dns = 'mysql:host=localhost;port=3307;dbname=checkmystars'; //indication sur le SGBDR utilisé et le nom de la base de données qu'on souhaite utilisé
    private $utilisateur = 'root'; //identifiant
    private $motDePasse = 'password'; //mot de passe

    public function getConnection() {
        $this->connexion = null;

        try {
            $this->connexion = new PDO( $dns, $utilisateur, $motDePasse ); // connnexion à la base de données
            $this->connexion->exec("USE checkmystars");
            $this->connexion->query("SET NAMES utf8");
        } catch (PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
        return $this->connexion;
    }
}
      
?> 