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

include("includes/mariadb.php");

$database = new Database();
$db = $database->getConnection();

function getNumberCriteriaByStar(PDO $pdo, int $star): int
{
    $sql = "
        SELECT COUNT(DISTINCT co.Critere_ID) AS nb_criteres
        FROM listescriteres_etoiles lce
        LEFT JOIN contient co
            ON co.ListesCriteres_ID = lce.ListesCriteres_ID
        WHERE lce.type_hebergement_id = 2
          AND lce.etoile = :star
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['star' => $star]);

    $result = $stmt->fetchColumn();
    return ($result === false) ? 0 : (int)$result;
}

function getNumberEstablishmentByStar(PDO $pdo, int $star): int
{
    $sql = "
        SELECT COUNT(*) AS nb
        FROM biens
        WHERE TypeHebergement_ID = 2
          AND Bien_Etoile_Actuelle = :star
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['star' => $star]);

    $result = $stmt->fetchColumn();
    return ($result === false) ? 0 : (int)$result;
}

function getNumberCriteriaByStatusAndStar(PDO $pdo, int $star, string $status): int
{
    $sql = "
        SELECT COUNT(DISTINCT c.Critere_ID) AS nb
        FROM listescriteres_etoiles lce
        JOIN contient co ON co.ListesCriteres_ID = lce.ListesCriteres_ID
        JOIN criteres c ON c.Critere_ID = co.Critere_ID
        WHERE lce.type_hebergement_id = 2
          AND lce.etoile = :star
          AND c.Critere_statut = :status
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(['star' => $star, 'status' => $status]);

    $result = $stmt->fetchColumn();
    return ($result === false) ? 0 : (int)$result;
}

