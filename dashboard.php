<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="fr">
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
                        <span class="input-group-text">Rechercher par</span>
                        <select class="form-select" id="inputGroupSelect01">
                            <option selected>--selectionner--</option>
                            <option value="1">Clients</option>
                            <option value="2">En cours</option>
                            <option value="3">Terminé</option>
                        </select>
                        <input id="recherche" type="text" aria-label="Last name" class="form-control">
                    </div>
                </div>
            </nav>
            <a href="#">
                <button>Creer</button>
            </a>
            <!-- Tableau -->
            <table class="table table-dark table-sm table-striped table-hover">
                <thead>
                    <tr>
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
                    require_once('./includes/mariadb.php');
                    
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    if (is_array($db)) {
                        echo "<tr><td colspan='6' class='text-center text-danger'>Erreur de connexion à la base de données</td></tr>";
                    } else {
                        try {
                            // Requête pour récupérer toutes les données
                            $sql = "SELECT nom, societe, telephone, mail, nombre_dossiers, status FROM clients ORDER BY nom ASC";
                            $stmt = $db->prepare($sql);
                            $stmt->execute();
                            
                            if ($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['societe']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['telephone']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['mail']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nombre_dossiers']) . "</td>";
                                    
                                    // Badge pour le statut (0 = En cours, 1 = Terminé)
                                    $statusText = $row['status'] == 1 ? 'Terminé' : 'En cours';
                                    $statusClass = $row['status'] == 1 ? 'bg-success' : 'bg-warning text-dark';
                                    echo "<td><span class='badge $statusClass'>$statusText</span></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>Aucune donnée trouvée</td></tr>";
                            }
                        } catch(PDOException $e) {
                            echo "<tr><td colspan='6' class='text-center text-danger'>Erreur : " . $e->getMessage() . "</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>