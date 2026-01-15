<?php
// Désactiver l'affichage des erreurs pour éviter la corruption du PDF
error_reporting(0);
ini_set('display_errors', 0);

// Nettoyer tout buffer existant dès le début
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

require_once('vendor/autoload.php');

// Récupérer les données JSON envoyées
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    ob_end_clean();
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Données invalides']);
    exit;
}

// Extraire et valider les données
$entreprise = $data['entreprise'] ?? [];
$facture = $data['facture'] ?? [];
$client = $data['client'] ?? [];
$lignes = $data['lignes'] ?? [];

// Vérifier que les champs essentiels sont présents
if (empty($entreprise['nom']) || empty($facture['numero']) || empty($lignes)) {
    ob_end_clean();
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Données essentielles manquantes']);
    exit;
}

// Calculer les totaux
$total_ht = 0;
$total_tva = 0;
$total_ttc = 0;

foreach ($lignes as &$ligne) {
    $total_ht += floatval($ligne['montant_ht']);
    $total_tva += floatval($ligne['montant_tva']);
    $total_ttc += floatval($ligne['montant_ttc']);
}

// Créer le PDF avec TCPDF
$pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetCreator($entreprise['nom']);
$pdf->SetAuthor($entreprise['nom']);
$pdf->SetTitle('Facture ' . $facture['numero']);
$pdf->SetSubject('Facture');

// Marges
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 40);

// Supprimer en-tête et pied de page par défaut
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// ========================================
// EN-TÊTE
// ========================================

$pdf->SetFont('helvetica', 'B', 24);
$pdf->SetTextColor(37, 99, 235);
$pdf->Cell(0, 10, $entreprise['nom'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 4, 'Diagnostic et Inspection Immobilière', 0, 1, 'L');
$pdf->Ln(3);

// Ligne de séparation
$pdf->SetDrawColor(37, 99, 235);
$pdf->SetLineWidth(1);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5);

// ========================================
// INFORMATIONS FACTURE ET CLIENT
// ========================================

$y_start = $pdf->GetY();

