<?php

function verif_connexion($email, $mdp){
        $stmt = get_db()->prepare('SELECT Email, MotPass FROM admin WHERE Email = :email');
        $stmt->execute(['email' => $email]);
        $trouver = $stmt->fetch(PDO::FETCH_ASSOC); 

        if($trouver && password_verify($mdp, $trouver['MotPass'])){
            return true;
        }else{
            return false;
        }
    }






?>