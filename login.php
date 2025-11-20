<?php

    require("includes/mariadb.php");

    $login = $_POST['email'];
    $password = $_POST['password'];

    $sql=("select * from personne where login = :login and MotPasse = :password");
    $requete = $connexion->prepare($sql);
    $requete->bindValue(':login', $login);
    $requete->bindValue(':password', $password);
    $requete->execute();
    $result = $requete->fetch(PDO::FETCH_ASSOC);

    echo(json_encode(array=("test"=>"a", "test2"=>"b")));
?>