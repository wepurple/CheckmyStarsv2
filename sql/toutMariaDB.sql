-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 27 nov. 2025 à 07:48
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
-- Base de données : `checkmystars`
--
CREATE DATABASE IF NOT EXISTS `checkmystars` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci;
USE `checkmystars`;

-- --------------------------------------------------------

--
-- Structure de la table `dossier`
--

DROP TABLE IF EXISTS `dossier`;
CREATE TABLE IF NOT EXISTS `dossier` (
  `IdDossier` int(11) NOT NULL AUTO_INCREMENT,
  `NumeroDossier` varchar(50) NOT NULL,
  `DateEnregistrement` datetime DEFAULT current_timestamp(),
  `IdHebergement` int(11) NOT NULL,
  `IdInspecteur` int(11) DEFAULT NULL,
  `IdDonneurOrdre` int(11) DEFAULT NULL,
  `IdClient` int(11) NOT NULL,
  PRIMARY KEY (`IdDossier`),
  KEY `IdHebergement` (`IdHebergement`),
  KEY `IdInspecteur` (`IdInspecteur`),
  KEY `IdDonneurOrdre` (`IdDonneurOrdre`),
  KEY `IdClient` (`IdClient`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `dossier`
--

INSERT INTO `dossier` (`IdDossier`, `NumeroDossier`, `DateEnregistrement`, `IdHebergement`, `IdInspecteur`, `IdDonneurOrdre`, `IdClient`) VALUES
(1, 'D2025-001', '2025-11-13 11:17:26', 1, 2, 3, 1),
(2, 'D2025-002', '2025-11-13 11:17:26', 2, 2, 3, 4);

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

DROP TABLE IF EXISTS `facture`;
CREATE TABLE IF NOT EXISTS `facture` (
  `IdFacture` int(11) NOT NULL AUTO_INCREMENT,
  `DateFacture` date DEFAULT curdate(),
  `DateInspection` date DEFAULT NULL,
  `IdDevis` int(11) DEFAULT NULL,
  `Quantite` int(11) DEFAULT 1,
  `PrixHT` decimal(10,2) DEFAULT NULL,
  `TVA` decimal(5,2) DEFAULT NULL,
  `PrixTTC` decimal(10,2) DEFAULT NULL,
  `IdDossier` int(11) NOT NULL,
  PRIMARY KEY (`IdFacture`),
  KEY `IdDossier` (`IdDossier`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `facture`
--

INSERT INTO `facture` (`IdFacture`, `DateFacture`, `DateInspection`, `IdDevis`, `Quantite`, `PrixHT`, `TVA`, `PrixTTC`, `IdDossier`) VALUES
(1, '2025-11-01', '2025-11-02', NULL, 1, 200.00, 20.00, 240.00, 1),
(2, '2025-11-05', '2025-11-06', NULL, 1, 350.00, 20.00, 420.00, 2);

-- --------------------------------------------------------

--
-- Structure de la table `hebergement`
--

DROP TABLE IF EXISTS `hebergement`;
CREATE TABLE IF NOT EXISTS `hebergement` (
  `IdHebergement` int(11) NOT NULL AUTO_INCREMENT,
  `TypeLogement` varchar(50) DEFAULT NULL,
  `Nom` varchar(100) DEFAULT NULL,
  `Adresse` varchar(100) DEFAULT NULL,
  `Complement` varchar(100) DEFAULT NULL,
  `CodePostal` varchar(12) DEFAULT NULL,
  `Ville` varchar(50) DEFAULT NULL,
  `Pays` varchar(50) DEFAULT NULL,
  `DateEnregistrement` datetime DEFAULT current_timestamp(),
  `IdClient` int(11) NOT NULL,
  PRIMARY KEY (`IdHebergement`),
  KEY `IdClient` (`IdClient`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `hebergement`
--

INSERT INTO `hebergement` (`IdHebergement`, `TypeLogement`, `Nom`, `Adresse`, `Complement`, `CodePostal`, `Ville`, `Pays`, `DateEnregistrement`, `IdClient`) VALUES
(1, 'Appartement', 'Appartement Jean Dupont', '12 rue de la Paix', '', '75001', 'Paris', 'France', '2025-11-13 11:17:26', 1),
(2, 'Maison', 'Maison Claire Lefevre', '78 rue Victor Hugo', '', '13001', 'Marseille', 'France', '2025-11-13 11:17:26', 4);

-- --------------------------------------------------------

--
-- Structure de la table `personne`
--

DROP TABLE IF EXISTS `personne`;
CREATE TABLE IF NOT EXISTS `personne` (
  `IdPersonne` int(11) NOT NULL AUTO_INCREMENT,
  `Nom` varchar(50) NOT NULL,
  `Prenom` varchar(25) NOT NULL,
  `Civilite` enum('Monsieur','Madame') DEFAULT NULL,
  `Telephone` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Adresse` varchar(100) DEFAULT NULL,
  `Complement` varchar(100) DEFAULT NULL,
  `CodePostal` varchar(12) DEFAULT NULL,
  `Ville` varchar(50) DEFAULT NULL,
  `Pays` varchar(50) DEFAULT NULL,
  `DateEnregistrement` datetime DEFAULT current_timestamp(),
  `Societe` varchar(100) DEFAULT NULL,
  `Role` enum('Administrateur','Inspecteur','Client','Commanditaire') DEFAULT NULL,
  `Login` varchar(50) DEFAULT NULL,
  `MotPasse` varchar(255) NOT NULL,
  PRIMARY KEY (`IdPersonne`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `Login` (`Login`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Déchargement des données de la table `personne`
--

INSERT INTO `personne` (`IdPersonne`, `Nom`, `Prenom`, `Civilite`, `Telephone`, `Email`, `Adresse`, `Complement`, `CodePostal`, `Ville`, `Pays`, `DateEnregistrement`, `Societe`, `Role`, `Login`, `MotPasse`) VALUES
(1, 'Dupont', 'Jean', 'Monsieur', '0601020304', 'jean.dupont@email.com', '12 rue de la Paix', '', '75001', 'Paris', 'France', '2025-11-13 11:17:26', '', 'Client', 'jdupont', 'mdp123'),
(2, 'Martin', 'Sophie', 'Madame', '0605060708', 'sophie.martin@email.com', '45 avenue des Champs', '', '75008', 'Paris', 'France', '2025-11-13 11:17:26', '', 'Inspecteur', 'smartin', 'mdp123'),
(3, 'Durand', 'Pierre', 'Monsieur', '0612345678', 'pierre.durand@email.com', '3 boulevard Voltaire', '', '69003', 'Lyon', 'France', '2025-11-13 11:17:26', 'ImmoDurand', 'Commanditaire', 'pdurand', 'mdp123'),
(4, 'Lefevre', 'Claire', 'Madame', '0623456789', 'claire.lefevre@email.com', '78 rue Victor Hugo', '', '13001', 'Marseille', 'France', '2025-11-13 11:17:26', '', 'Client', 'clefevre', 'mdp123'),
(9, 'Dupont', 'Jean', 'Monsieur', '0791919191', 'jean.dupont@example.com', 'Adresse', '12', '45000', 'Orléans', 'France', '2025-11-20 10:05:04', 'TerenceInc', 'Client', 'terence2', 'mdp69'),
(12, 'Kylian', 'Terence', 'Monsieur', '0791919191', 'angel.dupont@example.com', '14 rue du blazlal', '12', '45000', 'Orléans', 'France', '2025-11-20 10:07:35', 'TerenceInc', 'Client', 'angel', 'angel'),
(13, 'Kylian', 'Terence', 'Monsieur', '0791919191', 'inspec.dupont@example.com', '14 rue du blazlal', '12', '45000', 'Orléans', 'France', '2025-11-20 11:21:53', 'TerenceInc', 'Inspecteur', 'angel69', 'angel'),
(14, 'Kylian', 'Terence', 'Monsieur', '0791919191', 'inspect.dupont@example.com', '14 rue du blazlal', '12', '45000', 'Orléans', 'France', '2025-11-20 11:32:36', 'TerenceInc', 'Inspecteur', '', ''),
(15, 'Kylian', 'Terence', 'Monsieur', '0791919191', 'aaaaangel.dupont@example.com', '14 rue du blazlal', '12', '45000', 'Orléans', 'France', '2025-11-27 08:37:39', 'TerenceInc', 'Client', 'aaangel', 'angel');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `dossier`
--
ALTER TABLE `dossier`
  ADD CONSTRAINT `dossier_ibfk_1` FOREIGN KEY (`IdHebergement`) REFERENCES `hebergement` (`IdHebergement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dossier_ibfk_2` FOREIGN KEY (`IdInspecteur`) REFERENCES `personne` (`IdPersonne`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dossier_ibfk_3` FOREIGN KEY (`IdDonneurOrdre`) REFERENCES `personne` (`IdPersonne`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `dossier_ibfk_4` FOREIGN KEY (`IdClient`) REFERENCES `personne` (`IdPersonne`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `facture`
--
ALTER TABLE `facture`
  ADD CONSTRAINT `facture_ibfk_1` FOREIGN KEY (`IdDossier`) REFERENCES `dossier` (`IdDossier`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `hebergement`
--
ALTER TABLE `hebergement`
  ADD CONSTRAINT `hebergement_ibfk_1` FOREIGN KEY (`IdClient`) REFERENCES `personne` (`IdPersonne`) ON DELETE CASCADE ON UPDATE CASCADE;
--
-- Base de données : `checkmystars2`
--
CREATE DATABASE IF NOT EXISTS `checkmystars2` DEFAULT CHARACTER SET ascii COLLATE ascii_general_ci;
USE `checkmystars2`;

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

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administrateurs`
--
ALTER TABLE `administrateurs`
  ADD CONSTRAINT `administrateurs_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`);

--
-- Contraintes pour la table `administre`
--
ALTER TABLE `administre`
  ADD CONSTRAINT `administre_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `administrateurs` (`Utilisateur_ID`),
  ADD CONSTRAINT `administre_ibfk_2` FOREIGN KEY (`Critere_ID`) REFERENCES `criteres` (`Critere_ID`);

--
-- Contraintes pour la table `biens`
--
ALTER TABLE `biens`
  ADD CONSTRAINT `biens_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `donneurordre` (`Utilisateur_ID`),
  ADD CONSTRAINT `biens_ibfk_2` FOREIGN KEY (`AdressePostale_ID`) REFERENCES `adressespostales` (`AdressePostale_ID`),
  ADD CONSTRAINT `biens_ibfk_3` FOREIGN KEY (`TypeHebergement_ID`) REFERENCES `typeshebergements` (`TypeHebergement_ID`),
  ADD CONSTRAINT `biens_ibfk_4` FOREIGN KEY (`Utilisateur_ID_1`) REFERENCES `proprietaires` (`Utilisateur_ID`);

--
-- Contraintes pour la table `concerne`
--
ALTER TABLE `concerne`
  ADD CONSTRAINT `concerne_ibfk_1` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`),
  ADD CONSTRAINT `concerne_ibfk_2` FOREIGN KEY (`Dossier_ID`) REFERENCES `dossiers` (`Dossier_ID`);

--
-- Contraintes pour la table `contient`
--
ALTER TABLE `contient`
  ADD CONSTRAINT `contient_ibfk_1` FOREIGN KEY (`Critere_ID`) REFERENCES `criteres` (`Critere_ID`),
  ADD CONSTRAINT `contient_ibfk_2` FOREIGN KEY (`Photo_ID`) REFERENCES `photos` (`Photo_ID`),
  ADD CONSTRAINT `contient_ibfk_3` FOREIGN KEY (`ListesCriteres_ID`) REFERENCES `listescriteres` (`ListesCriteres_ID`);

--
-- Contraintes pour la table `devis`
--
ALTER TABLE `devis`
  ADD CONSTRAINT `devis_ibfk_1` FOREIGN KEY (`Dossier_ID`) REFERENCES `dossiers` (`Dossier_ID`);

--
-- Contraintes pour la table `donneurordre`
--
ALTER TABLE `donneurordre`
  ADD CONSTRAINT `donneurordre_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`);

--
-- Contraintes pour la table `dossiers`
--
ALTER TABLE `dossiers`
  ADD CONSTRAINT `dossiers_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `inspecteurs` (`Utilisateur_ID`);

--
-- Contraintes pour la table `effectue`
--
ALTER TABLE `effectue`
  ADD CONSTRAINT `effectue_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `inspecteurs` (`Utilisateur_ID`),
  ADD CONSTRAINT `effectue_ibfk_2` FOREIGN KEY (`Evaluation_ID`) REFERENCES `evaluations` (`Evaluation_ID`);

--
-- Contraintes pour la table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`ListesCriteres_ID`) REFERENCES `listescriteres` (`ListesCriteres_ID`);

--
-- Contraintes pour la table `factures_prixtotal`
--
ALTER TABLE `factures_prixtotal`
  ADD CONSTRAINT `factures_prixtotal_ibfk_1` FOREIGN KEY (`Devis_ID`) REFERENCES `devis` (`Devis_ID`);

--
-- Contraintes pour la table `inspecteurs`
--
ALTER TABLE `inspecteurs`
  ADD CONSTRAINT `inspecteurs_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`);

--
-- Contraintes pour la table `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`);

--
-- Contraintes pour la table `proprietaires`
--
ALTER TABLE `proprietaires`
  ADD CONSTRAINT `proprietaires_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`);

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`AdressePostale_ID`) REFERENCES `adressespostales` (`AdressePostale_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
