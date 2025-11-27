<?php
class User {
    private $connexion;
    private $table = "utilisateurs";
    private $table2 = "adressesPostales";

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
    public $Role;
    public $Login;
    public $MotPasse;

    /**
     * Constructeur avec $db pour la connexion à la base de données
     *
     * @param $db
     */
    public function __construct($db){
        $this->connexion = $db;
    }
    /**
        * Créer un produit
         *
        * @return void
    */
    public function creerAdresse(){
        $sql = "INSERT INTO " . $this->table2 . " SET AdressePostale_NumeroRue=:AdresseNum, AdressePostale_Complement=:Complement, AdressePostale_CodePostal=:CodePostal, AdressePostale_NomRue=:AdresseNom, AdressePostale_Ville=:Ville, AdressePostale_Pays=:Pays";
        $query = $this->connexion->prepare($sql);

        $this->AdresseNum=htmlspecialchars(strip_tags($this->AdresseNum));
        $this->Complement=htmlspecialchars(strip_tags($this->Complement));  
        $this->CodePostal=htmlspecialchars(strip_tags($this->CodePostal));
        $this->AdresseNom=htmlspecialchars(strip_tags($this->AdresseNom));
        $this->Ville=htmlspecialchars(strip_tags($this->Ville));
        $this->Pays=htmlspecialchars(strip_tags($this->Pays));

        $query->bindParam(":AdresseNum", $this->AdresseNum);
        $query->bindParam(":Complement", $this->Complement);
        $query->bindParam(":CodePostal", $this->CodePostal);
        $query->bindParam(":AdresseNom", $this->AdresseNom);
        $query->bindParam(":Ville", $this->Ville);
        $query->bindParam(":Pays", $this->Pays);

        if($query->execute()){
            return $this->connexion->lastInsertId();
            return true;
        }
        return false;
    }

    public function creer(){
        $sql = "INSERT INTO " . $this->table . " SET Nom=:Nom, Prenom=:Prenom, Civilite=:Civilite, Telephone=:Telephone, Email=:Email, Adresse=:Adresse, Complement=:Complement, CodePostal=:CodePostal, Ville=:Ville, Pays=:Pays, Societe=:Societe, Role=:Role, Login=:Login, MotPasse=:MotPasse";
        $query = $this->connexion->prepare($sql);

        $this->Nom=htmlspecialchars(strip_tags($this->Nom));
        $this->Prenom=htmlspecialchars(strip_tags($this->Prenom));
        $this->Civilite=htmlspecialchars(strip_tags($this->Civilite));
        $this->Telephone=htmlspecialchars(strip_tags($this->Telephone));
        $this->Email=htmlspecialchars(strip_tags($this->Email));
        $this->Adresse=htmlspecialchars(strip_tags($this->Adresse));
        $this->Complement=htmlspecialchars(strip_tags($this->Complement));
        $this->CodePostal=htmlspecialchars(strip_tags($this->CodePostal));
        $this->Ville=htmlspecialchars(strip_tags($this->Ville));
        $this->Pays=htmlspecialchars(strip_tags($this->Pays));
        $this->Societe=htmlspecialchars(strip_tags($this->Societe));
        $this->Role=htmlspecialchars(strip_tags($this->Role));
        $this->Login=htmlspecialchars(strip_tags($this->Login));
        $this->MotPasse=htmlspecialchars(strip_tags($this->MotPasse));

        $query->bindParam(":Nom", $this->Nom);
        $query->bindParam(":Prenom", $this->Prenom);
        $query->bindParam(":Civilite", $this->Civilite);
        $query->bindParam(":Telephone", $this->Telephone);
        $query->bindParam(":Email", $this->Email);
        $query->bindParam(":Adresse", $this->Adresse);
        $query->bindParam(":Complement", $this->Complement);
        $query->bindParam(":CodePostal", $this->CodePostal);
        $query->bindParam(":Ville", $this->Ville); 
        $query->bindParam(":Pays", $this->Pays);
        $query->bindParam(":Societe", $this->Societe);
        $query->bindParam(":Role", $this->Role);
        $query->bindParam(":Login", $this->Login);
        $query->bindParam(":MotPasse", $this->MotPasse);

        if($query->execute()){
            return true;

        }
        return false;
    }

    public function afficherUtilisateur(){
        $sql = "SELECT * FROM ". $this->table ." INNER JOIN ". $this->table2 ." ON ". $this->table.".AdressePostale_ID = ". $this->table2.".AdressePostale_ID;";
        $query = $this->connexion->prepare($sql);
        $query->execute();
        $row= $query->fetch(PDO::FETCH_ASSOC);
        $this->Utilisateur_ID=$row['Utilisateur_ID'];
        $this->Utilisateur_Nom=$row['Utilisateur_Nom'];
        $this->Utilisateur_Prenom=$row['Utilisateur_Prenom'];
        $this->Utilisateur_Civilite=$row['Utilisateur_Civilite'];
        $this->Utilisateur_Telephone=$row['Utilisateur_Telephone'];
        $this->Utilisateur_Mail=$row['Utilisateur_Mail'];
        $this->Utilisateur_Signature=$row['Utilisateur_Signature'];
        $this->AdressePostale_NumeroRue=$row['AdressePostale_NumeroRue'];
        $this->AdressePostale_Complement=$row['AdressePostale_Complement'];
        $this->AdressePostale_CodePostal=$row['AdressePostale_CodePostal'];
        $this->AdressePostale_NomRue=$row['AdressePostale_NomRue'];
        $this->AdressePostale_Ville=$row['AdressePostale_Ville'];
        $this->AdressePostale_Pays=$row['AdressePostale_Pays'];

        return $query;
    }
}
