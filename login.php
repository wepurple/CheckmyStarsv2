<?php

    require("includes/mariadb.php");

    $login = $_REQUEST['email'];
    $password = $_REQUEST['password'];

    $sql("select * from personne where login = :login and MotPasse = :password");
    $connection->bindParam(':login', $login);
    $connection->bindParam(':password', $password);
    $connection->prepare();


    echo(json_encode(["test"]));
?>