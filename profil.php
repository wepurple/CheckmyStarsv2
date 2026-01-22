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
            echo('<script>id = '.$_SESSION['ID']."</script>");
            require("./includes/navbar.php");
        ?>

        <div class="container mt-4 p-3 rounded shadow bg-light text-dark">

            <div class="row">

                <div class="col m-2 p-2 rounded">
                    
                    <h2>Mes informations</h2>

                    <table class="table table-borderless table-light">
                        <tr>
                            <th scope="row">Nom</th>
                            <td id="nom">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Prénom</th>
                            <td id="prenom">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Civilité</th>
                            <td id="civilite">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Adresse mail</th>
                            <td id="mail">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Téléphone</th>
                            <td id="tel">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Société</th>
                            <td id="societe">Chargement ...</td>
                        </tr>
                    </table>

                </div>

                <div class="col m-2 p-2 rounded">
                    <h2>Mon adresse</h2>

                    <table class="table table-borderless table-light">
                        <tr>
                            <th scope="row">Numéro de rue</th>
                            <td id="numRue">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Nom de la rue</th>
                            <td id="nomRue">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Complément d'adresse</th>
                            <td id="complement">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Code postal</th>
                            <td id="codePost">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Ville</th>
                            <td id="ville">Chargement ...</td>
                        </tr>
                        <tr>
                            <th scope="row">Pays</th>
                            <td id="pays">Chargement ...</td>
                        </tr>
                    </table>
                </div>

            </div>

        </div>

    </body>
</html>