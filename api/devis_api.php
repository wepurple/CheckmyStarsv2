<?php

require_once __DIR__ . '/helpers.php';

header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $pdo = getDbConnection();

    switch ($action) {
        case 'list_devis':
            listDevis($pdo);
            break;
        case 'get_devis':
            getDevis($pdo);
            break;
        case 'new_devis_number':
            newDevisNumber($pdo);
            break;
        case 'save_devis':
            saveDevis($pdo);
            break;
        case 'list_factures':
            listFactures($pdo);
            break;
        case 'get_facture':
            getFacture($pdo);
            break;
        case 'convert':
            convertToFacture($pdo);
            break;
        case 'list_entreprises':
            listEntreprises($pdo);
            break;
        case 'get_client_info':
            getClientInfo($pdo);
            break;
        case 'get_entreprise':
            getEntreprise($pdo);
            break;
        default:
            throw new Exception("Action invalide: '$action'");
    }

} catch (Throwable $e) {
    // Debug: afficher aussi la trace complète
    $debug = [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ];
    if (strpos($e->getMessage(), 'SQLSTATE') !== false) {
        $debug['full_error'] = $e->getMessage();
    }
    jsonError($e->getMessage(), $debug);
}

// ============================================================
// FONCTIONS DEVIS
// ============================================================

