<?php
/**
 * Définition de la classe user
 * objet user créé à partir d'une ligne de la table utilisateurs et de l'adresse correspondante
 * 
 * By Pedro
 */
class User {
    private $connexion;
    private $table = "utilisateurs";
    private $table2 = "adressesPostales";
    private $table3 = "societes";
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
    /**
        * Créer un produit
         *
        * @return void
    */
    /*
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
        */

    public function creer(){//crée un utilisateur et une adresse
        $sql = "CALL Create_User(:NumRue, :NomRue, :Comp, :CP, :Ville, :Pays, :Nom, :Prenom, :Civilite, :Password, :Email, :Telephone, :Signature, :Societe)";
        $query = $this->connexion->prepare($sql);
        
        $query->bindParam(":NumRue", $this->AdresseNum);
        $query->bindParam(":NomRue", $this->AdresseNom);
        $query->bindParam(":Comp", $this->Complement);
        $query->bindParam(":CP", $this->CodePostal);
        $query->bindParam(":Ville", $this->Ville);
        $query->bindParam(":Pays", $this->Pays);

        $query->bindParam(":Nom", $this->Nom);
        $query->bindParam(":Email", $this->Email);
        $query->bindParam(":Prenom", $this->Prenom);
        $query->bindParam(":Societe", $this->Societe);
        $query->bindParam(":Civilite", $this->Civilite);
        $query->bindParam(":Password", $this->MotPasse);
        $query->bindParam(":Telephone", $this->Telephone);
        $query->bindParam(":Signature", $this->Signature);

        if($query->execute()){
            return true;
        }else{
            return false;
        }
    }
 
    public function afficherUtilisateur($id){
        $sql = "CALL Get_User_ID(".$id.");";
        $query = $this->connexion->prepare($sql);
        $query->execute();

        return $query;
    }

    public function supprimerUtilisateur(){

        $sql = "DELETE FROM " . $this->table . " WHERE Utilisateur_ID = ?";
        $stmt = $this->connexion->prepare($sql);
        return $stmt->execute([$this->IdPersonne]);
    }

    //{
    //    "IdPersonne" : 25
    //}

    /*public function infoDossier(){
        $sql = "SELECT * FROM ". $this->table ." AS u INNER JOIN ". $this->tableProprietaires ." AS p ON u.Utilisateur_ID = p.Utilisateur_ID;";
        $query = $this->connexion->prepare($sql);
        $query->execute();

        $sql1 = "SELECT COUNT(Dossier_ID) FROM ".$this->tableDossier." AS d INNER JOIN ".$this->table." AS u ON d.Utilisateur_ID = u.Utilisateur_ID WHERE u.Utilisateur_ID = :id;";
        $query1 = $this->connexion->prepare($sql1);
        $query1->bindParam(":id", $this->IdPersonne, PDO::PARAM_INT);
        $query1->execute();
        return $query;
        
    }*/
    public function infoDossier() {
        $sql = "CALL Dashboard_Info";

        $query = $this->connexion->prepare($sql);
        $query->execute();
        return $query;
    }

    public function getPassword($id){//retourne le hash du mdp de la personne
        $sql = "CALL get_password($id)";

        $query = $this->connexion->prepare($sql);
        $query->execute();
        return $query;
    }

    public function editPassword($id, $newPassword){//remplace le mdp de la personne        
        $sql = "CALL update_password(:identifiant, :newMdp)";

        $query = $this->connexion->prepare($sql);
        $query->bindParam(':identifiant', $id);
        $query->bindParam(':newMdp', $newPassword);
        $query->execute();
        return $query;
    }

    public function updateInfos($id, $nom, $prenom, $mail, $genre, $societe, $tel, $numRue, $nomRue, $complement, $codePostal, $ville, $pays){
        $sql = "select Update_User(:nom, :prenom, :mail, :genre, :societe, :tel, :numRue, :nomRue, :complement, :cp, :ville, :pays, :id, :role)";

        $query = $this->connexion->prepare($sql);

        $query->bindValue(':nom', htmlspecialchars(strip_tags($nom)));
        $query->bindValue(':prenom', htmlspecialchars(strip_tags($prenom)));
        $query->bindValue(':mail', htmlspecialchars(strip_tags($mail)));
        $query->bindValue(':genre', htmlspecialchars(strip_tags($genre)));
        $query->bindValue(':societe', htmlspecialchars(strip_tags($societe)));
        $query->bindValue(':tel', htmlspecialchars(strip_tags($tel)));
        $query->bindValue(':numRue', htmlspecialchars(strip_tags($numRue)));
        $query->bindValue(':nomRue', htmlspecialchars(strip_tags($nomRue)));
        $query->bindValue(':complement', htmlspecialchars(strip_tags($complement)));
        $query->bindValue(':cp', htmlspecialchars(strip_tags($codePostal)));
        $query->bindValue(':ville', htmlspecialchars(strip_tags($ville)));
        $query->bindValue(':pays', htmlspecialchars(strip_tags($pays)));
        $query->bindValue(':id', htmlspecialchars(strip_tags($id)));
        $query->bindValue(':role', 9999);

        $query->execute();
        return $query;

    }
}
