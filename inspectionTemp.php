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
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion inspecteurs - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/search_inspecteurs.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php        
            require("./includes/navbar.php");
        ?>

        <div class="container-fluid p-3">

            <!-- Tableau -->
            <table class="table table-dark table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>N°</th>
                        <th>Critère</th>
                        <th>Status</th>
                        <th>Points</th>
                        <th>Validation</th>
                        <th>Commentaire</th>
                        <th>Photo</th>
                    </tr>
                </thead>
            </table>
            <!-- End Tableau -->

        </div>
    </body>
</html>