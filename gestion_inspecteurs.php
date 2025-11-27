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

//var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion inspecteurs - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logo.png">
    </head>

    <body>
        <?php        
            require("./includes/navbar.php");
        ?>

        <div class="container-fluid p-5">
            <nav class="navbar bg-body-tertiary">

                <div class ="d-flex flex-row">
                    <form>
                    </form>
                </div>

                <div class="container-fluid d-flex flex-row mb-3">
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="ex : Bordelot" aria-label="Search"/>
                        <button class="btn btn-outline-success" type="submit">Rechercher</button>
                    </form>
                </div>

                <table class="table table-dark table-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Civilité</th>
                            <th>Société</th>
                            <th>Mail</th>
                            <th>Telephone</th>
                            <th>Signature</th>
                            <th>Adresse postale</th>
                        </tr>
                    </thead>

                    <tbody class="table-group-divider">
                        <tr>
                            <th scope="row">1</th>
                            <td><input id="leNom" type="text"/></td>
                            <td><input id="lePrenom" type="text"/></td>
                            <td><input id="laCivilite" type="text"/></td>
                            <td><input id="laSociete" type="text"/></td>
                            <td><input id="leMail" type="mail"/></td>
                        </tr>
                    </tbody>

                    <tbody  class="table-group-divider" id="table-body">
                        <!-- Résultats dynamiques ici -->
                    </tbody>
                </table>

            </nav>
        </div>
    </body>
</html>