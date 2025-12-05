<?php
    session_start();

    //var_dump($_POST);

    include("includes/mariadb.php");

    $sql="select
    utilisateurs.Utilisateur_ID,
    Utilisateur_Nom,
    Utilisateur_Prenom,
    Utilisateur_Civilite,
    Utilisateur_Societe
    from utilisateurs inner join inspecteurs ON utilisateurs.Utilisateur_ID = inspecteurs.Utilisateur_ID";
    $db = new Database();

    $requete = $db->getConnection();
    //var_dump($requete);

    $requete = $requete->prepare($sql);
    $requete->execute();

    $result = $requete->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($result);
    echo(json_encode($result));

?>