<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(isset($_SESSION['Role']['Administrateur']) || isset($_SESSION['Role']['Inspecteur'])){
        if(!$_SESSION['Role']['Administrateur'] || !$_SESSION['Role']['Inspecteur']){
            header('Location: deco.php');
            die();
        }
    } else {
        header('Location: deco.php');
        die();
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
        <?php 
            require("./includes/navbar.php");


            $dossierId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            $utilisateurId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
            
            


        ?>
        
        <div>
            <div>
                <button type="button" class="btn btn-success" onclick="window.location.href='front_dossier.php?id=<?php echo $utilisateurId ; ?>'">
                <i class="bi bi-arrow-return-left"></i>
                </button>

                <div class="box-title text-center">
                    <h2>Dossier en cours : <?php echo $dossierId; ?></h2>
                </div>
            </div>
            <div>

            </div>
        </div>
    </body>