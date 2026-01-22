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

        <div class="container mt-4 p-3 rounded shadow bg-light text-dark">

            <div class="row">

                <div class="col m-2 p-2 rounded">
                    
                    <h2>Mes informations</h2>

                    <div class="input-group mb-1">

                        <span class="input-group-text">Nom</span>
                        <input id="nom" type="text" class="form-control" placeholder="ex: Macron" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Prénom</span>
                        <input id="prenom" type="text" class="form-control" placeholder="ex: Emmanuel" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Civilité</span>
                        <input id="civilite" type="text" class="form-control" placeholder="ex: Civilité" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Adresse Mail</span>
                        <input id="mail" type="text" class="form-control" placeholder="ex: exemple@mail.com" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Téléphone</span>
                        <input id="tel" type="text" class="form-control" placeholder="ex: 0612345678" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Société</span>
                        <input id="societe" type="text" class="form-control" placeholder="ex: Pedro & Cie" disabled>
                    </div>

                </div>

                <div class="col m-2 p-2 rounded">
                    <h2>Mon adresse</h2>

                    <div class="input-group mb-1">

                        <span class="input-group-text">Numéro de rue</span>
                        <input id="numRue" type="text" class="form-control" placeholder="ex: 18" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Nom de voie</span>
                        <input id="nomRue" type="text" class="form-control" placeholder="ex: Rue du moulin" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Complément d'adresse</span>
                        <input id="complement" type="text" class="form-control" placeholder="ex: Bis" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Code Postal</span>
                        <input id="codePost" type="text" class="form-control" placeholder="ex: 75000" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Ville</span>
                        <input id="ville" type="text" class="form-control" placeholder="ex: Paris" disabled>
                    </div>

                    <div class="input-group mb-1">
                        <span class="input-group-text">Pays</span>
                        <input id="pays" type="text" class="form-control" placeholder="ex: France" disabled>
                    </div>

                </div>

            </div>

            <!-- boutons -->
            <div class="row">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">

                    <button type="button" class="btn btn-outline-warning" onclick="edit()">
                        <i class="fa-solid fa-pen-to-square"></i>
                        Modifier mes informations
                    </button>

                    <button id="valider" type="button" class="btn btn-outline-success" onclick="" disabled>
                        <i class="fa-solid fa-check"></i>
                        Valider
                    </button>

                </div>
            </div>

        </div>

    </body>
</html>