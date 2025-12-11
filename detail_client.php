    session_start();
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dossiers - CheckMyStars</title>

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
                        <select class="form-select" id="inputGroupSelect01">
                            <option selected>--selectionner--</option>
                            <option value="1">Clients</option>
                            <option value="2">En cours</option>
                            <option value="3">Terminé</option>
                        </select>
                        <input id="recherche" type="text" aria-label="Last name" class="form-control">
                    </div>
                </div>
            </nav>
            <a href="#">
                <button>Creer</button>
            </a>
            <!-- Tableau -->
            <table class="table table-dark table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>N° DOSSIER</th>
                        <th>TYPE</th>
                        <th>CLIENT</th>
                        <th>DONNEUR D'ORDRE</th>
                        <th>ADRESSE HÉBERGEMENT</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody></tbody>