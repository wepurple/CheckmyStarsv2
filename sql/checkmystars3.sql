-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : ven. 30 jan. 2026 à 12:35
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
    
END$$

DROP PROCEDURE IF EXISTS `Create_User`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Create_User` (IN `NumRue` VARCHAR(50), IN `NomRue` VARCHAR(100), IN `Comp` VARCHAR(20), IN `CP` VARCHAR(10), IN `Ville` VARCHAR(50), IN `Pays` VARCHAR(50), IN `Nom` VARCHAR(50), IN `Prenom` VARCHAR(50), IN `Civilite` ENUM('Monsieur','Madame','Iel'), IN `MDP` VARCHAR(255), IN `Mail` VARCHAR(100), IN `Telephone` VARCHAR(50), IN `Signature` VARCHAR(100), IN `Societe` INT(100), IN `Role` INT)   BEGIN
    DECLARE v_adresse_id INT;
    DECLARE v_utilisateur_id INT;

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

DROP PROCEDURE IF EXISTS `Update_Password`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Update_Password` (IN `identifiant` INT, IN `mdp` VARCHAR(255))   update utilisateurs set utilisateur_password = mdp where utilisateur_id = identifiant$$

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
(1);

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
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(11, '14', 'bis', '45100', 'Rue des Bruyères', 'Orléans', 'France'),
(12, '3', NULL, '45100', 'Rue du Portereau', 'Orléans', 'France'),
(13, '88', NULL, '37000', 'Rue Nationale', 'Tours', 'France'),
(14, '21', 'appt 12', '54000', 'Rue Saint-Dizier', 'Nancy', 'France'),
(15, '6', NULL, '44000', 'Rue de Strasbourg', 'Nantes', 'France'),
(16, '39', '', '75011', 'Rue Oberkampf', 'Paris', 'France'),
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
(31, '14', NULL, '41600', 'Rue des Bruyères', 'Chaon', 'France'),
(33, '14', NULL, '41600', 'Rue des Bruyères', 'Chaon', 'France');

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
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

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
(109, 'H?tel Sanguinaires', '0495006677', '2025-04-02', 3, 13, 30, 1, 8);

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
(2, 2, 4),
(1, 100, 6),
(1, 100, 7),
(1, 100, 8),
(1, 100, 11),
(1, 100, 12),
(2, 100, 6),
(2, 100, 7),
(2, 100, 8),
(2, 100, 11),
(2, 100, 12),
(3, 100, 6),
(3, 100, 7),
(3, 100, 8),
(3, 100, 11),
(3, 100, 12),
(4, 100, 6),
(4, 100, 7),
(4, 100, 8),
(4, 100, 11),
(4, 100, 12),
(5, 100, 6),
(5, 100, 7),
(5, 100, 8),
(5, 100, 11),
(5, 100, 12),
(6, 100, 6),
(6, 100, 7),
(6, 100, 8),
(6, 100, 11),
(6, 100, 12),
(7, 100, 6),
(7, 100, 7),
(7, 100, 8),
(7, 100, 11),
(7, 100, 12),
(8, 100, 6),
(8, 100, 7),
(8, 100, 8),
(8, 100, 11),
(8, 100, 12),
(9, 100, 6),
(9, 100, 7),
(9, 100, 8),
(9, 100, 11),
(9, 100, 12),
(10, 100, 6),
(10, 100, 7),
(10, 100, 8),
(10, 100, 11),
(10, 100, 12),
(11, 100, 6),
(11, 100, 7),
(11, 100, 8),
(11, 100, 11),
(11, 100, 12),
(12, 100, 6),
(12, 100, 7),
(12, 100, 8),
(12, 100, 11),
(12, 100, 12),
(13, 100, 6),
(13, 100, 7),
(13, 100, 8),
(13, 100, 11),
(13, 100, 12),
(14, 100, 6),
(14, 100, 7),
(14, 100, 8),
(14, 100, 11),
(14, 100, 12),
(15, 100, 6),
(15, 100, 7),
(15, 100, 8),
(15, 100, 11),
(15, 100, 12),
(16, 100, 6),
(16, 100, 7),
(16, 100, 8),
(16, 100, 11),
(16, 100, 12),
(17, 100, 6),
(17, 100, 7),
(17, 100, 8),
(17, 100, 11),
(17, 100, 12),
(18, 100, 6),
(18, 100, 7),
(18, 100, 8),
(18, 100, 11),
(18, 100, 12),
(19, 100, 6),
(19, 100, 7),
(19, 100, 8),
(19, 100, 11),
(19, 100, 12),
(20, 100, 6),
(20, 100, 7),
(20, 100, 8),
(20, 100, 11),
(20, 100, 12),
(21, 100, 6),
(21, 100, 7),
(21, 100, 8),
(21, 100, 11),
(21, 100, 12),
(22, 100, 6),
(22, 100, 7),
(22, 100, 8),
(22, 100, 11),
(22, 100, 12),
(23, 100, 6),
(23, 100, 7),
(23, 100, 8),
(23, 100, 11),
(23, 100, 12),
(24, 100, 6),
(24, 100, 7),
(24, 100, 8),
(24, 100, 11),
(24, 100, 12),
(25, 100, 6),
(25, 100, 7),
(25, 100, 8),
(25, 100, 11),
(25, 100, 12),
(26, 100, 6),
(26, 100, 7),
(26, 100, 8),
(26, 100, 11),
(26, 100, 12),
(27, 100, 6),
(27, 100, 7),
(27, 100, 8),
(27, 100, 11),
(27, 100, 12),
(28, 100, 6),
(28, 100, 7),
(28, 100, 8),
(28, 100, 11),
(28, 100, 12),
(29, 100, 6),
(29, 100, 7),
(29, 100, 8),
(29, 100, 11),
(29, 100, 12),
(30, 100, 6),
(30, 100, 7),
(30, 100, 8),
(30, 100, 11),
(30, 100, 12),
(31, 100, 6),
(31, 100, 7),
(31, 100, 8),
(31, 100, 11),
(31, 100, 12),
(32, 100, 6),
(32, 100, 7),
(32, 100, 8),
(32, 100, 11),
(32, 100, 12),
(33, 100, 6),
(33, 100, 7),
(33, 100, 8),
(33, 100, 11),
(33, 100, 12),
(34, 100, 6),
(34, 100, 7),
(34, 100, 8),
(34, 100, 11),
(34, 100, 12),
(35, 100, 6),
(35, 100, 7),
(35, 100, 8),
(35, 100, 11),
(35, 100, 12),
(36, 100, 6),
(36, 100, 7),
(36, 100, 8),
(36, 100, 11),
(36, 100, 12),
(37, 100, 6),
(37, 100, 7),
(37, 100, 8),
(37, 100, 11),
(37, 100, 12),
(38, 100, 6),
(38, 100, 7),
(38, 100, 8),
(38, 100, 11),
(38, 100, 12),
(39, 100, 6),
(39, 100, 7),
(39, 100, 8),
(39, 100, 11),
(39, 100, 12),
(41, 100, 7),
(41, 100, 8),
(41, 100, 11),
(41, 100, 12),
(42, 100, 7),
(42, 100, 8),
(42, 100, 11),
(42, 100, 12),
(43, 100, 7),
(43, 100, 8),
(43, 100, 11),
(43, 100, 12),
(44, 100, 7),
(44, 100, 8),
(44, 100, 11),
(44, 100, 12),
(45, 100, 7),
(45, 100, 8),
(45, 100, 11),
(45, 100, 12),
(46, 100, 7),
(46, 100, 8),
(46, 100, 11),
(46, 100, 12),
(47, 100, 7),
(47, 100, 8),
(47, 100, 11),
(47, 100, 12),
(48, 100, 7),
(48, 100, 8),
(48, 100, 11),
(48, 100, 12),
(49, 100, 7),
(49, 100, 8),
(49, 100, 11),
(49, 100, 12),
(50, 100, 7),
(50, 100, 8),
(50, 100, 11),
(50, 100, 12),
(51, 100, 7),
(51, 100, 8),
(51, 100, 11),
(51, 100, 12),
(52, 100, 7),
(52, 100, 8),
(52, 100, 11),
(52, 100, 12),
(53, 100, 7),
(53, 100, 8),
(53, 100, 11),
(53, 100, 12),
(54, 100, 7),
(54, 100, 8),
(54, 100, 11),
(54, 100, 12),
(55, 100, 7),
(55, 100, 8),
(55, 100, 11),
(55, 100, 12),
(56, 100, 7),
(56, 100, 8),
(56, 100, 11),
(56, 100, 12),
(57, 100, 7),
(57, 100, 8),
(57, 100, 11),
(57, 100, 12),
(58, 100, 7),
(58, 100, 8),
(58, 100, 11),
(58, 100, 12),
(59, 100, 7),
(59, 100, 8),
(59, 100, 11),
(59, 100, 12),
(60, 100, 7),
(60, 100, 8),
(60, 100, 11),
(60, 100, 12),
(61, 100, 7),
(61, 100, 8),
(61, 100, 11),
(61, 100, 12),
(62, 100, 7),
(62, 100, 8),
(62, 100, 11),
(62, 100, 12),
(63, 100, 7),
(63, 100, 8),
(63, 100, 11),
(63, 100, 12),
(64, 100, 7),
(64, 100, 8),
(64, 100, 11),
(64, 100, 12),
(65, 100, 7),
(65, 100, 8),
(65, 100, 11),
(65, 100, 12),
(66, 100, 7),
(66, 100, 8),
(66, 100, 11),
(66, 100, 12),
(67, 100, 7),
(67, 100, 8),
(67, 100, 11),
(67, 100, 12),
(68, 100, 7),
(68, 100, 8),
(68, 100, 11),
(68, 100, 12),
(69, 100, 7),
(69, 100, 8),
(69, 100, 11),
(69, 100, 12),
(70, 100, 7),
(70, 100, 8),
(70, 100, 11),
(70, 100, 12),
(71, 100, 8),
(71, 100, 11),
(71, 100, 12),
(72, 100, 8),
(72, 100, 11),
(72, 100, 12),
(73, 100, 8),
(73, 100, 11),
(73, 100, 12),
(74, 100, 8),
(74, 100, 11),
(74, 100, 12),
(75, 100, 8),
(75, 100, 11),
(75, 100, 12),
(76, 100, 8),
(76, 100, 11),
(76, 100, 12),
(77, 100, 8),
(77, 100, 11),
(77, 100, 12),
(78, 100, 8),
(78, 100, 11),
(78, 100, 12),
(79, 100, 8),
(79, 100, 11),
(79, 100, 12),
(80, 100, 8),
(80, 100, 11),
(80, 100, 12),
(81, 100, 8),
(81, 100, 12),
(82, 100, 8),
(82, 100, 12),
(83, 100, 8),
(83, 100, 12),
(84, 100, 8),
(84, 100, 12),
(85, 100, 8),
(85, 100, 12),
(86, 100, 8),
(86, 100, 12),
(87, 100, 8),
(87, 100, 12),
(88, 100, 8),
(88, 100, 12),
(89, 100, 8),
(89, 100, 12),
(90, 100, 8),
(90, 100, 12),
(91, 100, 8),
(91, 100, 12),
(92, 100, 8),
(92, 100, 12),
(93, 100, 8),
(93, 100, 12),
(94, 100, 8),
(94, 100, 12),
(95, 100, 8),
(95, 100, 12),
(96, 100, 12),
(97, 100, 12),
(98, 100, 12),
(99, 100, 12),
(100, 100, 12),
(101, 100, 12),
(102, 100, 12),
(103, 100, 12),
(104, 100, 12),
(105, 100, 12),
(106, 100, 12),
(107, 100, 12),
(108, 100, 12),
(109, 100, 12),
(110, 100, 12),
(111, 100, 12),
(112, 100, 12),
(113, 100, 12),
(114, 100, 12),
(115, 100, 12),
(116, 100, 12),
(117, 100, 12),
(118, 100, 12),
(119, 100, 12),
(120, 100, 12),
(150, 100, 2),
(150, 100, 6),
(151, 100, 2),
(151, 100, 6);

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
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `criteres`
--

