<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion des dossiers - CheckMyStars</title>

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
                    <i class="fas fa-plus"></i> Ajouter un dossier
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
                        <th>N° DOSSIER</th>
                        <th>TYPE</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>ADRESSE HÉBERGEMENT</th>
                        <th> Ville</th>
                        <th> Pays </th>
                        <th>Status</th>
                    </tr>
                </thead>
        <tbody>
                    <?php
                    require_once('./includes/mariadb.php');
                    
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    if (is_array($db)) {
                        echo "<tr><td colspan='7' class='text-center text-danger'>Erreur de connexion à la base de données</td></tr>";
                    } else {
                        try {
                            // Requête pour récupérer tous les dossiers
                            $sql = "SELECT d.Dossier_ID, d.DOSSIER_NUMERO, t.TypeHebergement_Nom, u.Utilisateur_Nom, u.Utilisateur_Prenom, a.AdressePostale_NumeroRue, a.AdressePostale_NomRue, a.AdressePostale_CodePostal, a.AdressePostale_Ville, a.AdressePostale_Pays, d.status 
                                    FROM dossiers AS d 
                                    INNER JOIN utilisateurs AS u ON d.Utilisateur_ID = u.Utilisateur_ID 
                                    INNER JOIN adressespostales AS a ON a.AdressePostale_ID = u.AdressePostale_ID 
                                    INNER JOIN biens AS b ON b.AdressePostale_ID = a.AdressePostale_ID 
                                    INNER JOIN typeshebergements AS t ON t.TypeHebergement_ID = b.TypeHebergement_ID
                                    ORDER BY d.Dossier_ID DESC";
                            $stmt = $db->prepare($sql);
                            $stmt->execute();
                            
                            if ($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                     echo "<tr style='cursor: pointer;' onclick=\"window.location.href='detail_client.php?id=" . urlencode($row['Dossier_ID']) . "'\">";
                                    echo "<td>" . htmlspecialchars($row['Dossier_ID']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['DOSSIER_NUMERO']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['TypeHebergement_Nom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Utilisateur_Nom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Utilisateur_Prenom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['AdressePostale_NumeroRue']) . " " . htmlspecialchars($row['AdressePostale_NomRue']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['AdressePostale_CodePostal']) . " " . htmlspecialchars($row['AdressePostale_Ville']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['AdressePostale_Pays']) . "</td>";
                                    
                                    // Badge pour le statut (0 = En cours, 1 = Terminé)
                                    $statusText = $row['status'] == 1 ? 'Terminé' : 'En cours';
                                    $statusClass = $row['status'] == 1 ? 'bg-success' : 'bg-warning text-dark';
                                    echo "<td><span class='badge $statusClass'>$statusText</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center'>Aucune donnée trouvée</td></tr>";
                            }
                        } catch(PDOException $e) {
                            echo "<tr><td colspan='7' class='text-center text-danger'>Erreur : " . $e->getMessage() . "</td></tr>";
                        }
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
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un dossier au client</h1>
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
                                    <select class="form-select" id="typedebien" aria-label="Floating label select example">
                                        <option value="1">Maison</option>
                                        <option value="2">Appartement</option>
                                         <option value="3">Hotel</option>
                                         <option value="4">Camping</option>
                                        <option selected value="5">Local commercial</option>
                                    </select>
                                    <label for="floatingSelect">Type de bien </label>
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
                            <button type="button" class="btn btn-success" id="btnAjouter">Ajouter</button>
                        </div>
                    </div>                    
                </div>
            </div>

            <script>
            // Soumission du formulaire d'ajout de dossier
            document.getElementById('btnAjouter').addEventListener('click', function() {
                const payload = {
                    nom: document.getElementById('leNom').value,
                    prenom: document.getElementById('lePrenom').value,
                    mail: document.getElementById('leMail').value,
                    telephone: document.getElementById('leTel').value,
                    societe: document.getElementById('laSociete').value,
                    typeHebergement: document.getElementById('typedebien').value,
                    numRue: document.getElementById('leNumRue').value,
                    nomRue: document.getElementById('laAdresse').value,
                    complement: document.getElementById('leComplement').value,
                    codePostal: document.getElementById('leCode').value,
                    ville: document.getElementById('laVille').value,
                    pays: document.getElementById('lePays').value
                };

                fetch('models/crud/ajouterDossier.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(r => r.json())
                .then(result => {
                    if (result.success) {
                        alert('Dossier créé : ' + (result.numeroDossier || 'créé'));
                        location.reload();
                    } else {
                        alert('Erreur : ' + (result.message || 'Une erreur est survenue'));
                    }
                })
                .catch(err => alert('Erreur réseau : ' + err));
            });
            </script>
    </body>
</html>  