<?php
    session_start();
    require_once('./includes/mariadb.php');

    // Vérification des rôles
    if(!isset($_SESSION['Role']['Administrateur']) && !isset($_SESSION['Role']['Inspecteur'])){
        header('Location: deco.php');
        die();
    }


    $dossier_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $images = [];
    $bien_id = 0;

    $database = new Database();
    $db = $database->getConnection();

    // Récupération des images et du Bien_ID
    $sql_images = "SELECT p.Photo_Lien, d.Bien_ID 
                   FROM dossiers d 
                   LEFT JOIN photos p ON p.Bien_ID = d.Bien_ID 
                   WHERE d.Dossier_ID = :id";
    
    $stmt = $db->prepare($sql_images);
    $stmt->execute(['id' => $dossier_id]);
    
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if ($row['Photo_Lien']) $images[] = $row['Photo_Lien'];
        $bien_id = $row['Bien_ID']; 
    }

    // Gestion de l'Upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['new_image'])) {
        $target_dir = "img/";
        $filename = time() . "_" . basename($_FILES['new_image']['name']);
        $target_path = $target_dir . $filename;

        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target_path)) {
            // CORRECTION : Utilisez $db (votre instance de connexion) et non $pdo
            $insert = $db->prepare("INSERT INTO photos (Photo_Lien, Bien_ID) VALUES (?, ?)");
            
            // Assurez-vous que $_POST['bien_id'] est bien envoyé par le modal
            $insert->execute([$target_path, $_POST['bien_id']]);
            
            header("Location: front_dossier.php?id=$dossier_id");
            exit();
        }
    }
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
        <script src="js/front_dossier.js"></script>  
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </head>

    <body class="bg-secondary">
        <?php
            require_once "./includes/navbar.php";
        ?>
        <div class="container-sm mt-5">
            <div class="row justify-content-center">
                <div id="photoCarousel" class="carousel slide" data-bs-ride="false" data-images='<?php echo json_encode($images); ?>'>
                    <div class="carousel-inner" id="carouselContent">
                        </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
            </div>
<!--
                <div class="col-md 4 p-3 rounded">
                    <div id="Carousel" class="carousel slide"  data-bs-ride="carousel" data-bs-interval="2000" data-bs-pause="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="img\hotel_img3.jpg" style="border-radius: 20px;"  alt="Image 3">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img1.jpg" style="border-radius: 20px;"  alt="Image 1">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img2.jpg" style="border-radius: 20px;"  alt="Image 2">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md 4 p-3 rounded">
                    <div id="Carousel" class="carousel slide"  data-bs-ride="carousel" data-bs-interval="2000" data-bs-pause="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="img\hotel_img1.jpg" style="border-radius: 20px;"  alt="Image 1">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img2.jpg" style="border-radius: 20px;"  alt="Image 2">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img3.jpg" style="border-radius: 20px;"  alt="Image 3">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md 4 p-3 rounded">
                    <div id="Carousel" class="carousel slide"  data-bs-ride="carousel" data-bs-interval="2000" data-bs-pause="false">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="img\hotel_img2.jpg" style="border-radius: 20px;"  alt="Image 2">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img3.jpg" style="border-radius: 20px;"  alt="Image 3">
                            </div>
                            <div class="carousel-item">
                                <img src="img\hotel_img1.jpg" style="border-radius: 20px;"  alt="Image 1">
                            </div>
                        </div>
                    </div>
                </div>