// Définir les couleurs par niveau d'étoile
$starColors = [
    1 => ['bg' => 'info', 'gradient' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'],
    2 => ['bg' => 'success', 'gradient' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'],
    3 => ['bg' => 'warning', 'gradient' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'],
    4 => ['bg' => 'danger', 'gradient' => 'linear-gradient(135deg, #fa709a 0%, #fee140 100%)'],
    5 => ['bg' => 'primary', 'gradient' => 'linear-gradient(135deg, #30cfd0 0%, #330867 100%)']
];
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion inspecteurs - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="bootstrap%205.3/css/style1.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
        
        <style>
            .star-card {
                transition: all 0.3s ease;
                border: none;
                height: 100%;
                overflow: hidden;
            }
            
            .star-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
            }
            
            .card-header-gradient {
                color: white;
                font-weight: bold;
                padding: 1.5rem;
            }
            
            .stat-badge {
                display: inline-block;
                padding: 0.5rem 1rem;
                border-radius: 10px;
                font-weight: 600;
                margin: 0.25rem;
            }
            
            .stat-badge.x-badge {
                background: rgba(220, 53, 69, 0.2);
                color: #ff6b6b;
                border: 2px solid rgba(220, 53, 69, 0.3);
            }
            
            .stat-badge.o-badge {
                background: rgba(25, 135, 84, 0.2);
                color: #51cf66;
                border: 2px solid rgba(25, 135, 84, 0.3);
            }
            
            .stat-badge.na-badge {
                background: rgba(255, 193, 7, 0.2);
                color: #ffd43b;
                border: 2px solid rgba(255, 193, 7, 0.3);
            }
            
            .divider {
                height: 2px;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                margin: 1rem 0;
            }
            
            .establishment-info {
                background: rgba(255, 255, 255, 0.05);
                padding: 1rem;
                border-radius: 10px;
                margin: 1rem 0;
            }
            
            .criteria-count {
                font-size: 2.5rem;
                font-weight: bold;
                margin: 0.5rem 0;
            }
            
            .star-icon-large {
                font-size: 2rem;
                color: #ffd700;
                text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
            }
        </style>
    </head>

    <body class="bg-dark">
        <?php require("./includes/navbar.php"); ?>

        <div class="container-fluid py-4 px-4">
            <!-- Header Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h1 class="mb-2">
                                <i class="fas fa-clipboard-check text-primary"></i>
                                Gestion des Critères
                            </h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle"></i>
                                Tableau de bord des critères par niveau d'étoiles
                            </p>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-primary fs-6 p-3">
                                <i class="fas fa-building"></i>
                                Type Hébergement: <strong>ID 2</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cards Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 g-4">
                <?php
                    for ($x = 1; $x <= 5; $x++) {
                        $totalCriteria = getNumberCriteriaByStar($db, $x);
                        $criteriaX = getNumberCriteriaByStatusAndStar($db, $x, 'X');
                        $criteriaO = getNumberCriteriaByStatusAndStar($db, $x, 'O');
                        $criteriaNA = getNumberCriteriaByStatusAndStar($db, $x, 'NA');
                        $establishments = getNumberEstablishmentByStar($db, $x);
                        $color = $starColors[$x];
                        ?>
                        <div class="col">
                            <div class="card star-card shadow-lg">
                                <!-- Card Header with Gradient -->
                                <div class="card-header card-header-gradient text-center" 
                                     style="background: <?= $color['gradient'] ?>;">
                                    <div class="star-icon-large mb-2">
                                        <?= str_repeat('<i class="fas fa-star"></i>', $x) ?>
                                    </div>
                                    <h5 class="mb-0 fw-bold">
                                        Niveau <?= $x ?> Étoile<?= $x > 1 ? 's' : '' ?>
                                    </h5>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body text-center">
                                    <!-- Total Criteria -->
                                    <div class="mb-3">
                                        <div class="criteria-count text-<?= $color['bg'] ?>">
                                            <?= $totalCriteria ?>
                                        </div>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-list-check"></i>
                                            Critère<?= $totalCriteria > 1 ? 's' : '' ?> total<?= $totalCriteria > 1 ? 'aux' : '' ?>
                                        </p>
                                    </div>

                                    <div class="divider"></div>

                                    <!-- Status Badges -->
                                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
                                        <div class="stat-badge x-badge">
                                            <i class="fas fa-times-circle"></i>
                                            <strong><?= $criteriaX ?></strong> X
                                        </div>
                                        <div class="stat-badge o-badge">
                                            <i class="fas fa-check-circle"></i>
                                            <strong><?= $criteriaO ?></strong> O
                                        </div>
                                        <div class="stat-badge na-badge">
                                            <i class="fas fa-minus-circle"></i>
                                            <strong><?= $criteriaNA ?></strong> NA
                                        </div>
                                    </div>

                                    <div class="divider"></div>

                                    <!-- Establishments Info -->
                                    <div class="establishment-info">
                                        <i class="fas fa-hotel text-<?= $color['bg'] ?> fs-4"></i>
                                        <div class="mt-2">
                                            <div class="fs-4 fw-bold"><?= $establishments ?></div>
                                            <small class="text-muted">
                                                Établissement<?= $establishments > 1 ? 's' : '' ?>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="d-grid gap-2 mt-4">
                                        <a href="critereBack<?= $x ?>Star.php" 
                                           class="btn btn-<?= $color['bg'] ?> btn-lg">
                                            <i class="fas fa-arrow-right"></i>
                                            Accéder aux critères
                                        </a>
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer text-center bg-transparent border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-clock"></i>
                                        Dernière mise à jour: Aujourd'hui
                                    </small>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                ?>
            </div>

            <!-- Summary Section -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="card shadow-lg border-primary">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <i class="fas fa-star fs-1 text-warning mb-2"></i>
                                    <h4 class="fw-bold">5</h4>
                                    <p class="text-muted mb-0">Niveaux d'étoiles</p>
                                </div>
                                <div class="col-md-3">
                                    <i class="fas fa-clipboard-list fs-1 text-info mb-2"></i>
                                    <h4 class="fw-bold">
                                        <?php 
                                            $total = 0;
                                            for ($i = 1; $i <= 5; $i++) {
                                                $total += getNumberCriteriaByStar($db, $i);
                                            }
                                            echo $total;
                                        ?>
                                    </h4>
                                    <p class="text-muted mb-0">Critères totaux</p>
                                </div>
                                <div class="col-md-3">
                                    <i class="fas fa-building fs-1 text-success mb-2"></i>
                                    <h4 class="fw-bold">
                                        <?php 
                                            $totalEst = 0;
                                            for ($i = 1; $i <= 5; $i++) {
                                                $totalEst += getNumberEstablishmentByStar($db, $i);
                                            }
                                            echo $totalEst;
                                        ?>
                                    </h4>
                                    <p class="text-muted mb-0">Établissements totaux</p>
                                </div>
                                <div class="col-md-3">
                                    <i class="fas fa-chart-line fs-1 text-danger mb-2"></i>
                                    <h4 class="fw-bold">100%</h4>
                                    <p class="text-muted mb-0">Couverture système</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
