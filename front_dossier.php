<?php
    session_start();
    require_once('../includes/mariadb.php');

    // Vérification des rôles
    if(!isset($_SESSION['Role']['Administrateur']) && !isset($_SESSION['Role']['Inspecteur'])){
        header('Location: deco.php');
        die();
    }

    $dossier_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $bien_id = 0;
    $dossier_numero = ""; // Variable pour stocker le numéro du dossier

    $database = new Database();
    $db = $database->getConnection();

    // Récupération du Numéro de dossier, des images et du Bien_ID
    // On ajoute d.Dossier_numero dans le SELECT
    $sql_images = "SELECT d.Dossier_numero, d.Bien_ID, p.Photo_ID, p.Photo_Lien
                   FROM dossiers d 
                   LEFT JOIN photos p ON p.Bien_ID = d.Bien_ID 
                   WHERE d.Dossier_ID = :id";
    
    $stmt = $db->prepare($sql_images);
    $stmt->execute(['id' => $dossier_id]);
    
    $photoData = []; 
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // On récupère le numéro de dossier (il sera le même pour chaque ligne)
        $dossier_numero = $row['Dossier_numero'];
        $bien_id = $row['Bien_ID']; 

        if ($row['Photo_Lien']) {
            $photoData[] = [
                'Photo_ID'   => $row['Photo_ID'],
                'Photo_Lien' => $row['Photo_Lien'],
                'Bien_ID'    => $row['Bien_ID']
            ];
        }
    }

    // Gestion de l'Upload
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['new_image'])) {
        $target_dir = "img/";
        $filename = time() . "_" . basename($_FILES['new_image']['name']);
        $target_path = $target_dir . $filename;

        if (move_uploaded_file($_FILES['new_image']['tmp_name'], $target_path)) {
            $insert = $db->prepare("INSERT INTO photos (Photo_Lien, Bien_ID) VALUES (?, ?)");
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
        <link rel="stylesheet" href="../fontawesome-7.1.0/css/all.css">
        <link rel="stylesheet" href="bootstrap 5.3/css/styleimg.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>      
        <script src="js/front_dossier.js"></script>  
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php
            require_once "../includes/navbar.php";
        ?>
        <div class="container-sm mt-5">
            <div class="d-flex align-items-center gap-3 p-3">
                    <a href="gestion/gestion_dossiers.php" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>

                    <div class="bg-white rounded-pill shadow-sm px-4 py-2 border">
                        <span class="fw-medium text-secondary">
                            Dossier en cours : <span class="text-dark fw-bold"><?php echo $dossier_numero; ?></span>
                        </span>
                    </div>
            </div>

            <div class="row justify-content-center">
                <div id="photoCarousel" class="carousel slide" data-bs-ride="false" data-images='<?php echo json_encode($photoData); ?>'>
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
        </div>

        <!-- Bouton pour ouvrir le modal d'upload -->
        <div class="container mt-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background-color: #464646 !important;">
                        <button type="button" class="btn text-white" data-bs-toggle="modal" data-bs-target="#info_modal">Voir le devis</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background-color: #2b2b2b !important;">
                        <button type="button" class="btn text-white" data-bs-toggle="modal" data-bs-target="#info_modal">Voir la facture</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background-color: #464646 !important;">
                        <button onclick="window.location.href='../criteres/critere_inspecteur_etoile.php?id=<?php echo $dossier_id; ?>&num=<?php echo urlencode($dossier_numero); ?>'" type="button" class="btn text-white">Voir l'évaluation</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background-color: #2b2b2b !important;">
                        <button type="button" class="btn text-white" data-bs-toggle="modal" data-bs-target="#adresse_modal">Voir l'adresse</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background-color: #464646 !important;">
                        <button type="button" class="btn text-white" data-bs-toggle="modal" data-bs-target="#info_modal">Voir la date du RDV</button>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-white shadow-sm border-0" style="background-color: #2b2b2b !important;">
                        <button type="button" class="btn text-white" data-bs-toggle="modal" data-bs-target="#statuts_modal">Voir l'état du dossier</button>
                    </div>
                </div>
            </div>
        </div>


        <!-- Modal pour l'adresse -->
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

        <!-- Modal pour l'état du dossier -->
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

        <!-- Modal d'information générique -->
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



        <!-- Modals pour l'upload et la suppression des photos -->
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

        
        <!-- Modals pour la suppression des photos -->
        <div class="modal fade" id="deletePhotoModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-danger shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirmation de suppression</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <p>Voulez-vous vraiment supprimer cette image ? Cette action est irréversible.</p>
                        <img id="previewDeleteImg" src="" class="img-thumbnail mb-3" style="max-height: 150px;">
                    </div>
                    <div class="modal-footer">
                        <form id="deletePhotoForm" method="POST" action="delete_photo.php">
                            <input type="hidden" name="photo_id" id="deletePhotoId">
                            <input type="hidden" name="dossier_id" value="<?php echo $dossier_id; ?>">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lightbox Modal -->
        <div class="modal fade" id="lightboxModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-body p-0 position-relative text-center">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1051; filter: drop-shadow(0 0 2px black);"></button>
                        <img id="lightboxImg" src="" class="img-fluid rounded shadow-lg">
                        <div class="mt-3">
                            <button id="btnOpenDelete" class="btn btn-danger shadow">
                                <i class="fa-solid fa-trash me-2"></i>Supprimer l'image
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </body>
</html>