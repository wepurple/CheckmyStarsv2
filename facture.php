<?php
session_start();
require_once('includes/mariadb.php');

// Vérifier si l'utilisateur est connecté en tant qu'inspecteur
    if(isset($_SESSION['Role'])){
        if(!$_SESSION['Role']['Inspecteur'] || !$_SESSION['Role']['Administrateur']){
            header('Location: deco.php');
            die();
        }
    } else {
        header('Location: deco.php');
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Gestion des Factures - CheckMyStars</title>

    <link rel="stylesheet" href="./fontawesome-7.1.0/css/all.css">
    <link rel="stylesheet" href="bootstrap 5.3/css/bootstrap.css">
    <link rel="stylesheet" href="bootstrap 5.3/css/facture.css">
    <link rel="icon" type="image/x-icon" href="pictures/logosm.png">
</head>
<body class="bg-secondary">
     <?php
            require_once("./includes/navbar.php");
        ?>
    
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Chargement...</span>
        </div>
    </div>

    <div class="container-fluid mt-4">
        <div class="facture-wrapper">
            <!-- FORMULAIRE D'ÉDITION -->
            <div class="formulaire-section">
                <div class="d-flex gap-3 mb-4 justify-content-center">
                    <button class="btn btn-primary btn-lg px-4" id="btn-facture" onclick="handlePreview('FACTURE')">
                        <i class="fa-solid fa-file-invoice me-2"></i> Créer une facture
                    </button>
                    <button class="btn btn-success btn-lg px-4" id="btn-devis" onclick="handlePreview('DEVIS')">
                        <i class="fa-solid fa-file-lines me-2"></i> Créer un devis
                    </button>
                </div>
                <!-- Conteneur principal Facture / Devis (sans collapse) -->
                <div class="form-sections" id="formulairePrincipal">
                    <!-- SECTION FACTURE -->
                    <div class="accordion-item" id="section-facture">
                        <div id="collapseFactureSection" class="accordion-collapse show">
                            <div class="accordion-body p-0">
                                <div class="form-container">
                                    <!-- Accordion pour les formulaires Facture -->
                                    <div class="accordion mb-4" id="formulaireAccordionFacture">
                                        
                                        <!-- Informations Entreprise -->
                                        <div class="accordion-item">
                                            <h2 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEntreprise">
                                                    <i class="bi bi-building me-2"></i> Informations Entreprise
                                                </button>
                                            </h2>
                                            <div id="collapseEntreprise" class="accordion-collapse collapse show" data-bs-parent="#formulaireAccordionFacture">
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

                    <!-- SECTION DEVIS -->
                    <div class="accordion-item d-none" id="section-devis">
                        <div id="collapseDevisSection" class="accordion-collapse show">
                            <div class="accordion-body p-0">
                                <div class="form-container">
                                    <!-- Accordion pour les formulaires Devis -->
                                    <div class="accordion mb-4" id="formulaireAccordionDevis">
                                        
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
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Nom</label>
                                                            <input type="text" class="form-control" id="entreprise_nom_devis" value="CETIRE">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">SIRET</label>
                                                            <input type="text" class="form-control" id="entreprise_siret_devis" value="123 456 789 00012">
                                                        </div>
                                                        <div class="col-md-12 mb-2">
                                                            <label class="form-label">Adresse</label>
                                                            <input type="text" class="form-control" id="entreprise_adresse_devis" value="51 rue du Faubourg de Bourgogne">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Code Postal</label>
                                                            <input type="text" class="form-control" id="entreprise_cp_devis" value="45000">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Ville</label>
                                                            <input type="text" class="form-control" id="entreprise_ville_devis" value="ORLEANS">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">Téléphone</label>
                                                            <input type="text" class="form-control" id="entreprise_tel_devis" value="02 38 54 32 10">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label class="form-label">N° TVA</label>
                                                            <input type="text" class="form-control" id="entreprise_tva_devis" value="FR76 102 783 725 001">
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
                                                            <input type="text" class="form-control" id="devis_numero" value="DEV-2026-001">
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
                                                            <input type="text" class="form-control" id="client_nom_devis" value="Hôtel Le Château">
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

            <!-- PRÉVISUALISATION PDF -->
            <div class="apercu-section">
                <div class="sticky-actions">
                    <div class="d-flex gap-2 mb-3">
                        <button class="btn btn-primary flex-fill" onclick="updatePreview()">
                            <i class="bi bi-arrow-clockwise"></i> Actualiser l'aperçu
                        </button>
                        <button class="btn btn-success flex-fill" onclick="downloadPDF()">
                            <i class="bi bi-download"></i> Télécharger PDF
                        </button>
                    </div>
                    
                    <div class="preview-container">
                        <h3 class="mb-3" id="preview-title">Aperçu de la Facture</h3>
                        <div id="pdf-preview" class="preview-pdf">
                            <!-- La prévisualisation sera générée ici -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="bootstrap 5.3/js/bootstrap.bundle.min.js"></script>
    <script src="js/facture.js"></script>
</body>
</html>
