<?php
    session_start();
    
    //verifie le rôle de l'utilisateur connecté
    if(isset($_SESSION['Role']['Administrateur']) || isset($_SESSION['Role']['Inspecteur'])){
        if(!$_SESSION['Role']['Administrateur'] && !$_SESSION['Role']['Inspecteur']){
            header('Location: deco.php');
            die();
        }
    } else {
        header('Location: deco.php');
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Gestion des dossiers - CheckMyStars</title>

        <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.min.css">
        <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">

        <script type='text/javascript'>
 
            function getXhr(){
                                var xhr = null; 
                if(window.XMLHttpRequest) // Firefox et autres
                   xhr = new XMLHttpRequest(); 
                else if(window.ActiveXObject){ // Internet Explorer 
                   try {
                            xhr = new ActiveXObject("Msxml2.XMLHTTP");
                        } catch (e) {
                            xhr = new ActiveXObject("Microsoft.XMLHTTP");
                        }
                }
                else { // XMLHttpRequest non supporté par le navigateur 
                   alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest..."); 
                   xhr = false; 
                } 
                                return xhr;
            }
 
            /**
            * Méthode qui sera appelée sur le clic du bouton
            */
            function test(){
                var xhr = getXhr();
                // On définit ce qu'on va faire quand on aura la réponse
                xhr.onreadystatechange = function(){
                    // On ne fait quelque chose que si on a tout reçu et que le serveur est OK
                    if(xhr.readyState == 4 && xhr.status == 200){
                        try {
                            var data = JSON.parse(xhr.responseText);
                            if (data.error) {
                                alert('Erreur : ' + data.error);
                            } else {
                                // Remplir les champs avec les données reçues
                                document.getElementById('leMail').value = data.mail;
                                document.getElementById('leTel').value = data.telephone;
                                document.getElementById('laSociete').value = data.societe;
                                
                                // 
                                // document.getElementById('leCode').value = data.codepostal;
                                // document.getElementById('leNumRue').value = data.numerorue;
                                // document.getElementById('laAdresse').value = data.nomrue;
                                // document.getElementById('laVille').value = data.ville;
                                // document.getElementById('lePays').value = data.pays;
                            }
                        } catch(e) {
                            alert('Erreur de traitement de la réponse');
                        }
                    }
                }
 
                // Récupérer l'ID du client sélectionné
                sel = document.getElementById('leClient');
                idclient = sel.options[sel.selectedIndex].value;
                
                // Requête POST
                xhr.open("POST","ajaxtest/ajaxDossier.php",true);
                xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
                xhr.send("Utilisateur_ID="+idclient);
            }
        </script>

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
                        <th> Code Postal</th>
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

                        if (isset($_SESSION['Role']['Administrateur']) && $_SESSION['Role']['Administrateur']) {
                            $sql = "CALL Get_Dossier();";
                        } elseif (isset($_SESSION['Role']['Inspecteur']) && $_SESSION['Role']['Inspecteur']) {
                            $inspecteurID = $_SESSION['Utilisateur_ID'];
                            $sql = "CALL Get_Dossier_By_Inspecteur(:inspecteurID);";
                        }
                            $stmt = $db->prepare($sql);
                            $stmt->execute();
                            
                            if ($stmt->rowCount() > 0) {
                                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr style='cursor: pointer;' onclick=\"window.location.href='front_dossier.php?id=" . urlencode($row['Dossier_ID']) . "'\">";
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
                                    <?php
                                        require_once('./includes/mariadb.php');
                                        
                                        $database = new Database();
                                        $db = $database->getConnection();
                                        
                                        if (is_array($db)) {
                                            echo "<p class='text-danger'>Erreur de connexion à la base de données</p>";
                                        } else {
                                            try {
                                                $sql = "SELECT Utilisateur_ID, Utilisateur_Nom, Utilisateur_Prenom FROM utilisateurs ORDER BY Utilisateur_Nom ASC, Utilisateur_Prenom ASC";
                                                $stmt = $db->prepare($sql);
                                                $stmt->execute();
                                                
                                                echo '<select class="form-select" id="leClient" onchange="test()" ">';
                                                echo '<option selected disabled>Choisir un client</option>';
                                                
                                                if ($stmt->rowCount() > 0) {
                                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $fullName = htmlspecialchars($row['Utilisateur_Nom'] . ' ' . $row['Utilisateur_Prenom']);
                                                        echo '<option value="' . htmlspecialchars($row['Utilisateur_ID']) . '">' . $fullName . '</option>';
                                                    }
                                                } else {
                                                    echo '<option disabled>Aucun client trouvé</option>';
                                                }
                                                
                                                echo '</select>';
                                            } catch(PDOException $e) {
                                                echo "<p class='text-danger'>Erreur : " . $e->getMessage() . "</p>";
                                            }
                                        }

                                    ?>
                                </div>

                                <!-- <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="leNom" placeholder="" required>
                                    <label for="floatingInput">Nom *</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="lePrenom" placeholder="" required>
                                    <label for="floatingInput">Prenom *</label>
                                </div> -->

                                <div id="mail" class="form-floating mb-3">
                                    <input type="text" class="form-control" id="leMail" placeholder="" value="" required>
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

                                <div id = "tel" class="form-floating mb-3">
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

            
    </body>
</html>  