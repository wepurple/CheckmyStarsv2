<?php
session_start();

if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    header('Location: deco.php');
    die();
}

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
    <title>Gestion des utilisateurs - CheckMyStars</title>
    <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="bootstrap 5.3/js/bootstrap.js"></script>
    <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    <style>
        .search-card {
            border-left: 4px solid #0d6efd;
        }
        .table-actions {
            white-space: nowrap;
        }
    </style>
</head>
<body class="bg-secondary">
    <?php require("./includes/navbar.php"); ?>

    <div class="container-fluid py-4">

        <!-- En-tête avec bouton ajouter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div>
                        <h2 class="mb-0">Gestion des utilisateurs</h2>
                        <p class="text-muted mb-0">Administration des comptes</p>
                    </div>

                    <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                    </button>
                </div>
            </div>
        </div>

        <!-- Card de recherche et filtres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card search-card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title mb-3">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Recherche et filtres
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-4 col-lg-3">
                                <label for="filterType" class="form-label small text-muted">Type de filtre</label>
                                <select id="filterType" class="form-select">
                                    <option value="all">Tous les champs</option>
                                    <option value="id">ID</option>
                                    <option value="nom">Nom</option>
                                    <option value="prenom">Prénom</option>
                                    <option value="email">Email</option>
                                    <option value="role">Rôle</option>
                                    <option value="societe">Société</option>
                                </select>
                            </div>
                            <div class="col-md-8 col-lg-9">
                                <label for="searchInput" class="form-label small text-muted">Terme de recherche</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                    </span>
                                    <input type="text" 
                                        id="searchInput" 
                                        class="form-control" 
                                        placeholder="Rechercher un utilisateur...">
                                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                                        <i class="fa-solid fa-xmark"></i>
                                        Effacer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card du tableau -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fa-solid fa-user-group"></i>
                            Liste des utilisateurs
                        </h5>
                        <span class="badge bg-light text-dark" id="userCount">0 utilisateur(s)</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0" id="usersTable">
                                <thead class="table-dark sticky-top">
                                    <tr>
                                        <th class="text-center"><small>ID</small></th>
                                        <th><small>Nom</small></th>
                                        <th><small>Prénom</small></th>
                                        <th><small>Rôle</small></th>
                                        <th><small>Société</small></th>
                                        <th class="text-center"><small>Actions</small></th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Chargement...</span>
                                            </div>
                                            <p class="mt-3 text-muted">Chargement des données...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer text-muted small">
                        <div class="d-flex justify-content-between align-items-center">
                            <span id="resultInfo">Affichage de tous les utilisateurs</span>
                            <span class="text-end">CheckMyStars © 2026</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;">
    
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
                                    <option value="2">Inspecteur</option>
                                    <option value="3">Administrateur</option>
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

    <!-- Modal édition utilisateur -->
    <div class="modal fade" tabindex="-1" id="editModal">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="editIdUser">

                        <div class="row g-2">
                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="editLeNom" placeholder="">
                                <label for="editLeNom">Nom *</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="editLePrenom" placeholder="">
                                <label for="editLePrenom">Prénom *</label>
                            </div>

                            <div class="col-md-8 form-floating mb-3">
                                <input type="email" class="form-control" id="editLeMail" placeholder="">
                                <label for="editLeMail">Mail *</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="editLeTel" placeholder="">
                                <label for="editLeTel">Téléphone *</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <select class="form-select" id="editLeGenre">
                                    <option value="1">Monsieur</option>
                                    <option value="2">Madame</option>
                                    <option value="3">Iel</option>
                                </select>
                                <label for="editLeGenre">Civilité *</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <select class="form-select" id="editLaSociete">
                                    <?php

                                    $companies = getAllCompany($db);

                                    while ($row = $companies->fetch()) {
                                        echo '<option value="' . $row['Societe_ID'] . '">' . htmlspecialchars($row['Societe_Nom']) . '</option>';
                                    }
                                    ?>
                                </select>
                                <label for="editLaSociete">Société *</label>
                            </div>

                            <div class="col-md-12 form-floating mb-3">
                                <select class="form-select" id="editLeRole">
                                    <option value="0">Propriétaire</option>
                                    <option value="1">Donneur d'ordre</option>
                                    <option value="2">Inspecteur</option>
                                    <option value="3">Administrateur</option>
                                </select>
                                <label for="editLeRole">Rôle *</label>
                            </div>

                            <div class="col-md-12 form-floating mb-3">
                                <input type="text" class="form-control" id="editLaAdresseComplete" placeholder="">
                                <label for="editLaAdresseComplete">
                                    Adresse complète *
                                </label>
                                <small class="text-muted">Ex: 8 Boulevard du Port, 95000 Cergy</small>
                            </div>

                            <input type="hidden" id="editLeNumRue">
                            <input type="hidden" id="editLaAdresse">
                            <input type="hidden" id="editLeCode">
                            <input type="hidden" id="editLaVille">
                            <input type="hidden" id="editLePays">

                            <div class="col-md-12 form-floating mb-3">
                                <input type="text" class="form-control" id="editLeComplement" placeholder="">
                                <label for="editLeComplement">Complément</label>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-warning" onclick="updateUserById()">Enregistrer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal voir utilisateur -->
    <div class="modal fade" tabindex="-1" id="seeModal">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Détails utilisateur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form>
                        <div class="row g-2">
                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLeNom" placeholder="" disabled>
                                <label for="seeLeNom">Nom</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLePrenom" placeholder="" disabled>
                                <label for="seeLePrenom">Prénom</label>
                            </div>

                            <div class="col-md-8 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLeMail" placeholder="" disabled>
                                <label for="seeLeMail">Mail</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLeTel" placeholder="" disabled>
                                <label for="seeLeTel">Téléphone</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="seeGenre" placeholder="" disabled>
                                <label for="seeGenre">Civilité</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLaSociete" placeholder="" disabled>
                                <label for="seeLaSociete">Société</label>
                            </div>

                            <div class="col-md-12 form-floating mb-3">
                                <input type="text" class="form-control" id="seeRole" placeholder="" disabled>
                                <label for="seeRole">Role</label>
                            </div>

                            <div class="col-md-2 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLeNumRue" placeholder="" disabled>
                                <label for="seeLeNumRue">N° rue</label>
                            </div>

                            <div class="col-md-10 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLaAdresse" placeholder="" disabled>
                                <label for="seeLaAdresse">Adresse</label>
                            </div>

                            <div class="col-md-12 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLeComplement" placeholder="" disabled>
                                <label for="seeLeComplement">Complément</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLeCode" placeholder="" disabled>
                                <label for="seeLeCode">Code postal</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLaVille" placeholder="" disabled>
                                <label for="seeLaVille">Ville</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="seeLePays" placeholder="" disabled>
                                <label for="seeLePays">Pays</label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Retour</button>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/search_utilisateurs.js"></script>
</body>
</html>
