<?php
    session_start();

    //var_dump($_POST);

    include("includes/mariadb.php");

    $sql='select
    utilisateurs.Utilisateur_ID,
    Utilisateur_Nom,
    Utilisateur_Prenom,
    Utilisateur_Civilite,
    (select Societe_nom from societes where societes.Societe_ID = utilisateurs.Societe_ID) as "Utilisateur_Societe"
    from utilisateurs
    inner join inspecteurs
    ON utilisateurs.Utilisateur_ID = inspecteurs.Utilisateur_ID';

    $v = false;
    if (isset($_POST['value']) && $_POST['value']!=""){
        if (isset($_POST['type'])){
            $type = strip_tags(htmlspecialchars($_POST['type']));
            switch($type){
                case "1":
                    $critere="utilisateurs.Utilisateur_ID";
                    break;
                case "2":
                    $critere="utilisateurs.Utilisateur_Nom";
                    break;
                case "3":
                    $critere="utilisateurs.Utilisateur_Societe";
                    break;
                default:
                    $critere="utilisateurs.Utilisateur_ID";
            }
            $sql .= "\nwhere $critere LIKE :value";
            $v = true;
        }
    }

    $db = new Database();

    $requete = $db->getConnection();
    //var_dump($requete);

    $requete = $requete->prepare($sql);

    if($v){//bind seulement si on a un critere de recherche
        $requete->bindValue(':value', "%".htmlspecialchars($_POST['value'])."%", PDO::PARAM_STR);
    }

    //$requete->debugDumpParams();
    $requete->execute();

    $result = $requete->fetchAll(PDO::FETCH_ASSOC);

    //var_dump($result);
    echo(json_encode($result));

?>