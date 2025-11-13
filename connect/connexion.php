<?php 
session_start();

require_once('includes/compte.inc.php');

if(!empty($_POST['email'] && !empty($_POST['mdp']))){ //à vérifier dans index.php si les name sont bons
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    if(!verif_connexion($email, $mdp)){
        echo    "<script>
                    alert(\"Erreur de connexion\");
                </script>";
    }else{ 
        //$_SESSION['admin'] = $email;
        echo    "<script>
                    alert(\"Connexion réussie\");
                </script>";
    }
    
}