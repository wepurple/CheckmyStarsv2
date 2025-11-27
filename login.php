<?php
    session_start();

    include("includes/mariadb.php");

    $login = trim(strip_tags($_POST['email']));
    $password = trim(strip_tags($_POST['password']));

    $sql="select * from utilisateurs where utilisateur_mail = :login and utilisateur_password = :password";
    $db = new Database();

    $requete = $db->getConnection();
    //var_dump($requete);
    if(!is_array($requete)){
        $requete = $requete->prepare($sql);
        $requete->bindValue(':login', $login);
        $requete->bindValue(':password', $password);
        $requete->execute();

        $result = $requete->fetch(PDO::FETCH_ASSOC);

        //var_dump($result);
        if ($result){
            //on va voir dans les tables .administrateurs et .inspecteurs si l'utilisateur détient les rôles concernés
            $sql = "select * from administrateurs where Utilisateur_ID = :id";
            $requete = $requete->prepare($sql);
            $requete->bindValue(':login', $login);
            $requete->bindValue(':password', $password);
            $requete->execute();
            if($requete->fetch(PDO::FETCH_ASSOC)){$admin = true;}else{$admin = false};
            
            $sql = "select * from inspecteurs where Utilisateur_ID = :id";
            $requete = $requete->prepare($sql);
            $requete->bindValue(':login', $login);
            $requete->bindValue(':password', $password);
            $requete->execute();
            if($requete->fetch(PDO::FETCH_ASSOC)){$inspecteur = true;}else{$inspecteur=false};

            $_SESSION = array(
                "ID"=>$result["Utilisateur_ID"],
                "Nom"=>$result["Utilisateur_Nom"],
                "Prenom"=>$result["Utilisateur_Prenom"],
                "Role"=>array(
                    "Administrateur"=>$admin,
                    "Instepcteur"=>$inspecteur
                ),
                "Telephone"=>$result['Utilisateur_Telephone'],
                "Email"=>$result['Utilisateur_Mail'],
                "Civilite"=>$result['Utilisateur_Civilite']
            );
        }
            echo(json_encode($result));
    } else {
        echo(json_encode($requete));

    }

?>