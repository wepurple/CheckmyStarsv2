<?php
ob_clean();
header("Content-Type: application/json");
require_once "../../includes/mariadb.php";

class Users
{
    private $connexion;

    public function __construct($db)
    {
        $this->connexion = $db;
    }

    // Nouvelle fonction : vérifier si l'utilisateur a des dépendances
    public function checkUserDependencies($user_id, $current_role, $new_role)
    {
        // Si le rôle ne change pas, pas de problème
        if ($current_role == $new_role) {
            return ['can_change' => true];
        }

        $issues = [];

        // Si c'était un donneur d'ordre, vérifier les biens
        if ($current_role == 1) {
            $sql = "SELECT COUNT(*) as count FROM biens WHERE Donneur_ID = ?";
            $query = $this->connexion->prepare($sql);
            $query->execute([$user_id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $issues[] = "biens associés (" . $result['count'] . ")";
            }
        }

        // Si c'était un inspecteur, vérifier les dossiers
        if ($current_role == 2) {
            $sql = "SELECT COUNT(*) as count FROM dossiers WHERE Inspecteur_Id = ?";
            $query = $this->connexion->prepare($sql);
            $query->execute([$user_id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $issues[] = "dossiers en cours (" . $result['count'] . ")";
            }
        }

        // Si c'était un propriétaire, vérifier les biens
        if ($current_role == 0) {
            $sql = "SELECT COUNT(*) as count FROM biens WHERE Utilisateur_ID = ?";
            $query = $this->connexion->prepare($sql);
            $query->execute([$user_id]);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            if ($result['count'] > 0) {
                $issues[] = "biens enregistrés (" . $result['count'] . ")";
            }
        }

        return [
            'can_change' => empty($issues),
            'issues' => $issues
        ];
    }

    // Récupérer le rôle actuel de l'utilisateur
    public function getCurrentRole($user_id)
    {
        $sql = "SELECT 
                    CASE 
                        WHEN EXISTS(SELECT 1 FROM administrateurs WHERE Utilisateur_ID = ?) THEN 3
                        WHEN EXISTS(SELECT 1 FROM inspecteurs WHERE Utilisateur_ID = ?) THEN 2
                        WHEN EXISTS(SELECT 1 FROM donneurordre WHERE Donneur_ID = ?) THEN 1
                        WHEN EXISTS(SELECT 1 FROM proprietaires WHERE Utilisateur_ID = ?) THEN 0
                        ELSE -1
                    END as role";
        
        $query = $this->connexion->prepare($sql);
        $query->execute([$user_id, $user_id, $user_id, $user_id]);
        $result = $query->fetch(PDO::FETCH_ASSOC);
        
        return $result['role'];
    }

    public function updateUserById($nom, $prenom, $email, $civilite, $societe_id, $role_id, $telephone, $num_rue, $nom_rue, $complement, $code_postal, $ville, $pays, $id)
    {
        $sql = "SELECT Update_User(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) AS result";
        $query = $this->connexion->prepare($sql);
        
        $query->execute([
            $nom,
            $prenom,
            $email,
            $civilite,
            $societe_id,
            $telephone,
            $num_rue,
            $nom_rue,
            $complement,
            $code_postal,
            $ville,
            $pays,
            $id,
            $role_id
        ]);
        
        return $query;
    }
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db || $db === false) {
        throw new Exception("Erreur: Impossible de se connecter à la base de données");
    }

    $users = new Users($db);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data) {
            throw new Exception("Erreur: JSON invalide reçu");
        }

        if (!isset($data['id']) || empty($data['id'])) {
            throw new Exception("Erreur: ID utilisateur manquant");
        }

        $user_id = intval($data['id']);
        $new_role = !empty($data['role_id']) ? intval($data['role_id']) : 0;
        
        // Récupérer le rôle actuel
        $current_role = $users->getCurrentRole($user_id);
        
        // Vérifier les dépendances si le rôle change
        $check = $users->checkUserDependencies($user_id, $current_role, $new_role);
        
        if (!$check['can_change']) {
            throw new Exception(
                "Impossible de changer le rôle : cet utilisateur a des " . 
                implode(", ", $check['issues']) . 
                ". Veuillez d'abord réassigner ou supprimer ces éléments."
            );
        }

        // Si tout est OK, procéder à la mise à jour
        $result = $users->updateUserById(
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['email'] ?? '',
            $data['civilite'] ?? 'Iel',
            !empty($data['societe_id']) ? intval($data['societe_id']) : 7,
            $new_role,
            $data['telephone'] ?? '',
            $data['num_rue'] ?? '',
            $data['nom_rue'] ?? '',
            $data['complement'] ?? '',
            $data['code_postal'] ?? '',
            $data['ville'] ?? '',
            $data['pays'] ?? '',
            $user_id
        );

        echo json_encode([
            'success' => true, 
            'message' => 'Utilisateur modifié avec succès'
        ]);
        exit;
        
    } else {
        throw new Exception("Erreur: Méthode POST requise");
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage()
    ]);
    exit;
}
?>