-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 06 fév. 2026 à 15:42
-- Version du serveur : 11.5.2-MariaDB
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `checkmystars3`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `Create_company`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Create_company` (IN `NumRue` VARCHAR(50), IN `NomRue` VARCHAR(100), IN `Comp` VARCHAR(20), IN `CP` VARCHAR(10), IN `Ville` VARCHAR(100), IN `Pays` VARCHAR(100), IN `Societe_Nom` VARCHAR(150), IN `Societe_Mail` VARCHAR(150), IN `Societe_Telephone` VARCHAR(10))   BEGIN
	DECLARE v_adresse_id INT;
    DECLARE v_societes_id INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur lors de la création de l\'utilisateur';
	END;

	START TRANSACTION;
    
    
    
        INSERT INTO adressespostales (
        AdressePostale_NumeroRue,
        AdressePostale_NomRue,
        AdressePostale_Complement,
        AdressePostale_CodePostal,
        AdressePostale_Ville,
        AdressePostale_Pays
    )
    VALUES (
        NumRue, NomRue, Comp, CP, Ville, Pays
    );
    
    SET v_adresse_id = LAST_INSERT_ID();
    
    INSERT INTO societes (
    	Societe_Nom,
		Societe_Mail,
		Societe_Telephone,
        AdressePostale_ID
    )
    VALUES (
        Societe_Nom, Societe_Mail, Societe_Telephone, v_adresse_id
    );
    
    SET v_societes_id = LAST_INSERT_ID();
    
	COMMIT;

END$$

DROP PROCEDURE IF EXISTS `Create_Dossier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Create_Dossier` (IN `NumRue` VARCHAR(50), IN `NomRue` VARCHAR(100), IN `Comp` VARCHAR(50), IN `CP` VARCHAR(10), IN `Ville` VARCHAR(50), IN `Pays` VARCHAR(50), IN `BiensNom` VARCHAR(50), IN `BiensTel` VARCHAR(12), IN `BiensEtoiles` INT, IN `BiensDonneurID` INT, IN `BiensType` INT, IN `BiensUser` INT, IN `EtoileDossier` INT, IN `InspecteurID` INT)   BEGIN
	-- tt les var que jaurais besoin pour faire des test/ avoir la clé primaire pour une autre table
	DECLARE v_adresse_id INT;
    DECLARE v_biens_id INT;
    DECLARE v_dos_num VARCHAR(20);
    DECLARE v_last_id INT;
    DECLARE v_last_year INT;
    DECLARE v_current_year INT;
    DECLARE v_step VARCHAR(50);

    
	-- debut de la trans en le vla
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_step;
	END;

	START TRANSACTION;
    
    -- creation addr pos
    SET v_step = 'erreur a la partie INSERT adressespostales';
    INSERT INTO adressespostales (
            AdressePostale_NumeroRue,
            AdressePostale_NomRue,
            AdressePostale_Complement,
            AdressePostale_CodePostal,
            AdressePostale_Ville,
            AdressePostale_Pays
    )
    VALUES (
        NumRue, NomRue, Comp, CP, Ville, Pays
    );
    
    -- ici je recupere l'id de l'adresse que je viens de creer
    SET v_adresse_id = LAST_INSERT_ID();
    
    -- creation du bien
    SET v_step = 'erreur a la partie INSERT biens';
    INSERT INTO biens(
    		Biens_Nom,
        	Bien_Telephone,
        	Bien_DateEnregistrement,
        	Bien_Etoile_Actuelle,
        	Donneur_ID,
        	AdressePostale_ID,
        	TypeHebergement_ID,
        	Utilisateur_ID
    )
    VALUES (
        BiensNom, BiensTel, CURDATE(), BiensEtoiles, BiensDonneurID, v_adresse_id, BiensType, BiensUser
    );
    
    -- ici je recupere l'id du bien que je viens de creer
    SET v_biens_id = LAST_INSERT_ID();
    
    -- je chope la date
    SET v_current_year = YEAR(CURDATE());
    
    -- en gros ici je fais le num du dossier, repart de 1 si nouvelle année
    SELECT CAST(SUBSTRING_INDEX(MAX(Dossier_Numero), '-', -1) AS UNSIGNED), YEAR(MAX(Dossier_Date))
    INTO v_last_id, v_last_year
    FROM dossiers;
    	
    IF v_last_year IS NULL OR v_last_year <> v_current_year THEN
    	SET v_dos_num = CONCAT('DOS-', v_current_year, '-001');
    ELSE
    	SET v_dos_num = CONCAT(
            	'DOS-',
                v_current_year,
                '-',
                LPAD(v_last_id + 1, 3, '0')
        );
    END IF;
    
    -- creation du dossier
	SET v_step = 'erreur a la partie INSERT dossiers';
    INSERT INTO dossiers(
    	Dossier_Numero,
        Dossier_Date,
        Dossier_Etoile_Cible,
        Inspecteur_Id,
        status,
        Bien_ID,
        Proprietaire_ID,
        devis_id
    )
    VALUES (
    	v_dos_num, NOW(), EtoileDossier, InspecteurID, 0, v_biens_id, BiensUser, NULL
    );
    
    
    COMMIT;
    
END$$

DROP PROCEDURE IF EXISTS `Create_User`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Create_User` (IN `NumRue` VARCHAR(50), IN `NomRue` VARCHAR(100), IN `Comp` VARCHAR(20), IN `CP` VARCHAR(10), IN `Ville` VARCHAR(50), IN `Pays` VARCHAR(50), IN `Nom` VARCHAR(50), IN `Prenom` VARCHAR(50), IN `Civilite` ENUM('Monsieur','Madame','Iel'), IN `MDP` VARCHAR(255), IN `Mail` VARCHAR(100), IN `Telephone` VARCHAR(50), IN `Signature` VARCHAR(100), IN `Societe` INT(100), IN `Role` INT)   BEGIN
    DECLARE v_adresse_id INT;
    DECLARE v_utilisateur_id INT;
	
    -- debut de la trans en le vla
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur lors de la création de l\'utilisateur';
	END;

	START TRANSACTION;
    
    INSERT INTO adressespostales (
        AdressePostale_NumeroRue,
        AdressePostale_NomRue,
        AdressePostale_Complement,
        AdressePostale_CodePostal,
        AdressePostale_Ville,
        AdressePostale_Pays
    )
    VALUES (
        NumRue, NomRue, Comp, CP, Ville, Pays
    );

    SET v_adresse_id = LAST_INSERT_ID();

    INSERT INTO utilisateurs (
        Utilisateur_Nom,
        Utilisateur_Prenom,
        Utilisateur_Civilite,
        Utilisateur_Password,
        Utilisateur_Mail,
        Utilisateur_Telephone,
        Utilisateur_Signature,
        AdressePostale_ID,
        Societe_ID
    )
    VALUES (
        Nom, Prenom, Civilite, MDP, Mail, Telephone, Signature,
        v_adresse_id, Societe
    );

    SET v_utilisateur_id = LAST_INSERT_ID();

    IF Role = 3 THEN
        INSERT INTO administrateurs (Utilisateur_ID) VALUES (v_utilisateur_id);
    ELSEIF Role = 2 THEN
        INSERT INTO inspecteurs (Utilisateur_ID) VALUES (v_utilisateur_id);
    ELSEIF Role = 1 THEN
        INSERT INTO donneurordre (Donneur_ID, Societe_ID) VALUES (v_utilisateur_id, Societe);
    ELSEIF Role = 0 THEN
        INSERT INTO proprietaires (Utilisateur_ID) VALUES (v_utilisateur_id);
    END IF;

	COMMIT;

END$$

DROP PROCEDURE IF EXISTS `Dashboard_Info`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Dashboard_Info` ()   SELECT
	u.Utilisateur_ID,
    u.Utilisateur_Nom,
    u.Utilisateur_Prenom,
    u.Utilisateur_Telephone,
    u.Utilisateur_Mail,
    s.Societe_ID,
    s.Societe_Nom,
    COUNT(d.Dossier_ID) AS Nombre_Dossiers,
    COALESCE(MIN(d.Status), 1) AS Status_Global
FROM utilisateurs AS u
INNER JOIN proprietaires AS p ON u.Utilisateur_ID = p.Utilisateur_ID
LEFT JOIN dossiers AS d ON u.Utilisateur_ID = d.Proprietaire_ID
LEFT JOIN societes AS s ON s.Societe_ID = u.Societe_ID
GROUP BY 
     u.Utilisateur_ID, u.Utilisateur_Nom, u.Utilisateur_Prenom,
     u.Utilisateur_Telephone, u.Utilisateur_Mail$$

DROP PROCEDURE IF EXISTS `DeleteUserSafe`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUserSafe` (IN `p_user_id` INT)   BEGIN
    DECLARE v_adresse_id INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur lors de la suppression de l\'utilisateur';
    END;

    START TRANSACTION;

    -- Récupérer l'ID de l'adresse avant de supprimer l'utilisateur
    SELECT AdressePostale_ID INTO v_adresse_id 
    FROM utilisateurs 
    WHERE Utilisateur_ID = p_user_id;

    -- Supprimer d'abord les rôles de l'utilisateur
    DELETE FROM administrateurs WHERE Utilisateur_ID = p_user_id;
    DELETE FROM inspecteurs WHERE Utilisateur_ID = p_user_id;
    DELETE FROM proprietaires WHERE Utilisateur_ID = p_user_id;
    DELETE FROM donneurordre WHERE Donneur_ID = p_user_id;

    -- Vérifier s'il y a des biens ou dossiers associés
    IF EXISTS(SELECT 1 FROM biens WHERE Utilisateur_ID = p_user_id OR Donneur_ID = p_user_id) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Impossible de supprimer: cet utilisateur a des biens associés';
    END IF;

    IF EXISTS(SELECT 1 FROM dossiers WHERE Inspecteur_Id = p_user_id) THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Impossible de supprimer: cet utilisateur a des dossiers en cours';
    END IF;

    -- Supprimer l'utilisateur
    DELETE FROM utilisateurs WHERE Utilisateur_ID = p_user_id;

    -- Supprimer l'adresse postale associée
    IF v_adresse_id IS NOT NULL THEN
        DELETE FROM adressespostales WHERE AdressePostale_ID = v_adresse_id;
    END IF;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `Get_Adresse_Dossier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Adresse_Dossier` (IN `Dossier_ID` INT)   SELECT a.AdressePostale_NumeroRue,
    a.AdressePostale_NomRue,
    a.AdressePostale_Complement,
    a.AdressePostale_CodePostal,
    a.AdressePostale_Ville,
    a.AdressePostale_Pays
FROM dossiers AS d 
INNER JOIN proprietaires AS p ON d.Proprietaire_ID = p.Utilisateur_ID
INNER JOIN biens AS b ON b.Utilisateur_ID = p.Utilisateur_ID
INNER JOIN adressespostales AS a ON b.AdressePostale_ID = a.AdressePostale_ID
WHERE Dossier_Id = d.Dossier_ID and b.AdressePostale_ID = a.AdressePostale_ID and d.Bien_ID = b.Bien_ID$$

DROP PROCEDURE IF EXISTS `Get_Adresse_ID`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Adresse_ID` (IN `ID` INT)   SELECT
    a.AdressePostale_NumeroRue,
    a.AdressePostale_NomRue,
    a.AdressePostale_Complement,
    a.AdressePostale_CodePostal,
    a.AdressePostale_Ville,
    a.AdressePostale_Pays
FROM utilisateurs u
JOIN adressespostales a
	ON a.AdressePostale_ID = u.AdressePostale_ID
WHERE u.Utilisateur_ID = ID$$

DROP PROCEDURE IF EXISTS `Get_All_Inspectors`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_All_Inspectors` ()   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
    	ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Erreur lors de la récuperation des inspecteurs';
	END;
    
    SELECT * FROM inspecteurs;
    
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `Get_Companies`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Companies` ()   SELECT * FROM societes$$

DROP PROCEDURE IF EXISTS `Get_Devis_By_Dossier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Devis_By_Dossier` (IN `Dossier_ID` INT)   SELECT *
FROM devis AS d
INNER JOIN devis_client as dev_c ON d.Devis_ID = dev_c.Devis_ID
INNER JOIN devis_items as dev_i ON d.Devis_ID = dev_i.Devis_ID
INNER JOIN dossiers AS do ON d.Dossier_ID = do.Dossier_ID
WHERE Dossier_ID = do.Dossier_ID$$

DROP PROCEDURE IF EXISTS `Get_Dossier`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Dossier` ()   SELECT d.Dossier_ID, 
       d.DOSSIER_NUMERO, 
       t.TypeHebergement_Nom, 
       u.Utilisateur_Nom, 
       u.Utilisateur_Prenom, 
       a.AdressePostale_NumeroRue, 
       a.AdressePostale_NomRue, 
       a.AdressePostale_CodePostal, 
       a.AdressePostale_Ville, 
       a.AdressePostale_Pays, 
       d.status
FROM dossiers AS d
INNER JOIN utilisateurs AS u ON d.Proprietaire_ID = u.Utilisateur_ID
INNER JOIN biens AS b ON b.Bien_ID = d.Bien_ID
INNER JOIN adressespostales AS a ON a.AdressePostale_ID = b.AdressePostale_ID
INNER JOIN typeshebergements AS t ON t.TypeHebergement_ID = b.TypeHebergement_ID
ORDER BY d.Dossier_ID DESC$$

