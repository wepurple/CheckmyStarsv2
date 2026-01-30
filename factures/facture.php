<?php
session_start();
require_once('../includes/mariadb.php');

// Vérifier si l'utilisateur est connecté en tant qu'inspecteur
    if(isset($_SESSION['Role'])){
        if(!$_SESSION['Role']['Inspecteur'] && !$_SESSION['Role']['Administrateur']){
            header('Location: ../deco.php');
            die();
        }
    } else {
        header('Location: ../deco.php');
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    
    <title>Gestion des Factures - CheckMyStars</title>

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
            * Charge les données entreprise depuis le select
            */
            function loadEntrepriseData(){
                var sel = document.getElementById('entreprise_select_devis');
                var opt = sel.options[sel.selectedIndex];
                
                if (!opt || !opt.value) return;
                
                document.getElementById('entreprise_nom_devis').value = opt.getAttribute('data-nom') || '';
                document.getElementById('entreprise_adresse_devis').value = opt.getAttribute('data-adresse') || '';
                document.getElementById('entreprise_cp_devis').value = opt.getAttribute('data-cp') || '';
                document.getElementById('entreprise_ville_devis').value = opt.getAttribute('data-ville') || '';
                document.getElementById('entreprise_tel_devis').value = opt.getAttribute('data-tel') || '';
                document.getElementById('entreprise_siret_devis').value = opt.getAttribute('data-siret') || '';
                document.getElementById('entreprise_tva_devis').value = opt.getAttribute('data-tva') || '';
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
                                document.getElementById('client_adresse_devis').value = data.adresse || '';
                                document.getElementById('client_cp_devis').value = data.codepostal || '';
                                document.getElementById('client_ville_devis').value = data.ville || '';
                            }
                        } catch(e) {
                            console.error('Erreur de traitement:', e);
                            alert('Erreur de traitement de la réponse: ' + e.message);
                        }
                    }
                }
 
                // Récupérer l'ID du client sélectionné depuis l'attribut data-id
                var sel = document.getElementById('client_nom_devis');
                var idclient = sel.options[sel.selectedIndex].getAttribute('data-id');
                
                if (!idclient) {
                    alert('Erreur : impossibile récupérer l\'ID du client');
                    return;
                }
                
                // Requête POST
                xhr.open("POST","ajaxtest/ajaxDevis.php",true);
                xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
                xhr.send("Client_ID="+idclient);
            }
            
            /**
            * Récupère l'ID de l'entreprise sélectionnée
            */
            function getSelectedEntrepriseId(){
                var sel = document.getElementById('entreprise_select_devis');
                if (sel && sel.value) {
                    return parseInt(sel.value);
                }
                return 1; // ID par défaut
            }
            
            /**
            * Récupère l'ID du client sélectionné
            */
            function getSelectedClientId(){
                var sel = document.getElementById('client_nom_devis');
                if (sel && sel.selectedIndex > 0) {
                    return sel.options[sel.selectedIndex].getAttribute('data-id');
                }
                return null;
            }
        </script>
    <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
    <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
    <link rel="stylesheet" href="bootstrap 5.3/css/facture.css">
    <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
