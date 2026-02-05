<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(!isset($_SESSION['Role']) || !$_SESSION["first_log"]){
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
        <link rel="icon" type="image/x-icon" href="assets/pictures/logosm.png">
        
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/setPassword.js"></script>
    </head>

    <body class="bg-secondary p-3">

        <!-- conteneur -->
        <div class="container mt-2 mt-lg-5">

            <div class="row justify-content-center">

                <!-- formulaire -->
                <div class="col-sm-4 col-md-5 p-5 text-center rounded shadow bg-light-subtle">
                    
                    <h2>Définir mon mot de passe</h2>

                    <div class="input-group mt-4">
                        <span class="input-group-text">Mot de passe</span>
                        <input id="password" type="password" class="form-control">
                    </div>

                    <div class="input-group mt-3">
                        <span class="input-group-text">Confirmation</span>
                        <input id="confirm" type="password" class="form-control">
                    </div>

                    <div class="form-text text-start mt-2">
                        <ul class="">
                            <li>une majuscule</li>
                            <li>une minuscule</li>
                            <li>un chiffre</li>
                            <li>12 caractères minimum</li>
                            <li>1 caractère spécial</li>
                        </ul>
                    </div>

                    <!-- boutons -->
                    <div class="row mt-4">
                        <div class="d-grid gap-2 d-lg-flex justify-content-md-end">

                            <button id="validerButton" type="button" class="btn btn-outline-success" onclick="valider()">
                                <i class="fa-solid fa-check"></i>
                                Valider
                            </button>

                        </div>
                    </div>

                </div>


            </div>


        </div>


        <!-- Toast -->
            <div class="toast-container position-fixed top-0 end-0 me-2 mt-5 pt-5">
                <div id="toast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div id="toastText" class="toast-body">
                            Lorem Ipsum
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        <!-- fin toast -->

    </body>
</html>