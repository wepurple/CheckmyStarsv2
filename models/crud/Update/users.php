<?php
    ob_clean();
    header('Content-Type: application/json');
    
    require_once("../../includes/mariadb.php");
    
    class Users
    {
        private $connexion;
        private $tableUtilisateurs = "utilisateurs";
        private $tableAdressesPostales = "adressesPostales";
        private $tableSocietes = "societes";
        private $tableInspecteurs = "inspecteurs";
        private $tableAdministrateurs = "administrateurs";
        private $tableProprietaires = "proprietaires";
        private $tableDonneurordre = "donneurordre";
        private $tableDossier = "dossiers";

        public $IdPersonne;
        public $Nom;
        public $Prenom;
        public $Civilite;
        public $Telephone;
        public $Email;
        public $AdresseNum;
        public $AdresseNom;
        public $Complement;
        public $CodePostal;
        public $Ville;
        public $Pays;
        public $Societe;
        public $MotPasse;
        public $Signature;
        public $idAdresse;
        public $Utilisateur_ID;


        /**
         * Constructeur avec $db pour la connexion à la base de données
         *
         * @param $db
        */
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
        
        $users = new Users($db);
        
        // Traiter la requête POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (isset($data['id'])) {
                $result = $users->updateUserById(
                    $data['nom'],
                    $data['prenom'],
                    $data['email'],
                    $data['civilite'],
                    $data['societe_id'],
                    $data['telephone'],
                    $data['num_rue'],
                    $data['nom_rue'],
                    $data['complement'],
                    $data['code_postal'],
                    $data['ville'],
                    $data['pays'],
                    $data['id']
                );
                
                echo json_encode(['success' => true, 'message' => 'Utilisateur modifié']);
            } else {
                echo json_encode(['success' => false, 'error' => 'ID manquant']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
?>