-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 27 nov. 2025 à 08:24
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
(1, 'Dupont', 'Jean', 'Monsieur', '0601020304', 'jean.dupont@email.com', '12 rue de la Paix', '', '75001', 'Paris', 'France', '2025-11-13 11:17:26', '', 'Administrateur', 'jdupont', 'mdp123'),
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
