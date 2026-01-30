<?php
require_once(__DIR__ . '/../vendor/autoload.php');

// ========================================
// DONNÉES DU DEVIS
// ========================================

// Informations entreprise
$entreprise = [
    'nom' => 'CETIRE',
    'adresse' => '51 rue du Faubourg de Bourgogne',
    'cp' => '45000',
    'ville' => 'ORLEANS',
    'telephone' => '02 38 54 32 10',
    'fax' => '02 38 54 32 11',
    'siret' => '123 456 789 00012',
    'assurance_nom' => 'AXA Pro',
    'assurance_num' => 'POL-2024-456789',
    'naf' => '7120B',
    'capital' => '50 000 €',
    'tva' => 'FR76 102 783 725 001'
];

// Informations devis
$devis = [
    'numero' => 'DEV-2024-11-0342',
    'date' => '27/11/2024'
];

// Informations client
$client = [
    'nom' => 'Hôtel Le Château',
    'adresse' => '15 Avenue des Roses',
    'cp' => '45100',
    'ville' => 'ORLEANS'
];

// Informations dossier
$dossier = [
    'reference' => 'DOSS-2024-8756',
    'date_reperage' => '05/12/2024',
    'bien_nom' => 'Résidence Le Parc',
    'bien_adresse' => '28 Boulevard Victor Hugo',
    'bien_cp' => '45000',
    'bien_ville' => 'ORLEANS'
];

// Diagnostics réalisés
$diagnostics = 'Diagnostic de Performance Énergétique (DPE), Diagnostic Amiante, Diagnostic Gaz, Diagnostic Électricité';

// Lignes du devis
$lignes = [
    [
        'ref' => 'DPE-001',
        'designation' => 'Diagnostic de Performance Énergétique',
        'prix_unit' => 150.00,
        'tva' => 20,
        'quantite' => 1,
        'remise' => 0
    ],
    [
        'ref' => 'AMI-001',
        'designation' => 'Diagnostic Amiante avant travaux',
        'prix_unit' => 280.00,
        'tva' => 20,
        'quantite' => 1,
        'remise' => 0
    ],
    [
        'ref' => 'GAZ-001',
        'designation' => 'Diagnostic Installation Gaz',
        'prix_unit' => 120.00,
        'tva' => 20,
        'quantite' => 1,
        'remise' => 0
    ],
    [
        'ref' => 'ELEC-001',
        'designation' => 'Diagnostic Installation Électrique',
        'prix_unit' => 110.00,
        'tva' => 20,
        'quantite' => 1,
        'remise' => 10
    ]
];

// Coordonnées bancaires
$banque = [
    'code_banque' => '10278',
    'code_guichet' => '37285',
    'numero_compte' => '12010201',
    'cle' => '42',
    'iban' => 'FR76 1027 8372 8500 0120 1020 142',
    'bic' => 'CMCIFR2A'
];

// ========================================
// CALCULS
// ========================================

$total_ht = 0;
$total_tva = 0;
$total_ttc = 0;

foreach ($lignes as &$ligne) {
    $montant_ht = $ligne['prix_unit'] * $ligne['quantite'];
    $montant_remise = $montant_ht * ($ligne['remise'] / 100);
    $montant_ht_apres_remise = $montant_ht - $montant_remise;
    $montant_tva = $montant_ht_apres_remise * ($ligne['tva'] / 100);
    $montant_ttc = $montant_ht_apres_remise + $montant_tva;
    
    $ligne['montant_ht'] = $montant_ht_apres_remise;
    $ligne['montant_tva'] = $montant_tva;
    $ligne['montant_ttc'] = $montant_ttc;
    
    $total_ht += $montant_ht_apres_remise;
    $total_tva += $montant_tva;
    $total_ttc += $montant_ttc;
}

// ========================================
// CRÉATION DU PDF
// ========================================

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

$pdf->SetCreator('CETIRE');
$pdf->SetAuthor($entreprise['nom']);
$pdf->SetTitle('Devis ' . $devis['numero']);
$pdf->SetSubject('Devis commercial');

// Marges
$pdf->SetMargins(15, 15, 15);
$pdf->SetAutoPageBreak(true, 40); // Plus d'espace en bas pour le pied de page

// Supprimer en-tête et pied de page par défaut
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();

// ========================================
// EN-TÊTE
// ========================================