DROP PROCEDURE IF EXISTS `Get_Dossier_Etat`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Dossier_Etat` (IN `Dossier_ID` INT)   SELECT d.status,
	u.Utilisateur_Nom,
    u.Utilisateur_Prenom,
    u.Utilisateur_Mail,
    u.Utilisateur_Telephone
FROM dossiers as d
INNER JOIN inspecteurs AS i ON d.Inspecteur_Id = i.Utilisateur_ID
INNER JOIN utilisateurs AS u ON i.Utilisateur_ID = u.Utilisateur_ID
WHERE d.Dossier_ID = Dossier_ID$$

DROP PROCEDURE IF EXISTS `Get_Dossier_Inspecteur`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Dossier_Inspecteur` (IN `ID` INT)   SELECT d.Dossier_ID, 
       d.DOSSIER_NUMERO, 
       t.TypeHebergement_Nom, 
       u.Utilisateur_Nom, 
       u.Utilisateur_Prenom, 
       a.AdressePostale_NumeroRue, 
       a.AdressePostale_NomRue, 
       a.AdressePostale_CodePostal, 
       a.AdressePostale_Ville, 
       a.AdressePostale_Pays, 
       d.status
FROM dossiers AS d
INNER JOIN utilisateurs AS u ON d.Proprietaire_ID = u.Utilisateur_ID
INNER JOIN biens AS b ON b.Bien_ID = d.Bien_ID
INNER JOIN adressespostales AS a ON a.AdressePostale_ID = b.AdressePostale_ID
INNER JOIN typeshebergements AS t ON t.TypeHebergement_ID = b.TypeHebergement_ID
ORDER BY d.Dossier_ID DESC$$

DROP PROCEDURE IF EXISTS `Get_Password`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_Password` (IN `identifiant` INT)   select utilisateur_password as pwd from utilisateurs where utilisateur_id = identifiant$$

DROP PROCEDURE IF EXISTS `Get_User`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_User` ()   SELECT 
    u.Utilisateur_ID,
    u.Utilisateur_Nom,
    u.Utilisateur_Prenom,
    u.Utilisateur_Civilite,
    u.Utilisateur_Mail,
    u.Utilisateur_Signature,
    u.Utilisateur_Telephone,
	u.theme,
    s.Societe_ID,
    s.Societe_Nom,
    a.AdressePostale_NumeroRue,
    a.AdressePostale_NomRue,
    a.AdressePostale_Complement,
    a.AdressePostale_CodePostal,
    a.AdressePostale_Ville,
    a.AdressePostale_Pays,
    CASE 
        WHEN ad.Utilisateur_ID IS NOT NULL THEN '1'
        ELSE '0'
    END AS admin,
    CASE 
        WHEN ins.Utilisateur_ID IS NOT NULL THEN '1'
        ELSE '0'
    END AS inspecteur,
    CASE 
        WHEN pro.Utilisateur_ID IS NOT NULL THEN '1'
        ELSE '0'
    END AS proprietaire
FROM utilisateurs u
JOIN societes s 
    ON s.Societe_ID = u.Societe_ID
JOIN adressespostales a
    ON a.AdressePostale_ID = u.AdressePostale_ID
LEFT JOIN administrateurs ad
    ON ad.Utilisateur_ID = u.Utilisateur_ID
LEFT JOIN inspecteurs ins
    ON ins.Utilisateur_ID = u.Utilisateur_ID
LEFT JOIN proprietaires pro
    ON pro.Utilisateur_ID = u.Utilisateur_ID
ORDER BY u.Utilisateur_ID ASC$$

DROP PROCEDURE IF EXISTS `Get_User_ID`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Get_User_ID` (IN `ID` INT)   SELECT 
    u.Utilisateur_ID,
    u.Utilisateur_Nom,
    u.Utilisateur_Prenom,
    u.Utilisateur_Civilite,
    u.Utilisateur_Mail,
    u.Utilisateur_Signature,
    u.Utilisateur_Telephone,
    u.theme,
    s.Societe_ID, 
    s.Societe_Nom,
    a.AdressePostale_NumeroRue,
    a.AdressePostale_NomRue,
    a.AdressePostale_Complement,
    a.AdressePostale_CodePostal,
    a.AdressePostale_Ville,
    a.AdressePostale_Pays,
    CASE 
        WHEN ad.Utilisateur_ID IS NOT NULL THEN '1'
        ELSE '0'
    END AS admin,
    CASE 
        WHEN ins.Utilisateur_ID IS NOT NULL THEN '1'
        ELSE '0'
    END AS inspecteur,
    CASE 
        WHEN pro.Utilisateur_ID IS NOT NULL THEN '1'
        ELSE '0'
    END AS proprietaire,
    CASE 
        WHEN don.Donneur_ID IS NOT NULL THEN '1'
        ELSE '0'
    END AS donneurordre
FROM utilisateurs u
JOIN societes s 
    ON s.Societe_ID = u.Societe_ID
JOIN adressespostales a
    ON a.AdressePostale_ID = u.AdressePostale_ID
LEFT JOIN administrateurs ad
    ON ad.Utilisateur_ID = u.Utilisateur_ID
LEFT JOIN inspecteurs ins
    ON ins.Utilisateur_ID = u.Utilisateur_ID
LEFT JOIN proprietaires pro
    ON pro.Utilisateur_ID = u.Utilisateur_ID
LEFT JOIN donneurordre don
    ON don.Donneur_ID = u.Utilisateur_ID
WHERE u.Utilisateur_ID = ID$$

DROP PROCEDURE IF EXISTS `Set_Evaluation`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Set_Evaluation` (IN `Critere_evaluation` INT(255), IN `Valeur_evaluation` BOOLEAN, IN `Commentaire_evaluation` VARCHAR(255), IN `Dossier_evaluation` INT(255))   BEGIN

	INSERT INTO evaluations 
	VALUES (DEFAULT, Critere_evaluation, Valeur_evaluation, Commentaire_evaluation, Dossier_evaluation, DEFAULT);

END$$

DROP PROCEDURE IF EXISTS `Update_Password`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Update_Password` (IN `identifiant` INT, IN `mdp` VARCHAR(255))   BEGIN
	
    DECLARE nb_mdp INT;
    DECLARE last_mdp DATETIME;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Erreur lors de la modification du mpd';
    END;

    START TRANSACTION;

    UPDATE utilisateurs
    SET utilisateur_password = mdp
    WHERE utilisateur_id = identifiant;
    
    update utilisateurs
    set first_log = 0
    WHERE utilisateur_id = identifiant;
    
    SELECT COUNT(utilisateur_id)
    INTO nb_mdp
    FROM old_passwords
    where utilisateur_id = 1;
	
    SELECT MIN(date_password)
    into last_mdp
    FROM old_passwords
    WHERE utilisateur_id = identifiant;
    
    IF nb_mdp >= 5 THEN
    	DELETE FROM old_passwords
        WHERE date_password = last_mdp;
        
        INSERT INTO old_passwords (
        	utilisateur_id,
            password_hash,
            date_password
        )
        VALUES (identifiant, mdp, NOW());
        
    ELSE
    
    	INSERT INTO old_passwords (
        	utilisateur_id,
            password_hash,
            date_password
        )
        VALUES (identifiant, mdp, NOW());
    
    END IF;
    
    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `Update_Theme`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Update_Theme` (IN `ID` INT, IN `Theme` ENUM('light','dark'))   update utilisateurs set theme = Theme where utilisateur_id = ID$$

--
-- Fonctions
--
DROP FUNCTION IF EXISTS `Update_User`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `Update_User` (`Nom` VARCHAR(50), `Prenom` VARCHAR(50), `Mail` VARCHAR(50), `Genre` ENUM('Monsieur','Madame','Iel'), `Societe` INT(50), `Telephone` VARCHAR(20), `NumRue` VARCHAR(20), `Adresse` VARCHAR(50), `Complement` VARCHAR(20), `CP` VARCHAR(50), `Ville` VARCHAR(50), `Pays` VARCHAR(50), `ID` INT, `Role` INT) RETURNS INT(11)  BEGIN
	DECLARE addrID INT;
    UPDATE utilisateurs
    SET Utilisateur_Nom = Nom,
    Utilisateur_Prenom = Prenom,
	Societe_ID = Societe,
    Utilisateur_Mail = Mail,
    Utilisateur_Civilite = Genre,
    Utilisateur_Telephone = Telephone
    WHERE Utilisateur_ID = ID;
    SELECT AdressePostale_ID
    INTO addrID
    FROM utilisateurs
    WHERE Utilisateur_ID = ID;
    UPDATE adressespostales
    SET AdressePostale_NumeroRue = NumRue,
    AdressePostale_Complement = Complement,
    AdressePostale_CodePostal = CP,
    AdressePostale_NomRue = Adresse,
    AdressePostale_Ville = Ville,
    AdressePostale_Pays = Pays
    WHERE AdressePostale_ID = addrID;
	IF Role = 3 THEN
        DELETE FROM inspecteurs
        WHERE inspecteurs.Utilisateur_ID = ID;
        DELETE FROM donneurordre
        WHERE donneurordre.Donneur_ID = ID;
        DELETE FROM proprietaires
        WHERE proprietaires.Utilisateur_ID = ID;
        INSERT INTO administrateurs (Utilisateur_ID)
        SELECT ID
        WHERE NOT EXISTS (
            SELECT 1
            FROM administrateurs
            WHERE Utilisateur_ID = ID
        );
    END IF;
	IF Role = 2 THEN
        DELETE FROM administrateurs
        WHERE administrateurs.Utilisateur_ID = ID;
        DELETE FROM donneurordre
        WHERE donneurordre.Donneur_ID = ID;
        DELETE FROM proprietaires
        WHERE proprietaires.Utilisateur_ID = ID;
        INSERT INTO inspecteurs (Utilisateur_ID)
        SELECT ID
        WHERE NOT EXISTS (
            SELECT 1
            FROM inspecteurs
            WHERE Utilisateur_ID = ID
        );
    END IF;
    IF Role = 1 THEN
        DELETE FROM administrateurs
        WHERE administrateurs.Utilisateur_ID = ID;
        DELETE FROM inspecteurs
        WHERE inspecteurs.Utilisateur_ID = ID;
        DELETE FROM proprietaires
        WHERE proprietaires.Utilisateur_ID = ID;
        INSERT INTO donneurordre (Donneur_ID)
        SELECT ID
        WHERE NOT EXISTS (
            SELECT 1
            FROM donneurordre
            WHERE Donneur_ID = ID
        );
    END IF;
    IF Role = 0 THEN
        DELETE FROM administrateurs
        WHERE administrateurs.Utilisateur_ID = ID;
        DELETE FROM donneurordre
        WHERE donneurordre.Donneur_ID = ID;
        DELETE FROM inspecteurs
        WHERE inspecteurs.Utilisateur_ID = ID;
        INSERT INTO proprietaires (Utilisateur_ID)
        SELECT ID
        WHERE NOT EXISTS (
            SELECT 1
            FROM proprietaires
            WHERE Utilisateur_ID = ID
        );
    END IF;
    RETURN true;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

