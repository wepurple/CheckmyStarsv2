<?php

    include("includes/mariadb.php");

    $login = $_POST['email'];
    $password = $_POST['password'];

    $sql="select * from personne where login = :login and MotPasse = :password";
    $db = new Database();
    $requete = $db->getConnection();
    var_dump($requete);

    $requete->prepare($sql);
    //$requete->bindValue(':login', $login, PDO::PARAM_STR);
    //$requete->bindValue(':password', $password, PDO::PARAM_STR);
    $requete->execute();

    $result = $requete->fetch(PDO::FETCH_ASSOC);
    var_dump($result);

    echo(json_encode(array("test"=>"a", "test2"=>"b")));
?>