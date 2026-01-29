<?php
    ob_clean();
    header('Content-Type: application/json');
    
    require_once("../../includes/mariadb.php");

    class companies
    {
        public function getAllCompanies()
        {
            $sql = "CALL Get_Companies;";
            $query = $this->connexion->prepare($sql);
            $query->execute();

            return $query;
        }
    }
?>