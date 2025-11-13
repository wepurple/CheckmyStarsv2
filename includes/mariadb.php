<?php
// Connection au serveur
try { //try

    $dns = 'mariadb:host=172.20.33.6;dbname=CheckMyStars'; //indication sur le SGBDR utilisé et le nom de la base de données qu'on souhaite utilisé
    $utilisateur = 'root'; //identifiant
    $motDePasse = ''; //mot de passe

    $connection = new PDO( $dns, $utilisateur, $motDePasse ); // connnexion à la base de données
    $connection->exec("USE CheckMyStars");
    $connection->query("SET NAMES utf8"); // utilisation de l'encodage utf8 pour les accents et autres
    
} catch ( Exception $e ) { // capture de l'erreur si il y en a une
    echo "Connection à MariaDB impossible : ", $e->getMessage(); // affichage de l'erreur survenue lors de l'échec à la connexion
die(); // arrêt du code

}
?> 