<?php
session_start();
<<<<<<< Updated upstream
require_once('./../../includes/mariadb.php');
=======
require_once(__DIR__ . '/../../includes/mariadb.php');

// Sécurité : Vérification des droits (Administrateur ou Inspecteur)
if(!isset($_SESSION['Role']['Administrateur']) && !isset($_SESSION['Role']['Inspecteur'])){
    header('Location: deco.php');
    exit();
}
>>>>>>> Stashed changes

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['photo_id'])) {
    $photo_id = intval($_POST['photo_id']);
    $dossier_id = intval($_POST['dossier_id']);

    $database = new Database();
    $db = $database->getConnection();

    try {
        // 1. Récupérer le lien de la photo avant de supprimer l'entrée en base
        $query = "SELECT Photo_Lien FROM photos WHERE Photo_ID = :id";
        $stmt = $db->prepare($query);
        $stmt->execute(['id' => $photo_id]);
        $photo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($photo) {
            $filepath = $photo['Photo_Lien'];

            // 2. Supprimer l'entrée en base de données
            // Note: La table 'photos' est liée par contrainte ON DELETE CASCADE dans votre SQL
            $delete = $db->prepare("DELETE FROM photos WHERE Photo_ID = :id");
            $delete->execute(['id' => $photo_id]);

            // 3. Supprimer le fichier physique sur le serveur
            // On vérifie si le fichier existe pour éviter une erreur PHP
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }

        // Redirection vers le dossier avec un message de succès (optionnel)
        header("Location: front_dossier.php?id=$dossier_id&status=deleted");
        exit();

    } catch (Exception $e) {
        die("Erreur lors de la suppression : " . $e->getMessage());
    }
} else {
    header('Location: index.php');
    exit();
}