<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion clients - CheckMyStars</title>

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
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    <i class="fas fa-plus"></i> Ajouter un client
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
                <tbody>
                    <?php
                        $apiUrl = "http://localhost/checkmystars/models/crud/infoDossier.php";

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $apiUrl);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        $response = curl_exec($ch);
                        $err = curl_error($ch);

                        curl_close($ch);

                        if ($err) {
                            echo "<tr><td colspan='7' class='text-center text-danger'>Erreur API : $err</td></tr>";
                            exit;
                        }

                        $data = json_decode($response, true);

                        if (!$data || !isset($data["utilisateur"])) {
                            echo "<tr><td colspan='7' class='text-center text-danger'>Réponse API invalide</td></tr>";
                            exit;
                        }

                        $utilisateurs = $data["utilisateur"];

                        // Affichage des données
                        foreach ($utilisateurs as $row) {

                            echo "<tr style='cursor: pointer;' onclick=\"window.location.href='detail_client.php?id=" . urlencode($row['Utilisateur_ID']) . "'\">";

                            echo "<td>" . htmlspecialchars($row['Utilisateur_ID']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Utilisateur_Nom']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Utilisateurs_Societe']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Utilisateur_Telephone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Utilisateur_Mail']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['Nombre_Dossiers']) . "</td>";
                            $statusText = $row['Status_Global'] == 1 ? 'Terminé' : 'En cours';
                            $statusClass = $row['Status_Global'] == 1 ? 'bg-success' : 'bg-warning text-dark';
                             echo "<td><span class='badge $statusClass'>$statusText</span></td>";   

                            echo "</tr>";
                        }
                        ?>

                </tbody>
            </table>
            <!-- Vertically centered modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <!-- modal footer -->
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un client</h1>
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
                            <button type="button" class="btn btn-success">Ajouter</button>
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </body>
</html>