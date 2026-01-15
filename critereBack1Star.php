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

    <div class="search-filter-container" style="margin-bottom: 20px;">
        <select id="filterType" style="padding: 8px; margin-right: 10px;">
            <option value="all">Tous les champs</option>
            <option value="id">ID</option>
            <option value="description">Description</option>
            <option value="status">Status</option>
            <option value="points">Points</option>
        </select>
        
        <input type="text" 
            id="searchBar" 
            placeholder="Rechercher..." 
            style="padding: 8px; width: 300px;">
    </div>

    <div class="container-fluid p-3">
    <h2>Critères TEST<?= $star ?> étoile</h2>
    <table class="table table-sm table-striped table-hover" id="criteriaTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Description</th>
                <th>Status</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody id="table-body">
            <!-- Les données seront insérées ici par JS -->
        </tbody>
    </table>
    </div>

    <script src="js/criteria1StarBack.js"></script>
</body>
</html>
