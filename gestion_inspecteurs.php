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
        <script src="js/search_inspecteurs.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php        
            require("./includes/navbar.php");
        ?>

        <div class="container-fluid p-3">
            
            <!-- Formulaire de recherche -->
            <nav class="navbar">
                <div class="container-fluid d-flex flex-row mb-2">
                    <div class="input-group">
                        <span class="input-group-text">Rechercher par</span>
                        <select class="form-select" id="type">
                            <option selected value = "1">ID</option>
                            <option value="2">Nom</option>
                            <option value="3">Société</option>
                        </select>
                        <input id="recherche" type="text" aria-label="Last name" class="form-control">
                    </div>
                </div>
            </nav>

            <!-- Tableau -->
            <table class="table table-dark table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Civilité</th>
                        <th>Société</th>
                    </tr>
                </thead>

                <!-- proto pour ajouter des inspecteurs
                <tbody class="table-group-divider">
                    <tr>
                        <td><input id="leBouton" class="btn btn-outline-success" type="button" value="Ajouter"/></td>
                        <td><input id="leNom" type="text"/></td>
                        <td><input id="lePrenom" type="text"/></td>
                        <td><input id="laCivilite" type="text"/></td>
                        <td><input id="laSociete" type="text"/></td>
                        <td><input id="leMail" type="email"/></td>
                        <td><input id="leTel" type="tel"/></td>
                        <td><input id="laSignature" type="text"/></td>
                        <td><input id="lAdresse" type="text"/></td>
                    </tr>
                </tbody>
                -->

                <tbody  class="table-group-divider" id="table-body">
                    <!-- Rempli par js/search_inspecteurs.js -->
                </tbody>
            </table>
        </div>
    </body>
</html>