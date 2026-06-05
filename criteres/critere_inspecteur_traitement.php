<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(isset($_SESSION['Role']['Administrateur']) || isset($_SESSION['Role']['Inspecteur'])){
        if(!$_SESSION['Role']['Administrateur'] || !$_SESSION['Role']['Inspecteur']){
            header('Location: ../deco.php');
            die();
        }
    } else {
        header('Location: ../deco.php');
        die();
    }

    require_once './includes/mariadb.php';

    $database = new Database();
        $db = $database->getConnection();

        var_dump($db);

        // Vérification si la connexion a réussi et est bien un objet
        if (!is_object($db)) {
            die("Erreur de connexion : La base de données n'a pas retourné un objet valide.");
        }

        $dossierId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        $etoileId = filter_input(INPUT_GET, 'etoile', FILTER_SANITIZE_NUMBER_INT);

        var_dump($dossierId);

        if ($dossierId) {
            // Utilisation d'une requête préparée pour éviter les erreurs SQL et les injections
            $stmt = $db->prepare("SELECT Dossier_Numero FROM dossiers WHERE Dossier_ID = :id");
            $stmt->execute(['id' => $dossierId]);
            $numeroDossier = $stmt->fetchColumn();
        } else {
            $numeroDossier = "Inconnu";
        }
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Critères du dossier - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
        
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/search_inspecteurs.js"></script>
    </head>
    <body class="bg-secondary">
    <?php require("./includes/navbar.php"); ?>
        <div class="d-flex align-items-center gap-3 p-3">
            <a href="critere_inspecteur_etoile.php?id=<?php echo $dossierId; ?>" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="bi bi-arrow-left-short fs-3 text-dark"></i>
            </a>
        </div>
