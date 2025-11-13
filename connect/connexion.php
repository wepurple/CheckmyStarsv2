<?php 
session_start();

require_once('includes/compte.inc.php');

if(!empty($_POST['email'] && !empty($_POST['mdp']))){ //à vérifier dans index.php si les name sont bons
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    if(!verif_connexion($email, $mdp)){
            echo "<script>
                    alert(\"Erreur de connexion, le email ou le mot de passe est incorrect\");
                    window.location.href = '../formConnAdmin.php';
                </script>";
    }else{ 
        $_SESSION['admin'] = $email;
    }
    
}