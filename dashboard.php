<?php
    session_start();

    require_once("includes/mariadb.php");

    $database = new Database();
    $db = $database->getConnection();

    function getAllCompany($connexion)
    {
        $sql = "SELECT Societe_ID, Societe_Nom FROM societes ORDER BY Societe_Nom;";
        $query = $connexion->prepare($sql);
        $query->execute();
        return $query;
    }
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion clients - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/dashboard.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php
            require_once "./includes/navbar.php";
        ?>
        
         <div class="container-fluid p-3">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                </button>
                
                <div class="input-group" style="width: 400px;">
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
            <table class="table table-dark table-sm table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Société</th>
                        <th>Téléphone</th>
                        <th>Mail</th>
                        <th>Nombre de dossiers</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tabloBody">
                    <!-- remplit par le js -->
                </tbody>
            </table>

            <!-- Modal ajout utilisateur -->
            <div class="modal fade" tabindex="-1" id="addModal">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ajouter un utilisateur</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="addCancel()"></button>
                        </div>

                        <div class="modal-body">
                            <form id="addForm">
                                <div class="row g-2">
                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="leNom" placeholder="">
                                        <label for="leNom">Nom *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="lePrenom" placeholder="">
                                        <label for="lePrenom">Prénom *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <select class="form-select" id="leGenre">
                                            <option value="1">Monsieur</option>
                                            <option value="2">Madame</option>
                                            <option value="3">Iel</option>
                                        </select>
                                        <label for="leGenre">Civilité *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="email" class="form-control" id="leMail" placeholder="">
                                        <label for="leMail">Mail *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <select class="form-select" id="laSociete">
                                            <option value="">Sélectionner...</option>
                                            <option value="new_company">Créer une nouvelle entreprise</option>
                                            <?php
                                            $companies = getAllCompany($db);
                                            while ($row = $companies->fetch()) {
                                                echo '<option value="' . $row['Societe_ID'] . '">' . htmlspecialchars($row['Societe_Nom']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <label for="laSociete">Société *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <select class="form-select" id="leRole">
                                            <option value="0">Propriétaire</option>
                                            <option value="1">Donneur d'ordre</option>
                                        </select>
                                        <label for="leRole">Rôle *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="leTel" placeholder="">
                                        <label for="leTel">Téléphone *</label>
                                    </div>

                                    <div class="col-md-12 form-floating mb-3">
                                        <input type="text" class="form-control" id="laAdresseComplete" placeholder="">
                                        <label for="laAdresseComplete">
                                            Adresse complète *
                                        </label>
                                        <small class="text-muted">Ex: 8 Boulevard du Port, 95000 Cergy</small>
                                    </div>

                                    <input type="hidden" id="leNumRue">
                                    <input type="hidden" id="laAdresse">
                                    <input type="hidden" id="leCode">
                                    <input type="hidden" id="laVille">
                                    <input type="hidden" id="lePays">

                                    <div class="col-md-12 form-floating mb-3">
                                        <input type="text" class="form-control" id="leComplement" placeholder="">
                                        <label for="leComplement">Complément (bâtiment, étage...)</label>
                                    </div>

                                    <div class="col-md-12 form-floating mb-3">
                                        <input type="password" class="form-control" id="leMdp" placeholder="">
                                        <label for="leMdp">Mot de passe *</label>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="addCancel()">Annuler</button>
                            <button type="button" class="btn btn-success" onclick="addUser()">Créer</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
