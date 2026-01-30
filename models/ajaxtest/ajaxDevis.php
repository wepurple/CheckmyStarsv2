<?php
include_once __DIR__ . "/../../includes/mariadb.php";
$database = new Database();
$db = $database->getConnection();

if (isset($_POST["Client_ID"])) {
    $Client_ID = $_POST["Client_ID"];
    if (is_array($db)) {
        echo json_encode(['error' => 'Erreur de connexion à la base de données']);
    } else {
        try {
            $sql = "SELECT 
                        u.Utilisateur_ID,
                        u.Utilisateur_Nom,
                        u.Utilisateur_Prenom,
                        u.Utilisateur_Mail,
                        u.Utilisateur_Telephone,
                        a.AdressePostale_CodePostal, 
                        a.AdressePostale_Ville,
                        a.AdressePostale_NumeroRue,
                        a.AdressePostale_NomRue,
                        a.AdressePostale_Complement,
                        a.AdressePostale_Pays
                    FROM utilisateurs u
                    JOIN adressespostales a ON u.AdressePostale_ID = a.AdressePostale_ID
                    WHERE u.Utilisateur_ID = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $Client_ID, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                echo json_encode([
                    'utilisateur_id' => $row['Utilisateur_ID'],
                    'nom' => htmlspecialchars($row['Utilisateur_Nom'] . ' ' . $row['Utilisateur_Prenom']),
                    'email' => htmlspecialchars($row['Utilisateur_Mail'] ?? ''),
                    'telephone' => htmlspecialchars($row['Utilisateur_Telephone'] ?? ''),
                    'adresse' => htmlspecialchars($row['AdressePostale_NumeroRue'] . ' ' . $row['AdressePostale_NomRue']),
                    'complement' => htmlspecialchars($row['AdressePostale_Complement'] ?? ''),
                    'codepostal' => htmlspecialchars($row['AdressePostale_CodePostal']),
                    'ville' => htmlspecialchars($row['AdressePostale_Ville']),
                    'pays' => htmlspecialchars($row['AdressePostale_Pays'] ?? 'France')
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