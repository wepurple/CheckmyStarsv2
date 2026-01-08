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

function getInstitutionByStar(PDO $pdo, int $star): int
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

?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion inspecteurs - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php        
            require("./includes/navbar.php");
        ?>

        <div class="container-fluid p-3">

            <div class="row">
            <?php
                for ($x = 1; $x <= 5; $x++) {
                    ?>
                        <div class="card col" style="width: 18rem; margin: 15px">
                            <div class="card-body text-center">
                                <h5 class="card-title">Critères des <?= $x ?>  étoile</h5>
                                <p class="card-text"><?= getInstitutionByStar($db, $x) ?> Critères</p>
                                <div class="row">
                                    <div class="col">
                                        <p class="card-text">?? X</p>
                                    </div>
                                    <div class="col">
                                        <p class="card-text">?? O</p>
                                    </div>
                                    <div class="col">
                                        <p class="card-text">?? NA</p>
                                    </div>
                                </div>
                                </br>
                                <p class="card-text">?? établissement à ?? étoile</p>
                                </br>
                                <a href="#" class="btn btn-primary">Accéder aux critères</a>
                            </div>
                        </div>
                    <?php
                }
            ?>
            </div>

    </body>
</html>