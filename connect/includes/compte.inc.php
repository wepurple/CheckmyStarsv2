<?php

require('includes/connect.php');
        $sql = "select * from checkmystars where SID = :id_scan"; //préparation de la requête
        //debug_show($sql);
        $requete = $connection->prepare($sql);
        $requete->bindValue(':id_scan', $sid);
        $requete->execute();
        $result = $requete->fetch(PDO::FETCH_ASSOC);
        
?>