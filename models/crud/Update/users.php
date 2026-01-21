<?php
    ob_clean();
    header('Content-Type: application/json');
    
    require_once(dirname(__FILE__) . "/../../../../includes/mariadb.php");
    
    class Users
    {
        private $connexion;

        public function __construct($db)
        {
            $this->connexion = $db;
        }

        public function updateUserById($nom, $prenom, $email, $civilite, $societe_id, $telephone, $num_rue, $nom_rue, $complement, $code_postal, $ville, $pays, $id)
        {
            $sql = "SELECT Update_User(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $query = $this->connexion->prepare($sql);
            $query->execute([$nom, $prenom, $email, $civilite, $societe_id, $telephone, $num_rue, $nom_rue, $complement, $code_postal, $ville, $pays, $id]);

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
        
        // Traiter la requête POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (!$data) {
                throw new Exception("Erreur: JSON invalide reçu");
            }
            
            if (!isset($data['id']) || empty($data['id'])) {
                throw new Exception("Erreur: ID utilisateur manquant");
            }
            
            $result = $users->updateUserById(
                $data['nom'] ?? '',
                $data['prenom'] ?? '',
                $data['email'] ?? '',
                $data['civilite'] ?? '',
                $data['societe_id'] ?? '',
                $data['telephone'] ?? '',
                $data['num_rue'] ?? '',
                $data['nom_rue'] ?? '',
                $data['complement'] ?? '',
                $data['code_postal'] ?? '',
                $data['ville'] ?? '',
                $data['pays'] ?? '',
                intval($data['id'])
            );
            
            echo json_encode(['success' => true, 'message' => 'Utilisateur modifié avec succès']);
            exit;
        } else {
            throw new Exception("Erreur: Méthode POST requise");
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
?>