// Colonne gauche - Informations entreprise
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(90, 5, $entreprise['nom'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(80, 80, 80);
$adresse_texte = $entreprise['adresse'] . "\n" . 
                 $entreprise['cp'] . ' ' . $entreprise['ville'] . "\n" .
                 'Tél. : ' . $entreprise['tel'] . "\n" .
                 'SIRET : ' . $entreprise['siret'] . "\n" .
                 'N° TVA : ' . $entreprise['tva'];
$pdf->MultiCell(90, 3.5, $adresse_texte, 0, 'L', false, 1);

// Colonne droite - Informations facture
$pdf->SetXY(120, $y_start);
$pdf->SetFillColor(37, 99, 235);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(75, 8, 'FACTURE', 0, 1, 'C', true);

$pdf->SetX(120);
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(50, 50, 50);
$pdf->SetFillColor(245, 245, 245);
$pdf->Cell(25, 5, 'N° : ', 0, 0, 'L', false);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(50, 5, $facture['numero'], 0, 1, 'L', false);

$pdf->SetX(120);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(25, 5, 'Date : ', 0, 0, 'L', false);
$pdf->SetFont('helvetica', 'B', 9);
$date_obj = DateTime::createFromFormat('Y-m-d', $facture['date']);
$date_formatted = $date_obj ? $date_obj->format('d/m/Y') : $facture['date'];
$pdf->Cell(50, 5, $date_formatted, 0, 1, 'L', false);

$pdf->Ln(8);

// Encadré client
$pdf->SetFillColor(245, 245, 245);
$y_client = $pdf->GetY();
$pdf->Rect(120, $y_client, 75, 25, 'F');

$pdf->SetXY(120, $y_client);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetTextColor(37, 99, 235);
$pdf->Cell(75, 5, 'CLIENT', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(50, 50, 50);
$pdf->SetX(120);
$pdf->Cell(75, 4, $client['nom'], 0, 1, 'C');
$pdf->SetX(120);
$pdf->Cell(75, 4, $client['adresse'], 0, 1, 'C');
$pdf->SetX(120);
$pdf->Cell(75, 4, $client['cp'] . ' ' . $client['ville'], 0, 1, 'C');

$pdf->Ln(5);

// ========================================
// TABLEAU DES PRESTATIONS
// ========================================

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetFillColor(37, 99, 235);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(80, 7, 'Description', 1, 0, 'L', true);
$pdf->Cell(15, 7, 'Qté', 1, 0, 'C', true);
$pdf->Cell(25, 7, 'P.U. HT', 1, 0, 'R', true);
$pdf->Cell(25, 7, 'Montant HT', 1, 0, 'R', true);
$pdf->Cell(15, 7, 'TVA %', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'Montant TVA', 1, 1, 'R', true);

$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(50, 50, 50);
$fill = false;

foreach ($lignes as $ligne) {
    $montant_ht = floatval($ligne['montant_ht']);
    $montant_tva = floatval($ligne['montant_tva']);
    $montant_ttc = floatval($ligne['montant_ttc']);
    $quantite = floatval($ligne['quantite']);
    $prix_unitaire = floatval($ligne['prix_unitaire']);
    $tva_taux = floatval($ligne['tva_taux']);
    
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    
    $pdf->Cell(80, 6, substr($ligne['description'], 0, 50), 1, 0, 'L', $fill);
    $pdf->Cell(15, 6, number_format($quantite, 2), 1, 0, 'C', $fill);
    $pdf->Cell(25, 6, number_format($prix_unitaire, 2, ',', ' ') . '€', 1, 0, 'R', $fill);
    $pdf->Cell(25, 6, number_format($montant_ht, 2, ',', ' ') . '€', 1, 0, 'R', $fill);
    $pdf->Cell(15, 6, number_format($tva_taux, 1) . '%', 1, 0, 'C', $fill);
    $pdf->Cell(15, 6, number_format($montant_tva, 2, ',', ' ') . '€', 1, 1, 'R', $fill);
    
    $fill = !$fill;
}

$pdf->Ln(5);

// ========================================
// TOTAUX
// ========================================

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(155, 6, '', 0, 0);
$pdf->Cell(20, 6, 'Total HT :', 0, 0, 'R');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(20, 6, number_format($total_ht, 2, ',', ' ') . '€', 0, 1, 'R');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(155, 6, '', 0, 0);
$pdf->Cell(20, 6, 'Total TVA :', 0, 0, 'R');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(20, 6, number_format($total_tva, 2, ',', ' ') . '€', 0, 1, 'R');

$pdf->SetFillColor(37, 99, 235);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(155, 8, '', 0, 0);
$pdf->Cell(20, 8, 'Total TTC :', 0, 0, 'R', true);
$pdf->Cell(20, 8, number_format($total_ttc, 2, ',', ' ') . '€', 0, 1, 'R', true);

$pdf->Ln(8);

// ========================================
// CONDITIONS DE PAIEMENT
// ========================================

$pdf->SetTextColor(50, 50, 50);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(0, 6, 'CONDITIONS DE PAIEMENT', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(80, 80, 80);
$conditions = "Paiement à réception de facture par virement bancaire.\n\n" .
              "Conformément à l'article L.441-6 du Code de Commerce, tout retard de paiement entraînera " .
              "l'exigibilité d'une pénalité égale à trois fois le taux d'intérêt légal.\n\n" .
              "En cas de retard de paiement, une indemnité forfaitaire pour frais de recouvrement " .
              "de 40€ sera exigible (décret n°2012-1115).";
$pdf->MultiCell(0, 3.5, $conditions, 0, 'L');

$pdf->Ln(5);

// Pied de page
$pdf->SetY(-25);
$pdf->SetFont('helvetica', 'I', 7);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 4, $entreprise['nom'] . ' - SIRET : ' . $entreprise['siret'], 0, 1, 'C');
$pdf->Cell(0, 4, 'N° TVA : ' . $entreprise['tva'] . ' - Tél. : ' . $entreprise['tel'], 0, 1, 'C');
$pdf->Cell(0, 3, 'Document généré le ' . date('d/m/Y à H:i'), 0, 1, 'C');

// ========================================
// GÉNÉRATION / ENREGISTREMENT DU PDF
// ========================================

// Dossier de stockage local
$storageDir = __DIR__ . '/storage/factures';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0775, true);
}

// Nettoyer le numéro de facture pour le nom de fichier
$sanitizedNumero = preg_replace('/[^A-Za-z0-9_-]/', '_', $facture['numero']);
$filename = 'facture_' . $sanitizedNumero . '_' . date('Y-m-d_His') . '.pdf';
$filePath = $storageDir . '/' . $filename;

// Nettoyer TOUS les buffers de sortie avant de générer le PDF
while (ob_get_level()) {
    ob_end_clean();
}

// Générer le PDF en mémoire
$pdfContent = $pdf->Output('', 'S');

// Sauvegarder le PDF sur le serveur
file_put_contents($filePath, $pdfContent);

// Envoyer le PDF au navigateur
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Content-Length: ' . strlen($pdfContent));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
header('Expires: 0');

echo $pdfContent;
exit;