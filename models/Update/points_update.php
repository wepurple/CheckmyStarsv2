<?php
header('Content-Type: application/json');
require_once("../../includes/mariadb.php");

$Dossier_ID = $_POST['Dossier_ID'] ?? null;
$Points = $_POST['Points'] ?? null;

echo $Dossier_ID;
echo $Points;


?>