INSERT INTO `criteres` (`Critere_ID`, `Critere_description`, `Critere_statut`, `Critere_points`) VALUES
(1, 'Surface totale minimum (cuisine et coin cuisine compris) du logement meublé hors salle d\'eau et toilettes', 'O', 5),
(2, 'Surface totale majorée', 'O', 2),
(3, 'Prise de courant libre dans chaque pièce d\'habitation', 'X', 1),
(4, 'Tous les éclairages du logement fonctionnent et sont en bon état', 'X', 3),
(5, 'Mise à disposition d\'un téléphone privatif à l\'intérieur du logement', 'O', 1),
(6, 'Accès internet par un réseau local sans fil (WiFi)', 'X', 2),
(7, 'Accès internet filaire avec câble fourni', 'O', 2),
(8, 'Télévision à écran plat avec télécommande', 'X', 2),
(9, 'Accès à des chaînes supplémentaires à l\'offre de la TNT', 'O', 2),
(10, 'Possibilité d\'accéder à au moins deux chaînes internationales', 'O', 1),
(11, 'Radio', 'X', 2),
(12, 'Enceinte connectée', 'O', 1),
(13, 'Mise à disposition d\'un système de lecture de vidéos', 'O', 2),
(14, 'Occultation opaque dans chaque pièce comportant un couchage principal', 'X', 3),
(15, 'Le logement est équipé de double vitrage', 'O', 3),
(16, 'Existence d\'un système de chauffage en état de fonctionnement', 'X', 5),
(17, 'Existence d\'un système de climatisation ou de rafraîchissement d\'air', 'O', 3),
(18, 'Machine à laver le linge pour les logements de 4 personnes et plus', 'NA', 3),
(19, 'Sèche-linge électrique pour les logements de 6 personnes et plus', 'NA', 2),
(20, 'Étendoir ou séchoir à linge à l\'intérieur du logement', 'X', 2),
(21, 'Ustensiles de ménage appropriés au logement', 'X', 3),
(22, 'Fer et table à repasser', 'X', 2),
(23, 'Placards ou éléments de rangement dans le logement', 'NA', 3),
(24, 'Placards ou éléments de rangement dans chaque pièce d\'habitation', 'X', 3),
(25, 'Présence d\'une table et d\'assises correspondant à la capacité d\'accueil', 'X', 4),
(26, 'Présence d\'un canapé ou fauteuil(s) adapté(s)', 'X', 3),
(27, 'Présence d\'une table basse', 'X', 1),
(28, 'Respect des dimensions du ou des lits', 'X', 4),
(29, 'Matelas haute densité ou épaisseur de qualité', 'O', 2),
(30, 'Présence d\'oreillers en quantité suffisante', 'X', 2),
(31, 'Deux couvertures ou une couette par lit', 'X', 2),
(32, 'Matelas et oreillers protégés par alaises ou housses amovibles', 'X', 2),
(33, 'Éclairage en tête de lit par personne avec interrupteur individuel', 'X', 2),
(34, 'Commande de l\'éclairage central près du lit', 'O', 2),
(35, 'Prise de courant libre située près du lit', 'O', 1),
(36, 'Présence d\'une table de chevet par personne', 'X', 2),
(37, 'Salle d\'eau privative intérieure', 'X', 2),
(38, 'Salle d\'eau privative avec accès indépendant', 'X', 3),
(39, 'Salle d\'eau équipée lavabo, douche et/ou baignoire', 'X', 3),
(41, 'WC privatif intérieur au logement', 'X', 2),
(42, 'WC privatif indépendant de la salle d\'eau', 'O', 2),
(43, 'Deuxième salle d\'eau privative', 'NA', 5),
(44, 'Salle d\'eau supplémentaire équipée', 'NA', 3),
(45, 'WC privatif supplémentaire', 'NA', 2),
(46, 'Deux points lumineux dont un sur le lavabo', 'X', 2),
(47, 'Présence de produits d\'accueil', 'X', 3),
(48, 'Prise de courant libre à proximité du miroir', 'X', 2),
(49, 'Patères ou porte-serviettes', 'X', 1),
(50, 'Sèche-serviettes électrique', 'O', 2),
(51, 'Miroir de salle de bain', 'X', 2),
(52, 'Miroir en pied', 'O', 2),
(53, 'Tablette ou étagère proche du miroir', 'X', 2),
(54, 'Espaces de rangement supplémentaires', 'X', 2),
(55, 'Sèche-cheveux électrique', 'X', 1),
(56, 'Évier avec robinet mélangeur ou mitigeur', 'X', 3),
(57, 'Nombre de foyers respectés', 'X', 3),
(58, 'Plaque vitrocéramique, induction ou gaz', 'O', 2),
(59, 'Four ou mini-four', 'X', 3),
(60, 'Four à micro-ondes', 'X', 2),
(61, 'Ventilation ou VMC', 'X', 4),
(62, 'Hotte aspirante', 'O', 2),
(63, 'Quantité suffisante de vaisselle par personne', 'X', 3),
(64, 'Vaisselle supplémentaire par personne', 'O', 1),
(65, 'Équipement minimum pour la préparation des repas', 'X', 3),
(66, 'Au moins deux équipements de petit électroménager', 'X', 2),
(67, 'Autocuiseur, cuit-vapeur ou robot multifonction', 'O', 3),
(68, 'Cafetière', 'X', 2),
(69, 'Machine à expresso', 'O', 2),
(70, 'Bouilloire', 'X', 1),
(71, 'Grille-pain', 'X', 1),
(72, 'Lave-vaisselle à partir de 2 personnes', 'NA', 2),
(73, 'Lave-vaisselle 6 couverts ou plus', 'NA', 2),
(74, 'Réfrigérateur avec compartiment conservateur', 'X', 4),
(75, 'Congélateur ou compartiment congélateur', 'X', 2),
(76, 'Poubelle fermée avec couvercle', 'X', 1),
(77, 'Accès au 4ème étage sans ascenseur', 'NA', 4),
(78, 'Accès au 3ème étage sans ascenseur', 'NA', 4),
(79, 'Emplacements de stationnement à proximité', 'X', 4),
(80, 'Emplacements privatifs', 'X', 3),
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
(95, 'Sanitaires propres et en bon état', 'X ONC', 5),
(96, 'Sols, murs et plafonds propres', 'ONC', 5),
(97, 'Mobilier propre et en bon état', 'ONC', 5),
(98, 'Literie propre et en bon état', 'ONC', 5),
(99, 'Cuisine propre et équipements en bon état', 'ONC', 5),
(100, 'Brochures touristiques multilingues', 'X', 3),
(101, 'Livret d\'accueil', 'X', 2),
(102, 'Accueil sur place', 'X', 3),
(103, 'Cadeau de bienvenue', 'O', 2),
(104, 'Boîte à clés ou système équivalent', 'O', 2),
(105, 'Draps fournis systématiquement', 'X', 2),
(106, 'Linge de toilette fourni', 'X', 2),
(107, 'Linge de table', 'X', 2),
(108, 'Lits faits à l\'arrivée', 'O', 2),
(109, 'Matériel pour bébé sur demande', 'X', 2),
(110, 'Service de ménage proposé', 'X', 2),
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
(133, 'Obtention d\'un label environnemental', 'O', 3),
(148, 'test', 'X', 5),
(150, 'test', 'X', 5),
(151, 'test', 'NA', 8);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `devis`
--

