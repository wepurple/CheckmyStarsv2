<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    /*
    if(!isset($_SESSION['Role'])){
        header('Location: deco.php');
        die();
    } else if($_SESSION['Role']!="Administrateur"){
        header('Location: deco.php');
        die();
    }
        */

    require("./includes/navbar.php");



    var_dump($_SESSION);
?>