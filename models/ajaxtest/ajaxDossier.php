<?php
include_once "../includes/mariadb.php";
$database = new Database();
$db = $database->getConnection();

if (isset($_POST["Utilisateur_ID"])) {
    $Utilisateur_ID = $_POST["Utilisateur_ID"];
    if (is_array($db)) {
        echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    } else {
        try {
            $sql = "SELECT Utilisateur_Mail, Utilisateur_Telephone,Societe_Nom FROM utilisateurs  
            JOIN societes ON utilisateurs.Societe_ID= societes.Societe_ID
            WHERE Utilisateur_ID = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $Utilisateur_ID, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                echo json_encode([
                    'mail' => htmlspecialchars($row['Utilisateur_Mail']),
                    'telephone' => htmlspecialchars($row['Utilisateur_Telephone']),
                    'societe' => htmlspecialchars($row['Societe_Nom'])

                    // 'codepostal' => htmlspecialchars($row['AdressePostale_CodePostal']),
                    // 'numerorue' => htmlspecialchars($row['AdressePostale_NumeroRue']),
                    // 'nomrue' => htmlspecialchars($row['AdressePostale_NomRue']),
                    // 'ville' => htmlspecialchars($row['AdressePostale_Ville']),
                    // 'pays' => htmlspecialchars($row['AdressePostale_Pays'])
                ]);
            } else {
                echo json_encode(['error' => 'Utilisateur non trouvé']);
            }
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Erreur : ' . $e->getMessage()]);
        }
    }
}
?>