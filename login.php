<?php

    include("includes/mariadb.php");

    $login = trim(strip_tags($_POST['email']));
    $password = trim(strip_tags($_POST['password']));

    $sql="select * from personne where email = :login and MotPasse = :password";
    $db = new Database();

    $requete = $db->getConnection()->prepare($sql);
    $requete->bindValue(':login', $login);
    $requete->bindValue(':password', $password);
    $requete->execute();

    $result = $requete->fetch(PDO::FETCH_ASSOC);

    echo(json_encode($result));
?>