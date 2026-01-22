<?php
include_once "../includes/mariadb.php";
$database = new Database();
$db = $database->getConnection();

if (isset($_POST["Client_ID"])) {
    $Client_ID = $_POST["Client_ID"];
    if (is_array($db)) {
        echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    } else {
        try {
            $sql = "SELECT AdressePostale_CodePostal, AdressePostale_Ville,AdressePostale_NumeroRue,AdressePostale_NomRue FROM utilisateurs  
            JOIN adressespostales ON utilisateurs.AdressePostale_ID= adressespostales.AdressePostale_ID
            WHERE Utilisateur_ID = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $Client_ID, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                echo json_encode([
                    'adresse' => htmlspecialchars($row['AdressePostale_NumeroRue'] . ' ' . $row['AdressePostale_NomRue']),
                    'codepostal' => htmlspecialchars($row['AdressePostale_CodePostal']),
                    'ville' => htmlspecialchars($row['AdressePostale_Ville']),

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