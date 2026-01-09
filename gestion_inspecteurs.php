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

//var_dump($_SESSION);
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
        <script src="js/search_inspecteurs.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php        
            require("./includes/navbar.php");
        ?>

        <div class="container-fluid p-3">
            
            <!-- Formulaire de recherche -->
            <nav class="navbar">
                <div class="container-fluid d-flex flex-row mb-2">
                    <div class="input-group">

                        <!-- Button trigger modal -->
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                            Ajouter un compte Inspecteur
                        </button>

                        <span class="input-group-text">Rechercher par</span>
                        <select class="form-select" id="type">
                            <option selected value = "1">ID</option>
                            <option value="2">Nom</option>
                            <option value="3">Société</option>
                        </select>
                        <input id="recherche" type="text" aria-label="Last name" class="form-control">
                    </div>
                </div>
            </nav>

            <!-- Tableau -->
            <table class="table table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Civilité</th>
                        <th>Société</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody  class="table-group-divider" id="table-body">
                    <!-- Rempli par js/search_inspecteurs.js -->
                </tbody>
            </table>

            <!-- Modal d'ajout utilisateur -->
            <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="modal d'ajout utilisateur" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <!-- modal header -->
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un compte inspecteur</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- modal body -->
                        <div class="modal-body">
                            <form>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="leNom" placeholder="" required>
                                    <label for="floatingInput">Nom *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="lePrenom" placeholder="" required>
                                    <label for="floatingInput">Prenom *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="leMail" placeholder="" required>
                                    <label for="floatingInput">Adresse Mail *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" id="leGenre" aria-label="Floating label select example">
                                        <option value="1">Homme</option>
                                        <option value="2">Femme</option>
                                        <option selected value="3">Non-binaire</option>
                                    </select>
                                    <label for="floatingSelect">Genre *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="laSociete" placeholder="" required>
                                    <label for="floatingInput">Société *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="leTel" placeholder="" required>
                                    <label for="floatingInput">Téléphone *</label>
                                </div>

                                <hr>

                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="leNumRue" placeholder="" required>
                                    <label for="floatingInput">Numéro de rue *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="laAdresse" placeholder="" required>
                                    <label for="floatingInput">Adresse postale *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="leComplement" placeholder="">
                                    <label for="floatingInput">Complément d'adresse</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="leCode" placeholder="" required>
                                    <label for="floatingInput">Code postal *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="laVille" placeholder="" required>
                                    <label for="floatingInput">Ville *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="lePays" placeholder="" required>
                                    <label for="floatingInput">Pays *</label>
                                </div>

                            </form>
                        </div>
                        <!-- modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-success"><i class="fa-solid fa-check mx-1"></i>Ajouter</button>
                        </div>
                    </div>                    
                </div>
            </div>

            <!-- Modal confirmation suppression -->
            <div class="modal fade" tabindex="-1" id="confirmModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="supprText">Voulez-vous vraiment supprimer l'utilisateur ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-danger" id="supprConfirm">Confirmer</button>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Modal modification utilisateur -->
            <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="modal de modification d'un utilisateur" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <!-- modal header -->
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="editModalTitle">Modifier un compte inspecteur</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- modal body -->
                        <div class="modal-body">
                            <form>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLeNom" placeholder="" required>
                                    <label for="floatingInput">Nom *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLePrenom" placeholder="" required>
                                    <label for="floatingInput">Prenom *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="editLeMail" placeholder="" required>
                                    <label for="floatingInput">Adresse Mail *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <select class="form-select" id="editLeGenre" aria-label="Floating label select example">
                                        <option value="1">Monsieur</option>
                                        <option value="2">Madame</option>
                                        <option selected value="3">Iel</option>
                                    </select>
                                    <label for="floatingSelect">Genre *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLaSociete" placeholder="" required>
                                    <label for="floatingInput">Société *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="tel" class="form-control" id="editLeTel" placeholder="" required>
                                    <label for="floatingInput">Téléphone *</label>
                                </div>

                                <hr>

                                <div class="form-floating mb-3">
                                    <input type="number" class="form-control" id="editLeNumRue" placeholder="" required>
                                    <label for="floatingInput">Numéro de rue *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLaAdresse" placeholder="" required>
                                    <label for="floatingInput">Adresse postale *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLeComplement" placeholder="">
                                    <label for="floatingInput">Complément d'adresse</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLeCode" placeholder="" required>
                                    <label for="floatingInput">Code postal *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLaVille" placeholder="" required>
                                    <label for="floatingInput">Ville *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="editLePays" placeholder="" required>
                                    <label for="floatingInput">Pays *</label>
                                </div>

                            </form>
                        </div>
                        <!-- modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-success" onclick="edit()"><i class="fa-solid fa-check mx-1"></i>Modifier</button>
                        </div>
                    </div>                    
                </div>
            </div>

            <!-- Modal confirmation changement mdp -->
            <div class="modal fade" tabindex="-1" id="confirmResetPasswordModal">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p id="resetText">Voulez-vous vraiment réinitialiser le mot de passe de l'utilisateur ?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-danger" id="resetConfirm">Confirmer</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>