</head>
<body class="bg-light">
     <?php
            require_once("../includes/navbar.php");
        ?>
    
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>

    <div class="container-fluid px-2 px-md-3 px-lg-4 mt-3 mt-md-4 pb-5">
        <div class="facture-wrapper" id="facture-wrapper">
            <!-- FORMULAIRE D'ÉDITION -->
            <div class="formulaire-section" id="formulaire-section">
                <!-- Barre d'actions en haut -->
                <div class="card shadow-sm mb-3">
                    <div class="card-body p-2 p-md-3">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-lg-auto">
                                <button class="btn btn-success w-100 py-2" id="btn-devis" onclick="initNewDevis(); handlePreview('DEVIS')">
                                    <i class="fa-solid fa-plus me-1"></i> Créer un devis
                                </button>
                            </div>
                            <div class="col-12 col-sm-6 col-lg">
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">D</span>
                                    <select id="devisSelector" class="form-select" onchange="if(this.value) loadDevisFromDropdown()">
                                        <option value="">-- Sélectionner un devis --</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 col-lg">
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">F</span>
                                    <select id="factureSelector" class="form-select" onchange="if(this.value) loadFacture()">
                                        <option value="">-- Sélectionner une facture --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Conteneur principal Facture / Devis (sans collapse) -->
                <div class="form-sections" id="formulairePrincipal">
                    <!-- SECTION FACTURE -->
                    <div class="accordion-item" id="section-facture">
                        <div id="collapseFactureSection" class="accordion-collapse show">
                            <div class="accordion-body p-0">
                                <div class="card shadow-sm">
                                    <div class="card-body p-2 p-md-3">
                                    <!-- Accordion pour les formulaires Facture -->
                                    <div class="accordion" id="formulaireAccordionFacture">
                                        
                                        <!-- Informations Entreprise -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEntreprise">
                                                    <i class="bi bi-building me-2"></i> Informations Entreprise
                                                </button>
                                            </h2>
                                            <div id="collapseEntreprise" class="accordion-collapse collapse" data-bs-parent="#formulaireAccordionFacture">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Nom</label>
                                                            <input type="text" class="form-control" id="entreprise_nom" value="CETIRE">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">SIRET</label>
                                                            <input type="text" class="form-control" id="entreprise_siret" value="123 456 789 00012">
                                                        </div>
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Adresse</label>
                                                            <input type="text" class="form-control" id="entreprise_adresse" value="51 rue du Faubourg de Bourgogne">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Code Postal</label>
                                                            <input type="text" class="form-control" id="entreprise_cp" value="45000">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Ville</label>
                                                            <input type="text" class="form-control" id="entreprise_ville" value="ORLEANS">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Téléphone</label>
                                                            <input type="text" class="form-control" id="entreprise_tel" value="02 38 54 32 10">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">N° TVA</label>
                                                            <input type="text" class="form-control" id="entreprise_tva" value="FR76 102 783 725 001">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informations Facture -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFacture">
                                                    <i class="bi bi-receipt me-2"></i> Informations Facture
                                                </button>
                                            </h2>
                                            <div id="collapseFacture" class="accordion-collapse collapse" data-bs-parent="#formulaireAccordionFacture">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">N° Facture</label>
                                                            <input type="text" class="form-control" id="facture_numero" value="FACT-2026-001">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Date</label>
                                                            <input type="date" class="form-control" id="facture_date" value="2026-01-15">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informations Client -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseClient">
                                                    <i class="bi bi-person me-2"></i> Informations Client
                                                </button>
                                            </h2>
                                            <div id="collapseClient" class="accordion-collapse collapse" data-bs-parent="#formulaireAccordionFacture">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Nom Client</label>
                                                            <input type="text" class="form-control" id="client_nom" value="Hôtel Le Château">
                                                        </div>
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Adresse</label>
                                                            <input type="text" class="form-control" id="client_adresse" value="15 Avenue des Roses">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Code Postal</label>
                                                            <input type="text" class="form-control" id="client_cp" value="45100">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Ville</label>
                                                            <input type="text" class="form-control" id="client_ville" value="ORLEANS">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Prestations -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrestations">
                                                    <i class="bi bi-list-check me-2"></i> Prestations
                                                </button>
                                            </h2>
                                            <div id="collapsePrestations" class="accordion-collapse collapse" data-bs-parent="#formulaireAccordionFacture">
                                                <div class="accordion-body">
                                                    <div id="lignes-container">
                                                        <!-- Les lignes seront ajoutées dynamiquement -->
                                                    </div>
                                                    <button type="button" class="btn btn-success btn-add-ligne" onclick="addLigne('FACTURE')">
                                                        <i class="bi bi-plus-circle"></i> Ajouter une prestation
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION DEVIS -->
                    <div class="accordion-item d-none" id="section-devis">
                        <div id="collapseDevisSection" class="accordion-collapse show">
                            <div class="accordion-body p-0">
                                <div class="card shadow-sm">
                                    <div class="card-body p-2 p-md-3">
                                    <!-- Accordion pour les formulaires Devis -->
                                    <div class="accordion" id="formulaireAccordionDevis">
                                        
                                        <!-- Informations Entreprise Devis -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEntrepriseDevis">
                                                    <i class="bi bi-building me-2"></i> Informations Entreprise
                                                </button>
                                            </h2>
                                            <div id="collapseEntrepriseDevis" class="accordion-collapse collapse show" data-bs-parent="#formulaireAccordionDevis">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <label class="form-label">Sélectionner une entreprise</label>
                                                            <?php
                                                            // Charger les entreprises depuis la BDD
                                                            $database = new Database();
                                                            $dbEntreprise = $database->getConnection();
                                                            
                                                            if (!is_array($dbEntreprise)) {
                                                                try {
                                                                    $sqlEntreprise = "SELECT Entreprise_ID, Entreprise_Nom, Entreprise_Ville, Entreprise_Adresse, Entreprise_CodePostal, Entreprise_Telephone, Entreprise_SIRET, Entreprise_TVA_Intra FROM entreprisefacturation WHERE Entreprise_Actif = 1 ORDER BY Entreprise_Nom ASC";
                                                                    $stmtEntreprise = $dbEntreprise->prepare($sqlEntreprise);
                                                                    $stmtEntreprise->execute();
                                                                    
                                                                    echo '<select class="form-control" id="entreprise_select_devis" onchange="loadEntrepriseData()">';
                                                                    echo '<option value="" disabled>Choisir une entreprise</option>';
                                                                    
                                                                    $first = true;
                                                                    while($rowE = $stmtEntreprise->fetch(PDO::FETCH_ASSOC)) {
                                                                        $selected = $first ? ' selected' : '';
                                                                        echo '<option value="' . htmlspecialchars($rowE['Entreprise_ID']) . '"';
                                                                        echo ' data-nom="' . htmlspecialchars($rowE['Entreprise_Nom']) . '"';
                                                                        echo ' data-adresse="' . htmlspecialchars($rowE['Entreprise_Adresse']) . '"';
                                                                        echo ' data-cp="' . htmlspecialchars($rowE['Entreprise_CodePostal']) . '"';
                                                                        echo ' data-ville="' . htmlspecialchars($rowE['Entreprise_Ville']) . '"';
                                                                        echo ' data-tel="' . htmlspecialchars($rowE['Entreprise_Telephone']) . '"';
                                                                        echo ' data-siret="' . htmlspecialchars($rowE['Entreprise_SIRET']) . '"';
                                                                        echo ' data-tva="' . htmlspecialchars($rowE['Entreprise_TVA_Intra']) . '"';
                                                                        echo $selected . '>';
                                                                        echo htmlspecialchars($rowE['Entreprise_Nom'] . ' - ' . $rowE['Entreprise_Ville']);
                                                                        echo '</option>';
                                                                        $first = false;
                                                                    }
                                                                    
                                                                    echo '</select>';
                                                                } catch(PDOException $e) {
                                                                    echo "<p class='text-danger'>Erreur : " . $e->getMessage() . "</p>";
                                                                }
                                                            } else {
                                                                echo "<p class='text-danger'>Erreur de connexion</p>";
                                                            }
                                                            ?>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Nom</label>
                                                            <input type="text" class="form-control" id="entreprise_nom_devis" value="CETIRE" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">SIRET</label>
                                                            <input type="text" class="form-control" id="entreprise_siret_devis" value="123 456 789 00012" readonly>
                                                        </div>
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Adresse</label>
                                                            <input type="text" class="form-control" id="entreprise_adresse_devis" value="51 rue du Faubourg de Bourgogne" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Code Postal</label>
                                                            <input type="text" class="form-control" id="entreprise_cp_devis" value="45000" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Ville</label>
                                                            <input type="text" class="form-control" id="entreprise_ville_devis" value="ORLEANS" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Téléphone</label>
                                                            <input type="text" class="form-control" id="entreprise_tel_devis" value="02 38 54 32 10" readonly>
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">N° TVA</label>
                                                            <input type="text" class="form-control" id="entreprise_tva_devis" value="FR76 102 783 725 001" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informations Devis -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDevisInfo">
                                                    <i class="bi bi-receipt me-2"></i> Informations Devis
                                                </button>
                                            </h2>
                                            <div id="collapseDevisInfo" class="accordion-collapse collapse" data-bs-parent="#formulaireAccordionDevis">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">N° Devis</label>
                                                            <input type="text" class="form-control" id="devis_numero" readonly placeholder="Génération automatique...">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Date</label>
                                                            <input type="date" class="form-control" id="devis_date" value="2026-01-15">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informations Client Devis -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseClientDevis">
                                                    <i class="bi bi-person me-2"></i> Informations Client
                                                </button>
                                            </h2>
                                            <div id="collapseClientDevis" class="accordion-collapse collapse" data-bs-parent="#formulaireAccordionDevis">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Nom Client</label>
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
                                                
                                                echo '<select class="form-control" id="client_nom_devis" onchange="loadClientData()">';
                                                echo '<option selected disabled>Choisir un client</option>';
                                                
                                                if ($stmt->rowCount() > 0) {
                                                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                        $fullName = htmlspecialchars($row['Utilisateur_Nom'] . ' ' . $row['Utilisateur_Prenom']);
                                                        echo '<option value="' . $fullName . '" data-id="' . htmlspecialchars($row['Utilisateur_ID']) . '">' . $fullName . '</option>';
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
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Adresse</label>
                                                            <input type="text" class="form-control" id="client_adresse_devis" value="15 Avenue des Roses">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Code Postal</label>
                                                            <input type="text" class="form-control" id="client_cp_devis" value="45100">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Ville</label>
                                                            <input type="text" class="form-control" id="client_ville_devis" value="ORLEANS">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Prestations Devis -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePrestationsDevis">
                                                    <i class="bi bi-list-check me-2"></i> Prestations
                                                </button>
                                            </h2>
                                            <div id="collapsePrestationsDevis" class="accordion-collapse collapse" data-bs-parent="#formulaireAccordionDevis">
                                                <div class="accordion-body">
                                                    <div id="lignes-container-devis">
                                                        <!-- Les lignes seront ajoutées dynamiquement -->
                                                    </div>
                                                    <button type="button" class="btn btn-success btn-add-ligne" onclick="addLigne('DEVIS')">
                                                        <i class="bi bi-plus-circle"></i> Ajouter une prestation
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- PRÉVISUALISATION PDF -->
            <div class="apercu-section" id="apercu-section">
                <div class="sticky-actions">
                    <!-- Barre d'actions principale -->
                    <div class="card shadow-sm mb-3" id="action-buttons-card">
                        <div class="card-body p-2">
                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                <button class="btn btn-primary flex-fill" onclick="updatePreview()" id="btn-actualiser">
                                    <i class="fa-solid fa-sync me-1"></i> <span class="d-none d-sm-inline">Actualiser</span>
                                </button>
                                <button class="btn btn-success flex-fill" onclick="downloadPDF()" id="btn-download">
                                    <i class="fa-solid fa-download me-1"></i> <span class="d-none d-sm-inline">Télécharger</span>
                                </button>
                                <button class="btn btn-warning flex-fill" onclick="saveDevis()" id="btn-save-devis">
                                    <i class="fa-solid fa-save me-1"></i> <span class="d-none d-sm-inline">Sauvegarder</span>
                                </button>
                                <button class="btn btn-info flex-fill text-white" onclick="convertDevisToFacture()" id="btn-convert-facture">
                                    <i class="fa-solid fa-file-invoice me-1"></i> <span class="d-none d-sm-inline">Convertir</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Badge lecture seule (affiché dynamiquement) -->
                    <div id="readonly-badge-container"></div>
                    
                    <div class="card shadow-sm">
                        <div class="card-header py-2">
                            <h5 class="mb-0 fs-6" id="preview-title">Aperçu du Devis</h5>
                        </div>
                        <div class="card-body p-2 p-md-3">
                            <div id="pdf-preview" class="preview-pdf">
                                <!-- La prévisualisation sera générée ici -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre d'actions sticky mobile (visible seulement sur mobile) -->
    <div class="mobile-action-bar d-lg-none" id="mobile-action-bar">
        <div class="d-flex gap-2 justify-content-between">
            <button class="btn btn-success flex-fill py-2" onclick="downloadPDF()">
                <i class="fa-solid fa-download"></i> PDF
            </button>
            <button class="btn btn-warning flex-fill py-2" onclick="saveDevis()" id="btn-save-mobile">
                <i class="fa-solid fa-save"></i> Sauver
            </button>
            <button class="btn btn-info flex-fill py-2 text-white" onclick="convertDevisToFacture()" id="btn-convert-mobile">
                <i class="fa-solid fa-file-invoice"></i> Facture
            </button>
        </div>
    </div>

    <script src="bootstrap 5.3/js/bootstrap.bundle.min.js"></script>
    <script src="js/facture.js?v=<?= time() ?>"></script>
</body>
</html>
