<?php
    ob_clean();
    header('Content-Type: application/json');
    
    require_once("../../includes/mariadb.php");
    
    class Users
    {
        private $connexion;

        public function __construct($db)
        {
            $this->connexion = $db;
        }

        public function updateUserById($nom, $prenom, $email, $civilite, $societe_id, $role_id, $telephone, $num_rue, $nom_rue, $complement, $code_postal, $ville, $pays, $id)
        {
            $sql = "SELECT Update_User(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?) AS result";
            $query = $this->connexion->prepare($sql);
            
            // ORDRE : Nom, Prenom, Societe, Mail, Genre, Telephone, NumRue, Complement, CP, Adresse, Ville, Pays, ID
            $query->execute([
                $nom,           // 1. Nom
                $prenom,        // 2. Prenom
                $societe_id,    // 3. Societe
                $role_id,
                $email,         // 4. Mail
                $civilite,      // 5. Genre
                $telephone,     // 6. Telephone
                $num_rue,       // 7. NumRue
                $complement,    // 8. Complement
                $code_postal,   // 9. CP
                $nom_rue,       // 10. Adresse (nom de rue)
                $ville,         // 11. Ville
                $pays,          // 12. Pays
                $id             // 13. ID
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
                !empty($data['societe_id']) ? intval($data['societe_id']) : 7,
                !empty($data['role_id']) ? intval($data['role_id']) : 0,
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