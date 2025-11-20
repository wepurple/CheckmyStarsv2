<?php

    require("includes/mariadb.php");
    var_dump($_POST);

    $login = $_POST['email'];
    $password = $_POST['password'];

    $sql=("select * from personne where login = :login and MotPasse = :password");
    $connexion->bindValue($login, ':login');
    $connexion->bindValue($password, ':password');
    $connexion->prepare();


    echo(json_encode(["test"]));
?>