<?php
session_start();
    require_once('./includes/mariadb.php');

    // Vérification des rôles
    if(!isset($_SESSION['Role']['Administrateur']) && !isset($_SESSION['Role']['Inspecteur'])){
        header('Location: deco.php');
        die();
    }



?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Paramètres- CheckMyStars</title>
        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <link rel="stylesheet" href="bootstrap 5.3/css/styleimg.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>      
        <script src="js/front_dossier.js"></script>  
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php
            require_once "./includes/navbar.php";
        ?>
    
        <div>
            <div class="container mt-4 p-3 rounded shadow bg-light-subtle">
                <h1 class="text-center"><i class="fa-solid fa-gear"></i> Paramètres</h1>
            </div>
            <div class="container mt-4 p-3 rounded shadow bg-light-subtle">
                <div class="row">

                    <!-- colonne infos personelles -->
                    <div class="col m-2 p-2 rounded">
                        
                        <h2>Mes informations Utilisateur</h2>

                        <div class="input-group mb-1">

                            <span class="input-group-text">Nom *</span>
                            <input id="nom" type="text" class="form-control" placeholder="ex: Macron" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Prénom *</span>
                            <input id="prenom" type="text" class="form-control" placeholder="ex: Emmanuel" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Civilité *</span>
                            <select id="civilite" class="form-select" disabled>
                                <option value="Monsieur">Monsieur</option>
                                <option value="Madame">Madame</option>
                                <option value="Iel" selected>Non-binaire</option>
                            </select>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Adresse Mail *</span>
                            <input id="mail" type="text" class="form-control" placeholder="ex: exemple@mail.com" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Téléphone *</span>
                            <input id="tel" type="text" class="form-control" placeholder="ex: 0612345678" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Société *</span>
                            <select id="societe" class="form-select" disabled>
                            </select>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Rôle</span>
                            <input id="role" type="text" class="form-control" placeholder="" disabled value="<?php
                                if($_SESSION['Role']["Administrateur"]){
                                    switch($_SESSION['Civilite']){
                                        case "Monsieur":
                                            echo('Administrateur');
                                            break;
                                        case "Madame":
                                            echo('Administratrice');
                                            break;
                                        default:
                                            echo('Administrateur.ice');
                                    }
                                }elseif($_SESSION['Role']["Inspecteur"]){
                                    switch($_SESSION['Civilite']){
                                        case "Monsieur":
                                            echo('Inspecteur');
                                            break;
                                        case "Madame":
                                            echo('Inspectrice');
                                            break;
                                        default:
                                            echo('Inspecteur.ice');
                                    }
                                }else{
                                    echo('N/A');
                                }
                            ?>">
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Mot de passe</span>
                            <input id="password" type="password" class="form-control" placeholder="*************" disabled>
                            <button class="btn btn-secondary" type="button" onclick="editPasswordBtn()">Modifier</button>
                        </div>

                    </div>

                    <!-- colonne adresse -->
                    <div class="col m-2 p-2 rounded">
                        <h2>Mon adresse</h2>

                        <div class="input-group mb-1">

                            <span class="input-group-text">Numéro de rue *</span>
                            <input id="numRue" type="text" class="form-control" placeholder="ex: 18" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Nom de voie *</span>
                            <input id="nomRue" type="text" class="form-control" placeholder="ex: Boulevard Haussmann" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Complément d'adresse</span>
                            <input id="complement" type="text" class="form-control" placeholder="ex: Bis" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Code Postal *</span>
                            <input id="codePost" type="text" class="form-control" placeholder="ex: 75000" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Ville *</span>
                            <input id="ville" type="text" class="form-control" placeholder="ex: Paris" disabled>
                        </div>

                        <div class="input-group mb-1">
                            <span class="input-group-text">Pays *</span>
                            <input id="pays" type="text" class="form-control" placeholder="ex: France" disabled>
                        </div>

                    </div>

                </div>

                <!-- boutons -->
                <div class="row">
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">

                        <button id="editButton" type="button" class="btn btn-outline-warning" onclick="edit()">
                            <i class="fa-solid fa-pen-to-square"></i>
                            Modifier mes informations
                        </button>

                        <button id="validerButton" type="button" class="btn btn-outline-success" onclick="valider()" disabled>
                            <i class="fa-solid fa-check"></i>
                            Valider
                        </button>

                        <button id="cancelButton" type="button" class="btn btn-outline-danger" onclick="cancel()" disabled>
                            <i class="fa-solid fa-xmark"></i>
                            Annuler
                        </button>

                    </div>
                </div>

            </div>
            </div>

            <div class="container mt-4 p-3 rounded shadow bg-light-subtle">
                <h2>Apparence</h2>

                <div class="dropdown">
                    <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-palette me-2"></i>Couleur de fond
                    </button>
                    <ul class="dropdown-menu shadow">
                        <li><a class="dropdown-item bg-change" href="#" data-color="#f8f9fa">Clair (Défaut)</a></li>
                        <li><a class="dropdown-item bg-change" href="#" data-color="#212529">Sombre</a></li>
                        <li><a class="dropdown-item bg-change" href="#" data-color="#1a1d2b">Bleu Nuit</a></li>
                        <li><a class="dropdown-item bg-change" href="#" data-color="#2d3436">Gris Anthracite</a></li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <div class="px-3">
                                <label class="form-label small">Couleur personnalisée :</label>
                                <input type="color" class="form-control form-control-color w-100" id="customColorPicker">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>





    </body>
</html>