-->
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #464646 !important;">
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#info_modal">Voir le devis</button>
                        </div>
                    </div>
                </div>
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #2b2b2b !important;">
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#info_modal">Voir la facture</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #464646 !important;">
                            <button onclick="window.location.href='critere_inspecteur_etoile.php'" type="button" class="btn">Voir l'evaluation</button>
                        </div>
                    </div>
                </div>
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #2b2b2b !important;">
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#adresse_modal">Voir l'adresse</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #464646 !important;">
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#info_modal">Voir la date du RDV</button>
                        </div>
                    </div>
                </div>
                <div class="col-md rounded">
                    <div class="card text-white rounded shadow-md border" style="background-color: #2b2b2b !important;">
                            <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#statuts_modal">Voir l'etat du dossier</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="adresse_modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div header class="header">
                        <h5 class="modal-title">Informations</h5>
                        <div class="modal-body">
                            <p>
                                <?php
                                    $database = new Database();
                                    $db = $database->getConnection();

                                    try {
                                        $sql = "CALL Get_Adresse_Dossier($dossier_id);";
                                        $stmt = $db->prepare($sql);
                                        $stmt->execute();

                                        if ($stmt->rowCount() > 0) {
                                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<td>" . htmlspecialchars($row['AdressePostale_NumeroRue']) . " " . htmlspecialchars($row['AdressePostale_NomRue']) . "</td>" . " " . htmlspecialchars($row['AdressePostale_Complement']) . "</td>" . "</br>" ;
                                                echo "<td>" . htmlspecialchars($row['AdressePostale_Pays']) . "</td>" . "</br>";
                                                echo "<td>" . htmlspecialchars($row['AdressePostale_Ville']) . "</td>" . "</br>" ;
                                                echo "<td>" . htmlspecialchars($row['AdressePostale_CodePostal']) . "</td>" . "</br>" ;
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>Aucune donnée trouvée</td></tr>";
                                        }
                                    
                                    } catch(PDOException $e) {
                                        echo "<tr><td colspan='7' class='text-center text-danger'>Erreur : " . $e->getMessage() . "</td></tr>";
                                    };
                                ?>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="statuts_modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div header class="header">
                        <h5 class="modal-title">Informations</h5>
                        <div class="modal-body">
                            <p>
                                <?php
                                    $database = new Database();
                                    $db = $database->getConnection();

                                    try {
                                        $sql = "CALL Get_Dossier_Etat($dossier_id);";
                                        $stmt = $db->prepare($sql);
                                        $stmt->execute();

                                        if ($stmt->rowCount() > 0) {
                                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) { 
                                                $statusText = $row['status'] == 1 ?  'terminé' : 'en cours';
                                                $statusClass = $row['status'] == 1 ? 'bg-success' : 'bg-warning text-dark';
                                                echo "<h5> Le dossier est actuellement : </h5><td><span class='badge $statusClass'>$statusText</span></td></br>";
                                                echo "</br> <h5> Assigné à : </h5>";
                                                echo "<td>" . htmlspecialchars($row['Utilisateur_Nom']) . "</td>"  . " " ;
                                                echo "<td>" . htmlspecialchars($row['Utilisateur_Prenom']) . "</td>" . "</br>";
                                                echo "<td>" . htmlspecialchars($row['Utilisateur_Mail']) . "</td>" . "</br>";
                                                echo "<td>" . htmlspecialchars($row['Utilisateur_Telephone']) . "</td>" . "</br>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='7' class='text-center'>Aucune donnée trouvée</td></tr>";
                                        }
                                    
                                    } catch(PDOException $e) {
                                        echo "<tr><td colspan='7' class='text-center text-danger'>Erreur : " . $e->getMessage() . "</td></tr>";
                                    };
                                ?>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal" id="info_modal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div header class="header">
                        <h5 class="modal-title">Informations</h5>
                        <div class="modal-body">
                            <p> En cours de développement ... </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="uploadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-secondary shadow">
                    <form id="uploadForm" method="POST" enctype="multipart/form-data">
                        <div class="modal-header bg-dark text-white">
                            <h5 class="modal-title">Ajouter une photo</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="bien_id" value="<?php echo $bien_id; ?>">
                            <input type="file" name="new_image" class="form-control bg-secondary text-white" accept="image/jpeg" required>
                        </div>
                        <div class="modal-footer bg-dark">
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        

        
    </body>
</html>