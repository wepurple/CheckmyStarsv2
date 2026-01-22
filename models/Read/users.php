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
        public $Role;
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

        public function getAllUsers()
        {
            $sql = "CALL Get_User;";
            $query = $this->connexion->prepare($sql);
            $query->execute();

            return $query;
        }

        public function getUserById($id){
            $sql = "CALL Get_User_ID(".$id.");";
            $query = $this->connexion->prepare($sql);
            $query->execute();

            return $query;
        }
    }
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $users = new Users($db);
        
        // Vérifier si on cherche un utilisateur spécifique par ID
        if (isset($_GET['IdPersonne']) && !empty($_GET['IdPersonne'])) {
            $id = intval($_GET['IdPersonne']);
            $result = $users->getUserById($id);
            $data = $result->fetch(PDO::FETCH_ASSOC);
        } else {
            // Sinon, retourner tous les utilisateurs
            $result = $users->getAllUsers();
            $data = $result->fetchAll(PDO::FETCH_ASSOC);
        }
        
        echo json_encode($data);
    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
?>