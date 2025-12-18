<?php
session_start();

//Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
if(!isset($_SESSION['Role'])){
    header('Location: deco.php');
    die();
} else if(!$_SESSION['Role']['Administrateur']){
    header('Location: deco.php');
    die();
}

function getInstitutionByStar($star) {
    $result = 0;



    return $result;
}

?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion inspecteurs - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/search_inspecteurs.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php        
            require("./includes/navbar.php");
        ?>

        <div class="container-fluid p-3">

        <?php
            for ($x = 1; $x <= 4; $x++) {
                ?>
                    <div class="card" style="width: 18rem;">
                        <div class="card-body text-center">
                            <h5 class="card-title">Critères des ?? étoile</h5>
                            <p class="card-text">?? Critères</p>
                            <div class="row">
                                <div class="col">
                                    <p class="card-text">?? X</p>
                                </div>
                                <div class="col">
                                    <p class="card-text">?? O</p>
                                </div>
                                <div class="col">
                                    <p class="card-text">?? NA</p>
                                </div>
                            </div>
                            </br>
                            <p class="card-text">?? établissement à ?? étoile</p>
                            </br>
                            <a href="#" class="btn btn-primary">Accéder aux critères</a>
                        </div>
                    </div>
                <?php
            }
        ?>

    </body>
</html>