function listDevis(PDO $pdo): void {
    $stmt = $pdo->query("
        SELECT Devis_ID, Devis_Numero, Devis_Document, Devis_DateEmission, Devis_Verrouille 
        FROM devis 
        ORDER BY Devis_ID DESC 
        LIMIT 100
    ");
    jsonSuccess(['devis' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

function getDevis(PDO $pdo): void {
    $id = $_GET['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        throw new Exception('Paramètre id manquant');
    }

    $devis = getDevisComplet($pdo, (int)$id);
    if (!$devis) {
        throw new Exception('Devis introuvable');
    }

    $devis['client'] = $devis['client'] ?? null;
    $devis['items'] = $devis['items'] ?? [];
    $devis['locked'] = !empty($devis['Devis_Verrouille']);

    jsonSuccess(['devis' => $devis]);
}

/**
 * Génère et réserve un nouveau numéro de devis unique
 */
function newDevisNumber(PDO $pdo): void {
    $pdo->beginTransaction();
    try {
        $numero = generateDocumentNumber($pdo, 'DEVIS');
        $pdo->commit();
        jsonSuccess(['numero' => $numero]);
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

function saveDevis(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode POST requise');
    }

    $payload = json_decode(file_get_contents('php://input'), true);
    if (!is_array($payload)) {
        throw new Exception('Requête JSON invalide');
    }

    $doc = $payload['documentInfo'] ?? [];
    $client = $payload['client'] ?? [];
    $lignes = $payload['lignes'] ?? [];
    $totalTTC = (float)($payload['totalTTC'] ?? 0);
    $entrepriseId = (int)($payload['entrepriseId'] ?? 1);
    $type = $payload['type'] ?? 'DEVIS';

    // === SÉCURITÉ : Bloquer la modification des factures ===
    if ($type === 'FACTURE') {
        throw new Exception('Impossible de modifier une facture existante. Les factures sont en lecture seule.');
    }

    // Validation entreprise
    $stmt = $pdo->prepare("SELECT Entreprise_ID FROM entreprisefacturation WHERE Entreprise_ID = ? AND Entreprise_Actif = 1");
    $stmt->execute([$entrepriseId]);
    if (!$stmt->fetch()) {
        throw new Exception('Entreprise invalide ou inactive');
    }

    // Validation montant
    if ($totalTTC <= 0) {
        throw new Exception('Le montant total doit être > 0');
    }

    $pdo->beginTransaction();

    try {
        // Générer le numéro si nécessaire
        $numero = trim($doc['numero'] ?? '');
        $generated = empty($numero) || $numero === 'AUTO';

        if ($generated) {
            $numero = generateDocumentNumber($pdo, 'DEVIS');
        } else {
            // Vérifier unicité
            $stmt = $pdo->prepare("SELECT Devis_ID, Devis_Verrouille FROM devis WHERE Devis_Numero = ?");
            $stmt->execute([$numero]);
            $existing = $stmt->fetch();
            if ($existing) {
                if (!empty($existing['Devis_Verrouille'])) {
                    throw new Exception('Ce devis est verrouillé');
                }
                throw new Exception('Ce numéro existe déjà');
            }
        }

        // Insérer le devis (Entreprise_ID goes to devis_client table)
        $devisId = insertRecord($pdo, 'devis', 'Devis_ID', [
            'Devis_Numero' => $numero,
            'Devis_DateEmission' => $doc['date'] ?? date('Y-m-d'),
            'Devis_montant' => $totalTTC,
            'Devis_Document' => 'DEVIS',
            'Devis_Verrouille' => 0
        ]);

        // Insérer les lignes
        if (!empty($lignes) && tableExists($pdo, 'devis_items')) {
            $stmtItem = $pdo->prepare("
                INSERT INTO devis_items (Devis_ID, description, quantite, prix_unitaire, tva, total) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach ($lignes as $li) {
                $desc = $li['description'] ?? '';
                $qte = (float)($li['quantite'] ?? 0);
                $pu = (float)($li['prix_unitaire'] ?? 0);
                $tva = (float)($li['tva_taux'] ?? 0);
                $total = (float)($li['montant_ttc'] ?? ($qte * $pu * (1 + $tva / 100)));
                if ($desc && $qte > 0) {
                    $stmtItem->execute([$devisId, $desc, $qte, $pu, $tva, $total]);
                }
            }
        }

        // Insérer la relation client (Utilisateur_ID et Entreprise_ID)
        if (tableExists($pdo, 'devis_client')) {
            $utilisateurId = isset($client['utilisateur_id']) ? (int)$client['utilisateur_id'] : null;
            
            $pdo->prepare("
                INSERT INTO devis_client (Devis_ID, Utilisateur_ID, Entreprise_ID) 
                VALUES (?, ?, ?)
            ")->execute([
                $devisId,
                $utilisateurId,
                $entrepriseId
            ]);
        }

        $pdo->commit();
        jsonSuccess(['devisId' => $devisId, 'numero' => $numero, 'generated' => $generated]);

    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

// ============================================================
// FONCTIONS FACTURES
// ============================================================

function listFactures(PDO $pdo): void {
    $stmt = $pdo->query("
        SELECT 
            f.Facture_ID, f.Facture_Numero, f.Facture_DateCreation, f.Facture_Montant,
            f.Devis_ID, d.Devis_Numero, fc.nom as client_nom
        FROM factures_prixtotal f
        LEFT JOIN devis d ON d.Devis_ID = f.Devis_ID
        LEFT JOIN facture_client fc ON fc.Facture_ID = f.Facture_ID
        ORDER BY f.Facture_DateCreation DESC
    ");
    jsonSuccess(['factures' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

function getFacture(PDO $pdo): void {
    $id = $_GET['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        throw new Exception('Paramètre id manquant');
    }

    $facture = getFactureComplet($pdo, (int)$id);
    if (!$facture) {
        throw new Exception('Facture introuvable');
    }

    $facture['locked'] = true;
    $facture['readonly'] = true;

    jsonSuccess(['facture' => $facture]);
}

function convertToFacture(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Méthode POST requise');
    }

    $devisId = $_GET['devis_id'] ?? null;
    if (!$devisId) {
        $body = json_decode(file_get_contents('php://input'), true);
        $devisId = $body['devis_id'] ?? null;
    }
    if (!$devisId) {
        throw new Exception('Paramètre devis_id requis');
    }

    $devis = getDevisComplet($pdo, (int)$devisId);
    if (!$devis) {
        throw new Exception('Devis introuvable');
    }

    // Vérifier si déjà converti
    $stmt = $pdo->prepare("SELECT Facture_ID, Facture_Numero FROM factures_prixtotal WHERE Devis_ID = ?");
    $stmt->execute([$devisId]);
    $existing = $stmt->fetch();
    if ($existing) {
        jsonSuccess([
            'already_exists' => true,
            'factureId' => (int)$existing['Facture_ID'],
            'numeroFacture' => $existing['Facture_Numero'],
            'message' => 'Une facture existe déjà pour ce devis'
        ]);
    }

    $pdo->beginTransaction();

    try {
        $numeroFacture = generateDocumentNumber($pdo, 'FACTURE');

        // Calculer le total
        $total = (float)($devis['Devis_montant'] ?? 0);
        if ($total <= 0 && !empty($devis['items'])) {
            $total = array_reduce($devis['items'], fn($sum, $item) => $sum + (float)($item['total'] ?? 0), 0);
        }

        // Insérer la facture
        $factureId = insertRecord($pdo, 'factures_prixtotal', 'Facture_ID', [
            'Facture_Numero' => $numeroFacture,
            'Facture_DateCreation' => date('Y-m-d H:i:s'),
            'Facture_Montant' => $total,
            'Facture_Document' => 'FACTURE',
            'Devis_ID' => $devisId
        ]);

        // Copier les items
        if (!empty($devis['items'])) {
            $stmtItem = $pdo->prepare("
                INSERT INTO facture_items (Facture_ID, description, quantite, prix_unitaire, tva, total)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            foreach ($devis['items'] as $item) {
                $stmtItem->execute([
                    $factureId,
                    $item['description'] ?? '',
                    $item['quantite'] ?? 0,
                    $item['prix_unitaire'] ?? 0,
                    $item['tva'] ?? 20,
                    $item['total'] ?? 0
                ]);
            }
        }

        // Copier le client avec Utilisateur_ID
        if (!empty($devis['client'])) {
            $utilisateurId = $devis['client']['Utilisateur_ID'] ?? null;
            $entrepriseId = $devis['client']['Entreprise_ID'] ?? $devis['Entreprise_ID'] ?? 1;
            
            $pdo->prepare("
                INSERT INTO facture_client (Facture_ID, Utilisateur_ID, Entreprise_ID, nom, adresse, email, telephone)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ")->execute([
                $factureId,
                $utilisateurId,
                $entrepriseId,
                $devis['client']['nom'] ?? '',
                $devis['client']['adresse'] ?? '',
                $devis['client']['email'] ?? null,
                $devis['client']['telephone'] ?? null
            ]);
        }

        // Verrouiller le devis
        $pdo->prepare("UPDATE devis SET Devis_Verrouille = 1, Devis_DateVerrouillage = NOW() WHERE Devis_ID = ?")
            ->execute([$devisId]);

        $pdo->commit();

        jsonSuccess([
            'factureId' => $factureId,
            'numeroFacture' => $numeroFacture,
            'total' => $total,
            'devisId' => $devisId,
            'devisNumero' => $devis['Devis_Numero'] ?? null,
            'message' => 'Facture créée avec succès'
        ]);

    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    }
}

/**
 * Insère un enregistrement avec gestion auto-increment ou ID manuel
 */
function insertRecord(PDO $pdo, string $table, string $idField, array $data): int {
    // Nettoyer les noms
    $table = preg_replace('/[`]/', '', $table);
    $idField = preg_replace('/[`]/', '', $idField);
    
    if (empty($data)) {
        throw new Exception("Impossible d'insérer un enregistrement vide dans $table");
    }

    // Vérifier si ID est auto-increment
    try {
        $result = $pdo->query("SHOW COLUMNS FROM `$table` WHERE Field = '$idField'");
        $col = $result->fetch(PDO::FETCH_ASSOC);
        $isAuto = !empty($col) && strpos($col['Extra'] ?? '', 'auto_increment') !== false;
    } catch (Throwable $e) {
        $isAuto = false;
    }

    $columns = array_keys($data);
    $values = array_values($data);

    // Si pas auto-increment, générer un ID manuel
    if (!$isAuto) {
        $maxId = (int)$pdo->query("SELECT COALESCE(MAX(`$idField`), 0) + 1 FROM `$table`")->fetchColumn();
        array_unshift($columns, $idField);
        array_unshift($values, $maxId);
    }

    // Construire requête avec backticks
    $cols = implode('`, `', $columns);
    $placeholders = implode(', ', array_fill(0, count($values), '?'));
    $sql = "INSERT INTO `$table` (`$cols`) VALUES ($placeholders)";

    $stmt = $pdo->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erreur requête: " . implode(", ", $pdo->errorInfo()));
    }
    
    if (!$stmt->execute($values)) {
        throw new Exception("Erreur exécution: " . implode(", ", $stmt->errorInfo()));
    }

    return $isAuto ? (int)$pdo->lastInsertId() : $maxId;
}

// ============================================================
// FONCTIONS ENTREPRISES
// ============================================================

/**
 * Liste toutes les entreprises de facturation actives
 */
function listEntreprises(PDO $pdo): void {
    $stmt = $pdo->query("
        SELECT 
            Entreprise_ID,
            Entreprise_Nom,
            Entreprise_Adresse,
            Entreprise_CodePostal,
            Entreprise_Ville,
            Entreprise_Pays,
            Entreprise_Email,
            Entreprise_Telephone,
            Entreprise_SIRET,
            Entreprise_TVA_Intra
        FROM entreprisefacturation 
        WHERE Entreprise_Actif = 1
        ORDER BY Entreprise_Nom ASC
    ");
    jsonSuccess(['entreprises' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
}

/**
 * Récupère les informations complètes d'un client (utilisateur)
 */
function getClientInfo(PDO $pdo): void {
    $id = $_GET['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        throw new Exception('Paramètre id manquant');
    }

    $stmt = $pdo->prepare("
        SELECT 
            u.Utilisateur_ID,
            u.Utilisateur_Nom,
            u.Utilisateur_Prenom,
            u.Utilisateur_Mail,
            u.Utilisateur_Telephone,
            CONCAT(u.Utilisateur_Nom, ' ', COALESCE(u.Utilisateur_Prenom, '')) AS nom_complet,
            CONCAT(a.AdressePostale_NumeroRue, ' ', a.AdressePostale_NomRue) AS adresse,
            a.AdressePostale_Complement AS complement,
            a.AdressePostale_CodePostal AS code_postal,
            a.AdressePostale_Ville AS ville,
            a.AdressePostale_Pays AS pays
        FROM utilisateurs u
        LEFT JOIN adressespostales a ON a.AdressePostale_ID = u.AdressePostale_ID
        WHERE u.Utilisateur_ID = ?
    ");
    $stmt->execute([(int)$id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$client) {
        throw new Exception('Client introuvable');
    }

    jsonSuccess(['client' => $client]);
}
/**
 * Récupère les informations complètes d'une entreprise de facturation
 */
function getEntreprise(PDO $pdo): void {
    $id = $_GET['id'] ?? null;
    if (!$id || !is_numeric($id)) {
        throw new Exception('Paramètre id manquant');
    }

    $stmt = $pdo->prepare("
        SELECT
            Entreprise_ID,
            Entreprise_Nom,
            Entreprise_Adresse,
            Entreprise_CodePostal,
            Entreprise_Ville,
            Entreprise_Pays,
            Entreprise_Email,
            Entreprise_Telephone,
            Entreprise_SIRET,
            Entreprise_TVA_Intra
        FROM entreprisefacturation
        WHERE Entreprise_ID = ? AND Entreprise_Actif = 1
    ");
    $stmt->execute([(int)$id]);
    $entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entreprise) {
        throw new Exception('Entreprise introuvable');
    }

    jsonSuccess(['entreprise' => $entreprise]);
}