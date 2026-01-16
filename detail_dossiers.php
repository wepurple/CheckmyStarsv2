<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dossiers- CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
        <script src="bootstrap 5.3/js/bootstrap.js"></script>
        <script src="js/search_inspecteurs.js"></script>
        <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
    </head>

    <body class="bg-secondary">
        <?php
            require_once "./includes/navbar.php";
        ?>

            <div class="container-fluid p-3">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <button type="button" class="btn btn-danger" onclick="location.href='dashboard.php'" > 
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord 
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
                        <th> Code Postal</th>
                        <th> Ville</th>
                        <th> Pays </th>
                        <th>Status</th>
                    </tr>
                </thead>
        <tbody>
                    <?php
                    require_once './includes/mariadb.php';
                    
                    $database = new Database();
                    $db = $database->getConnection();

                    $doBienID = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
                    
                    if (is_array($db)) {
                        echo "<tr><td colspan='10' class='text-center text-danger'>Erreur de connexion à la base de données</td></tr>";
                    } elseif (empty($doBienID)) {
                        echo "<tr><td colspan='10' class='text-center text-warning'>Aucun utilisateur sélectionné</td></tr>";
                    }else {
                        try {
                            // Requête pour récupérer tous les dossiers
                            $sql = "SELECT b.Bien_ID,
                            u.Utilisateur_Nom,
                            u.Utilisateur_Prenom,
                            u.Utilisateur_Telephone,
                            b.Biens_Nom,
                            b.Bien_Telephone,
                            b.Bien_DateEnregistrement,
                            b.Bien_Etoile_Actuelle,
                            b.AdressePostale_ID,
                            t.TypeHebergement_Nom,
                            d.DonneurOrdre_Entreprine_Nom
                            FROM biens as b
                            INNER JOIN utilisateurs as u on b.Utilisateur_ID = u.Utilisateur_ID
                            INNER JOIN donneurordre as d on b.Donneur_ID = d.Donneur_ID
                            INNER JOIN typeshebergements as t on b.TypeHebergement_ID = t.TypeHebergement_ID
                            INNER JOIN dossiers as do on u.Utilisateur_ID = do.Utilisateur_ID
                            WHERE b.Bien_ID = :doBienID
                            ORDER BY B.Bien_ID DESC";
                            
                            $stmt = $db->query($sql);
                            $stmt->execute([':doBienID' => $doBienID]);
                            
                            if ($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<td>" . htmlspecialchars($row['Bien_ID']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Utilisateur_Nom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Utilisateur_Prenom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Utilisateur_Telephone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Biens_Nom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Bien_Telephone']) . " " ."</td>";
                                    echo "<td>" . htmlspecialchars($row['Bien_DateEnregistrement']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Bien_Etoile_Actuelle']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['AdressePostale_ID']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['TypeHebergement_Nom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['DonneurOrdre_Entreprine_Nom']) . "</td>";
                                    
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
</body>
</html>
