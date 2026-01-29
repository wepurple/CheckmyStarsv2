<?php
/**
 * Helper pour les APIs - Connexion BDD et gestion JSON
 */

require_once dirname(__DIR__) . '/includes/mariadb.php';

/**
 * Obtenir une connexion PDO validée
 * @throws Exception si la connexion échoue
 */
function getDbConnection(): PDO {
    $db = new Database();
    $pdo = $db->getConnection();
    if (!($pdo instanceof PDO)) {
        $msg = is_array($pdo) && isset($pdo[1]) ? $pdo[1] : 'Connexion BDD échouée';
        throw new Exception($msg);
    }
    return $pdo;
}

/**
 * Envoyer une réponse JSON de succès
 */
function jsonSuccess(array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['success' => true], $data));
    exit;
}

/**
 * Envoyer une réponse JSON d'erreur
 */
function jsonError(string $message, int $code = 400): void {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

/**
 * Vérifier si une table existe
 */
function tableExists(PDO $pdo, string $name): bool {
    $q = $pdo->prepare("SHOW TABLES LIKE ?");
    $q->execute([$name]);
    return (bool)$q->fetchColumn();
}

/**
 * Génère un numéro de document unique (DEVIS ou FACTURE)
 * Utilise un verrou transactionnel pour garantir l'unicité en concurrence
 * 
 * @param PDO $pdo Connexion PDO (doit être dans une transaction)
 * @param string $type 'DEVIS' ou 'FACTURE'
 * @return string Le numéro généré (ex: D-2026-00001, F-2026-00001)
 */
function generateDocumentNumber(PDO $pdo, string $type): string {
    $year = date('Y');
    $prefix = ($type === 'FACTURE') ? 'F' : 'D';
    
    // Verrouillage de la ligne pour éviter les doublons en concurrence
    $stmt = $pdo->prepare("
        SELECT last_number FROM document_counters 
        WHERE type = ? AND year = ? 
        FOR UPDATE
    ");
    $stmt->execute([$type, $year]);
    $row = $stmt->fetch();
    
    if ($row) {
        $newNumber = (int)$row['last_number'] + 1;
        $stmt = $pdo->prepare("
            UPDATE document_counters 
            SET last_number = ? 
            WHERE type = ? AND year = ?
        ");
        $stmt->execute([$newNumber, $type, $year]);
    } else {
        // Première entrée pour ce type/année
        $newNumber = 1;
        $stmt = $pdo->prepare("
            INSERT INTO document_counters (type, year, last_number) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$type, $year, $newNumber]);
    }
    
    // Format: D-2026-00001 ou F-2026-00001
    return sprintf('%s-%s-%05d', $prefix, $year, $newNumber);
}

/**
 * Vérifie si un document est verrouillé (lecture seule)
 */
function isDocumentLocked(PDO $pdo, string $type, int $id): bool {
    if ($type === 'DEVIS') {
        $stmt = $pdo->prepare("SELECT Devis_Verrouille FROM devis WHERE Devis_ID = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row && (bool)$row['Devis_Verrouille'];
    } elseif ($type === 'FACTURE') {
        // Les factures sont TOUJOURS verrouillées après création
        $stmt = $pdo->prepare("SELECT Facture_ID FROM factures_prixtotal WHERE Facture_ID = ?");
        $stmt->execute([$id]);
        return (bool)$stmt->fetch();
    }
    return false;
}

/**
 * Récupère une facture complète avec items et client (utilise Utilisateur_ID si disponible)
 */
function getFactureComplet(PDO $pdo, int $factureId): ?array {
    $stmt = $pdo->prepare("SELECT * FROM factures_prixtotal WHERE Facture_ID = ?");
    $stmt->execute([$factureId]);
    $facture = $stmt->fetch();
    
    if (!$facture) return null;
    
    // Items
    $stmt = $pdo->prepare("SELECT * FROM facture_items WHERE Facture_ID = ?");
    $stmt->execute([$factureId]);
    $facture['items'] = $stmt->fetchAll();
    
    // Client - avec jointure utilisateur
    $stmt = $pdo->prepare("
        SELECT 
            fc.*,
            u.Utilisateur_Nom,
            u.Utilisateur_Prenom,
            u.Utilisateur_Mail AS email_utilisateur,
            u.Utilisateur_Telephone AS telephone_utilisateur,
            CONCAT(u.Utilisateur_Nom, ' ', COALESCE(u.Utilisateur_Prenom, '')) AS nom_complet,
            CONCAT(a.AdressePostale_NumeroRue, ' ', a.AdressePostale_NomRue) AS adresse_complete,
            a.AdressePostale_CodePostal AS code_postal,
            a.AdressePostale_Ville AS ville
        FROM facture_client fc
        LEFT JOIN utilisateurs u ON u.Utilisateur_ID = fc.Utilisateur_ID
        LEFT JOIN adressespostales a ON a.AdressePostale_ID = u.AdressePostale_ID
        WHERE fc.Facture_ID = ?
    ");
    $stmt->execute([$factureId]);
    $clientData = $stmt->fetch();
    
    if ($clientData) {
        if (!empty($clientData['Utilisateur_ID'])) {
            $clientData['nom'] = $clientData['nom_complet'] ?: $clientData['nom'];
            $clientData['adresse'] = $clientData['adresse_complete'] ?: $clientData['adresse'];
            $clientData['email'] = $clientData['email_utilisateur'] ?: $clientData['email'];
            $clientData['telephone'] = $clientData['telephone_utilisateur'] ?: $clientData['telephone'];
        }
        $facture['client'] = $clientData;
    } else {
        $facture['client'] = null;
    }
    
    // Devis source si lié
    if (!empty($facture['Devis_ID'])) {
        $stmt = $pdo->prepare("SELECT Devis_Numero FROM devis WHERE Devis_ID = ?");
        $stmt->execute([$facture['Devis_ID']]);
        $devis = $stmt->fetch();
        $facture['devis_numero'] = $devis ? $devis['Devis_Numero'] : null;
    }
    
    // Entreprise émettrice
    if (!empty($facture['Entreprise_ID'])) {
        $stmt = $pdo->prepare("SELECT * FROM entreprisefacturation WHERE Entreprise_ID = ?");
        $stmt->execute([$facture['Entreprise_ID']]);
        $facture['entreprise'] = $stmt->fetch() ?: null;
    }
    
    return $facture;
}

/**
 * Récupère un devis complet avec items et client (utilise Utilisateur_ID si disponible)
 */
function getDevisComplet(PDO $pdo, int $devisId): ?array {
    $stmt = $pdo->prepare("SELECT * FROM devis WHERE Devis_ID = ?");
    $stmt->execute([$devisId]);
    $devis = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$devis) return null;
    
    // Items
    $stmt = $pdo->prepare("SELECT * FROM devis_items WHERE Devis_ID = ?");
    $stmt->execute([$devisId]);
    $devis['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Client - utilise la vue si disponible, sinon requête avec jointure
    $stmt = $pdo->prepare("
        SELECT 
            dc.*,
            u.Utilisateur_Nom,
            u.Utilisateur_Prenom,
            u.Utilisateur_Mail AS email_utilisateur,
            u.Utilisateur_Telephone AS telephone_utilisateur,
            CONCAT(u.Utilisateur_Nom, ' ', COALESCE(u.Utilisateur_Prenom, '')) AS nom_complet,
            CONCAT(a.AdressePostale_NumeroRue, ' ', a.AdressePostale_NomRue) AS adresse_complete,
            a.AdressePostale_CodePostal AS code_postal,
            a.AdressePostale_Ville AS ville,
            a.AdressePostale_Pays AS pays
        FROM devis_client dc
        LEFT JOIN utilisateurs u ON u.Utilisateur_ID = dc.Utilisateur_ID
        LEFT JOIN adressespostales a ON a.AdressePostale_ID = u.AdressePostale_ID
        WHERE dc.Devis_ID = ?
    ");
    $stmt->execute([$devisId]);
    $clientData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($clientData) {
        // Prioriser les données utilisateur si Utilisateur_ID est défini
        if (!empty($clientData['Utilisateur_ID'])) {
            $clientData['nom'] = $clientData['nom_complet'] ?: $clientData['nom'];
            $clientData['adresse'] = $clientData['adresse_complete'] ?: $clientData['adresse'];
            $clientData['email'] = $clientData['email_utilisateur'] ?: $clientData['email'];
            $clientData['telephone'] = $clientData['telephone_utilisateur'] ?: $clientData['telephone'];
        }
        $devis['client'] = $clientData;
    } else {
        $devis['client'] = null;
    }
    
    // Entreprise émettrice
    if (!empty($devis['Entreprise_ID'])) {
        $stmt = $pdo->prepare("SELECT * FROM entreprisefacturation WHERE Entreprise_ID = ?");
        $stmt->execute([$devis['Entreprise_ID']]);
        $devis['entreprise'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
    
    return $devis;
}
