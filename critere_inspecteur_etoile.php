<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(isset($_SESSION['Role']['Administrateur']) && isset($_SESSION['Role']['Inspecteur'])){
        if(!$_SESSION['Role']['Administrateur'] && !$_SESSION['Role']['Inspecteur']){
            header('Location: deco.php');
            die();
        }
    } else {
        header('Location: deco.php');
        die();
    }



    require_once './includes/mariadb.php';

    $database = new Database();
        $db = $database->getConnection();

        //var_dump($db);

        // Vérification si la connexion a réussi et est bien un objet
        if (!is_object($db)) {
            die("Erreur de connexion : La base de données n'a pas retourné un objet valide.");
        }

        $dossierId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        if ($dossierId) {
            // Utilisation d'une requête préparée pour éviter les erreurs SQL et les injections
            $stmt = $db->prepare("SELECT Dossier_Numero FROM dossiers WHERE Dossier_ID = :id");
            $stmt->execute(['id' => $dossierId]);
            $numeroDossier = $stmt->fetchColumn();
        } else {
            $numeroDossier = "Inconnu";
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
    <?php require("./includes/navbar.php"); ?>
        
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

                    <div class="d-flex align-items-center gap-3 p-3">
                            <div class="bg-white rounded-pill shadow-sm d-flex align-items-center ps-4 pe-2 py-1 border">
                                <span class="me-3 fw-medium text-dark">Sélectionner le nombre d'étoile :</span>
                                
                                <select id="selectEtoiles" class="form-select border-0 rounded-pill bg-light fw-bold text-center text-dark" style="width: 80px; background-color: #adb5bd !important;">
                                    <option value="">--</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                </select>
                            </div>
                    </div>
                </div>
                <div>
                    <div class="d-flex flex-column gap-4 p-4">
                        <div id="contenu-1" class="mt-4 p-4 bg-white rounded shadow-sm d-none text-dark">
                            <h5>Critères pour 1 étoile</h5>
                            <p>Critères 1, 2, 3, ....</p>
                            <button type="submit" class="btn btn-white bg-white rounded-pill px-4 py-2 shadow-sm fw-bold border-0">
                        Valider
                    </button>
                        </div>

                        <div id="contenu-2" class="mt-4 p-4 bg-white rounded shadow-sm d-none text-dark">
                            <h5>Critères pour 2 étoiles</h5>
                            <p>Critères 1, 2, 3, ....</p>
                            <button type="submit" class="btn btn-white bg-white rounded-pill px-4 py-2 shadow-sm fw-bold border-0">
                        Valider
                    </button>
                        </div>

                        <div id="contenu-3" class="mt-4 p-4 bg-white rounded shadow-sm d-none text-dark">
                            <h5>Critères pour 3 étoiles</h5>
                            <p>Critères 1, 2, 3, ....</p>
                            <button type="submit" class="btn btn-white bg-white rounded-pill px-4 py-2 shadow-sm fw-bold border-0">
                        Valider
                    </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>







        <script>
        document.getElementById('selectEtoiles').addEventListener('change', function() {
            // Masquer tous les contenus qui commencent par "contenu-"
            document.querySelectorAll('[id^="contenu-"]').forEach(el => {
                el.classList.add('d-none');
            });

            // Récupérer la valeur sélectionnée
            const valeur = this.value;

            // Afficher le bloc correspondant s'il existe
            if (valeur) {
                const cible = document.getElementById('contenu-' + valeur);
                if (cible) {
                    cible.classList.remove('d-none');
                }
            }
        });
        </script>
    </body>
</html>