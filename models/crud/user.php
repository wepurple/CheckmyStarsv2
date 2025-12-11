<?php
class User {
    private $connexion;
    private $table = "utilisateurs";
    private $table2 = "adressesPostales";
    private $tableInspecteurs = "inspecteurs";
    private $tableAdministrateurs = "administrateurs";
    private $tableProprietaires = "proprietaires";
    private $tableDonneurordre = "donneurordre";
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
        $sql = "INSERT INTO " . $this->table . " SET Utilisateur_Nom=:Nom, Utilisateur_Prenom=:Prenom, Utilisateur_Civilite=:Civilite, Utilisateur_Telephone=:Telephone, Utilisateur_Mail=:Email, Utilisateur_Societe=:Societe, Utilisateur_Password=:MotPasse, AdressePostale_ID=:AdressePostale_ID";
        $query = $this->connexion->prepare($sql);

        $this->Nom=htmlspecialchars(strip_tags($this->Nom));
        $this->Prenom=htmlspecialchars(strip_tags($this->Prenom));
        $this->Civilite=htmlspecialchars(strip_tags($this->Civilite));
        $this->Telephone=htmlspecialchars(strip_tags($this->Telephone));
        $this->Email=htmlspecialchars(strip_tags($this->Email));
        $this->Societe=htmlspecialchars(strip_tags($this->Societe));
        $this->MotPasse=htmlspecialchars(strip_tags($this->MotPasse));
        $this->idAdresse=htmlspecialchars(strip_tags($this->idAdresse));

        $query->bindParam(":Nom", $this->Nom);
        $query->bindParam(":Prenom", $this->Prenom);
        $query->bindParam(":Civilite", $this->Civilite);
        $query->bindParam(":Telephone", $this->Telephone);
        $query->bindParam(":Email", $this->Email);
        $query->bindParam(":Societe", $this->Societe);
        $query->bindParam(":MotPasse", $this->MotPasse);
        $query->bindParam(":AdressePostale_ID", $this->idAdresse);

        if($query->execute()){
            return true;

        }
        return false;
    }
 
    public function afficherUtilisateur(){
        $sql = "SELECT * FROM ". $this->table .";";
        $query = $this->connexion->prepare($sql);
        $query->execute();
        $query->bindParam(":IdPersonne", $this->IdPersonne);
        $query->bindParam(":Nom", $this->Nom);
        $query->bindParam(":Prenom", $this->Prenom);
        $query->bindParam(":Civilite", $this->Civilite);
        $query->bindParam(":Telephone", $this->Telephone);
        $query->bindParam(":Email", $this->Email);
        $query->bindParam(":Societe", $this->Societe);
        $query->bindParam(":Signature", $this->Signature);
        $query->bindParam(":AdresseNum", $this->AdresseNum);
        $query->bindParam(":Complement", $this->Complement);
        $query->bindParam(":CodePostal", $this->CodePostal);
        $query->bindParam(":AdresseNom", $this->AdresseNom);
        $query->bindParam(":Ville", $this->Ville);
        $query->bindParam(":Pays", $this->Pays);

        return $query;
    }

    public function supprimerUtilisateur(){

        $sql = "SELECT AdressePostale_ID FROM ".$this->table." WHERE Utilisateur_ID = ?";
        $query = $this->connexion->prepare($sql);
        $query->bindParam(1, $this->IdPersonne, PDO::PARAM_INT);
        $query->execute();
        $adresse = $query->fetch(PDO::FETCH_ASSOC);

        if ($adresse && isset($adresse['AdressePostale_ID'])) {
            $adresseID = $adresse['AdressePostale_ID'];
        } else {
            $adresseID = null;
        }

        $sql3 = "DELETE FROM " . $this->tableAdministrateurs . " WHERE Utilisateur_ID = ?";
        $query3 = $this->connexion->prepare($sql3);
        $query3->bindParam(1, $this->IdPersonne, PDO::PARAM_INT);
        $query3->execute();

        $sql4 = "DELETE FROM " . $this->tableInspecteurs . " WHERE Utilisateur_ID = ?";
        $query4 = $this->connexion->prepare($sql4);
        $query4->bindParam(1, $this->IdPersonne, PDO::PARAM_INT);
        $query4->execute();

        $sql5 = "DELETE FROM " . $this->tableProprietaires . " WHERE Utilisateur_ID = ?";
        $query5 = $this->connexion->prepare($sql5);
        $query5->bindParam(1, $this->IdPersonne, PDO::PARAM_INT);
        $query5->execute();

        $sql6 = "DELETE FROM " . $this->tableProprietaires . " WHERE Utilisateur_ID = ?";
        $query6 = $this->connexion->prepare($sql6);
        $query6->bindParam(1, $this->IdPersonne, PDO::PARAM_INT);
        $query6->execute();

        $sql1 = "DELETE FROM " . $this->tableDonneurordre . " WHERE Utilisateur_ID = ?";
        $query1 = $this->connexion->prepare($sql1);
        $query1->bindParam(1, $this->IdPersonne, PDO::PARAM_INT);
        $query1->execute();


        if ($adresseID) {
            $sql2 = "DELETE FROM ".$this->table2." WHERE AdressePostale_ID =?";
            $query2 = $this->connexion->prepare($sql2);
            $query2->bindParam(1, $adresseID, PDO::PARAM_INT);
            $query2->execute();
        }
    }

    //{
    //    "IdPersonne" : 25
    //}

    public function infoDossier(){
        $sql = "SELECT * FROM ". $this->table ." AS u INNER JOIN ". $this->tableProprietaires ." AS p ON u.Utilisateur_ID = p.Utilisateur_ID;";
        $query = $this->connexion->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
