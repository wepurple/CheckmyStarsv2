<?php
session_start();

require_once("includes/mariadb.php");

// Même protection que gestion_inspecteurs.php : réservé admin
if (isset($_SESSION['Role']['Administrateur'])) {
    if (!$_SESSION['Role']['Administrateur']) {
        header('Location: deco.php');
        die();
    }
} else {
    header('Location: deco.php');
    die();
}

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
    <title>Gestion utilisateurs - CheckMyStars</title>

    <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
    <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
     <link rel="stylesheet" href="bootstrap 5.3/css/style1.css">
    <link rel="icon" type="image/x-icon" href="pictures/logosm.png">

    <script src="bootstrap 5.3/js/bootstrap.js"></script>
    <script src="js/search_utilisateurs.js"></script>
</head>

<body class="bg-secondary">
<?php require("./includes/navbar.php"); ?>

<div class="container-fluid p-3">

    <!-- Barre de recherche + bouton ajouter -->
    <nav class="navbar">
        <div class="container-fluid d-flex flex-row mb-2">
            <div class="input-group">

                <!-- Bouton Ajout -->
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="fa-solid fa-plus"></i>
                    Ajouter un utilisateur
                </button>

                <span class="input-group-text">Rechercher par</span>

                <select class="form-select max-w-10" id="type">
                    <option selected value="1">ID</option>
                    <option value="2">Nom</option>
                    <option value="3">Prénom</option>
                    <option value="4">Mail</option>
                    <option value="5">Role</option>
                    <option value="6">Société</option>
                </select>

                <input type="text" class="form-control" id="recherche" placeholder="Votre recherche...">

                <button onclick="loadTable()">test</button>
            </div>
        </div>
    </nav>

    <!-- Tableau -->
    <div class="table-responsive">
        <table class="table table-dark table-striped table-hover align-middle">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Nom</th>
                <th scope="col">Prénom</th>
                <th scope="col">Mail</th>
                <th scope="col">Role</th>
                <th scope="col">Société</th>
                <th scope="col" class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody id="table-body">
            <!-- Rempli en JS -->
            <tr>
                <td colspan="7" class="text-center">Chargement...</td>
            </tr>
            </tbody>
        </table>
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

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="leNumRue" placeholder="">
                                <label for="leNumRue">N° rue *</label>
                            </div>

                            <div class="col-md-8 form-floating mb-3">
                                <input type="text" class="form-control" id="laAdresse" placeholder="">
                                <label for="laAdresse">Adresse *</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="leComplement" placeholder="">
                                <label for="leComplement">Complément</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="leCode" placeholder="">
                                <label for="leCode">Code postal *</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="laVille" placeholder="">
                                <label for="laVille">Ville *</label>
                            </div>

                            <div class="col-md-6 form-floating mb-3">
                                <input type="text" class="form-control" id="lePays" placeholder="">
                                <label for="lePays">Pays *</label>
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

                            <div class="col-md-2 form-floating mb-3">
                                <input type="text" class="form-control" id="editLeNumRue" placeholder="">
                                <label for="editLeNumRue">N° rue *</label>
                            </div>

                            <div class="col-md-10 form-floating mb-3">
                                <input type="text" class="form-control" id="editLaAdresse" placeholder="">
                                <label for="editLaAdresse">Adresse *</label>
                            </div>

                            <div class="col-md-12 form-floating mb-3">
                                <input type="text" class="form-control" id="editLeComplement" placeholder="">
                                <label for="editLeComplement">Complément</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="editLeCode" placeholder="">
                                <label for="editLeCode">Code postal *</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="editLaVille" placeholder="">
                                <label for="editLaVille">Ville *</label>
                            </div>

                            <div class="col-md-4 form-floating mb-3">
                                <input type="text" class="form-control" id="editLePays" placeholder="">
                                <label for="editLePays">Pays *</label>
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
</body>
</html>
