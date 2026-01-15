<?php
session_start();

if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    header('Location: deco.php');
    die();
}

$star = isset($_GET['star']) ? (int)$_GET['star'] : 1;
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Critères <?= $star ?> étoile - CheckMyStars</title>
    <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
</head>
<body class="bg-secondary">
    <?php require("./includes/navbar.php"); ?>
    
    <!-- Formulaire de recherche -->
    <nav class="navbar">
        <div class="container-fluid d-flex flex-row mb-2">
            <div class="input-group">

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                    Ajouter un critère
                </button>

                <span class="input-group-text">Rechercher par</span>
                    <select class="form-select" id="type">
                        <option selected value="1">ID</option>
                        <option value="2">Description</option>
                        <option value="3">Status</option>
                        <option value="4">Points</option>
                    </select>
                <input id="recherche" type="text" aria-label="Last name" class="form-control">
            </div>
        </div>
    </nav>

    <div class="container-fluid p-3">
        <h2>Critères <?= $star ?> étoile</h2>
        <table class="table table-sm table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Points</th>
                </tr>
            </thead>
            <tbody class="table-group-divider" id="table-body">
                <!-- Rempli par JS -->
            </tbody>
        </table>
    </div>

    <script src="js/criteriaStarBack.js"></script>
</body>
</html>
