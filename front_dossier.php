<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dossiers- CheckMyStars</title>
        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <link rel="stylesheet" href="bootstrap 5.3/css/styleimg.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/search_inspecteurs.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </head>

    <body class="bg-secondary">
        <?php
            require_once "./includes/navbar.php";
        ?>
        <div class="container-sm">
            <div class="row justify-content-center">
                <div class="col-md 4 p-3 rounded">
                    <div id="Carousel" class="carousel slide"  data-bs-ride="carousel" data-bs-interval="2000" data-bs-pause="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="img\hotel_img3.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 3">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img1.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 1">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img2.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 2">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md 4 p-3 rounded">
                    <div id="Carousel" class="carousel slide"  data-bs-ride="carousel" data-bs-interval="2000" data-bs-pause="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="img\hotel_img1.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 1">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img2.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 2">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img3.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 3">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md 4 p-3 rounded">
                    <div id="Carousel" class="carousel slide"  data-bs-ride="carousel" data-bs-interval="2000" data-bs-pause="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="img\hotel_img2.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 2">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img3.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 3">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img1.jpg" style="border-radius: 20px;" class="d-block w-100" alt="Image 1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #464646 !important;">
                            <button type="button" class="btn" data-bs-toggle="info modal">Voir le devis</button>
                        </div>
                    </div>
                </div>
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #2b2b2b !important;">
                            <button type="button" class="btn" data-bs-toggle="info modal">Voir la facture</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #464646 !important;">
                            <button type="button" class="btn" data-bs-toggle="info modal">Voir l'evolution</button>
                        </div>
                    </div>
                </div>
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #2b2b2b !important;">
                            <button type="button" class="btn" data-bs-toggle="info modal">Voir l'adresse</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #464646 !important;">
                            <button type="button" class="btn" data-bs-toggle="info modal">Voir la date du RDV</button>
                        </div>
                    </div>
                </div>
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #2b2b2b !important;">
                            <button type="button" class="btn" data-bs-toggle="info modal">Voir l'etat du dossier</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="info modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div header class="header">
                        <h5 class="modal-title">Informations</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>