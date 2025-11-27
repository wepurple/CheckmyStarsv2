-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 27 nov. 2025 à 08:46
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

--
-- Déchargement des données de la table `administre`
--

INSERT INTO `administre` (`Utilisateur_ID`, `Critere_ID`) VALUES
(1, 1),
(1, 2),
(1, 3);

-- --------------------------------------------------------

--
-- Structure de la table `adressespostales`
--

DROP TABLE IF EXISTS `adressespostales`;
CREATE TABLE IF NOT EXISTS `adressespostales` (
  `AdressePostale_ID` int(11) NOT NULL,
  `AdressePostale_NumeroRue` varchar(50) DEFAULT NULL,
  `AdressePostale_Complement` varchar(50) DEFAULT NULL,
  `AdressePostale_CodePostal` varchar(50) DEFAULT NULL,
  `AdressePostale_NomRue` varchar(256) DEFAULT NULL,
  `AdressePostale_Ville` varchar(256) DEFAULT NULL,
  `AdressePostale_Pays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`AdressePostale_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `adressespostales`
--

INSERT INTO `adressespostales` (`AdressePostale_ID`, `AdressePostale_NumeroRue`, `AdressePostale_Complement`, `AdressePostale_CodePostal`, `AdressePostale_NomRue`, `AdressePostale_Ville`, `AdressePostale_Pays`) VALUES
(1, '10', 'A', '75001', 'Rue de Rivoli', 'Paris', 'France'),
(2, '25', NULL, '69002', 'Rue Merciere', 'Lyon', 'France'),
(3, '5', NULL, '06000', 'Avenue des Fleurs', 'Nice', 'France'),
(4, '42', NULL, '13001', 'La Canebiere', 'Marseille', 'France');

-- --------------------------------------------------------

--
-- Structure de la table `biens`
--

DROP TABLE IF EXISTS `biens`;
CREATE TABLE IF NOT EXISTS `biens` (
  `Bien_ID` int(11) NOT NULL,
  `Biens_Nom` varchar(50) DEFAULT NULL,
  `Bien_Telephone` varchar(50) DEFAULT NULL,
  `Bien_DateEnregistrement` date DEFAULT NULL,
  `Bien_Etoile_Actuelle` int(11) DEFAULT NULL,
  `Utilisateur_ID` int(11) DEFAULT NULL,
  `AdressePostale_ID` int(11) NOT NULL,
  `TypeHebergement_ID` int(11) NOT NULL,
  `Utilisateur_ID_1` int(11) NOT NULL,
  PRIMARY KEY (`Bien_ID`),
  KEY `Utilisateur_ID` (`Utilisateur_ID`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`),
  KEY `TypeHebergement_ID` (`TypeHebergement_ID`),
  KEY `Utilisateur_ID_1` (`Utilisateur_ID_1`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `biens`
--

INSERT INTO `biens` (`Bien_ID`, `Biens_Nom`, `Bien_Telephone`, `Bien_DateEnregistrement`, `Bien_Etoile_Actuelle`, `Utilisateur_ID`, `AdressePostale_ID`, `TypeHebergement_ID`, `Utilisateur_ID_1`) VALUES
(1, 'Hotel du Centre', '0102030405', '2024-01-01', 3, 4, 1, 1, 3),
(2, 'Gite des Monts', '0102030406', '2024-02-15', 4, 4, 2, 2, 3);

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
(2, 2);

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
(1, 1, 1),
(2, 2, 1),
(3, 3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `criteres`
--

DROP TABLE IF EXISTS `criteres`;
CREATE TABLE IF NOT EXISTS `criteres` (
  `Critere_ID` int(11) NOT NULL,
  `Critere_nom` varchar(50) DEFAULT NULL,
  `Critere_valeur` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Critere_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `criteres`
--

INSERT INTO `criteres` (`Critere_ID`, `Critere_nom`, `Critere_valeur`) VALUES
(1, 'Proprete', 'Bonne'),
(2, 'Accueil', 'Excellent'),
(3, 'Securite', 'Correcte');

-- --------------------------------------------------------

--
-- Structure de la table `devis`
--

DROP TABLE IF EXISTS `devis`;
CREATE TABLE IF NOT EXISTS `devis` (
  `Devis_ID` int(11) NOT NULL,
  `Devis_DateAccepattion` datetime DEFAULT NULL,
  `Devis_montant` decimal(10,2) DEFAULT NULL,
  `Devis_Numero` varchar(50) NOT NULL,
  `Devis_DateEmission` datetime NOT NULL,
  `Devis_Document` varchar(50) DEFAULT NULL,
  `Dossier_ID` int(11) NOT NULL,
  PRIMARY KEY (`Devis_ID`),
  UNIQUE KEY `Dossier_ID` (`Dossier_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `devis`
--

INSERT INTO `devis` (`Devis_ID`, `Devis_DateAccepattion`, `Devis_montant`, `Devis_Numero`, `Devis_DateEmission`, `Devis_Document`, `Dossier_ID`) VALUES
(1, '2025-01-20 09:00:00', 1500.00, 'DEV-001', '2025-01-18 09:00:00', 'doc-dev1.pdf', 1),
(2, '2025-02-15 12:00:00', 2100.00, 'DEV-002', '2025-02-12 12:00:00', 'doc-dev2.pdf', 2);

-- --------------------------------------------------------

--
-- Structure de la table `donneurordre`
--

DROP TABLE IF EXISTS `donneurordre`;
CREATE TABLE IF NOT EXISTS `donneurordre` (
  `Utilisateur_ID` int(11) NOT NULL,
  `DonneurOrdre_Entreprine_Nom` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `donneurordre`
--

INSERT INTO `donneurordre` (`Utilisateur_ID`, `DonneurOrdre_Entreprine_Nom`) VALUES
(4, 'Entreprise Soleil');

-- --------------------------------------------------------

--
-- Structure de la table `dossiers`
--

DROP TABLE IF EXISTS `dossiers`;
CREATE TABLE IF NOT EXISTS `dossiers` (
  `Dossier_ID` int(11) NOT NULL,
  `Dossier_Numero` varchar(50) DEFAULT NULL,
  `Dossier_Date` datetime DEFAULT NULL,
  `Dossier_Etoile_Cible` int(11) DEFAULT NULL,
  `Utilisateur_ID` int(11) NOT NULL,
  PRIMARY KEY (`Dossier_ID`),
  KEY `Utilisateur_ID` (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `dossiers`
--

INSERT INTO `dossiers` (`Dossier_ID`, `Dossier_Numero`, `Dossier_Date`, `Dossier_Etoile_Cible`, `Utilisateur_ID`) VALUES
(1, 'DOS-2025-001', '2025-01-15 10:00:00', 4, 2),
(2, 'DOS-2025-002', '2025-02-10 14:00:00', 3, 2);

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

--
-- Déchargement des données de la table `effectue`
--

INSERT INTO `effectue` (`Utilisateur_ID`, `Evaluation_ID`) VALUES
(2, 1),
(2, 2);

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

--
-- Déchargement des données de la table `evaluations`
--

INSERT INTO `evaluations` (`Evaluation_ID`, `Evaluation_Date`, `Evaluation_Document`, `Evaluation_Résultat`, `Bien_ID`, `ListesCriteres_ID`) VALUES
(1, '2025-01-22 16:00:00', 'eval1.pdf', 'Conforme', 1, 1),
(2, '2025-02-18 10:00:00', 'eval2.pdf', 'Non Conforme', 2, 2);

-- --------------------------------------------------------

--
-- Structure de la table `factures_prixtotal`
--

DROP TABLE IF EXISTS `factures_prixtotal`;
CREATE TABLE IF NOT EXISTS `factures_prixtotal` (
  `Facture_ID` int(11) NOT NULL,
  `Facture_Numero` varchar(50) NOT NULL,
  `Facture_DateCreation` datetime NOT NULL,
  `Facture_DatePayee` datetime DEFAULT NULL,
  `Facture_Document` varchar(50) DEFAULT NULL,
  `Devis_ID` int(11) NOT NULL,
  PRIMARY KEY (`Facture_ID`),
  UNIQUE KEY `Devis_ID` (`Devis_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `factures_prixtotal`
--

INSERT INTO `factures_prixtotal` (`Facture_ID`, `Facture_Numero`, `Facture_DateCreation`, `Facture_DatePayee`, `Facture_Document`, `Devis_ID`) VALUES
(1, 'FAC-001', '2025-01-25 15:00:00', '2025-01-30 10:00:00', 'doc-fac1.pdf', 1),
(2, 'FAC-002', '2025-02-20 11:00:00', NULL, 'doc-fac2.pdf', 2);

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
(2);

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
(2);

-- --------------------------------------------------------

--
-- Structure de la table `photos`
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `Photo_ID` int(11) NOT NULL,
  `Photo_Lien` varchar(350) DEFAULT NULL,
  `Bien_ID` int(11) NOT NULL,
  PRIMARY KEY (`Photo_ID`),
  KEY `Bien_ID` (`Bien_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `photos`
--

INSERT INTO `photos` (`Photo_ID`, `Photo_Lien`, `Bien_ID`) VALUES
(1, '/photos/bien1-1.jpg', 1),
(2, '/photos/bien1-2.jpg', 1),
(3, '/photos/bien2-1.jpg', 2);

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
(3);

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
  `Utilisateur_ID` int(11) NOT NULL,
  `Utilisateur_Nom` varchar(50) NOT NULL,
  `Utilisateur_Prenom` varchar(50) DEFAULT NULL,
  `Utilisateur_Civilite` varchar(50) DEFAULT NULL,
  `Utilisateur_Password` varchar(256) DEFAULT NULL,
  `Utilisateur_Mail` varchar(250) NOT NULL,
  `Utilisateur_Telephone` varchar(50) DEFAULT NULL,
  `Utilisateur_Signature` varchar(255) DEFAULT NULL,
  `AdressePostale_ID` int(11) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`Utilisateur_ID`, `Utilisateur_Nom`, `Utilisateur_Prenom`, `Utilisateur_Civilite`, `Utilisateur_Password`, `Utilisateur_Mail`, `Utilisateur_Telephone`, `Utilisateur_Signature`, `AdressePostale_ID`) VALUES
(1, 'Dupont', 'Marie', 'Mme', 'pass123', 'marie.dupont@mail.com', '0600000001', NULL, 1),
(2, 'Martin', 'Luc', 'M.', 'pass123', 'luc.martin@mail.com', '0600000002', NULL, 2),
(3, 'Bernard', 'Julie', 'Mme', 'pass123', 'julie.bernard@mail.com', '0600000003', NULL, 3),
(4, 'Durand', 'Paul', 'M.', 'pass123', 'paul.durand@mail.com', '0600000004', NULL, 4);

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
