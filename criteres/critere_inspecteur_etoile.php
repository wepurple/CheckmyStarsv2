<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(isset($_SESSION['Role']['Administrateur']) || isset($_SESSION['Role']['Inspecteur'])){
        if(!$_SESSION['Role']['Administrateur'] && !$_SESSION['Role']['Inspecteur']){
            header('Location: ../deco.php');
            die();
        }
    } else {
        header('Location: ../deco.php');
        die();
    }
    require_once '../includes/mariadb.php';

    $id = isset($_GET['id']) ? intval ($_GET['id']) : null;

    $dossier_numero = isset($_GET['num']) ? $_GET['num'] : "Non défini";
    $dossier_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Critères du dossier - CheckMyStars</title>

        <link rel="stylesheet" href="../bootstrap 5.3/css/bootstrap.css">
        <link rel="stylesheet" href="../fontawesome-7.1.0/css/all.css">
        <link rel="icon" type="image/x-icon" href="../assets/pictures/logosm.png">
        <script src="../bootstrap 5.3/js/bootstrap.js"></script>
        <script src="../js/etoile_eval.js"></script>
    </head>
    <body data-id="<?php echo $id; ?>" class="bg-secondary">
    <?php require("../includes/navbar.php"); ?>
        
        <div>
            <div>
                <div class="d-flex align-items-center gap-3 p-3">
                    <a href="front_dossier.php?id=<?php echo $id; ?>" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>

                    <div class="bg-white rounded-pill shadow-sm px-4 py-2 border">
                        <span class="fw-medium text-secondary">
                            Dossier en cours : <span class="text-dark fw-bold"><?php echo $dossier_numero; ?></span>
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
                <div class="container-fluid">
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
                                                <option value="points">Valeurs</option>
                                                <option value="points">Commentaires</option>
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
                                                <label>
                                                    <input type="checkbox" id="checkAll"> 
                                                    Tout sélectionner / Tout désélectionner
                                                </label>
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
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped mb-0" id="criteriaTable">
                                            <thead class="table-dark sticky-top">
                                                <tr>
                                                    <th class="text-center">
                                                        <small>Id</small>
                                                    </th>
                                                    <th>
                                                        <small>Description</small>
                                                    </th>
                                                    <th class="text-center">
                                                        <small>Status</small>
                                                    </th>
                                                    <th class="text-center">
                                                        <small>Points</small>
                                                    </th>
                                                    <th class="text-center">
                                                        <small>Valeurs</small>
                                                    </th>
                                                    <th class="text-center">
                                                        <small>Commentaires</small>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                <form action="" method="post">
                                                    <tr>
                                                        <td colspan="6" class="text-center py-5">
                                                            <p class="mt-3 text-muted">Selectionnez une étoile pour afficher les critères</p>
                                                        </td>
                                                    </tr>
                                                </form>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <button class="btn btn-primary m-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Valider l'évaluation" onclick="Evaluer();">
                                    Valider
                                </button>
                                <button class="btn btn-primary m-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Sauvegarder l'évaluation en cours " onclick="pointsTotal();">
                                    Sauvegarder
                                </button>
                                <div class="card-footer text-muted small">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-end">CheckMyStars © 2026</span>
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