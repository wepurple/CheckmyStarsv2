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
    <style>
        .star-badge {
            font-size: 1.2rem;
            padding: 0.5rem 1rem;
        }
        .search-card {
            border-left: 4px solid #0d6efd;
        }
    </style>
</head>
<body class="bg-secondary">
    <?php require("./includes/navbar.php"); ?>

    <div class="container-fluid py-4">
        <!-- En-tête avec badge étoile -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center gap-3">
                    <div>
                        <h2 class="mb-0">Critères <?= $star ?> étoile</h2>
                        <p class="text-muted mb-0">Gestion des critères d'évaluation</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de recherche et filtres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card search-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="bi bi-search"></i> Recherche et filtres
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4 col-lg-3">
                                <label for="filterType" class="form-label small text-muted">Type de filtre</label>
                                <select id="filterType" class="form-select">
                                    <option value="all">Tous les champs</option>
                                    <option value="id">ID</option>
                                    <option value="description">Description</option>
                                    <option value="status">Status</option>
                                    <option value="points">Points</option>
                                </select>
                            </div>
                            <div class="col-md-8 col-lg-9">
                                <label for="searchBar" class="form-label small text-muted">Terme de recherche</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                                        </svg>
                                    </span>
                                    <input type="text" 
                                        id="searchBar" 
                                        class="form-control" 
                                        placeholder="Rechercher dans les critères...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('searchBar').value=''; document.getElementById('searchBar').dispatchEvent(new Event('input'));">
                                        ✕ Effacer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card du tableau -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-table" viewBox="0 0 16 16">
                                <path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zm15 2h-4v3h4V4zm0 4h-4v3h4V8zm0 4h-4v3h3a1 1 0 0 0 1-1v-2zm-5 3v-3H6v3h4zm-5 0v-3H1v2a1 1 0 0 0 1 1h3zm-4-4h4V8H1v3zm0-4h4V4H1v3zm5-3v3h4V4H6zm4 4H6v3h4V8z"/>
                            </svg>
                            Liste des critères
                        </h5>
                        <span class="badge bg-light text-dark" id="resultCount">Chargement...</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="criteriaTable">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th style="width: 80px;" class="text-center">
                                            <small>ID</small>
                                        </th>
                                        <th>
                                            <small>Description</small>
                                        </th>
                                        <th style="width: 150px;" class="text-center">
                                            <small>Status</small>
                                        </th>
                                        <th style="width: 120px;" class="text-center">
                                            <small>Points</small>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Chargement des données...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="tableInfo">Aucune donnée</span>
                            <span class="text-end">CheckMyStars © 2026</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/criteria1StarBack.js"></script>
</body>
</html>
