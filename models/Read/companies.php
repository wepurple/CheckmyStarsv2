<?php
    ob_clean();
    header('Content-Type: application/json');
    
    require_once("../../includes/mariadb.php");

    public function getAllUsers()
    {
        $sql = "CALL Get_Companies;";
        $query = $this->connexion->prepare($sql);
        $query->execute();

        return $query;
    }
?>