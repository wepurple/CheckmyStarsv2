<?php
    session_start();

    if(isset($_SESSION['ID'])){
        $inspecteurID = $_SESSION['ID'];
    }
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="<?= $_SESSION['Theme'] ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dossiers- CheckMyStars</title>

        <link rel="stylesheet" href="../../bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="../../fontawesome-7.1.0/css/all.css">
        <script src="../../bootstrap 5.3/js/bootstrap.js"></script>
        <script type="text/javascript">
            console.log("ok");
        </script>   
        <link rel="icon" type="image/x-icon" href="../../assets/pictures/logosm.png">
    </head>

    <body>
        <?php
            require_once "../../includes/navbar.php";
        ?>

            <div class="container-fluid mt-3">
            
                <div class="row mb-3">

                    <div class="col">
                        <button type="button" class="btn btn-danger" onclick="location.href='/checkmystars/dashboard.php'" > 
                            <i class="fas fa-arrow-left"></i> Retour au tableau de bord 
                        </button>

                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            <i class="fas fa-plus"></i> Ajouter un dossier
                        </button>
                    </div>

                    <div class="d-flex align-items-end flex-column col">
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

                </div>

                <!-- Tableau -->
                <table class="table table-sm table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>N° dossier</th>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Adresse hébergement</th>
                            <th> Code Postal</th>
                            <th> Ville</th>
                            <th> Pays </th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        require_once '../../includes/mariadb.php';
                        
                        $database = new Database();
                        $db = $database->getConnection();
                        
                        $utilisateurId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);


                        if (is_array($db)) {
                            echo "<tr><td colspan='10' class='text-center text-danger'>Erreur de connexion à la base de données</td></tr>";
                        } elseif (empty($utilisateurId)) {
                            echo "<tr><td colspan='10' class='text-center text-warning'>Aucun utilisateur sélectionné</td></tr>";
                        } else {
                            try {
                                // Requête pour récupérer tous les dossiers
                                $sql = "SELECT d.Dossier_ID,d.DOSSIER_NUMERO,t.TypeHebergement_Nom, u.Utilisateur_Nom,u.Utilisateur_Prenom, a.AdressePostale_NumeroRue, a.AdressePostale_NomRue,a.AdressePostale_CodePostal, a.AdressePostale_Ville, a.AdressePostale_Pays,d.status
                                FROM dossiers AS d
                                INNER JOIN utilisateurs AS u ON d.Proprietaire_ID = u.Utilisateur_ID
                                INNER JOIN biens AS b ON b.Bien_ID = d.Bien_ID
                                INNER JOIN adressespostales AS a ON a.AdressePostale_ID = b.AdressePostale_ID
                                INNER JOIN typeshebergements AS t ON t.TypeHebergement_ID = b.TypeHebergement_ID
                                WHERE d.Proprietaire_ID = :utilisateurId
                                ORDER BY d.Dossier_ID DESC;";
                                
                                
                                $stmt = $db->prepare($sql);
                                $stmt->execute([':utilisateurId' => $utilisateurId]);
                                
                                if ($stmt->rowCount() > 0) {
                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<tr style='cursor: pointer;' onclick=\"window.location.href='../../front_dossier.php?id=" . urlencode($row['Dossier_ID']) . "'\">";
                                        echo "<td>" . htmlspecialchars($row['Dossier_ID']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['DOSSIER_NUMERO']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['TypeHebergement_Nom']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Utilisateur_Nom']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['Utilisateur_Prenom']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['AdressePostale_NumeroRue']) . " " . htmlspecialchars($row['AdressePostale_NomRue']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['AdressePostale_CodePostal']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['AdressePostale_Ville']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['AdressePostale_Pays']) . "</td>";
                                        
                                        // Badge pour le statut (0 = En cours, 1 = Terminé)
                                        $statusText = $row['status'] == 1 ? 'Terminé' : 'En cours';
                                        $statusClass = $row['status'] == 1 ? 'bg-success' : 'bg-warning text-dark';
                                        echo "<td><span class='badge $statusClass'>$statusText</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Aucune donnée trouvée</td></tr>";
                                }
                            } catch(PDOException $e) {
                                echo "<tr><td colspan='7' class='text-center text-danger'>Erreur : " . $e->getMessage() . "</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>

            </div>

            <!-- Toast -->
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;">
            
            </div>

            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <!-- modal footer -->
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un dossier au client</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- modal body -->
                        <div class="modal-body">
                            <form id="addForm">
                                <div class="row g-2">
                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="leNom" placeholder="" disabled>
                                        <label for="floatingInput">Nom</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="lePrenom" placeholder="" disabled>
                                        <label for="floatingInput">Prénom</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="leMail" placeholder="" disabled>
                                        <label for="floatingInput">Mail</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="laSociete" placeholder="" disabled>
                                        <label for="floatingInput">Société</label>
                                    </div>

                                    <hr>

                                    <div class="col-md-12 form-floating mb-3">
                                        <input type="text" class="form-control" id="leNomBien" placeholder="">
                                        <label for="leNomBien">Nom du bien *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <input type="text" class="form-control" id="leTelBien" placeholder="">
                                        <label for="leNomBien">Téléphone du bien</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <select class="form-select" id="typeBien" aria-label="Floating label select example">
                                            <option value="1">Gite</option>
                                            <option value="2">Hotel</option>
                                            <option value="3">Camping</option>
                                        </select>
                                        <label for="floatingSelect">Type de bien *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <select class="form-select" id="etoileActuel" aria-label="Floating label select example">
                                            <option value="0">Aucune etoile</option>
                                            <option value="1">1 étoile</option>
                                            <option value="2">2 étoile</option>
                                            <option value="3">3 étoile</option>
                                            <option value="4">4 étoile</option>
                                            <option value="5">5 étoile</option>
                                        </select>
                                        <label for="floatingSelect">Étoile actuel du bien *</label>
                                    </div>

                                    <div class="col-md-6 form-floating mb-3">
                                        <select class="form-select" id="etoileCible" aria-label="Floating label select example">
                                            <option value="1">1 étoile</option>
                                            <option value="2">2 étoile</option>
                                            <option value="3">3 étoile</option>
                                            <option value="4">4 étoile</option>
                                            <option value="5">5 étoile</option>
                                        </select>
                                        <label for="floatingSelect">Étoile cible du bien *</label>
                                    </div>

                                    <div class="col-md-12 form-floating mb-3">
                                        <select class="form-select" id="donneurOrdre" aria-label="Floating label select example">
                                            <option value="0">Pas de donneur d'orde</option>
                                            <option value="new_orderingParty">Crée un donneur d'ordre</option>
                                            <?php
                                                try {
                                                    $sql = "SELECT U.Utilisateur_ID, U.Utilisateur_Nom, U.Utilisateur_Prenom
                                                            FROM donneurordre D
                                                            JOIN utilisateurs U ON U.Utilisateur_ID = D.Donneur_ID";

                                                    $stmt = $db->prepare($sql);
                                                    $stmt->execute();

                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        echo '<option value="' . htmlspecialchars($row['Utilisateur_ID']) . '">'
                                                            . htmlspecialchars($row['Utilisateur_Nom']) . ' ' . htmlspecialchars($row['Utilisateur_Prenom'])
                                                            . '</option>';
                                                    }
                                                } catch (PDOException $e) {
                                                    echo '<option value="0">Erreur: ' . htmlspecialchars($e->getMessage()) . '</option>';
                                                }
                                            ?>
                                        </select>
                                        <label for="floatingSelect">Donneur d'ordre *</label>
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
                                </div>
                            </form>
                        </div>
                        <!-- modal footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="button" class="btn btn-success" id="btnAjouter" onclick="submitPreFillClientInfo()">Ajouter</button>
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
                                            <option value="new_company">Créer une nouvelle entreprise</option>
                                            <?php
                                            //$companies = getAllCompany($db);
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

        <script src="../../js/detail_client.js"></script>
    </body>
</html>