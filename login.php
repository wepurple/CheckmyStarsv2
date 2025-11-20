<?php

    include("includes/mariadb.php");

    $login = $_POST['email'];
    $password = $_POST['password'];

    $sql=("select * from personne where login = :login and MotPasse = :password");
    $db = new Database();
    $requete = $db->getConnection();

    $requete.getConnection()->prepare($sql);
    $requete.getConnection()->bindValue(':login', $login);
    $requete.getConnection()->bindValue(':password', $password);
    $requete.getConnection()->execute();
    $result = $requete->fetch(PDO::FETCH_ASSOC);

    echo(json_encode(array("test"=>"a", "test2"=>"b")));
?>