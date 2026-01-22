<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(isset($_SESSION['Role']['Administrateur']) || isset($_SESSION['Role']['Inspecteur'])){
        if(!$_SESSION['Role']['Administrateur'] || !$_SESSION['Role']['Inspecteur']){
            header('Location: deco.php');
            die();
        }
    } else {
        header('Location: deco.php');
        die();
    }
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
    </head>
    <body class="bg-secondary">
        <?php 
            require("./includes/navbar.php");

            // Connexion à la base de données
            require_once './includes/mariadb.php';
            $database = new Database();
            $db = $database->getConnection();

            // Récupération de l'ID du dossier depuis l'URL
            $dossierId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

            // Requête pour obtenir le numéro du dossier
            $NumDossier = "SELECT Dossier_Numero FROM dossiers WHERE Dossier_ID = '$dossierId'";

            // Exécution de la requête
            $result = $db->query($NumDossier);
            $numeroDossier = $result->fetchColumn();
        ?>
        
        <div>
            <div>
                <div class="d-flex align-items-center gap-3 p-3">
                    <a href="front_dossier.php?id=<?php echo $dossierId; ?>" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-arrow-left-short fs-3 text-dark"></i>
                    </a>

                    <div class="bg-white rounded-pill shadow-sm px-4 py-2 border">
                        <span class="fw-medium text-secondary">
                            Dossier en cours : <span class="text-dark fw-bold"><?php echo $numeroDossier; ?></span>
                        </span>
                    </div>
                </div>
            </div>
                <div>
                    <label for="etoiles-select">Sélectionner le nombre d'étoiles :</label>

                    <select name="etoiles" id="etoiles-select">
                        <option value="">--étoile--</option>
                        <option value="un">1</option>
                        <option value="deux">2</option>
                        <option value="trois">3</option>
                        <option value="quatre">4</option>
                        <option value="cinq">5</option>
                    </select>

                    <button>
                        valider
                    </button>
                </div>
            </div>
        </div>
    </body>