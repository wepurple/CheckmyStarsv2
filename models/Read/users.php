<?php
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
        public function __construct($db){
            $this->connexion = $db;
        }

        public function getAllUsers(){
            $sql = "CALL Get_User_ID;";
            $query = $this->connexion->prepare($sql);
            $query->execute();

            return $query;
        }
    }
?>