DROP TABLE IF EXISTS `administrateurs`;
CREATE TABLE IF NOT EXISTS `administrateurs` (
  `Utilisateur_ID` int(11) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `administrateurs`
--

INSERT INTO `administrateurs` (`Utilisateur_ID`) VALUES
(1),
(23),
(24);

-- --------------------------------------------------------

--
-- Structure de la table `administre`
--

DROP TABLE IF EXISTS `administre`;
CREATE TABLE IF NOT EXISTS `administre` (
  `Utilisateur_ID` int(11) NOT NULL,
  `Critere_ID` int(11) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`,`Critere_ID`),
  KEY `Critere_ID` (`Critere_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `adressespostales`
--

DROP TABLE IF EXISTS `adressespostales`;
CREATE TABLE IF NOT EXISTS `adressespostales` (
  `AdressePostale_ID` int(11) NOT NULL AUTO_INCREMENT,
  `AdressePostale_NumeroRue` varchar(50) DEFAULT NULL,
  `AdressePostale_Complement` varchar(50) DEFAULT NULL,
  `AdressePostale_CodePostal` varchar(50) DEFAULT NULL,
  `AdressePostale_NomRue` varchar(256) DEFAULT NULL,
  `AdressePostale_Ville` varchar(256) DEFAULT NULL,
  `AdressePostale_Pays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`AdressePostale_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `adressespostales`
--

INSERT INTO `adressespostales` (`AdressePostale_ID`, `AdressePostale_NumeroRue`, `AdressePostale_Complement`, `AdressePostale_CodePostal`, `AdressePostale_NomRue`, `AdressePostale_Ville`, `AdressePostale_Pays`) VALUES
(1, '10', NULL, '75007', 'Rue de Rivoli', 'Paris', 'France'),
(2, '25', NULL, '69002', 'Rue Mercière', 'Lyon', 'France'),
(3, '5', NULL, '45000', 'Avenue des Fleurs', 'Orléans', 'France'),
(4, '42', NULL, '33000', 'Cours de l’Intendance', 'Bordeaux', 'France'),
(5, '8', 'Bât A', '59000', 'Rue Nationale', 'Lille', 'France'),
(6, '17', NULL, '34000', 'Rue de la Loge', 'Montpellier', 'France'),
(11, '14', '', '45100', 'Rue des Bruyères', 'Orléans', 'France'),
(12, '3', NULL, '45100', 'Rue du Portereau', 'Orléans', 'France'),
(13, '88', NULL, '37000', 'Rue Nationale', 'Tours', 'France'),
(14, '21', 'appt 12', '54000', 'Rue Saint-Dizier', 'Nancy', 'France'),
(15, '6', NULL, '44000', 'Rue de Strasbourg', 'Nantes', 'France'),
(16, '8', '', '28190', 'Rue Jean Bouvart', 'Saint-Luperce', 'France'),
(17, '2', NULL, '69003', 'Rue de la Part-Dieu', 'Lyon', 'France'),
(18, '77', 'Bât C', '35000', 'Rue de Saint-Malo', 'Rennes', 'France'),
(19, '11', NULL, '67000', 'Rue des Grandes Arcades', 'Strasbourg', 'France'),
(20, '4', NULL, '21000', 'Rue de la Liberté', 'Dijon', 'France'),
(21, '51', NULL, '45100', 'Avenue de la Source', 'Orléans', 'France'),
(22, '9', 'Résidence Soleil', '45000', 'Rue de Bourgogne', 'Orléans', 'France'),
(23, '130', NULL, '45160', 'Route de Sandillon', 'Olivet', 'France'),
(24, '7', NULL, '41000', 'Rue du Château', 'Blois', 'France'),
(25, '18', NULL, '76000', 'Rue Jeanne d’Arc', 'Rouen', 'France'),
(26, '26', NULL, '29100', 'Quai de l’Odet', 'Quimper', 'France'),
(27, '1', NULL, '06000', 'Promenade des Anglais', 'Nice', 'France'),
(28, '15', NULL, '13200', 'Rue de la République', 'Arles', 'France'),
(29, '22', NULL, '74200', 'Avenue du Léman', 'Thonon-les-Bains', 'France'),
(30, '5', NULL, '20137', 'Route des Sanguinaires', 'Ajaccio', 'France'),
(37, '14', '', '41600', 'Rue des Bruyères', 'Chaon', 'France'),
(40, '14', '', '41600', 'Rue des Bruyères', 'Chaon', 'France'),
(41, '67', '', '46100', 'rue des bruyere', 'Chaon', 'France'),
(49, '12', '', '45100', 'Rue de Louis', 'Orléans', 'France'),
(53, '12', 'bis', '45100', 'Rue de Louis', 'Orléans', 'France'),
(56, '12', 'bis', '45100', 'Rue de Louis', 'Orléans', 'France'),
(62, '67', 'bis', '41600', 'test temp', 'chaon', 'France'),
(66, '14', '', '41600', 'Rue des Bruyères', 'Chaon', 'France'),
(67, '14', '', '41600', 'Rue des Bruyères', 'Chaon', 'France'),
(68, '14', '', '41600', 'Rue des Bruyères', 'Chaon', 'France'),
(69, '14', '', '41600', 'Rue des Bruyères', 'Chaon', 'France'),
(70, '4', 'n 358', '45100', 'Rue des Pivoines', 'Orléans', 'France'),
(71, '14', 'bis', '67130', 'Rue', 'Wisches', 'France'),
(72, '14', '', '67130', 'Rue', 'Wisches', 'France'),
(73, '4', 'appartement 358', '45100', 'Rue des Pivoines', 'Orléans', 'France'),
(78, '12', 'bis', '45100', 'Rue de Louis', 'Orléans', 'France');

-- --------------------------------------------------------

--
-- Structure de la table `biens`
--

DROP TABLE IF EXISTS `biens`;
CREATE TABLE IF NOT EXISTS `biens` (
  `Bien_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Biens_Nom` varchar(50) DEFAULT NULL,
  `Bien_Telephone` varchar(50) DEFAULT NULL,
  `Bien_DateEnregistrement` date DEFAULT NULL,
  `Bien_Etoile_Actuelle` int(11) DEFAULT NULL,
  `Donneur_ID` int(11) DEFAULT NULL,
  `AdressePostale_ID` int(11) NOT NULL,
  `TypeHebergement_ID` int(11) NOT NULL,
  `Utilisateur_ID` int(11) NOT NULL,
  PRIMARY KEY (`Bien_ID`),
  KEY `Utilisateur_ID` (`Donneur_ID`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`),
  KEY `TypeHebergement_ID` (`TypeHebergement_ID`),
  KEY `Utilisateur_ID_1` (`Utilisateur_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `biens`
--

INSERT INTO `biens` (`Bien_ID`, `Biens_Nom`, `Bien_Telephone`, `Bien_DateEnregistrement`, `Bien_Etoile_Actuelle`, `Donneur_ID`, `AdressePostale_ID`, `TypeHebergement_ID`, `Utilisateur_ID`) VALUES
(1, 'H?tel Lumi?re', '0140101010', '2025-01-10', 4, 13, 21, 1, 5),
(101, 'G?te des Bruy?res', '0238121212', '2025-01-14', 3, 14, 22, 2, 6),
(102, 'Camping de la Rivi?re', '0556123456', '2025-01-20', 2, 15, 23, 3, 9),
(103, 'H?tel du Centre', '0142000303', '2025-02-02', 5, 13, 24, 1, 7),
(104, 'G?te des Vignes', '0238454545', '2025-02-11', 2, 14, 25, 2, 8),
(105, 'Camping Azur', '0493000000', '2025-02-15', 3, 16, 27, 3, 10),
(106, 'H?tel des Arts', '0141112233', '2025-03-05', 0, 15, 26, 1, 11),
(107, 'G?te Loire & Nature', '0238008899', '2025-03-11', 4, 16, 28, 2, 12),
(108, 'Camping du L?man', '0450001122', '2025-03-19', 1, 15, 29, 3, 6),
(109, 'H?tel Sanguinaires', '0495006677', '2025-04-02', 3, 13, 30, 1, 8),
(112, 'Gite de magnifique trence', '0273568145', '2026-02-04', 2, 13, 49, 2, 21),
(115, 'Gite de magnifique terence', '0273568145', '2026-02-04', 2, 13, 53, 2, 21),
(118, 'Gite de magnifique terence', '0273568145', '2026-02-04', 2, 13, 56, 2, 21),
(127, 'Le gite de angel', '0769155622', '2026-02-05', 2, 13, 66, 1, 21),
(128, 'Le gite de angel', '0769155622', '2026-02-05', 4, 13, 67, 1, 21),
(129, 'Le gite de angel', '0769155622', '2026-02-05', 2, 13, 68, 1, 21),
(130, 'Le gite de angel', '0769155622', '2026-02-05', 4, NULL, 69, 1, 21),
(135, 'Gite de magnifique terence', '0273568145', '2026-02-06', 2, 13, 78, 2, 21);

-- --------------------------------------------------------

--
-- Structure de la table `concerne`
--

DROP TABLE IF EXISTS `concerne`;
CREATE TABLE IF NOT EXISTS `concerne` (
  `Bien_ID` int(11) NOT NULL,
  `Dossier_ID` int(11) NOT NULL,
  PRIMARY KEY (`Bien_ID`,`Dossier_ID`),
  KEY `Dossier_ID` (`Dossier_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `concerne`
--

INSERT INTO `concerne` (`Bien_ID`, `Dossier_ID`) VALUES
(1, 1),
(101, 2),
(102, 3),
(103, 4),
(104, 5),
(105, 6),
(106, 7),
(107, 8),
(108, 9),
(109, 10);

-- --------------------------------------------------------

--
-- Structure de la table `contient`
--

DROP TABLE IF EXISTS `contient`;
CREATE TABLE IF NOT EXISTS `contient` (
  `Critere_ID` int(11) NOT NULL,
  `Photo_ID` int(11) NOT NULL,
  `ListesCriteres_ID` int(11) NOT NULL,
  PRIMARY KEY (`Critere_ID`,`Photo_ID`,`ListesCriteres_ID`),
  KEY `Photo_ID` (`Photo_ID`),
  KEY `ListesCriteres_ID` (`ListesCriteres_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `contient`
--

INSERT INTO `contient` (`Critere_ID`, `Photo_ID`, `ListesCriteres_ID`) VALUES
(1, 100, 1),
(1, 100, 2),
(1, 100, 3),
(1, 100, 4),
(1, 100, 5),
(2, 100, 1),
(2, 100, 2),
(2, 100, 3),
(2, 100, 4),
(2, 100, 5),
(3, 100, 1),
(3, 100, 2),
(3, 100, 3),
(3, 100, 4),
(3, 100, 5),
(4, 100, 1),
(4, 100, 2),
(4, 100, 3),
(4, 100, 4),
(4, 100, 5),
(5, 100, 1),
(5, 100, 2),
(5, 100, 3),
(5, 100, 4),
(5, 100, 5),
(6, 100, 1),
(6, 100, 2),
(6, 100, 3),
(6, 100, 4),
(6, 100, 5),
(7, 100, 1),
(7, 100, 2),
(7, 100, 3),
(7, 100, 4),
(7, 100, 5),
(8, 100, 1),
(8, 100, 2),
(8, 100, 3),
(8, 100, 4),
(8, 100, 5),
(9, 100, 1),
(9, 100, 2),
(9, 100, 3),
(9, 100, 4),
(9, 100, 5),
(10, 100, 1),
(10, 100, 2),
(10, 100, 3),
(10, 100, 4),
(10, 100, 5),
(11, 100, 1),
(11, 100, 2),
(11, 100, 3),
(11, 100, 4),
(11, 100, 5),
(12, 100, 1),
(12, 100, 2),
(12, 100, 3),
(12, 100, 4),
(12, 100, 5),
(13, 100, 1),
(13, 100, 2),
(13, 100, 3),
(13, 100, 4),
(13, 100, 5),
(14, 100, 1),
(14, 100, 2),
(14, 100, 3),
(14, 100, 4),
(14, 100, 5),
(15, 100, 1),
(15, 100, 2),
(15, 100, 3),
(15, 100, 4),
(15, 100, 5),
(16, 100, 1),
(16, 100, 2),
(16, 100, 3),
(16, 100, 4),
(16, 100, 5),
(17, 100, 1),
(17, 100, 2),
(17, 100, 3),
(17, 100, 4),
(17, 100, 5),
(18, 100, 1),
(18, 100, 2),
(18, 100, 3),
(18, 100, 4),
(18, 100, 5),
(19, 100, 1),
(19, 100, 2),
(19, 100, 3),
(19, 100, 4),
(19, 100, 5),
(20, 100, 1),
(20, 100, 2),
(20, 100, 3),
(20, 100, 4),
(20, 100, 5),
(21, 100, 1),
(21, 100, 2),
(21, 100, 3),
(21, 100, 4),
(21, 100, 5),
(22, 100, 1),
(22, 100, 2),
(22, 100, 3),
(22, 100, 4),
(22, 100, 5),
(23, 100, 1),
(23, 100, 2),
(24, 100, 1),
(24, 100, 2),
(24, 100, 3),
(24, 100, 4),
(24, 100, 5),
(25, 100, 1),
(25, 100, 2),
(25, 100, 3),
(25, 100, 4),
(25, 100, 5),
(26, 100, 1),
(26, 100, 2),
(26, 100, 3),
(26, 100, 4),
(26, 100, 5),
(27, 100, 1),
(27, 100, 2),
(27, 100, 3),
(27, 100, 4),
(27, 100, 5),
(28, 100, 1),
(28, 100, 2),
(28, 100, 3),
(28, 100, 4),
(28, 100, 5),
(29, 100, 1),
(29, 100, 2),
(29, 100, 3),
(29, 100, 4),
(29, 100, 5),
(30, 100, 1),
(30, 100, 2),
(30, 100, 3),
(30, 100, 4),
(30, 100, 5),
(31, 100, 1),
(31, 100, 2),
(31, 100, 3),
(31, 100, 4),
(31, 100, 5),
(32, 100, 1),
(32, 100, 2),
(32, 100, 3),
(32, 100, 4),
(32, 100, 5),
(33, 100, 1),
(33, 100, 2),
(33, 100, 3),
(33, 100, 4),
(33, 100, 5),
(34, 100, 1),
(34, 100, 2),
(34, 100, 3),
(34, 100, 4),
(34, 100, 5),
(35, 100, 1),
(35, 100, 2),
(35, 100, 3),
(35, 100, 4),
(35, 100, 5),
(36, 100, 1),
(36, 100, 2),
(36, 100, 3),
(36, 100, 4),
(36, 100, 5),
(37, 100, 1),
(37, 100, 2),
(37, 100, 3),
(37, 100, 4),
(37, 100, 5),
(38, 100, 1),
(38, 100, 2),
(38, 100, 3),
(38, 100, 4),
(38, 100, 5),
(39, 100, 1),
(39, 100, 2),
(39, 100, 3),
(40, 100, 1),
(40, 100, 2),
(40, 100, 3),
(40, 100, 4),
(40, 100, 5),
(41, 100, 1),
(41, 100, 2),
(41, 100, 3),
(41, 100, 4),
(42, 100, 1),
(42, 100, 2),
(42, 100, 3),
(42, 100, 4),
(42, 100, 5),
(46, 100, 1),
(46, 100, 2),
(46, 100, 3),
(46, 100, 4),
(46, 100, 5),
(47, 100, 1),
(47, 100, 2),
(47, 100, 3),
(47, 100, 4),
(47, 100, 5),
(48, 100, 1),
(48, 100, 2),
(48, 100, 3),
(48, 100, 4),
(48, 100, 5),
(49, 100, 1),
(49, 100, 2),
(49, 100, 3),
(49, 100, 4),
(49, 100, 5),
(50, 100, 1),
(50, 100, 2),
(50, 100, 3),
(50, 100, 4),
(50, 100, 5),
(51, 100, 1),
(51, 100, 2),
(51, 100, 3),
(51, 100, 4),
(51, 100, 5),
(52, 100, 1),
(52, 100, 2),
(52, 100, 3),
(52, 100, 4),
(52, 100, 5),
(53, 100, 1),
(53, 100, 2),
(53, 100, 3),
(53, 100, 4),
(53, 100, 5),
(54, 100, 1),
(54, 100, 2),
(54, 100, 3),
(54, 100, 4),
(54, 100, 5),
(55, 100, 1),
(55, 100, 2),
(55, 100, 3),
(55, 100, 4),
(55, 100, 5),
(56, 100, 1),
(56, 100, 2),
(56, 100, 3),
(56, 100, 4),
(56, 100, 5),
(57, 100, 1),
(57, 100, 2),
(57, 100, 3),
(57, 100, 4),
(57, 100, 5),
(58, 100, 1),
(58, 100, 2),
(58, 100, 3),
(58, 100, 4),
(58, 100, 5),
(59, 100, 1),
(59, 100, 2),
(59, 100, 3),
(59, 100, 4),
(59, 100, 5),
(60, 100, 1),
(60, 100, 2),
(60, 100, 3),
(60, 100, 4),
(60, 100, 5),
(61, 100, 1),
(61, 100, 2),
(61, 100, 3),
(61, 100, 4),
(61, 100, 5),
(62, 100, 1),
(62, 100, 2),
(62, 100, 3),
(62, 100, 4),
(62, 100, 5),
(63, 100, 1),
(63, 100, 2),
(63, 100, 3),
(63, 100, 4),
(63, 100, 5),
(64, 100, 1),
(64, 100, 2),
(64, 100, 3),
(64, 100, 4),
(64, 100, 5),
(65, 100, 1),
(65, 100, 2),
(65, 100, 3),
(65, 100, 4),
(65, 100, 5),
(66, 100, 1),
(66, 100, 2),
(66, 100, 3),
(66, 100, 4),
(66, 100, 5),
(67, 100, 1),
(67, 100, 2),
(67, 100, 3),
(67, 100, 4),
(67, 100, 5),
(68, 100, 1),
(68, 100, 2),
(68, 100, 3),
(68, 100, 4),
(68, 100, 5),
(69, 100, 1),
(69, 100, 2),
(69, 100, 3),
(69, 100, 4),
(69, 100, 5),
(70, 100, 1),
(70, 100, 2),
(70, 100, 3),
(70, 100, 4),
(70, 100, 5),
(71, 100, 1),
(71, 100, 2),
(71, 100, 3),
(71, 100, 4),
(71, 100, 5),
(72, 100, 1),
(72, 100, 2),
(72, 100, 3),
(72, 100, 4),
(72, 100, 5),
(73, 100, 1),
(73, 100, 2),
(73, 100, 3),
(73, 100, 4),
(73, 100, 5),
(74, 100, 1),
(74, 100, 2),
(74, 100, 3),
(74, 100, 4),
(74, 100, 5),
(75, 100, 1),
(75, 100, 2),
(75, 100, 3),
(75, 100, 4),
(75, 100, 5),
(76, 100, 1),
(76, 100, 2),
(76, 100, 3),
(76, 100, 4),
(76, 100, 5),
(77, 100, 1),
(77, 100, 2),
(78, 100, 1),
(78, 100, 2),
(78, 100, 3),
(78, 100, 4),
(78, 100, 5),
(79, 100, 1),
(79, 100, 2),
(79, 100, 3),
(79, 100, 4),
(79, 100, 5),
(80, 100, 1),
(80, 100, 2),
(80, 100, 3),
(80, 100, 4),
(80, 100, 5),
(81, 100, 1),
(81, 100, 2),
(81, 100, 3),
(81, 100, 4),
(81, 100, 5),
(82, 100, 1),
(82, 100, 2),
(82, 100, 3),
(82, 100, 4),
(82, 100, 5),
(83, 100, 1),
(83, 100, 2),
(83, 100, 3),
(83, 100, 4),
(83, 100, 5),
(84, 100, 1),
(84, 100, 2),
(84, 100, 3),
(84, 100, 4),
(84, 100, 5),
(85, 100, 1),
(85, 100, 2),
(85, 100, 3),
(85, 100, 4),
(85, 100, 5),
(86, 100, 1),
(86, 100, 2),
(86, 100, 3),
(86, 100, 4),
(86, 100, 5),
(87, 100, 1),
(87, 100, 2),
(87, 100, 3),
(87, 100, 4),
(87, 100, 5),
(88, 100, 1),
(88, 100, 2),
(88, 100, 3),
(88, 100, 4),
(88, 100, 5),
(89, 100, 1),
(89, 100, 2),
(89, 100, 3),
(89, 100, 4),
(89, 100, 5),
(90, 100, 1),
(90, 100, 2),
(90, 100, 3),
(90, 100, 4),
(90, 100, 5),
(91, 100, 1),
(91, 100, 2),
(91, 100, 3),
(91, 100, 4),
(91, 100, 5),
(92, 100, 1),
(92, 100, 2),
(92, 100, 3),
(92, 100, 4),
(92, 100, 5),
(93, 100, 1),
(93, 100, 2),
(93, 100, 3),
(93, 100, 4),
(93, 100, 5),
(94, 100, 1),
(94, 100, 2),
(94, 100, 3),
(94, 100, 4),
(94, 100, 5),
(95, 100, 1),
(95, 100, 2),
(95, 100, 3),
(95, 100, 4),
(95, 100, 5),
(96, 100, 1),
(96, 100, 2),
(96, 100, 3),
(96, 100, 4),
(96, 100, 5),
(97, 100, 1),
(97, 100, 2),
(97, 100, 3),
(97, 100, 4),
(97, 100, 5),
(98, 100, 1),
(98, 100, 2),
(98, 100, 3),
(98, 100, 4),
(98, 100, 5),
(99, 100, 1),
(99, 100, 2),
(99, 100, 3),
(99, 100, 4),
(99, 100, 5),
(100, 100, 1),
(100, 100, 2),
(100, 100, 3),
(100, 100, 4),
(100, 100, 5),
(101, 100, 1),
(101, 100, 2),
(101, 100, 3),
(101, 100, 4),
(101, 100, 5),
(102, 100, 1),
(102, 100, 2),
(102, 100, 3),
(102, 100, 4),
(102, 100, 5),
(103, 100, 1),
(103, 100, 2),
(103, 100, 3),
(103, 100, 4),
(103, 100, 5),
(104, 100, 1),
(104, 100, 2),
(104, 100, 3),
(104, 100, 4),
(104, 100, 5),
(105, 100, 1),
(105, 100, 2),
(105, 100, 3),
(105, 100, 4),
(105, 100, 5),
(106, 100, 1),
(106, 100, 2),
(106, 100, 3),
(106, 100, 4),
(106, 100, 5),
(107, 100, 1),
(107, 100, 2),
(107, 100, 3),
(107, 100, 4),
(107, 100, 5),
(108, 100, 1),
(108, 100, 2),
(108, 100, 3),
(108, 100, 4),
(108, 100, 5),
(109, 100, 1),
(109, 100, 2),
(109, 100, 3),
(109, 100, 4),
(109, 100, 5),
(110, 100, 1),
(110, 100, 2),
(110, 100, 3),
(110, 100, 4),
(110, 100, 5),
(111, 100, 1),
(111, 100, 2),
(111, 100, 3),
(111, 100, 4),
(111, 100, 5),
(112, 100, 1),
(112, 100, 2),
(112, 100, 3),
(112, 100, 4),
(112, 100, 5),
(113, 100, 1),
(113, 100, 2),
(113, 100, 3),
(113, 100, 4),
(113, 100, 5),
(114, 100, 1),
(114, 100, 2),
(114, 100, 3),
(114, 100, 4),
(114, 100, 5),
(115, 100, 1),
(115, 100, 2),
(115, 100, 3),
(115, 100, 4),
(115, 100, 5),
(116, 100, 1),
(116, 100, 2),
(116, 100, 3),
(116, 100, 4),
(116, 100, 5),
(117, 100, 1),
(117, 100, 2),
(117, 100, 3),
(117, 100, 4),
(117, 100, 5),
(118, 100, 1),
(118, 100, 2),
(118, 100, 3),
(118, 100, 4),
(118, 100, 5),
(119, 100, 1),
(119, 100, 2),
(119, 100, 3),
(119, 100, 4),
(119, 100, 5),
(120, 100, 1),
(120, 100, 2),
(120, 100, 3),
(120, 100, 4),
(120, 100, 5),
(121, 100, 1),
(121, 100, 2),
(121, 100, 3),
(121, 100, 4),
(121, 100, 5),
(122, 100, 1),
(122, 100, 2),
(122, 100, 3),
(122, 100, 4),
(122, 100, 5),
(123, 100, 1),
(123, 100, 2),
(123, 100, 3),
(123, 100, 4),
(123, 100, 5),
(124, 100, 1),
(124, 100, 2),
(124, 100, 3),
(124, 100, 4),
(124, 100, 5),
(125, 100, 1),
(125, 100, 2),
(125, 100, 3),
(125, 100, 4),
(125, 100, 5),
(126, 100, 1),
(126, 100, 2),
(126, 100, 3),
(126, 100, 4),
(126, 100, 5),
(127, 100, 1),
(127, 100, 2),
(127, 100, 3),
(127, 100, 4),
(127, 100, 5),
(128, 100, 1),
(128, 100, 2),
(128, 100, 3),
(128, 100, 4),
(128, 100, 5),
(129, 100, 1),
(129, 100, 2),
(129, 100, 3),
(129, 100, 4),
(129, 100, 5),
(130, 100, 1),
(130, 100, 2),
(130, 100, 3),
(130, 100, 4),
(130, 100, 5),
(131, 100, 1),
(131, 100, 2),
(131, 100, 3),
(131, 100, 4),
(131, 100, 5),
(132, 100, 1),
(132, 100, 2),
(132, 100, 3),
(132, 100, 4),
(132, 100, 5),
(133, 100, 1),
(133, 100, 2),
(133, 100, 3),
(133, 100, 4),
(133, 100, 5);

-- --------------------------------------------------------

--
-- Structure de la table `criteres`
--

DROP TABLE IF EXISTS `criteres`;
CREATE TABLE IF NOT EXISTS `criteres` (
  `Critere_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Critere_description` varchar(150) DEFAULT NULL,
  `Critere_statut` varchar(50) DEFAULT NULL,
  `Critere_points` int(11) DEFAULT NULL,
  PRIMARY KEY (`Critere_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=183 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `criteres`
--

INSERT INTO `criteres` (`Critere_ID`, `Critere_description`, `Critere_statut`, `Critere_points`) VALUES
(1, 'Surface totale minimum (cuisine et coin cuisine compris) du logement meublé hors salle d\'eau et toilettess', 'X', 5),
(2, 'Surface totale majorée', 'O', 5),
(3, 'Prise de courant libre dans chaque pièce d\'habitation', 'X', 1),
(4, 'Tous les éclairages du logement fonctionnent et sont en bon état', 'X', 3),
(5, 'Mise à disposition d\'un téléphone privatif à l\'intérieur du logement', 'O', 1),
(6, 'Accès internet par un réseau local sans fil (WiFi)', 'O', 2),
(7, 'Accès internet filaire avec câble fourni', 'O', 2),
(8, 'Télévision à écran plat avec télécommande', 'O', 2),
(9, 'Accès à des chaînes supplémentaires à l\'offre de la TNT', 'O', 2),
(10, 'Possibilité d\'accéder à au moins deux chaînes internationales', 'O', 1),
(11, 'Radio', 'O', 2),
(12, 'Enceinte connectée', 'O', 1),
(13, 'Mise à disposition d\'un système de lecture de vidéos', 'O', 2),
(14, 'Occultation opaque : extérieure ou intérieure dans chaque pièce comportant un couchage principal', 'X', 3),
(15, 'Le logement est équipé de double vitrage', 'O', 3),
(16, 'Existence d\'un système de chauffage en état de fonctionnement', 'X', 5),
(17, 'Existence d\'un système de climatisation et/ou de rafraîchissement d\'air en état de fonctionnement', 'O', 3),
(18, 'Machine à laver le linge pour les logements de 4 personnes et plus', 'O', 3),
(19, 'Sèche-linge électrique pour les logements de 6 personnes et plus', 'O', 2),
(20, 'Etendoir ou séchoir à linge à l\'intérieur du logement', 'X', 2),
(21, 'Ustensiles de ménage appropriés au logement', 'X', 3),
(22, 'Fer et table à repasser', 'O', 2),
(23, 'Placards ou éléments de rangement dans le logement', 'X', 3),
(24, 'Placards ou éléments de rangement dans chaque pièce d\'habitation', 'O', 3),
(25, 'Présence d\'une table et d\'assises correspondant à la capacité d\'accueil du logement', 'X', 4),
(26, 'Présence d\'un canapé ou fauteuil(s) adapté(s) à la capacité d\'accueil', 'X', 3),
(27, 'Présence d\'une table basse', 'X', 1),
(28, 'Respect des dimensions du (ou des) lit(s)', 'X', 4),
(29, 'Matelas haute densité et/ou avec une épaisseur de qualité', 'O', 2),
(30, 'Présence d\'oreiller(s) en quantité suffisante', 'X', 2),
(31, 'Deux couvertures ou une couette par lit - couette obligatoire pour les catégories 3*, 4* et 5*', 'X', 2),
(32, 'Matelas et oreillers protégés par des alaises ou des housses amovibles', 'X', 2),
(33, 'Eclairage en-tête de lit par personne avec interrupteur individuel', 'X', 2),
(34, 'Interrupteur ou système de commande de l\'éclairage central près du lit', 'O', 2),
(35, 'Présence d\'une prise de courant libre située près du lit', 'O', 1),
(36, 'Présence d\'une table de chevet par personne', 'O', 2),
(37, 'Une salle d\'eau privative dans un espace clos et aéré intérieur au logement', 'X', 2),
(38, 'Une salle d\'eau privative avec accès indépendant dans un espace intérieur au logement', 'X', 3),
(39, 'Présence d\'une salle d\'eau ainsi équipée : lavabo avec eau chaude, douche et/ou baignoire', 'X', 3),
(40, 'Présence d\'une salle d\'eau avec dimensions supérieures au standard', 'O', 2),
(41, 'WC privatif intérieur au logement', 'X', 2),
(42, 'WC privatif indépendant de la salle d\'eau', 'O', 2),
(43, 'Deuxième salle d\'eau privative', 'NA', 5),
(44, 'Salle d\'eau supplémentaire équipée', 'NA', 3),
(45, 'WC privatif supplémentaire', 'NA', 2),
(46, 'Deux points lumineux dont un sur le lavabo', 'O', 2),
(47, 'Présence de produits d\'accueil', 'O', 3),
(48, 'Prise de courant libre à proximité du miroir', 'O', 2),
(49, 'Patères ou porte-serviettes', 'X', 1),
(50, 'Sèche-serviettes électrique', 'O', 2),
(51, 'Miroir de salle de bain', 'X', 2),
(52, 'Miroir en pied', 'O', 2),
(53, 'Tablette ou étagère proche du miroir', 'X', 2),
(54, 'Espaces de rangement supplémentaires', 'O', 2),
(55, 'Sèche-cheveux électrique', 'O', 1),
(56, 'Évier avec robinet mélangeur ou mitigeur', 'X', 3),
(57, 'Nombre de foyers respectés', 'X', 3),
(58, 'Plaque vitrocéramique, induction ou gaz', 'O', 2),
(59, 'Four ou mini-four', 'X', 3),
(60, 'Four à micro-ondes', 'O', 2),
(61, 'Ventilation ou VMC', 'X', 4),
(62, 'Hotte aspirante', 'O', 2),
(63, 'Quantité suffisante de vaisselle par personne', 'X', 3),
(64, 'Vaisselle supplémentaire par personne', 'O', 1),
(65, 'Équipement minimum pour la préparation des repas', 'X', 3),
(66, 'Au moins deux équipements de petit électroménager', 'O', 2),
(67, 'Autocuiseur, cuit-vapeur ou robot multifonction', 'O', 3),
(68, 'Cafetière', 'X', 2),
(69, 'Machine à expresso', 'O', 2),
(70, 'Bouilloire', 'O', 1),
(71, 'Grille-pain', 'O', 1),
(72, 'Lave-vaisselle à partir de 2 personnes', 'O', 2),
(73, 'Lave-vaisselle 6 couverts ou plus', 'O', 2),
(74, 'Réfrigérateur avec compartiment conservateur', 'X', 4),
(75, 'Congélateur ou compartiment congélateur', 'O', 2),
(76, 'Poubelle fermée avec couvercle', 'X', 1),
(77, 'Accès au 4ème étage sans ascenseur', 'X', 4),
(78, 'Accès au 3ème étage sans ascenseur', 'O', 4),
(79, 'Emplacements de stationnement à proximité', 'X', 4),
(80, 'Emplacements privatifs', 'O', 3),
(81, 'Garage ou abri couvert privatif', 'O', 2),
(82, 'Balcon, loggia ou véranda', 'O', 2),
(83, 'Terrasse ou jardin privé', 'O', 3),
(84, 'Parc ou jardin de grande superficie', 'O', 4),
(85, 'Mobilier de jardin privatif', 'O', 2),
(86, 'Plancha ou barbecue extérieur', 'O', 2),
(87, 'Équipement léger de loisirs', 'O', 2),
(88, 'Équipement aménagé de loisirs', 'O', 2),
(89, 'Piscine', 'O', 2),
(90, 'Piscine chauffée', 'O', 2),
(91, 'Rangement pour équipement sportif', 'O', 1),
(92, 'Vue paysagère', 'O', 2),
(93, 'Accès immédiat à des activités', 'O', 3),
(94, 'Accès immédiat aux commerces et transports', 'O', 3),
(95, 'Sanitaires propres et en bon état', 'X', 5),
(96, 'Sols, murs et plafonds propres', 'X', 5),
(97, 'Mobilier propre et en bon état', 'X', 5),
(98, 'Literie propre et en bon état', 'X', 5),
(99, 'Cuisine propre et équipements en bon état', 'X', 5),
(100, 'Brochures touristiques multilingues', 'X', 3),
(101, 'Livret d\'accueil', 'O', 2),
(102, 'Accueil sur place', 'O', 3),
(103, 'Cadeau de bienvenue', 'O', 2),
(104, 'Boîte à clés ou système équivalent', 'O', 2),
(105, 'Draps fournis systématiquement', 'X', 2),
(106, 'Linge de toilette fourni', 'X', 2),
(107, 'Linge de table', 'O', 2),
(108, 'Lits faits à l\'arrivée', 'O', 2),
(109, 'Matériel pour bébé sur demande', 'O', 2),
(110, 'Service de ménage proposé', 'O', 2),
(111, 'Produits d\'entretien', 'X', 2),
(112, 'Adaptateurs électriques', 'O', 2),
(113, 'Site internet dédié au logement', 'O', 2),
(114, 'Site internet en langue étrangère', 'O', 1),
(115, 'Animaux de compagnie admis', 'O', 2),
(116, 'Informations sur l\'accessibilité', 'X', 2),
(117, 'Télécommande adaptée', 'O', 2),
(118, 'Siège de douche avec barre d\'appui', 'O', 2),
(119, 'WC avec barre d\'appui', 'O', 2),
(120, 'Largeur des portes adaptée', 'O', 2),
(121, 'Document accessible', 'X', 1),
(122, 'Label Tourisme et Handicap', 'O', 3),
(123, 'Mesure de réduction de consommation d\'énergie', 'X', 3),
(124, 'Mesure supplémentaire de réduction d\'énergie', 'O', 1),
(125, 'Borne de recharge pour véhicules électriques', 'O', 2),
(126, 'Mesure de réduction de consommation d\'eau', 'X', 3),
(127, 'Mesure supplémentaire de réduction d\'eau', 'O', 1),
(128, 'Tri des déchets', 'X', 1),
(129, 'Composteur', 'O', 1),
(130, 'Sensibilisation environnementale des clients', 'X', 2),
(131, 'Produits d\'accueil écologiques', 'O', 2),
(132, 'Produits d\'entretien écologiques', 'X', 1),
(133, 'Obtention d\'un label environnemental', 'O', 3);

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

DROP TABLE IF EXISTS `devis`;
CREATE TABLE IF NOT EXISTS `devis` (
  `Devis_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Devis_DateAccepattion` datetime DEFAULT NULL,
  `Devis_montant` decimal(10,2) DEFAULT NULL,
  `Devis_Numero` varchar(50) NOT NULL,
  `Devis_DateEmission` datetime NOT NULL,
  `Devis_Document` varchar(50) DEFAULT NULL,
  `Dossier_ID` int(11) DEFAULT NULL,
  `Devis_Verrouille` tinyint(1) NOT NULL DEFAULT 0,
  `Devis_DateVerrouillage` datetime DEFAULT NULL,
  PRIMARY KEY (`Devis_ID`),
  UNIQUE KEY `unique_devis_numero` (`Devis_Numero`),
  UNIQUE KEY `Dossier_ID` (`Dossier_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `devis`
--

INSERT INTO `devis` (`Devis_ID`, `Devis_DateAccepattion`, `Devis_montant`, `Devis_Numero`, `Devis_DateEmission`, `Devis_Document`, `Dossier_ID`, `Devis_Verrouille`, `Devis_DateVerrouillage`) VALUES
(1, NULL, 480.00, 'D-2026-00001', '2026-01-05 10:00:00', 'DEVIS', 2, 0, NULL),
(2, '2026-01-10 15:22:00', 624.00, 'D-2026-00002', '2026-01-10 09:00:00', 'DEVIS', 1, 1, '2026-01-10 15:25:00'),
(3, NULL, 300.00, 'D-2026-00003', '2026-01-18 11:30:00', 'DEVIS', 4, 0, NULL),
(4, NULL, 220.00, 'D-2026-00004', '2026-01-22 17:45:00', 'DEVIS', 5, 0, NULL),
(5, '2026-01-25 09:05:00', 890.00, 'D-2026-00005', '2026-01-25 08:40:00', 'DEVIS', 6, 1, '2026-01-25 09:06:00'),
(6, NULL, 150.00, 'D-2026-00006', '2026-01-26 14:00:00', 'DEVIS', 8, 0, NULL),
(7, NULL, 168.00, 'D-2026-00084', '2026-01-30 00:00:00', 'DEVIS', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `devis_client`
--

DROP TABLE IF EXISTS `devis_client`;
CREATE TABLE IF NOT EXISTS `devis_client` (
  `Client_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Devis_ID` int(11) NOT NULL,
  `Utilisateur_ID` int(11) DEFAULT NULL,
  `Entreprise_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Client_ID`),
  KEY `Devis_ID` (`Devis_ID`),
  KEY `idx_devis_client_utilisateur` (`Utilisateur_ID`),
  KEY `idx_devis_client_entreprise` (`Entreprise_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `devis_client`
--

INSERT INTO `devis_client` (`Client_ID`, `Devis_ID`, `Utilisateur_ID`, `Entreprise_ID`) VALUES
(1, 1, 6, NULL),
(2, 2, NULL, 2),
(3, 3, 7, NULL),
(4, 4, 8, NULL),
(5, 5, NULL, 3),
(6, 6, 12, NULL),
(7, 7, 1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `devis_items`
--

DROP TABLE IF EXISTS `devis_items`;
CREATE TABLE IF NOT EXISTS `devis_items` (
  `Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Devis_ID` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `tva` decimal(5,2) DEFAULT 20.00,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Item_ID`),
  KEY `Devis_ID` (`Devis_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `devis_items`
--

INSERT INTO `devis_items` (`Item_ID`, `Devis_ID`, `description`, `quantite`, `prix_unitaire`, `tva`, `total`) VALUES
(1, 1, 'Diagnostic conformité hébergement', 1.00, 400.00, 20.00, 480.00),
(2, 2, 'Diagnostic + rapport détaillé', 1.00, 520.00, 20.00, 624.00),
(3, 3, 'Visite de contrôle', 1.00, 250.00, 20.00, 300.00),
(4, 4, 'Audit rapide (1h)', 2.00, 91.67, 20.00, 220.00),
(5, 5, 'Audit complet + recommandations', 1.00, 741.67, 20.00, 890.00),
(6, 6, 'Mise à jour dossier + photos', 1.00, 125.00, 20.00, 150.00),
(7, 7, 'Diagnostics + visite', 1.00, 140.00, 20.00, 168.00);

-- --------------------------------------------------------

--
-- Structure de la table `document_counters`
--

DROP TABLE IF EXISTS `document_counters`;
CREATE TABLE IF NOT EXISTS `document_counters` (
  `type` enum('DEVIS','FACTURE') NOT NULL,
  `year` year(4) NOT NULL,
  `last_number` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`type`,`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `document_counters`
--

INSERT INTO `document_counters` (`type`, `year`, `last_number`) VALUES
('DEVIS', '2026', 328),
('FACTURE', '2026', 6);

-- --------------------------------------------------------

--
-- Structure de la table `donneurordre`
--

DROP TABLE IF EXISTS `donneurordre`;
CREATE TABLE IF NOT EXISTS `donneurordre` (
  `Donneur_ID` int(11) NOT NULL,
  `Societe_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Donneur_ID`),
  KEY `fk_donneurordre_societe` (`Societe_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `donneurordre`
--

INSERT INTO `donneurordre` (`Donneur_ID`, `Societe_ID`) VALUES
(13, 2),
(14, 2),
(15, 4),
(16, 4);

-- --------------------------------------------------------

--
-- Structure de la table `dossiers`
--

DROP TABLE IF EXISTS `dossiers`;
CREATE TABLE IF NOT EXISTS `dossiers` (
  `Dossier_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Dossier_Numero` varchar(50) DEFAULT NULL,
  `Dossier_Date` datetime DEFAULT NULL,
  `Dossier_Etoile_Cible` int(11) DEFAULT NULL,
  `Inspecteur_Id` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `Bien_ID` int(11) DEFAULT NULL,
  `Proprietaire_ID` int(255) NOT NULL,
  `devis_id` int(11) DEFAULT NULL,
  `facture_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`Dossier_ID`),
  KEY `Utilisateur_ID` (`Inspecteur_Id`),
  KEY `fk_dossiers_bien` (`Bien_ID`),
  KEY `FK_Proprietaire_ID` (`Proprietaire_ID`),
  KEY `fk_dossiers_devis` (`devis_id`),
  KEY `fk_dossier_facture` (`facture_id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `dossiers`
--

INSERT INTO `dossiers` (`Dossier_ID`, `Dossier_Numero`, `Dossier_Date`, `Dossier_Etoile_Cible`, `Inspecteur_Id`, `status`, `Bien_ID`, `Proprietaire_ID`, `devis_id`, `facture_id`) VALUES
(1, 'DOS-2025-001', '2025-02-10 14:00:00', 4, 3, 1, 1, 5, NULL, NULL),
(2, 'DOS-2025-002', '2025-02-18 09:30:00', 3, 4, 0, 101, 6, NULL, NULL),
(3, 'DOS-2025-003', '2025-02-25 11:00:00', 2, 3, 1, 102, 9, NULL, NULL),
(4, 'DOS-2025-004', '2025-03-03 16:15:00', 5, 4, 0, 103, 7, NULL, NULL),
(5, 'DOS-2025-005', '2025-03-10 10:00:00', 2, 3, 0, 104, 8, NULL, NULL),
(6, 'DOS-2025-006', '2025-03-18 14:30:00', 3, 4, 1, 105, 10, NULL, NULL),
(7, 'DOS-2025-007', '2025-03-25 09:00:00', 1, 3, 0, 106, 11, NULL, NULL),
(8, 'DOS-2025-008', '2025-04-02 15:00:00', 4, 4, 0, 107, 12, NULL, NULL),
(9, 'DOS-2025-009', '2025-04-12 13:45:00', 2, 3, 1, 108, 6, NULL, NULL),
(10, 'DOS-2025-010', '2025-04-20 09:30:00', 3, 4, 0, 109, 8, NULL, NULL),
(14, 'DOS-2026-001', '2026-02-04 17:14:00', 2, 3, 0, 118, 21, NULL, NULL),
(31, 'DOS-2026-002', '2026-02-06 14:10:11', 2, 3, 0, 135, 21, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `effectue`
--

DROP TABLE IF EXISTS `effectue`;
CREATE TABLE IF NOT EXISTS `effectue` (
  `Utilisateur_ID` int(11) NOT NULL,
  `Evaluation_ID` int(11) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`,`Evaluation_ID`),
  KEY `Evaluation_ID` (`Evaluation_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entreprisefacturation`
--

DROP TABLE IF EXISTS `entreprisefacturation`;
CREATE TABLE IF NOT EXISTS `entreprisefacturation` (
  `Entreprise_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Entreprise_Nom` varchar(100) NOT NULL,
  `Entreprise_Adresse` varchar(255) NOT NULL,
  `Entreprise_CodePostal` varchar(10) NOT NULL,
  `Entreprise_Ville` varchar(100) NOT NULL,
  `Entreprise_Pays` varchar(100) DEFAULT 'France',
  `Entreprise_Email` varchar(100) DEFAULT NULL,
  `Entreprise_Telephone` varchar(20) DEFAULT NULL,
  `Entreprise_SIRET` varchar(20) DEFAULT NULL,
  `Entreprise_TVA_Intra` varchar(30) DEFAULT NULL,
  `Entreprise_Actif` tinyint(1) NOT NULL,
  PRIMARY KEY (`Entreprise_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entreprisefacturation`
--

INSERT INTO `entreprisefacturation` (`Entreprise_ID`, `Entreprise_Nom`, `Entreprise_Adresse`, `Entreprise_CodePostal`, `Entreprise_Ville`, `Entreprise_Pays`, `Entreprise_Email`, `Entreprise_Telephone`, `Entreprise_SIRET`, `Entreprise_TVA_Intra`, `Entreprise_Actif`) VALUES
(1, 'CETIRE', '51 rue du Faubourg de Bourgogne', '45000', 'ORLEANS', 'France', 'factures@cetire.fr', '0238543210', '123 456 789 00012', 'FR76 102 783 725 001', 1),
(2, 'Hôtel Lumière SA', '12 rue des Arts', '75002', 'Paris', 'France', 'compta@hotellumiere.fr', '0140101010', '552 123 999 00018', 'FR55 552123999', 1),
(3, 'Groupe Loire Tourisme', '8 quai du Port', '44000', 'Nantes', 'France', 'accounting@loiretourisme.fr', '0240000000', '321 654 987 00021', 'FR12 321654987', 1),
(4, 'CampingAzur', '2 avenue des Pins', '06000', 'Nice', 'France', 'finance@campingazur.fr', '0493000000', '789 111 222 00034', 'FR98 789111222', 1);

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
CREATE TABLE IF NOT EXISTS `evaluations` (
  `Evaluation_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Critere_ID` int(11) DEFAULT NULL,
  `Value` tinyint(1) DEFAULT NULL,
  `Commentaire` varchar(500) DEFAULT NULL,
  `Dossier_ID` int(11) DEFAULT NULL,
  `Date` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`Evaluation_ID`),
  KEY `Critere_ID` (`Critere_ID`),
  KEY `Dossier_ID` (`Dossier_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=1317 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `evaluations` (`Evaluation_ID`, `Critere_ID`, `Value`, `Commentaire`, `Dossier_ID`, `Date`) VALUES
(1079, 23, 1, '', 5, '2026-02-06'),
(1080, 24, 1, '', 5, '2026-02-06'),
(1081, 25, 1, '', 5, '2026-02-06'),
(1082, 26, 1, '', 5, '2026-02-06'),
(1083, 27, 1, '', 5, '2026-02-06'),
(1084, 28, 1, '', 5, '2026-02-06'),
(1085, 29, 1, '', 5, '2026-02-06'),
(1086, 30, 1, '', 5, '2026-02-06'),
(1087, 31, 1, '', 5, '2026-02-06'),
(1088, 32, 1, '', 5, '2026-02-06'),
(1089, 33, 1, '', 5, '2026-02-06'),
(1090, 34, 1, '', 5, '2026-02-06'),
(1091, 35, 1, '', 5, '2026-02-06'),
(1092, 36, 1, '', 5, '2026-02-06'),
(1093, 37, 1, '', 5, '2026-02-06'),
(1094, 38, 1, '', 5, '2026-02-06'),
(1095, 39, 1, '', 5, '2026-02-06'),
(1096, 40, 1, '', 5, '2026-02-06'),
(1097, 41, 1, '', 5, '2026-02-06'),
(1098, 42, 1, '', 5, '2026-02-06'),
(1099, 46, 1, '', 5, '2026-02-06'),
(1100, 47, 1, '', 5, '2026-02-06'),
(1101, 48, 1, '', 5, '2026-02-06'),
(1102, 49, 1, '', 5, '2026-02-06'),
(1103, 50, 1, '', 5, '2026-02-06'),
(1104, 51, 1, '', 5, '2026-02-06'),
(1105, 52, 1, '', 5, '2026-02-06'),
(1106, 53, 1, '', 5, '2026-02-06'),
(1107, 54, 1, '', 5, '2026-02-06'),
(1108, 55, 1, '', 5, '2026-02-06'),
(1109, 56, 1, '', 5, '2026-02-06'),
(1110, 57, 1, '', 5, '2026-02-06'),
(1111, 58, 1, '', 5, '2026-02-06'),
(1112, 59, 1, '', 5, '2026-02-06'),
(1113, 60, 1, '', 5, '2026-02-06'),
(1114, 61, 1, '', 5, '2026-02-06'),
(1115, 62, 1, '', 5, '2026-02-06'),
(1116, 63, 1, '', 5, '2026-02-06'),
(1117, 64, 1, '', 5, '2026-02-06'),
(1118, 65, 1, '', 5, '2026-02-06'),
(1119, 66, 1, '', 5, '2026-02-06'),
(1120, 67, 1, '', 5, '2026-02-06'),
(1121, 68, 1, '', 5, '2026-02-06'),
(1122, 69, 1, '', 5, '2026-02-06'),
(1123, 70, 1, '', 5, '2026-02-06'),
(1124, 71, 1, '', 5, '2026-02-06'),
(1125, 72, 1, '', 5, '2026-02-06'),
(1126, 73, 1, '', 5, '2026-02-06'),
(1127, 74, 1, '', 5, '2026-02-06'),
(1128, 75, 1, '', 5, '2026-02-06'),
(1129, 76, 1, '', 5, '2026-02-06'),
(1130, 77, 1, '', 5, '2026-02-06'),
(1131, 78, 1, '', 5, '2026-02-06'),
(1132, 79, 1, '', 5, '2026-02-06'),
(1133, 80, 1, '', 5, '2026-02-06'),
(1134, 81, 1, '', 5, '2026-02-06'),
(1135, 82, 1, '', 5, '2026-02-06'),
(1136, 83, 1, '', 5, '2026-02-06'),
(1137, 84, 1, '', 5, '2026-02-06'),
(1138, 85, 1, '', 5, '2026-02-06'),
(1139, 86, 1, '', 5, '2026-02-06'),
(1140, 87, 1, '', 5, '2026-02-06'),
(1141, 88, 1, '', 5, '2026-02-06'),
(1142, 89, 1, '', 5, '2026-02-06'),
(1143, 90, 1, '', 5, '2026-02-06'),
(1144, 91, 1, '', 5, '2026-02-06'),
(1145, 92, 1, '', 5, '2026-02-06'),
(1146, 93, 1, '', 5, '2026-02-06'),
(1147, 94, 1, '', 5, '2026-02-06'),
(1148, 95, 1, '', 5, '2026-02-06'),
(1149, 96, 1, '', 5, '2026-02-06'),
(1150, 97, 1, '', 5, '2026-02-06'),
(1151, 98, 1, '', 5, '2026-02-06'),
(1152, 99, 1, '', 5, '2026-02-06'),
(1153, 100, 1, '', 5, '2026-02-06'),
(1154, 101, 1, '', 5, '2026-02-06'),
(1155, 102, 1, '', 5, '2026-02-06'),
(1156, 103, 1, '', 5, '2026-02-06'),
(1157, 104, 1, '', 5, '2026-02-06'),
(1158, 105, 1, '', 5, '2026-02-06'),
(1159, 106, 1, '', 5, '2026-02-06'),
(1160, 107, 1, '', 5, '2026-02-06'),
(1161, 108, 1, '', 5, '2026-02-06'),
(1162, 109, 1, '', 5, '2026-02-06'),
(1163, 110, 1, '', 5, '2026-02-06'),
(1164, 111, 1, '', 5, '2026-02-06'),
(1165, 112, 1, '', 5, '2026-02-06'),
(1166, 113, 1, '', 5, '2026-02-06'),
(1167, 114, 1, '', 5, '2026-02-06'),
(1168, 115, 1, '', 5, '2026-02-06'),
(1169, 116, 1, '', 5, '2026-02-06'),
(1170, 117, 1, '', 5, '2026-02-06'),
(1171, 118, 1, '', 5, '2026-02-06'),
(1172, 119, 1, '', 5, '2026-02-06'),
(1173, 120, 1, '', 5, '2026-02-06'),
(1174, 121, 1, '', 5, '2026-02-06'),
(1175, 122, 1, '', 5, '2026-02-06'),
(1176, 123, 1, '', 5, '2026-02-06'),
(1177, 124, 1, '', 5, '2026-02-06'),
(1178, 125, 1, '', 5, '2026-02-06'),
(1179, 126, 1, '', 5, '2026-02-06'),
(1180, 127, 1, '', 5, '2026-02-06'),
(1181, 128, 1, '', 5, '2026-02-06'),
(1182, 129, 1, '', 5, '2026-02-06'),
(1183, 130, 1, '', 5, '2026-02-06'),
(1184, 131, 1, '', 5, '2026-02-06'),
(1185, 132, 1, '', 5, '2026-02-06'),
(1186, 133, 1, '', 5, '2026-02-06'),
(1187, 1, 1, 'ca marche en vla', 5, '2026-02-06'),
(1188, 2, 1, '', 5, '2026-02-06'),
(1189, 3, 1, '', 5, '2026-02-06'),
(1190, 4, 1, '', 5, '2026-02-06'),
(1191, 5, 1, '', 5, '2026-02-06'),
(1192, 6, 1, '', 5, '2026-02-06'),
(1193, 7, 1, '', 5, '2026-02-06'),
(1194, 8, 1, '', 5, '2026-02-06'),
(1195, 9, 1, '', 5, '2026-02-06'),
(1196, 10, 1, '', 5, '2026-02-06'),
(1197, 11, 1, '', 5, '2026-02-06'),
(1198, 12, 1, '', 5, '2026-02-06'),
(1199, 13, 1, '', 5, '2026-02-06'),
(1200, 14, 1, '', 5, '2026-02-06'),
(1201, 15, 1, '', 5, '2026-02-06'),
(1202, 16, 1, '', 5, '2026-02-06'),
(1203, 17, 1, '', 5, '2026-02-06'),
(1204, 18, 1, '', 5, '2026-02-06'),
(1205, 19, 1, '', 5, '2026-02-06'),
(1206, 20, 1, '', 5, '2026-02-06'),
(1207, 21, 1, '', 5, '2026-02-06'),
(1208, 22, 1, '', 5, '2026-02-06'),
(1209, 23, 1, '', 5, '2026-02-06'),
(1210, 24, 1, '', 5, '2026-02-06'),
(1211, 25, 1, '', 5, '2026-02-06'),
(1212, 26, 1, '', 5, '2026-02-06'),
(1213, 27, 1, '', 5, '2026-02-06'),
(1214, 28, 1, '', 5, '2026-02-06'),
(1215, 29, 1, '', 5, '2026-02-06'),
(1216, 30, 1, '', 5, '2026-02-06'),
(1217, 31, 1, '', 5, '2026-02-06'),
(1218, 32, 1, '', 5, '2026-02-06'),
(1219, 33, 1, '', 5, '2026-02-06'),
(1220, 34, 1, '', 5, '2026-02-06'),
(1221, 35, 1, '', 5, '2026-02-06'),
(1222, 36, 1, '', 5, '2026-02-06'),
(1223, 37, 1, '', 5, '2026-02-06'),
(1224, 38, 1, '', 5, '2026-02-06'),
(1225, 39, 1, '', 5, '2026-02-06'),
(1226, 40, 1, '', 5, '2026-02-06'),
(1227, 41, 1, '', 5, '2026-02-06'),
(1228, 42, 1, '', 5, '2026-02-06'),
(1229, 46, 1, '', 5, '2026-02-06'),
(1230, 47, 1, '', 5, '2026-02-06'),
(1231, 48, 1, '', 5, '2026-02-06'),
(1232, 49, 1, '', 5, '2026-02-06'),
(1233, 50, 1, '', 5, '2026-02-06'),
(1234, 51, 1, '', 5, '2026-02-06'),
(1235, 52, 1, '', 5, '2026-02-06'),
(1236, 53, 1, '', 5, '2026-02-06'),
(1237, 54, 1, '', 5, '2026-02-06'),
(1238, 55, 1, '', 5, '2026-02-06'),
(1239, 56, 1, '', 5, '2026-02-06'),
(1240, 57, 1, '', 5, '2026-02-06'),
(1241, 58, 1, '', 5, '2026-02-06'),
(1242, 59, 1, '', 5, '2026-02-06'),
(1243, 60, 1, '', 5, '2026-02-06'),
(1244, 61, 1, '', 5, '2026-02-06'),
(1245, 62, 1, '', 5, '2026-02-06'),
(1246, 63, 1, '', 5, '2026-02-06'),
(1247, 64, 1, '', 5, '2026-02-06'),
(1248, 65, 1, '', 5, '2026-02-06'),
(1249, 66, 1, '', 5, '2026-02-06'),
(1250, 67, 1, '', 5, '2026-02-06'),
(1251, 68, 1, '', 5, '2026-02-06'),
(1252, 69, 1, '', 5, '2026-02-06'),
(1253, 70, 1, '', 5, '2026-02-06'),
(1254, 71, 1, '', 5, '2026-02-06'),
(1255, 72, 1, '', 5, '2026-02-06'),
(1256, 73, 1, '', 5, '2026-02-06'),
(1257, 74, 1, '', 5, '2026-02-06'),
(1258, 75, 1, '', 5, '2026-02-06'),
(1259, 76, 1, '', 5, '2026-02-06'),
(1260, 77, 1, '', 5, '2026-02-06'),
(1261, 78, 1, '', 5, '2026-02-06'),
(1262, 79, 1, '', 5, '2026-02-06'),
(1263, 80, 1, '', 5, '2026-02-06'),
(1264, 81, 1, '', 5, '2026-02-06'),
(1265, 82, 1, '', 5, '2026-02-06'),
(1266, 83, 1, '', 5, '2026-02-06'),
(1267, 84, 1, '', 5, '2026-02-06'),
(1268, 85, 1, '', 5, '2026-02-06'),
(1269, 86, 1, '', 5, '2026-02-06'),
(1270, 87, 1, '', 5, '2026-02-06'),
(1271, 88, 1, '', 5, '2026-02-06'),
(1272, 89, 1, '', 5, '2026-02-06'),
(1273, 90, 1, '', 5, '2026-02-06'),
(1274, 91, 1, '', 5, '2026-02-06'),
(1275, 92, 1, '', 5, '2026-02-06'),
(1276, 93, 1, '', 5, '2026-02-06'),
(1277, 94, 1, '', 5, '2026-02-06'),
(1278, 95, 1, '', 5, '2026-02-06'),
(1279, 96, 1, '', 5, '2026-02-06'),
(1280, 97, 1, '', 5, '2026-02-06'),
(1281, 98, 1, '', 5, '2026-02-06'),
(1282, 99, 1, '', 5, '2026-02-06'),
(1283, 100, 1, '', 5, '2026-02-06'),
(1284, 101, 1, '', 5, '2026-02-06'),
(1285, 102, 1, '', 5, '2026-02-06'),
(1286, 103, 1, '', 5, '2026-02-06'),
(1287, 104, 1, '', 5, '2026-02-06'),
(1288, 105, 1, '', 5, '2026-02-06'),
(1289, 106, 1, '', 5, '2026-02-06'),
(1290, 107, 1, '', 5, '2026-02-06'),
(1291, 108, 1, '', 5, '2026-02-06'),
(1292, 109, 1, '', 5, '2026-02-06'),
(1293, 110, 1, '', 5, '2026-02-06'),
(1294, 111, 1, '', 5, '2026-02-06'),
(1295, 112, 1, '', 5, '2026-02-06'),
(1296, 113, 1, '', 5, '2026-02-06'),
(1297, 114, 1, '', 5, '2026-02-06'),
(1298, 115, 1, '', 5, '2026-02-06'),
(1299, 116, 1, '', 5, '2026-02-06'),
(1300, 117, 1, '', 5, '2026-02-06'),
(1301, 118, 1, '', 5, '2026-02-06'),
(1302, 119, 1, '', 5, '2026-02-06'),
(1303, 120, 1, '', 5, '2026-02-06'),
(1304, 121, 1, '', 5, '2026-02-06'),
(1305, 122, 1, '', 5, '2026-02-06'),
(1306, 123, 1, '', 5, '2026-02-06'),
(1307, 124, 1, '', 5, '2026-02-06'),
(1308, 125, 1, '', 5, '2026-02-06'),
(1309, 126, 1, '', 5, '2026-02-06'),
(1310, 127, 1, '', 5, '2026-02-06'),
(1311, 128, 1, '', 5, '2026-02-06'),
(1312, 129, 1, '', 5, '2026-02-06'),
(1313, 130, 1, '', 5, '2026-02-06'),
(1314, 131, 1, '', 5, '2026-02-06'),
(1315, 132, 1, '', 5, '2026-02-06'),
(1316, 133, 1, '', 5, '2026-02-06');

-- --------------------------------------------------------

--
-- Structure de la table `factures_prixtotal`
--

DROP TABLE IF EXISTS `factures_prixtotal`;
CREATE TABLE IF NOT EXISTS `factures_prixtotal` (
  `Facture_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Facture_Numero` varchar(50) NOT NULL,
  `Facture_DateCreation` datetime NOT NULL,
  `Facture_DatePayee` datetime DEFAULT NULL,
  `Facture_Document` varchar(50) DEFAULT NULL,
  `Devis_ID` int(11) NOT NULL,
  `Facture_Montant` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`Facture_ID`),
  UNIQUE KEY `Devis_ID` (`Devis_ID`),
  UNIQUE KEY `unique_facture_numero` (`Facture_Numero`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `factures_prixtotal`
--

INSERT INTO `factures_prixtotal` (`Facture_ID`, `Facture_Numero`, `Facture_DateCreation`, `Facture_DatePayee`, `Facture_Document`, `Devis_ID`, `Facture_Montant`) VALUES
(1, 'F-2026-00005', '2026-01-10 15:29:54', NULL, 'FACTURE', 2, 624.00),
(2, 'F-2026-00006', '2026-01-25 09:10:00', '2026-01-28 18:30:00', 'FACTURE', 5, 890.00);

-- --------------------------------------------------------

--
-- Structure de la table `facture_client`
--

DROP TABLE IF EXISTS `facture_client`;
CREATE TABLE IF NOT EXISTS `facture_client` (
  `Client_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Facture_ID` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `adresse` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `Utilisateur_ID` int(11) DEFAULT NULL,
  `Entreprise_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Client_ID`),
  KEY `Facture_ID` (`Facture_ID`),
  KEY `fk_facture_client_user_idx` (`Utilisateur_ID`),
  KEY `idx_facture_client_entreprise` (`Entreprise_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `facture_client`
--

INSERT INTO `facture_client` (`Client_ID`, `Facture_ID`, `nom`, `adresse`, `email`, `telephone`, `Utilisateur_ID`, `Entreprise_ID`) VALUES
(1, 1, 'Hôtel Lumière SA', '12 rue des Arts, 75002 Paris', 'compta@hotellumiere.fr', '0140101010', NULL, 2),
(2, 2, 'Groupe Loire Tourisme', '8 quai du Port, 44000 Nantes', 'accounting@loiretourisme.fr', '0240000000', NULL, 3);

-- --------------------------------------------------------

--
-- Structure de la table `facture_items`
--

DROP TABLE IF EXISTS `facture_items`;
CREATE TABLE IF NOT EXISTS `facture_items` (
  `Item_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Facture_ID` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `quantite` decimal(10,2) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `tva` decimal(5,2) DEFAULT 20.00,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`Item_ID`),
  KEY `Facture_ID` (`Facture_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `facture_items`
--

INSERT INTO `facture_items` (`Item_ID`, `Facture_ID`, `description`, `quantite`, `prix_unitaire`, `tva`, `total`) VALUES
(1, 1, 'Diagnostic + rapport détaillé', 1.00, 520.00, 20.00, 624.00),
(2, 2, 'Audit complet + recommandations', 1.00, 741.67, 20.00, 890.00);

-- --------------------------------------------------------

--
-- Structure de la table `inspecteurs`
--

DROP TABLE IF EXISTS `inspecteurs`;
CREATE TABLE IF NOT EXISTS `inspecteurs` (
  `Utilisateur_ID` int(11) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `inspecteurs`
--

INSERT INTO `inspecteurs` (`Utilisateur_ID`) VALUES
(3),
(4),
(25);

-- --------------------------------------------------------

--
-- Structure de la table `listescriteres`
--

DROP TABLE IF EXISTS `listescriteres`;
CREATE TABLE IF NOT EXISTS `listescriteres` (
  `ListesCriteres_ID` int(11) NOT NULL,
  PRIMARY KEY (`ListesCriteres_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `listescriteres`
--

INSERT INTO `listescriteres` (`ListesCriteres_ID`) VALUES
(1),
(2),
(3),
(4),
(5);

-- --------------------------------------------------------

--
-- Structure de la table `listescriteres_etoiles`
--

DROP TABLE IF EXISTS `listescriteres_etoiles`;
CREATE TABLE IF NOT EXISTS `listescriteres_etoiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ListesCriteres_ID` int(11) NOT NULL,
  `etoile` int(11) NOT NULL,
  `type_hebergement_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_liste_etoile` (`ListesCriteres_ID`,`etoile`),
  KEY `type_hebergement_id` (`type_hebergement_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `listescriteres_etoiles`
--

INSERT INTO `listescriteres_etoiles` (`id`, `ListesCriteres_ID`, `etoile`, `type_hebergement_id`) VALUES
(1, 1, 1, 1),
(2, 2, 2, 1),
(3, 3, 3, 1),
(4, 4, 4, 1),
(5, 5, 5, 1),
(6, 6, 1, 2),
(7, 7, 2, 2),
(8, 8, 3, 2),
(9, 9, 1, 3),
(10, 10, 2, 3),
(11, 2, 1, 2),
(12, 3, 1, 3),
(13, 4, 2, 1),
(14, 5, 2, 2),
(15, 11, 4, 2),
(16, 12, 5, 2);

-- --------------------------------------------------------

--
-- Structure de la table `old_passwords`
--

DROP TABLE IF EXISTS `old_passwords`;
CREATE TABLE IF NOT EXISTS `old_passwords` (
  `utilisateur_id` int(11) DEFAULT NULL,
  `password_hash` varchar(200) DEFAULT NULL,
  `date_password` datetime DEFAULT NULL,
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `old_passwords`
--

INSERT INTO `old_passwords` (`utilisateur_id`, `password_hash`, `date_password`) VALUES
(1, '$2y$10$GpaE9s093seFbknKgKLUi.SGvtdM3klLlGljuVyWOX8Maog/jPft6', '2026-02-06 14:23:37'),
(1, '$2y$10$GpaE9s093seFbknKgKLUi.SGvtdM3klLlGljuVyWOX8Maog/jPft6', '2026-02-06 14:23:39'),
(1, '$2y$10$GpaE9s093seFbknKgKLUi.SGvtdM3klLlGljuVyWOX8Maog/jPft6', '2026-02-06 14:23:40'),
(3, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:23:45'),
(3, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:23:46'),
(3, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:23:49'),
(3, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:23:51'),
(3, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:23:53'),
(5, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:24:09'),
(5, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:24:12'),
(5, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:24:16'),
(5, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:24:19'),
(5, '\"$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu\"', '2026-02-06 14:24:22'),
(1, '$2y$10$.7BSnrykdGe1OVTLpeVfe.ydwPrvv5xfdiyo1zUqqsZ2ozscdMMiK', '2026-02-06 16:34:47'),
(1, '$2y$10$iTj.NkihSKsl8g8Lp5YVQOpTMG/Re.LBHlrD/sndArR4n/otleT8a', '2026-02-06 16:35:45');

-- --------------------------------------------------------

--
-- Structure de la table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `Photo_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Photo_Lien` varchar(350) DEFAULT NULL,
  `Bien_ID` int(11) NOT NULL,
  PRIMARY KEY (`Photo_ID`),
  KEY `Bien_ID` (`Bien_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `photos`
--

INSERT INTO `photos` (`Photo_ID`, `Photo_Lien`, `Bien_ID`) VALUES
(2, './img/hotel_lumiere_2.jpg', 1),
(100, './img/hotel_lumiere_3.jpg', 1),
(105, 'C:\\wamp64\\www\\CheckMyStars/assets/img/1770391905_chambre_1.jpg', 135);

-- --------------------------------------------------------

--
-- Structure de la table `proprietaires`
--

DROP TABLE IF EXISTS `proprietaires`;
CREATE TABLE IF NOT EXISTS `proprietaires` (
  `Utilisateur_ID` int(11) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `proprietaires`
--

INSERT INTO `proprietaires` (`Utilisateur_ID`) VALUES
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12),
(21);

-- --------------------------------------------------------

--
-- Structure de la table `societes`
--

DROP TABLE IF EXISTS `societes`;
CREATE TABLE IF NOT EXISTS `societes` (
  `Societe_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Societe_Nom` varchar(150) NOT NULL,
  `Societe_Mail` varchar(255) DEFAULT NULL,
  `Societe_Telephone` varchar(255) DEFAULT NULL,
  `AdressePostale_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Societe_ID`),
  UNIQUE KEY `uq_societe_nom` (`Societe_Nom`),
  KEY `Societe_Mail` (`Societe_Mail`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `societes`
--

INSERT INTO `societes` (`Societe_ID`, `Societe_Nom`, `Societe_Mail`, `Societe_Telephone`, `AdressePostale_ID`) VALUES
(1, 'CheckMyStars HQ', 'contact@checkmystars.fr', '0238000000', 3),
(2, 'SAS AuditHôtel', 'contact@audithotel.fr', '0142000000', 1),
(3, 'Orléans Inspection', 'orleans@inspection.fr', '0238543210', 11),
(4, 'DedSec Conseil', 'hello@dedsec.fr', '0974001122', 2),
(5, 'Gîtes de Loire', 'resa@gitesloire.fr', '0238123456', 12),
(6, 'Camping & Co', 'support@campingco.fr', '0556008899', 4),
(12, 'Les gites martinant', 'temartinant@stpbb.org', '0781014861', 71);

-- --------------------------------------------------------

--
-- Structure de la table `typeshebergements`
--

DROP TABLE IF EXISTS `typeshebergements`;
CREATE TABLE IF NOT EXISTS `typeshebergements` (
  `TypeHebergement_ID` int(11) NOT NULL,
  `TypeHebergement_Nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`TypeHebergement_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `typeshebergements`
--

INSERT INTO `typeshebergements` (`TypeHebergement_ID`, `TypeHebergement_Nom`) VALUES
(1, 'Hotel'),
(2, 'Gite'),
(3, 'Camping');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `Utilisateur_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Utilisateur_Nom` varchar(50) NOT NULL,
  `Utilisateur_Prenom` varchar(50) DEFAULT NULL,
  `Utilisateur_Civilite` enum('Monsieur','Madame','Iel') DEFAULT NULL,
  `Utilisateur_Password` varchar(2048) DEFAULT NULL,
  `Utilisateur_Mail` varchar(250) NOT NULL,
  `Utilisateur_Telephone` varchar(50) DEFAULT NULL,
  `Utilisateur_Signature` varchar(255) DEFAULT NULL,
  `AdressePostale_ID` int(11) NOT NULL,
  `Societe_ID` int(11) DEFAULT NULL,
  `first_log` tinyint(1) NOT NULL DEFAULT 1,
  `theme` enum('light','dark') NOT NULL DEFAULT 'light',
  PRIMARY KEY (`Utilisateur_ID`),
  UNIQUE KEY `unique_email` (`Utilisateur_Mail`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`),
  KEY `fk_utilisateurs_societe` (`Societe_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`Utilisateur_ID`, `Utilisateur_Nom`, `Utilisateur_Prenom`, `Utilisateur_Civilite`, `Utilisateur_Password`, `Utilisateur_Mail`, `Utilisateur_Telephone`, `Utilisateur_Signature`, `AdressePostale_ID`, `Societe_ID`, `first_log`, `theme`) VALUES
(1, 'Dupont', 'Marie', 'Madame', '$2y$10$iTj.NkihSKsl8g8Lp5YVQOpTMG/Re.LBHlrD/sndArR4n/otleT8a', 'marie.dupont@checkmystars.fr', '0669696969', 'Marie Dupont – Administratrice', 16, 5, 0, 'dark'),
(3, 'Martin', 'Luc', 'Monsieur', '$2y$10$zCMi/F3mcnfmOS80JOgGnONsms3wQamqOQIvzRw33e7RD5ElakiUW', 'luc.martin@inspection.fr', '0600000103', 'Luc Martin – Inspecteur', 17, 3, 0, 'light'),
(4, 'Bernard', 'Julie', 'Madame', '$2y$10$demoHashInsp2', 'julie.bernard@inspection.fr', '0600000104', 'Julie Bernard – Inspectrice', 18, 3, 0, 'light'),
(5, 'Bourdon', 'Angel', 'Monsieur', '$2y$10$demoHashProp1', 'angel.bourdon@gmail.com', '0670000005', NULL, 11, 5, 0, 'light'),
(6, 'Paster', 'Michael', 'Monsieur', '$2y$10$demoHashProp2', 'michael.paster@gmail.com', '0670000006', NULL, 12, 5, 0, 'light'),
(7, 'Lefevre', 'Camille', 'Madame', '$2y$10$demoHashProp3', 'camille.lefevre@gmail.com', '0670000007', NULL, 13, 5, 0, 'light'),
(8, 'Moreau', 'Nina', 'Madame', '$2y$10$demoHashProp4', 'nina.moreau@gmail.com', '0670000008', NULL, 14, 5, 0, 'light'),
(9, 'Petit', 'Hugo', 'Monsieur', '$2y$10$demoHashProp5', 'hugo.petit@gmail.com', '0670000009', NULL, 15, 6, 0, 'light'),
(10, 'Roux', 'Sarah', 'Madame', '$2y$10$demoHashProp6', 'sarah.roux@gmail.com', '0670000010', NULL, 20, 6, 0, 'light'),
(11, 'Garcia', 'Enzo', 'Monsieur', '$2y$10$demoHashProp7', 'enzo.garcia@gmail.com', '0670000011', NULL, 6, 6, 0, 'light'),
(12, 'Fournier', 'Lina', 'Madame', '$2y$10$demoHashProp8', 'lina.fournier@gmail.com', '0670000012', NULL, 5, 5, 0, 'light'),
(13, 'Durand', 'Paul', 'Monsieur', '$2y$10$demoHashDO1', 'paul.durand@audithotel.fr', '0611000013', 'Paul Durand – Donneur d’ordre', 1, 2, 0, 'light'),
(14, 'Robert', 'Chloé', 'Madame', '$2y$10$demoHashDO2', 'chloe.robert@audithotel.fr', '0611000014', 'Chloé Robert – Donneur d’ordre', 2, 2, 0, 'light'),
(15, 'Faure', 'Thomas', 'Monsieur', '$2y$10$demoHashDO3', 'thomas.faure@dedsec.fr', '0611000015', 'Thomas Faure – Donneur d’ordre', 4, 4, 0, 'light'),
(16, 'Masson', 'Emma', 'Madame', '$2y$10$demoHashDO4', 'emma.masson@dedsec.fr', '0611000016', 'Emma Masson – Donneur d’ordre', 3, 4, 0, 'light'),
(21, 'Bourdon', 'Eric', 'Monsieur', '$2y$10$S.c3TxlDpszFVXJRd/gEK.BzCpyH2deyvC6/tQrABtJOyFp3RmvMC', 'Eric@gmail.com', '0769155622', NULL, 40, 1, 1, 'light'),
(23, 'Jean', 'Clanche', 'Iel', '$2y$10$XxyKFhn57EEmASJeuPpwpu4qV9NYb940egj9NKQ/A79BgfCiqmggy', 'jean@clanche.ez', '0633333333', NULL, 70, 4, 0, 'light'),
(24, 'Martinant', 'Térence', 'Monsieur', '$2y$10$epSrvw0GuhgQdFT3rtvWLOxDtq2Wo2CAJyRSkXlSy6VZ2sJBmbxHG', 'temartinant@stpbb.org', '0781014861', NULL, 72, 12, 0, 'dark'),
(25, 'John', 'Cena', 'Monsieur', '$2y$10$yAPCIQvswzz062HyWIa9guAAuJWYxS23WLIY18kIdd0fRd1q6TlO6', 'john@mail.com', '0638418795', NULL, 73, 12, 0, 'dark');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD CONSTRAINT `administrateurs_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`),
  ADD CONSTRAINT `fk_administrateurs_user` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `administre`
--
ALTER TABLE `administre`
  ADD CONSTRAINT `administre_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `administrateurs` (`Utilisateur_ID`),
  ADD CONSTRAINT `administre_ibfk_2` FOREIGN KEY (`Critere_ID`) REFERENCES `criteres` (`Critere_ID`),
  ADD CONSTRAINT `fk_administre_user` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `biens`
--
ALTER TABLE `biens`
  ADD CONSTRAINT `biens_ibfk_1` FOREIGN KEY (`Donneur_ID`) REFERENCES `donneurordre` (`Donneur_ID`),
  ADD CONSTRAINT `biens_ibfk_2` FOREIGN KEY (`AdressePostale_ID`) REFERENCES `adressespostales` (`AdressePostale_ID`),
  ADD CONSTRAINT `biens_ibfk_3` FOREIGN KEY (`TypeHebergement_ID`) REFERENCES `typeshebergements` (`TypeHebergement_ID`),
  ADD CONSTRAINT `biens_ibfk_4` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `proprietaires` (`Utilisateur_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_biens_donneur` FOREIGN KEY (`Donneur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_biens_utilisateur` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `concerne`
--
ALTER TABLE `concerne`
  ADD CONSTRAINT `concerne_ibfk_1` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `concerne_ibfk_2` FOREIGN KEY (`Dossier_ID`) REFERENCES `dossiers` (`Dossier_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `contient`
--
ALTER TABLE `contient`
  ADD CONSTRAINT `contient_ibfk_1` FOREIGN KEY (`Critere_ID`) REFERENCES `criteres` (`Critere_ID`),
  ADD CONSTRAINT `contient_ibfk_2` FOREIGN KEY (`Photo_ID`) REFERENCES `photos` (`Photo_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `contient_ibfk_3` FOREIGN KEY (`ListesCriteres_ID`) REFERENCES `listescriteres` (`ListesCriteres_ID`);

--
-- Contraintes pour la table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `devis_ibfk_1` FOREIGN KEY (`Dossier_ID`) REFERENCES `dossiers` (`Dossier_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `devis_client`
--
ALTER TABLE `devis_client`
  ADD CONSTRAINT `devis_client_ibfk_1` FOREIGN KEY (`Devis_ID`) REFERENCES `devis` (`Devis_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_devis_client_entreprise` FOREIGN KEY (`Entreprise_ID`) REFERENCES `entreprisefacturation` (`Entreprise_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_devis_client_user` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE SET NULL;

--
-- Contraintes pour la table `devis_items`
--
ALTER TABLE `devis_items`
  ADD CONSTRAINT `devis_items_ibfk_1` FOREIGN KEY (`Devis_ID`) REFERENCES `devis` (`Devis_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `donneurordre`
--
ALTER TABLE `donneurordre`
  ADD CONSTRAINT `donneurordre_ibfk_1` FOREIGN KEY (`Donneur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`),
  ADD CONSTRAINT `fk_donneurordre_societe` FOREIGN KEY (`Societe_ID`) REFERENCES `societes` (`Societe_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donneurordre_user` FOREIGN KEY (`Donneur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dossiers`
--
ALTER TABLE `dossiers`
  ADD CONSTRAINT `FK_Proprietaire_ID` FOREIGN KEY (`Proprietaire_ID`) REFERENCES `proprietaires` (`Utilisateur_ID`),
  ADD CONSTRAINT `dossiers_ibfk_1` FOREIGN KEY (`Inspecteur_Id`) REFERENCES `inspecteurs` (`Utilisateur_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dossier_facture` FOREIGN KEY (`facture_id`) REFERENCES `factures_prixtotal` (`Facture_ID`),
  ADD CONSTRAINT `fk_dossiers_bien` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dossiers_devis` FOREIGN KEY (`devis_id`) REFERENCES `devis` (`Devis_ID`),
  ADD CONSTRAINT `fk_dossiers_inspecteur` FOREIGN KEY (`Inspecteur_Id`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dossiers_proprietaire` FOREIGN KEY (`Proprietaire_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `effectue`
--
ALTER TABLE `effectue`
  ADD CONSTRAINT `effectue_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `inspecteurs` (`Utilisateur_ID`),
  ADD CONSTRAINT `effectue_ibfk_2` FOREIGN KEY (`Evaluation_ID`) REFERENCES `evaluations` (`Evaluation_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_effectue_user` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`Critere_ID`) REFERENCES `criteres` (`Critere_ID`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`Dossier_ID`) REFERENCES `dossiers` (`Dossier_ID`);

--
-- Contraintes pour la table `factures_prixtotal`
--
ALTER TABLE `factures_prixtotal`
  ADD CONSTRAINT `factures_prixtotal_ibfk_1` FOREIGN KEY (`Devis_ID`) REFERENCES `devis` (`Devis_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture_client`
--
ALTER TABLE `facture_client`
  ADD CONSTRAINT `facture_client_ibfk_1` FOREIGN KEY (`Facture_ID`) REFERENCES `factures_prixtotal` (`Facture_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_facture_client_user` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE SET NULL;

--
-- Contraintes pour la table `facture_items`
--
ALTER TABLE `facture_items`
  ADD CONSTRAINT `facture_items_ibfk_1` FOREIGN KEY (`Facture_ID`) REFERENCES `factures_prixtotal` (`Facture_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inspecteurs`
--
ALTER TABLE `inspecteurs`
  ADD CONSTRAINT `fk_inspecteurs_user` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `inspecteurs_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `old_passwords`
--
ALTER TABLE `old_passwords`
  ADD CONSTRAINT `old_passwords_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`Utilisateur_ID`);

--
-- Contraintes pour la table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `proprietaires`
--
ALTER TABLE `proprietaires`
  ADD CONSTRAINT `fk_proprietaires_user` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `proprietaires_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `societes`
--
ALTER TABLE `societes`
  ADD CONSTRAINT `societes_ibfk_1` FOREIGN KEY (`AdressePostale_ID`) REFERENCES `adressespostales` (`AdressePostale_ID`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `fk_utilisateurs_societe` FOREIGN KEY (`Societe_ID`) REFERENCES `societes` (`Societe_ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`AdressePostale_ID`) REFERENCES `adressespostales` (`AdressePostale_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
