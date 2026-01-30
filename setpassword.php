<?php
    session_start();

    //Si on tente d'accéder à la page via l'url sans être connecté, on se fait dégager avant de charger la page
    if(!isset($_SESSION['Role'])){
        header('Location: deco.php');
        die();
    }

    //var_dump($_SESSION);
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Mon profil - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
        
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/profil.js"></script>
    </head>

    <body class="bg-secondary">

        <!-- conteneur -->
        <div class="container mt-4 p-3 rounded shadow bg-light-subtle">

            <div class="row">

                <!-- colonne infos personelles -->
                <div class="col m-2 p-2 rounded">
                    
                    <h2>Modifier mon mot de passe</h2>

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


            </div>

            <!-- boutons -->
            <div class="row">
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">

                    <button id="validerButton" type="button" class="btn btn-outline-success" onclick="valider()">
                        <i class="fa-solid fa-check"></i>
                        Valider
                    </button>

                </div>
            </div>

        </div>

        
        <!-- Modal changement mdp -->
        <div class="modal fade" id="modalPassword">
            <div class="modal-dialog modal-dialog-centered">

                <div class="modal-content">

                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Modifier mon mot de passe</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <div class="form-floating mb-2">
                            <input type="password" class="form-control" id="oldPassword" placeholder="" required>
                            <label for="oldPassword">Ancien mot de passe</label>
                        </div>

                        <div class="form-floating mb-2">
                            <input type="password" class="form-control" id="newPassword" placeholder="" required>
                            <label for="newPassword">Nouveau mot de passe</label>
                        </div>

                        <div class="form-floating">
                            <input type="password" class="form-control" id="confirmPassword" placeholder="Mot de passe" required>
                            <label for="confirmPassword">Confirmer nouveau mot de passe</label>
                        </div>

                        <div id="passwordHelpBlock" class="form-text mt-2">
                            <ul class="list-group-flush mb-0">
                                <li class="list-group-item">12 caractères</li>
                                <li class="list-group-item">1 caractère spécial #?!@$%^&*-</li>
                                <li class="list-group-item">1 majuscule</li>
                                <li class="list-group-item">1 minuscule</li>
                                <li class="list-group-item">1 chiffre</li>
                            </ul>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="cancelPassword()">Annuler</button>
                        <button id="submitPwBtn" type="button" class="btn btn-primary" onclick="submitPassword()">Valider</button>
                    </div>

                </div>

            </div>
        </div>

        <!-- Toast -->
        <div class="toast-container position-fixed top-0 end-0 me-2 mt-5 pt-5">
            <div id="toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div id="toastText" class="toast-body">
                        Lorem Ipsum
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>

    </body>
</html>