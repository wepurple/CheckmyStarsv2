<?php
session_start();

//Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
if(isset($_SESSION['Role']['Administrateur'])){
    if(!$_SESSION['Role']['Administrateur']){
        header('Location: ../deco.php');
        die();
    }
} else {
    header('Location: ../deco.php');
    die();
}

include("../includes/mariadb.php");

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

// Définir les couleurs Bootstrap par niveau d'étoile
$starColors = [
    1 => 'danger',
    2 => 'warning',
    3 => 'info',
    4 => 'primary',
    5 => 'success'
];
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="<?= $_SESSION['Theme'] ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion inspecteurs - CheckMyStars</title>

        <link rel="stylesheet" href="../bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="../https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="../bootstrap%205.3/css/style1.css">
        <script src="../bootstrap 5.3/js/bootstrap.js"></script>
        <link rel="icon" type="image/x-icon" href="../assets/pictures/logosm.png">
    </head>

    <body>
        <?php require("../includes/navbar.php"); ?>

        <div class="container-fluid py-4 px-3">
            <!-- Cards Grid -->
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 g-4 mb-4">
                <?php
                    for ($x = 1; $x <= 5; $x++) {
                        $totalCriteria = getNumberCriteriaByStar($db, $x);
                        $criteriaX = getNumberCriteriaByStatusAndStar($db, $x, 'X');
                        $criteriaO = getNumberCriteriaByStatusAndStar($db, $x, 'O');
                        $criteriaNA = getNumberCriteriaByStatusAndStar($db, $x, 'NA');
                        $criteriaXONC = getNumberCriteriaByStatusAndStar($db, $x, 'X ONC');
                        $establishments = getNumberEstablishmentByStar($db, $x);
                        $color = $starColors[$x];
                    ?>
                        <div class="col">
                            <div class="card h-100 shadow-lg border-<?= $color ?>">
                                <!-- Card Header -->
                                <div class="card-header text-center bg-<?= $color ?> bg-gradient text-white py-3">
                                    <h5 class="fw-bold mb-0">
                                        Niveau <?= $x ?> Étoile<?= $x > 1 ? 's' : '' ?>
                                    </h5>
                                </div>

                                <!-- Card Body -->
                                <div class="card-body text-center d-flex flex-column">
                                    <!-- Total Criteria -->
                                    <div class="mb-3">
                                        <div class="display-3 fw-bold text-<?= $color ?> mb-2">
                                            <?= $totalCriteria ?>
                                        </div>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-list-check"></i>
                                            Critère<?= $totalCriteria > 1 ? 's' : '' ?> <?= $totalCriteria > 1 ? 'totaux' : 'total' ?>
                                        </p>
                                    </div>

                                    <hr class="my-3">

                                    <!-- Status Badges -->
                                    <div class="d-flex justify-content-center flex-wrap gap-2 mb-3">
                                        <span class="badge bg-danger fs-6 py-2 px-3">
                                            <i class="fas fa-times-circle"></i> <?= $criteriaX ?> X
                                        </span>
                                        <span class="badge bg-success fs-6 py-2 px-3">
                                            <i class="fas fa-check-circle"></i> <?= $criteriaO ?> O
                                        </span>
                                        <span class="badge bg-warning text-dark fs-6 py-2 px-3">
                                            <i class="fas fa-minus-circle"></i> <?= $criteriaNA ?> NA
                                        </span>
                                        <span class="badge bg-info text-dark fs-6 py-2 px-3">
                                            <i class="fas fa-minus-circle"></i> <?= $criteriaXONC ?> X ONC
                                        </span>
                                    </div>

                                    <hr class="my-3">

                                    <!-- Establishments Info -->
                                    <div class="alert alert-<?= $color ?> mb-3" role="alert">
                                        <i class="fas fa-hotel fs-4"></i>
                                        <div class="mt-2">
                                            <div class="fs-3 fw-bold"><?= $establishments ?></div>
                                            <small>
                                                Établissement<?= $establishments > 1 ? 's' : '' ?>
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="d-grid gap-2 mt-auto">
                                        <a href="critereBackStar.php?star=<?= $x ?>" 
                                        class="btn btn-<?= $color ?> btn-lg">
                                            <i class="fas fa-arrow-right"></i>
                                            Accéder aux critères
                                        </a>
                                    </div>
                                </div>

                                <!-- Card Footer -->
                                <div class="card-footer text-center bg-transparent border-<?= $color ?>">
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
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-lg border-primary">
                        <div class="card-header bg-primary bg-gradient text-white text-center py-3">
                            <h4 class="mb-0">
                                <i class="fas fa-chart-bar"></i> Statistiques Globales
                            </h4>
                        </div>
                        <div class="card-body py-4">
                            <div class="row text-center g-4 justify-content-center">
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="p-3 bg-info bg-opacity-10 rounded-3">
                                        <i class="fas fa-clipboard-list fs-1 text-info mb-3"></i>
                                        <h3 class="fw-bold mb-1">
                                            <?php 
                                                $total = 0;
                                                for ($i = 1; $i <= 5; $i++) {
                                                    $total += getNumberCriteriaByStar($db, $i);
                                                }
                                                echo $total;
                                            ?>
                                        </h3>
                                        <p class="text-muted mb-0 small">Critères totaux</p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-4">
                                    <div class="p-3 bg-success bg-opacity-10 rounded-3">
                                        <i class="fas fa-building fs-1 text-success mb-3"></i>
                                        <h3 class="fw-bold mb-1">
                                            <?php 
                                                $totalEst = 0;
                                                for ($i = 1; $i <= 5; $i++) {
                                                    $totalEst += getNumberEstablishmentByStar($db, $i);
                                                }
                                                echo $totalEst;
                                            ?>
                                        </h3>
                                        <p class="text-muted mb-0 small">Établissements totaux</p>
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
