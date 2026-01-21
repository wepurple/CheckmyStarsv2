<?php
session_start();

// Vérification de l'authentification
if(!isset($_SESSION['Role']) || !$_SESSION['Role']['Administrateur']){
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit;
}

// Inclure la connexion à la base de données
require_once('../../includes/mariadb.php');

// Vérifier que c'est une requête POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

try {
    // Récupérer les données
    $description = $_POST['description'] ?? null;
    $statut = $_POST['statut'] ?? null;
    $points = $_POST['points'] ?? null;
    $star = $_POST['star'] ?? null;
    
    // Validation
    if (empty($description) || empty($statut) || empty($star)) {
        throw new Exception('Données manquantes (description, statut ou niveau d\'étoile)');
    }
    
    // Connexion à la base de données
    $database = new Database();
    $db = $database->getConnection();
    
    // Démarrer une transaction
    $db->beginTransaction();
    
    try {
        $sqlInsertCritere = "INSERT INTO criteres (Critere_description, Critere_statut, Critere_points) 
                             VALUES (:description, :statut, :points)";
        
        $stmtCritere = $db->prepare($sqlInsertCritere);
        $stmtCritere->bindParam(':description', $description, PDO::PARAM_STR);
        $stmtCritere->bindParam(':statut', $statut, PDO::PARAM_STR);
        
        // Gérer les points qui peuvent être NULL
        if (empty($points)) {
            $stmtCritere->bindValue(':points', null, PDO::PARAM_NULL);
        } else {
            $stmtCritere->bindParam(':points', $points, PDO::PARAM_INT);
        }
        
        $stmtCritere->execute();
        
        // Récupérer l'ID du critère nouvellement créé
        $newCritereId = $db->lastInsertId();
        
        $sqlGetListe = "SELECT ListesCriteres_ID 
                        FROM listescriteres_etoiles 
                        WHERE type_hebergement_id = 2 AND etoile = :star";
        
        $stmtGetListe = $db->prepare($sqlGetListe);
        $stmtGetListe->bindParam(':star', $star, PDO::PARAM_INT);
        $stmtGetListe->execute();
        
        $listeIds = $stmtGetListe->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($listeIds)) {
            throw new Exception("Aucune liste de critères trouvée pour $star étoile(s)");
        }
        
        $sqlInsertContient = "INSERT INTO contient (Critere_ID, Photo_ID, ListesCriteres_ID) 
                              VALUES (:critere_id, 100, :liste_id)";
        
        $stmtContient = $db->prepare($sqlInsertContient);
        
        foreach ($listeIds as $listeId) {
            $stmtContient->bindParam(':critere_id', $newCritereId, PDO::PARAM_INT);
            $stmtContient->bindParam(':liste_id', $listeId, PDO::PARAM_INT);
            $stmtContient->execute();
        }
        
        // Valider la transaction
        $db->commit();
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Critère créé avec succès',
            'id' => $newCritereId,
            'listes_linked' => count($listeIds)
        ]);
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
}
?>
