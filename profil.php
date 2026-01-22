<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(!isset($_SESSION['Role'])){
        header('Location: deco.php');
        die();
    }

    //var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mon profil - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
        
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/profil.js"></script>
    </head>

    <body class="bg-secondary">
        <?php        
            require("./includes/navbar.php");
        ?>

        <div class="container mt-4 p-3 bg-dark rounded shadow">
            <div class="row">
                <div class="col m-2 p-2 bg-secondary border rounded">
                    <h2><u>Mes informations</u></h2>
                </div>
                <div class="col m-2 p-2 bg-secondary border rounded">
                    <h2><u>Mon adresse</u></h2>
                </div>
            </div>
        </div>

    </body>
</html>