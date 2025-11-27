-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 27 nov. 2025 à 07:40
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
-- Base de données : `checkmystars2`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateurs`
--

DROP TABLE IF EXISTS `administrateurs`;
CREATE TABLE IF NOT EXISTS `administrateurs` (
  `Utilisateur_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `administre`
--

DROP TABLE IF EXISTS `administre`;
CREATE TABLE IF NOT EXISTS `administre` (
  `Utilisateur_ID` varchar(50) NOT NULL,
  `Critere_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`,`Critere_ID`),
  KEY `Critere_ID` (`Critere_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `adressespostales`
--

DROP TABLE IF EXISTS `adressespostales`;
CREATE TABLE IF NOT EXISTS `adressespostales` (
  `AdressePostale_ID` varchar(50) NOT NULL,
  `AdressePostale_NumeroRue` varchar(50) DEFAULT NULL,
  `AdressePostale_Complement` varchar(50) DEFAULT NULL,
  `AdressePostale_CodePostal` varchar(50) DEFAULT NULL,
  `AdressePostale_NomRue` varchar(256) DEFAULT NULL,
  `AdressePostale_Ville` varchar(256) DEFAULT NULL,
  `AdressePostale_Pays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`AdressePostale_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `biens`
--

DROP TABLE IF EXISTS `biens`;
CREATE TABLE IF NOT EXISTS `biens` (
  `Bien_ID` varchar(50) NOT NULL,
  `Biens_Nom` varchar(50) DEFAULT NULL,
  `Bien_Telephone` varchar(50) DEFAULT NULL,
  `Bien_DateEnregistrement` date DEFAULT NULL,
  `Bien_Etoile_Actuelle` int(11) DEFAULT NULL,
  `Utilisateur_ID` varchar(50) DEFAULT NULL,
  `AdressePostale_ID` varchar(50) NOT NULL,
  `TypeHebergement_ID` varchar(50) NOT NULL,
  `Utilisateur_ID_1` varchar(50) NOT NULL,
  PRIMARY KEY (`Bien_ID`),
  KEY `Utilisateur_ID` (`Utilisateur_ID`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`),
  KEY `TypeHebergement_ID` (`TypeHebergement_ID`),
  KEY `Utilisateur_ID_1` (`Utilisateur_ID_1`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `concerne`
--

DROP TABLE IF EXISTS `concerne`;
CREATE TABLE IF NOT EXISTS `concerne` (
  `Bien_ID` varchar(50) NOT NULL,
  `Dossier_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Bien_ID`,`Dossier_ID`),
  KEY `Dossier_ID` (`Dossier_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `contient`
--

DROP TABLE IF EXISTS `contient`;
CREATE TABLE IF NOT EXISTS `contient` (
  `Critere_ID` varchar(50) NOT NULL,
  `Photo_ID` varchar(50) NOT NULL,
  `ListesCriteres_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Critere_ID`,`Photo_ID`,`ListesCriteres_ID`),
  KEY `Photo_ID` (`Photo_ID`),
  KEY `ListesCriteres_ID` (`ListesCriteres_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `criteres`
--

DROP TABLE IF EXISTS `criteres`;
CREATE TABLE IF NOT EXISTS `criteres` (
  `Critere_ID` varchar(50) NOT NULL,
  `Critere_nom` varchar(50) DEFAULT NULL,
  `Critere_valeur` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Critere_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

DROP TABLE IF EXISTS `devis`;
CREATE TABLE IF NOT EXISTS `devis` (
  `Devis_ID` varchar(50) NOT NULL,
  `Devis_DateAccepattion` datetime DEFAULT NULL,
  `Devis_montant` decimal(10,2) DEFAULT NULL,
  `Devis_Numero` varchar(50) NOT NULL,
  `Devis_DateEmission` datetime NOT NULL,
  `Devis_Document` varchar(50) DEFAULT NULL,
  `Dossier_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Devis_ID`),
  UNIQUE KEY `Dossier_ID` (`Dossier_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `donneurordre`
--

DROP TABLE IF EXISTS `donneurordre`;
CREATE TABLE IF NOT EXISTS `donneurordre` (
  `Utilisateur_ID` varchar(50) NOT NULL,
  `DonneurOrdre_Entreprine_Nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossiers`
--

DROP TABLE IF EXISTS `dossiers`;
CREATE TABLE IF NOT EXISTS `dossiers` (
  `Dossier_ID` varchar(50) NOT NULL,
  `Dossier_Numero` varchar(50) DEFAULT NULL,
  `Dossier_Date` datetime DEFAULT NULL,
  `Dossier_Etoile_Cible` int(11) DEFAULT NULL,
  `Utilisateur_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Dossier_ID`),
  KEY `Utilisateur_ID` (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `effectue`
--

DROP TABLE IF EXISTS `effectue`;
CREATE TABLE IF NOT EXISTS `effectue` (
  `Utilisateur_ID` varchar(50) NOT NULL,
  `Evaluation_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`,`Evaluation_ID`),
  KEY `Evaluation_ID` (`Evaluation_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluations`
--

DROP TABLE IF EXISTS `evaluations`;
CREATE TABLE IF NOT EXISTS `evaluations` (
  `Evaluation_ID` varchar(50) NOT NULL,
  `Evaluation_Date` datetime DEFAULT NULL,
  `Evaluation_Document` varchar(50) DEFAULT NULL,
  `Evaluation_Résultat` varchar(50) DEFAULT NULL,
  `Bien_ID` varchar(50) NOT NULL,
  `ListesCriteres_ID` varchar(50) NOT NULL,
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
  `Facture_ID` varchar(50) NOT NULL,
  `Facture_Numero` varchar(50) NOT NULL,
  `Facture_DateCreation` datetime NOT NULL,
  `Facture_DatePayee` datetime DEFAULT NULL,
  `Facture_Document` varchar(50) DEFAULT NULL,
  `Devis_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Facture_ID`),
  UNIQUE KEY `Devis_ID` (`Devis_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inspecteurs`
--

DROP TABLE IF EXISTS `inspecteurs`;
CREATE TABLE IF NOT EXISTS `inspecteurs` (
  `Utilisateur_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `listescriteres`
--

DROP TABLE IF EXISTS `listescriteres`;
CREATE TABLE IF NOT EXISTS `listescriteres` (
  `ListesCriteres_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`ListesCriteres_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `Photo_ID` varchar(50) NOT NULL,
  `Photo_Lien` varchar(350) DEFAULT NULL,
  `Bien_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Photo_ID`),
  KEY `Bien_ID` (`Bien_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `proprietaires`
--

DROP TABLE IF EXISTS `proprietaires`;
CREATE TABLE IF NOT EXISTS `proprietaires` (
  `Utilisateur_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `typeshebergements`
--

DROP TABLE IF EXISTS `typeshebergements`;
CREATE TABLE IF NOT EXISTS `typeshebergements` (
  `TypeHebergement_ID` varchar(50) NOT NULL,
  `TypeHebergement_Nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`TypeHebergement_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `Utilisateur_ID` varchar(50) NOT NULL,
  `Utilisateur_Nom` varchar(50) NOT NULL,
  `Utilisateur_Prenom` varchar(50) DEFAULT NULL,
  `Utilisateur_Civilite` varchar(50) DEFAULT NULL,
  `Utilisateur_Password` varchar(256) DEFAULT NULL,
  `Utilisateur_Mail` varchar(250) NOT NULL,
  `Utilisateur_Telephone` varchar(50) DEFAULT NULL,
  `Utilisateur_Signature` varchar(255) DEFAULT NULL,
  `AdressePostale_ID` varchar(50) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