$pdf->SetFont('helvetica', 'B', 24);
$pdf->SetTextColor(41, 128, 185);
$pdf->Cell(0, 10, 'CETIRE', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->Cell(0, 4, 'Expert en diagnostic immobilier', 0, 1, 'L');
$pdf->Ln(3);

// Ligne de séparation
$pdf->SetDrawColor(41, 128, 185);
$pdf->SetLineWidth(0.5);
$pdf->Line(15, $pdf->GetY(), 195, $pdf->GetY());
$pdf->Ln(5); // Réduit de 8 à 5

// ========================================
// INFORMATIONS DEVIS ET CLIENT
// ========================================

$y_start = $pdf->GetY();

// Colonne gauche - Informations entreprise
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(90, 5, $entreprise['nom'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(80, 80, 80);
$pdf->MultiCell(90, 4, 
    $entreprise['adresse'] . "\n" .
    $entreprise['cp'] . ' ' . $entreprise['ville'] . "\n" .
    'Tél. : ' . $entreprise['telephone'] . "\n" .
    'SIRET : ' . $entreprise['siret'] . "\n" .
    'N°TVA : ' . $entreprise['tva']
, 0, 'L', false, 0);

// Colonne droite - Informations devis
$pdf->SetXY(120, $y_start);
$pdf->SetFillColor(41, 128, 185);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(75, 8, 'DEVIS', 0, 1, 'C', true);

$pdf->SetX(120);
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(35, 5, 'N° : ', 0, 0, 'R');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(40, 5, $devis['numero'], 0, 1, 'L');

$pdf->SetX(120);
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(35, 5, 'Date : ', 0, 0, 'R');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(40, 5, $devis['date'], 0, 1, 'L');

$pdf->Ln(5); // Réduit de 8 à 5

// Encadré client
$pdf->SetFillColor(245, 245, 245);
$pdf->Rect(120, $pdf->GetY(), 75, 25, 'F');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(100, 5, '', 0, 0);
$pdf->Cell(75, 5, 'CLIENT', 0, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(100, 4, '', 0, 0);
$pdf->Cell(75, 4, $client['nom'], 0, 1, 'C');
$pdf->Cell(100, 4, '', 0, 0);
$pdf->Cell(75, 4, $client['adresse'], 0, 1, 'C');
$pdf->Cell(100, 4, '', 0, 0);
$pdf->Cell(75, 4, $client['cp'] . ' ' . $client['ville'], 0, 1, 'C');

$pdf->Ln(6); // Réduit de 10 à 6

// ========================================
// INFORMATIONS DOSSIER
// ========================================

$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetTextColor(41, 128, 185);
$pdf->Cell(0, 6, 'Devis correspondant au dossier :', 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(80, 80, 80);
$pdf->Cell(45, 5, 'Référence : ', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(50, 5, $dossier['reference'], 0, 0, 'L');
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(30, 5, 'Prévu le : ', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 5, $dossier['date_reperage'], 0, 1, 'L');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(45, 5, 'Immeuble à visiter : ', 0, 0, 'L');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->MultiCell(0, 5, $dossier['bien_nom'] . ' - ' . $dossier['bien_adresse'] . ', ' . $dossier['bien_cp'] . ' ' . $dossier['bien_ville'], 0, 'L');

$pdf->Ln(3);
$pdf->SetFont('helvetica', 'I', 9);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 4, 'Diagnostics : ' . $diagnostics, 0, 'L');

$pdf->Ln(5); // Réduit de 8 à 5

// ========================================
// TABLEAU DES PRESTATIONS
// ========================================

$pdf->SetFont('helvetica', 'B', 8);
$pdf->SetFillColor(41, 128, 185);
$pdf->SetTextColor(255, 255, 255);

$pdf->Cell(25, 7, 'Référence', 1, 0, 'C', true);
$pdf->Cell(60, 7, 'Désignation', 1, 0, 'C', true);
$pdf->Cell(20, 7, 'P.U. HT', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'TVA', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'Qté', 1, 0, 'C', true);
$pdf->Cell(15, 7, 'Remise', 1, 0, 'C', true);
$pdf->Cell(30, 7, 'Total TTC', 1, 1, 'C', true);

$pdf->SetFont('helvetica', '', 8);
$pdf->SetTextColor(50, 50, 50);
$pdf->SetFillColor(250, 250, 250);

$fill = false;
foreach ($lignes as $ligne) {
    $pdf->Cell(25, 6, $ligne['ref'], 1, 0, 'C', $fill);
    $pdf->Cell(60, 6, $ligne['designation'], 1, 0, 'L', $fill);
    $pdf->Cell(20, 6, number_format($ligne['prix_unit'], 2, ',', ' ') . ' €', 1, 0, 'R', $fill);
    $pdf->Cell(15, 6, $ligne['tva'] . '%', 1, 0, 'C', $fill);
    $pdf->Cell(15, 6, $ligne['quantite'], 1, 0, 'C', $fill);
    $pdf->Cell(15, 6, $ligne['remise'] . '%', 1, 0, 'C', $fill);
    $pdf->Cell(30, 6, number_format($ligne['montant_ttc'], 2, ',', ' ') . ' €', 1, 1, 'R', $fill);
    $fill = !$fill;
}

$pdf->Ln(5);

// ========================================
// TOTAUX
// ========================================

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(135, 6, '', 0, 0);
$pdf->Cell(30, 6, 'Total HT :', 0, 0, 'R');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(15, 6, number_format($total_ht, 2, ',', ' ') . ' €', 0, 1, 'R');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(135, 6, '', 0, 0);
$pdf->Cell(30, 6, 'Total TVA :', 0, 0, 'R');
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(15, 6, number_format($total_tva, 2, ',', ' ') . ' €', 0, 1, 'R');

$pdf->SetFillColor(41, 128, 185);
$pdf->SetTextColor(255, 255, 255);
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(135, 8, '', 0, 0);
$pdf->Cell(30, 8, 'Total TTC :', 0, 0, 'R', true);
$pdf->Cell(15, 8, number_format($total_ttc, 2, ',', ' ') . ' €', 0, 1, 'R', true);

$pdf->Ln(6); // Réduit de 10 à 6

// ========================================
// BON POUR ACCORD
// ========================================

$pdf->SetTextColor(50, 50, 50);
$pdf->SetFont('helvetica', 'B', 11);
$pdf->Cell(0, 6, 'BON POUR ACCORD', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(0, 5, 'Dater et signer', 0, 1, 'L');

$pdf->Ln(8); // Réduit de 15 à 8

// ========================================
// INFORMATIONS DE PAIEMENT
// ========================================

$pdf->SetFont('helvetica', 'I', 9);
$pdf->SetTextColor(80, 80, 80);
$pdf->MultiCell(0, 4, 
    "Si cela vous agrée, merci de nous renvoyer le présent devis signé :\n" .
    "• par retour de mail avec le virement sur le RIB ci-dessous\n" .
    "• ou par courrier postal accompagné du règlement à : CETIRE, 51 rue du Faubourg de Bourgogne 45000 ORLEANS"
, 0, 'L');

$pdf->Ln(5);

// Coordonnées bancaires
$pdf->SetFillColor(245, 245, 245);
$pdf->Rect(15, $pdf->GetY(), 180, 20, 'F');

$y_bank = $pdf->GetY() + 3;
$pdf->SetY($y_bank);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->SetTextColor(50, 50, 50);
$pdf->Cell(45, 5, 'Code banque', 1, 0, 'C');
$pdf->Cell(45, 5, 'Code guichet', 1, 0, 'C');
$pdf->Cell(45, 5, 'N° de compte', 1, 0, 'C');
$pdf->Cell(45, 5, 'Clé', 1, 1, 'C');

$pdf->SetFont('helvetica', '', 9);
$pdf->Cell(45, 5, $banque['code_banque'], 1, 0, 'C');
$pdf->Cell(45, 5, $banque['code_guichet'], 1, 0, 'C');
$pdf->Cell(45, 5, $banque['numero_compte'], 1, 0, 'C');
$pdf->Cell(45, 5, $banque['cle'], 1, 1, 'C');

$pdf->Ln(2);
$pdf->SetFont('helvetica', 'B', 9);
$pdf->Cell(0, 4, 'IBAN : ' . $banque['iban'] . '  -  BIC : ' . $banque['bic'], 0, 1, 'C');

// ========================================
// PIED DE PAGE
// ========================================

$pdf->SetY(-1);
$pdf->SetFont('helvetica', 'I', 7);
$pdf->SetTextColor(150, 150, 150);
$pdf->Cell(0, 4, $entreprise['nom'] . ' - ' . $entreprise['siret'] . ' - APE : ' . $entreprise['naf'], 0, 1, 'C');
$pdf->Cell(0, 4, 'Capital social : ' . $entreprise['capital'] . ' - TVA : ' . $entreprise['tva'], 0, 1, 'C');

// ========================================
// GÉNÉRATION DU PDF
// ========================================

$sanitizedNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', $devis['numero']);
$filename = 'devis_' . $sanitizedNumber . '_' . date('Ymd_His') . '.pdf';

// Nettoyer le buffer de sortie pour éviter la corruption du PDF
if (ob_get_length()) {
    ob_end_clean();
}

// Envoyer directement le PDF au navigateur
$pdf->Output($filename, 'D');
exit;
?>