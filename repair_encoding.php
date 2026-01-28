<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

require_once("includes/mariadb.php");
$database = new Database();
$db = $database->getConnection();

echo "<h2>Réparation des encodages</h2><pre>";

// Vérifier l'encodage de la table
$check = $db->query("SHOW CREATE TABLE adressespostales")->fetch(PDO::FETCH_ASSOC);
echo "Encodage table adressespostales :\n";
echo htmlspecialchars($check['Create Table']) . "\n\n";

// Compter les lignes avec problème
$count = $db->query("SELECT COUNT(*) FROM adressespostales WHERE AdressePostale_NomRue LIKE '%?%' OR AdressePostale_NomRue REGEXP '[^[:print:][:space:]]'")->fetchColumn();
echo "Lignes corrompues détectées : {$count}\n\n";

if ($count > 0) {
    // Afficher les 5 premières lignes corrompues
    $stmt = $db->query("SELECT AdressePostale_ID, AdressePostale_NomRue FROM adressespostales WHERE AdressePostale_NomRue LIKE '%?%' LIMIT 5");
    echo "Exemples AVANT réparation :\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  ID {$row['AdressePostale_ID']}: {$row['AdressePostale_NomRue']}\n";
    }
    echo "\n";

    // RÉPARATION
    try {
        // Méthode 1 : Conversion CAST/CONVERT
        $sql = "UPDATE adressespostales 
                SET AdressePostale_NomRue = CONVERT(CAST(CONVERT(AdressePostale_NomRue USING latin1) AS BINARY) USING utf8mb4)
                WHERE AdressePostale_NomRue LIKE '%?%' OR AdressePostale_NomRue REGEXP '[^[:print:][:space:]]'";
        
        $result = $db->exec($sql);
        echo "✓ {$result} ligne(s) réparée(s) avec succès !\n\n";

        // Afficher après réparation
        $stmt = $db->query("SELECT AdressePostale_ID, AdressePostale_NomRue FROM adressespostales WHERE AdressePostale_ID IN (SELECT AdressePostale_ID FROM (SELECT AdressePostale_ID FROM adressespostales ORDER BY AdressePostale_ID DESC LIMIT 5) tmp)");
        echo "Exemples APRÈS réparation :\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "  ID {$row['AdressePostale_ID']}: {$row['AdressePostale_NomRue']}\n";
        }

    } catch (PDOException $e) {
        echo "✗ Erreur SQL : " . $e->getMessage() . "\n";
        echo "\nTentative méthode alternative...\n";
        
        // Méthode 2 : Remplacement manuel si la conversion échoue
        $replacements = [
            'Bruy?res' => 'Bruyères',
            'Rivi?re' => 'Rivière',
            'Egl?se' => 'Église',
            // Ajoute d'autres remplacements si nécessaire
        ];
        
        $total = 0;
        foreach ($replacements as $bad => $good) {
            $stmt = $db->prepare("UPDATE adressespostales SET AdressePostale_NomRue = REPLACE(AdressePostale_NomRue, :bad, :good) WHERE AdressePostale_NomRue LIKE :pattern");
            $stmt->execute([':bad' => $bad, ':good' => $good, ':pattern' => "%{$bad}%"]);
            $total += $stmt->rowCount();
        }
        echo "✓ {$total} ligne(s) réparée(s) manuellement\n";
    }

} else {
    echo "✓ Aucune corruption détectée !\n";
}

echo "\n=== Réparation terminée ===</pre>";
?>
