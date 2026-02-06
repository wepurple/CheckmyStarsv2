<?php
session_start();
require_once('./../../includes/mariadb.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['photo_id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    // Optionnel : Récupérer le lien pour supprimer le fichier sur le disque
    $stmt = $db->prepare("SELECT Photo_Lien FROM photos WHERE Photo_ID = ?");
    $stmt->execute([$_POST['photo_id']]);
    $photo = $stmt->fetch();

    if ($photo) {
        // Suppression SQL
        $delete = $db->prepare("DELETE FROM photos WHERE Photo_ID = ?");
        if ($delete->execute([$_POST['photo_id']])) {
            // Suppression physique du fichier si il existe
            if (file_exists($photo['Photo_Lien'])) {
                unlink($photo['Photo_Lien']);
            }
        }
    }
    header("Location: front_dossier.php?id=" . $_POST['dossier_id']);
    exit();
}