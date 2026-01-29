<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(isset($_SESSION['Role']['Administrateur']) || isset($_SESSION['Role']['Inspecteur'])){
        if(!$_SESSION['Role']['Administrateur'] && !$_SESSION['Role']['Inspecteur']){
            header('Location: deco.php');
            die();
        }
    } else {
        header('Location: deco.php');
        die();
    }
    require_once './includes/mariadb.php';

    $id = isset($_GET['id']) ? intval ($_GET['id']) : null;

?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Critères du dossier - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
        
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/search_inspecteurs.js"></script>
        <script src="js/etoile_eval.js"></script>
    </head>
    <body class="bg-secondary">
    <?php require("./includes/navbar.php"); ?>
        
        <div>
            <div>
                <div class="d-flex align-items-center gap-3 p-3">
                    <a href="front_dossier.php?id=<?php echo $id; ?>" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-arrow-left-short fs-3 text-dark"></i>
                    </a>

                    <div class="bg-white rounded-pill shadow-sm px-4 py-2 border">
                        <span class="fw-medium text-secondary">
                            Dossier en cours : <span class="text-dark fw-bold"><?php echo $id; ?></span>
                        </span>
                    </div>

                    <div class="bg-white rounded-pill shadow-sm d-flex align-items-center ps-4 pe-2 py-1 border">
                        <span class="me-3 fw-medium text-dark">Sélectionner le nombre d'étoile :</span>
                        
                        <select id="selectEtoiles" class="form-select border-0 rounded-pill bg-light fw-bold text-center text-dark" style="width: 80px; background-color: #adb5bd !important;">
                            <option value="">-</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
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
            </div>
        </div>
    </body>
</html>