INSERT INTO `devis` (`Devis_ID`, `Devis_DateAccepattion`, `Devis_montant`, `Devis_Numero`, `Devis_DateEmission`, `Devis_Document`, `Dossier_ID`, `Devis_Verrouille`, `Devis_DateVerrouillage`) VALUES
(1, NULL, 480.00, 'D-2026-00001', '2026-01-05 10:00:00', 'DEVIS', 2, 0, NULL),
(2, '2026-01-10 15:22:00', 624.00, 'D-2026-00002', '2026-01-10 09:00:00', 'DEVIS', 1, 1, '2026-01-10 15:25:00'),
(3, NULL, 300.00, 'D-2026-00003', '2026-01-18 11:30:00', 'DEVIS', 4, 0, NULL),
(4, NULL, 220.00, 'D-2026-00004', '2026-01-22 17:45:00', 'DEVIS', 5, 0, NULL),
(5, '2026-01-25 09:05:00', 890.00, 'D-2026-00005', '2026-01-25 08:40:00', 'DEVIS', 6, 1, '2026-01-25 09:06:00'),
(6, NULL, 150.00, 'D-2026-00006', '2026-01-26 14:00:00', 'DEVIS', 8, 0, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `devis_client`
--

INSERT INTO `devis_client` (`Client_ID`, `Devis_ID`, `Utilisateur_ID`, `Entreprise_ID`) VALUES
(1, 1, 6, NULL),
(2, 2, NULL, 2),
(3, 3, 7, NULL),
(4, 4, 8, NULL),
(5, 5, NULL, 3),
(6, 6, 12, NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `devis_items`
--

INSERT INTO `devis_items` (`Item_ID`, `Devis_ID`, `description`, `quantite`, `prix_unitaire`, `tva`, `total`) VALUES
(1, 1, 'Diagnostic conformité hébergement', 1.00, 400.00, 20.00, 480.00),
(2, 2, 'Diagnostic + rapport détaillé', 1.00, 520.00, 20.00, 624.00),
(3, 3, 'Visite de contrôle', 1.00, 250.00, 20.00, 300.00),
(4, 4, 'Audit rapide (1h)', 2.00, 91.67, 20.00, 220.00),
(5, 5, 'Audit complet + recommandations', 1.00, 741.67, 20.00, 890.00),
(6, 6, 'Mise à jour dossier + photos', 1.00, 125.00, 20.00, 150.00);

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
('DEVIS', '2026', 74),
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
  `Nb_Points_X` int(255) NOT NULL,
  `Nb_Points_O` int(255) NOT NULL,
  `Nb_Points_NA` int(255) NOT NULL,
  `Nb_Points_ONC` int(255) NOT NULL,
  PRIMARY KEY (`Dossier_ID`),
  KEY `Utilisateur_ID` (`Inspecteur_Id`),
  KEY `fk_dossiers_bien` (`Bien_ID`),
  KEY `FK_Proprietaire_ID` (`Proprietaire_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `dossiers`
--

INSERT INTO `dossiers` (`Dossier_ID`, `Dossier_Numero`, `Dossier_Date`, `Dossier_Etoile_Cible`, `Inspecteur_Id`, `status`, `Bien_ID`, `Proprietaire_ID`, `Nb_Points_X`, `Nb_Points_O`, `Nb_Points_NA`, `Nb_Points_ONC`) VALUES
(1, 'DOS-2025-001', '2025-02-10 14:00:00', 4, 3, 1, 1, 5, 78, 22, 10, 0),
(2, 'DOS-2025-002', '2025-02-18 09:30:00', 3, 4, 0, 101, 6, 55, 18, 15, 5),
(3, 'DOS-2025-003', '2025-02-25 11:00:00', 2, 3, 1, 102, 9, 44, 15, 22, 0),
(4, 'DOS-2025-004', '2025-03-03 16:15:00', 5, 4, 0, 103, 7, 90, 28, 5, 0),
(5, 'DOS-2025-005', '2025-03-10 10:00:00', 2, 3, 0, 104, 8, 40, 12, 30, 8),
(6, 'DOS-2025-006', '2025-03-18 14:30:00', 3, 4, 1, 105, 10, 60, 20, 12, 0),
(7, 'DOS-2025-007', '2025-03-25 09:00:00', 1, 3, 0, 106, 11, 22, 8, 40, 10),
(8, 'DOS-2025-008', '2025-04-02 15:00:00', 4, 4, 0, 107, 12, 70, 25, 8, 0),
(9, 'DOS-2025-009', '2025-04-12 13:45:00', 2, 3, 1, 108, 6, 35, 14, 25, 0),
(10, 'DOS-2025-010', '2025-04-20 09:30:00', 3, 4, 0, 109, 8, 58, 20, 15, 2);

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
  `Evaluation_ID` int(11) NOT NULL,
  `Evaluation_Date` datetime DEFAULT NULL,
  `Evaluation_Document` varchar(50) DEFAULT NULL,
  `Evaluation_Résultat` varchar(50) DEFAULT NULL,
  `Bien_ID` int(11) NOT NULL,
  `ListesCriteres_ID` int(11) NOT NULL,
  PRIMARY KEY (`Evaluation_ID`),
  KEY `Bien_ID` (`Bien_ID`),
  KEY `ListesCriteres_ID` (`ListesCriteres_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

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
(4);

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
(5),
(6),
(7),
(8),
(9),
(10),
(11),
(12);

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
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

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
-- Structure de la table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `Photo_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Photo_Lien` varchar(350) DEFAULT NULL,
  `Bien_ID` int(11) NOT NULL,
  PRIMARY KEY (`Photo_ID`),
  KEY `Bien_ID` (`Bien_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=105 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `photos`
--

INSERT INTO `photos` (`Photo_ID`, `Photo_Lien`, `Bien_ID`) VALUES
(2, './img/hotel_lumiere_2.jpg', 1),
(100, './img/hotel_lumiere_3.jpg', 1);

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
(12);

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(9, 'NTCSTestModal', 'test@gmail.com', '0769155622', 33);

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
  PRIMARY KEY (`Utilisateur_ID`),
  UNIQUE KEY `unique_email` (`Utilisateur_Mail`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`),
  KEY `fk_utilisateurs_societe` (`Societe_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`Utilisateur_ID`, `Utilisateur_Nom`, `Utilisateur_Prenom`, `Utilisateur_Civilite`, `Utilisateur_Password`, `Utilisateur_Mail`, `Utilisateur_Telephone`, `Utilisateur_Signature`, `AdressePostale_ID`, `Societe_ID`) VALUES
(1, 'Dupont', 'Marie', 'Madame', '$2y$10$fDXKq5jEs7C5FiB5Nd/4FOItFogXRu.lR2fVDl94XcJvSSs6qrCdu', 'marie.dupont@checkmystars.fr', '0669696969', 'Marie Dupont – Administratrice', 16, 5),
(3, 'Martin', 'Luc', 'Monsieur', '$2y$10$demoHashInsp1', 'luc.martin@inspection.fr', '0600000103', 'Luc Martin – Inspecteur', 17, 3),
(4, 'Bernard', 'Julie', 'Madame', '$2y$10$demoHashInsp2', 'julie.bernard@inspection.fr', '0600000104', 'Julie Bernard – Inspectrice', 18, 3),
(5, 'Bourdon', 'Angel', 'Monsieur', '$2y$10$demoHashProp1', 'angel.bourdon@gmail.com', '0670000005', NULL, 11, 5),
(6, 'Paster', 'Michael', 'Monsieur', '$2y$10$demoHashProp2', 'michael.paster@gmail.com', '0670000006', NULL, 12, 5),
(7, 'Lefevre', 'Camille', 'Madame', '$2y$10$demoHashProp3', 'camille.lefevre@gmail.com', '0670000007', NULL, 13, 5),
(8, 'Moreau', 'Nina', 'Madame', '$2y$10$demoHashProp4', 'nina.moreau@gmail.com', '0670000008', NULL, 14, 5),
(9, 'Petit', 'Hugo', 'Monsieur', '$2y$10$demoHashProp5', 'hugo.petit@gmail.com', '0670000009', NULL, 15, 6),
(10, 'Roux', 'Sarah', 'Madame', '$2y$10$demoHashProp6', 'sarah.roux@gmail.com', '0670000010', NULL, 20, 6),
(11, 'Garcia', 'Enzo', 'Monsieur', '$2y$10$demoHashProp7', 'enzo.garcia@gmail.com', '0670000011', NULL, 6, 6),
(12, 'Fournier', 'Lina', 'Madame', '$2y$10$demoHashProp8', 'lina.fournier@gmail.com', '0670000012', NULL, 5, 5),
(13, 'Durand', 'Paul', 'Monsieur', '$2y$10$demoHashDO1', 'paul.durand@audithotel.fr', '0611000013', 'Paul Durand – Donneur d’ordre', 1, 2),
(14, 'Robert', 'Chloé', 'Madame', '$2y$10$demoHashDO2', 'chloe.robert@audithotel.fr', '0611000014', 'Chloé Robert – Donneur d’ordre', 2, 2),
(15, 'Faure', 'Thomas', 'Monsieur', '$2y$10$demoHashDO3', 'thomas.faure@dedsec.fr', '0611000015', 'Thomas Faure – Donneur d’ordre', 4, 4),
(16, 'Masson', 'Emma', 'Madame', '$2y$10$demoHashDO4', 'emma.masson@dedsec.fr', '0611000016', 'Emma Masson – Donneur d’ordre', 3, 4);

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
  ADD CONSTRAINT `fk_dossiers_bien` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`ListesCriteres_ID`) REFERENCES `listescriteres` (`ListesCriteres_ID`);

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
