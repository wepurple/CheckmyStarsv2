-- ============================================
-- Migration : Gestion complète DEVIS → FACTURE
-- Date : 2026-01-22
-- Compatible MySQL 8.0
-- ============================================

-- 1) Table compteur pour générer les numéros uniques
-- Cette table garantit l'unicité même en cas de concurrence
CREATE TABLE IF NOT EXISTS `document_counters` (
  `type` ENUM('DEVIS', 'FACTURE') NOT NULL,
  `year` YEAR NOT NULL,
  `last_number` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`type`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Initialiser les compteurs pour l'année en cours
INSERT IGNORE INTO `document_counters` (`type`, `year`, `last_number`) VALUES
('DEVIS', 2026, 0),
('FACTURE', 2026, 0);

-- 2) Procédure pour ajouter colonne si elle n'existe pas
DELIMITER //
CREATE PROCEDURE AddColumnIfNotExists(
    IN tableName VARCHAR(64),
    IN columnName VARCHAR(64),
    IN columnDef VARCHAR(255)
)
BEGIN
    SET @colExists = (
        SELECT COUNT(*) FROM information_schema.columns 
        WHERE table_schema = DATABASE() 
        AND table_name = tableName 
        AND column_name = columnName
    );
    IF @colExists = 0 THEN
        SET @sql = CONCAT('ALTER TABLE `', tableName, '` ADD COLUMN `', columnName, '` ', columnDef);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END //
DELIMITER ;

-- 3) Ajouter colonne montant à factures_prixtotal
CALL AddColumnIfNotExists('factures_prixtotal', 'Facture_Montant', 'DECIMAL(10,2) DEFAULT NULL');

-- 4) Ajouter colonnes verrouillage au devis
CALL AddColumnIfNotExists('devis', 'Devis_Verrouille', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL AddColumnIfNotExists('devis', 'Devis_DateVerrouillage', 'DATETIME DEFAULT NULL');

-- Supprimer la procédure temporaire
DROP PROCEDURE IF EXISTS AddColumnIfNotExists;

-- 5) Ajouter contrainte UNIQUE sur Devis_Numero (si pas déjà)
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
               WHERE table_schema = DATABASE() 
               AND table_name = 'devis' 
               AND index_name = 'unique_devis_numero');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE `devis` ADD UNIQUE INDEX `unique_devis_numero` (`Devis_Numero`)',
    'SELECT "Index already exists" as msg');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6) Ajouter contrainte UNIQUE sur Facture_Numero (si pas déjà)
SET @exist := (SELECT COUNT(*) FROM information_schema.statistics 
               WHERE table_schema = DATABASE() 
               AND table_name = 'factures_prixtotal' 
               AND index_name = 'unique_facture_numero');
SET @sqlstmt := IF(@exist = 0, 
    'ALTER TABLE `factures_prixtotal` ADD UNIQUE INDEX `unique_facture_numero` (`Facture_Numero`)',
    'SELECT "Index already exists" as msg');
PREPARE stmt FROM @sqlstmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 7) Table pour stocker les items de facture (copie figée du devis)
CREATE TABLE IF NOT EXISTS `facture_items` (
  `Item_ID` INT NOT NULL AUTO_INCREMENT,
  `Facture_ID` INT NOT NULL,
  `description` VARCHAR(255) NOT NULL,
  `quantite` DECIMAL(10,2) NOT NULL,
  `prix_unitaire` DECIMAL(10,2) NOT NULL,
  `tva` DECIMAL(5,2) DEFAULT 20.00,
  `total` DECIMAL(10,2) NOT NULL,
  PRIMARY KEY (`Item_ID`),
  KEY `Facture_ID` (`Facture_ID`),
  CONSTRAINT `facture_items_ibfk_1` FOREIGN KEY (`Facture_ID`) 
    REFERENCES `factures_prixtotal` (`Facture_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8) Table pour stocker le client de la facture (copie figée)
CREATE TABLE IF NOT EXISTS `facture_client` (
  `Client_ID` INT NOT NULL AUTO_INCREMENT,
  `Facture_ID` INT NOT NULL,
  `nom` VARCHAR(100) NOT NULL,
  `adresse` TEXT,
  `email` VARCHAR(100) DEFAULT NULL,
  `telephone` VARCHAR(20) DEFAULT NULL,
  PRIMARY KEY (`Client_ID`),
  KEY `Facture_ID` (`Facture_ID`),
  CONSTRAINT `facture_client_ibfk_1` FOREIGN KEY (`Facture_ID`) 
    REFERENCES `factures_prixtotal` (`Facture_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Mise à jour des compteurs existants
-- ============================================

-- Synchroniser le compteur DEVIS avec les numéros existants (format D-YYYY-NNNNN)
UPDATE `document_counters` dc
SET `last_number` = GREATEST(`last_number`, COALESCE((
    SELECT MAX(
        CASE 
            WHEN Devis_Numero REGEXP '^D-[0-9]{4}-[0-9]+$' 
            THEN CAST(SUBSTRING_INDEX(Devis_Numero, '-', -1) AS UNSIGNED)
            ELSE 0
        END
    )
    FROM `devis`
    WHERE Devis_Numero LIKE CONCAT('D-', dc.year, '-%')
), 0))
WHERE dc.type = 'DEVIS';

-- Synchroniser le compteur FACTURE avec les numéros existants (format F-YYYY-NNNNN)
UPDATE `document_counters` dc
SET `last_number` = GREATEST(`last_number`, COALESCE((
    SELECT MAX(
        CASE 
            WHEN Facture_Numero REGEXP '^F-[0-9]{4}-[0-9]+$' 
            THEN CAST(SUBSTRING_INDEX(Facture_Numero, '-', -1) AS UNSIGNED)
            ELSE 0
        END
    )
    FROM `factures_prixtotal`
    WHERE Facture_Numero LIKE CONCAT('F-', dc.year, '-%')
), 0))
WHERE dc.type = 'FACTURE';

-- Message de confirmation
SELECT 'Migration terminée avec succès' AS status;
