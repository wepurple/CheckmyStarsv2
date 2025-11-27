<?php
class User {
    private $connexion;
    private $table = "personne";

    public $IdPersonne;
    public $Nom;
    public $Prenom;
    public $Civilite;
    public $Telephone;
    public $Email;
    public $Adresse;
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

    public function afficherClient(){
        $sql = "SELECT * FROM ". $this->table." WHERE Role = 'Client';";
        $query = $this->connexion->prepare($sql);
        $query->execute();
        $row= $query->fetch(PDO::FETCH_ASSOC);
        $this->IdPersonne=$row['IdPersonne'];
        $this->Nom=$row['Nom'];
        $this->Prenom=$row['Prenom'];
        $this->Civilite=$row['Civilite'];
        $this->Telephone=$row['Telephone'];
        $this->Email=$row['Email'];
        $this->Adresse=$row['Adresse'];
        $this->Complement=$row['Complement'];
        $this->CodePostal=$row['CodePostal'];
        $this->Ville=$row['Ville'];
        $this->Pays=$row['Pays'];
        $this->Societenom=$row['Societe'];
        $this->Role=$row['Role'];
    }
}
