<?php
    // Connection au serveur
    try { //try

        $dns = 'mysql:host=localhost;port=3307;dbname=checkmystars'; //indication sur le SGBDR utilisé et le nom de la base de données qu'on souhaite utilisé
        $utilisateur = 'root'; //identifiant
        $motDePasse = 'password'; //mot de passe

        $connexion = new PDO( $dns, $utilisateur, $motDePasse ); // connnexion à la base de données
        $connexion->exec("USE checkmystars");
        $connexion->query("SET NAMES utf8"); // utilisation de l'encodage utf8 pour les accents et autres
        
    } catch ( Exception $e ) { // capture de l'erreur si il y en a une
        echo "Connexion à MariaDB impossible : ", $e->getMessage(); // affichage de l'erreur survenue lors de l'échec à la connexion
    die(); // arrêt du code

    }
?> 