-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : jeu. 08 jan. 2026 à 07:08
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
  `AdressePostale_ID` int(11) NOT NULL AUTO_INCREMENT,
  `AdressePostale_NumeroRue` varchar(50) DEFAULT NULL,
  `AdressePostale_Complement` varchar(50) DEFAULT NULL,
  `AdressePostale_CodePostal` varchar(50) DEFAULT NULL,
  `AdressePostale_NomRue` varchar(256) DEFAULT NULL,
  `AdressePostale_Ville` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `AdressePostale_Pays` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`AdressePostale_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `adressespostales`
--

INSERT INTO `adressespostales` (`AdressePostale_ID`, `AdressePostale_NumeroRue`, `AdressePostale_Complement`, `AdressePostale_CodePostal`, `AdressePostale_NomRue`, `AdressePostale_Ville`, `AdressePostale_Pays`) VALUES
(1, '10', 'A', '75001', 'Rue de Rivoli', 'Paris', 'France'),
(2, '25', NULL, '69002', 'Rue Merciere', 'Lyon', 'France'),
(3, '5', NULL, '06000', 'Avenue des Fleurs', 'Nice', 'France'),
(4, '42', NULL, '13001', 'La Canebiere', 'Marseille', 'France'),
(34, '14', '12', '45000', 'rue du bazouzou', 'Orléans', 'France'),
(41, '14', '', '41600', 'rue des bruyere', 'chaon', 'France'),
(42, '14', '', '41600', 'rue des bruyere', 'chaon', 'France');

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
(1, 'Hotel Lumiere', '0140203040', '2025-01-10', 3, 4, 1, 1, 29),
(2, 'Gite du Soleil', '0230405060', '2025-02-05', 2, 4, 2, 2, 29);

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
(1, 1, 4),
(2, 2, 4),
(3, 3, 7);

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
) ENGINE=InnoDB AUTO_INCREMENT=148 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `criteres`
--

INSERT INTO `criteres` (`Critere_ID`, `Critere_description`, `Critere_statut`, `Critere_points`) VALUES
(1, 'Surface totale minimum (cuisine et coin cuisine compris) du logement meublé hors salle d\'\'eau et toilettes', 'X', 5),
(2, 'Surface totale majorée', 'O', NULL),
(3, 'Prise de courant libre dans chaque pièce d\'\'habitation', 'X', 1),
(4, 'Tous les éclairages du logement fonctionnent et sont en bon état', 'X', 3),
(5, "Mise à disposition d\'un téléphone privatif à l\'intérieur du logement", 'O', 1),
(6, 'Accès internet par un réseau local sans fil (WiFi)', 'X', 2),
(7, 'Accès internet filaire avec câble fourni', 'O', 2),
(8, 'Télévision à écran plat avec télécommande', 'X', 2),
(9, "Accès à des chaînes supplémentaires à l\'offre de la TNT", 'O', 2),
(10, "Possibilité d\'accéder à au moins deux chaînes internationales", 'O', 1),
(11, 'Radio', 'X', 2),
(12, 'Enceinte connectée', 'O', 1),
(13, "Mise à disposition d\'un système de lecture de vidéos", 'O', 2),
(14, 'Occultation opaque dans chaque pièce comportant un couchage principal', 'X', 3),
(15, 'Le logement est équipé de double vitrage', 'O', 3),
(16, "Existence d\'un système de chauffage en état de fonctionnement", 'X', 5),
(17, "Existence d\'un système de climatisation ou de rafraîchissement d\'air", 'O', 3),
(18, 'Machine à laver le linge pour les logements de 4 personnes et plus', 'NA', 3),
(19, 'Sèche-linge électrique pour les logements de 6 personnes et plus', 'NA', 2),
(20, "Étendoir ou séchoir à linge à l\'intérieur du logement", 'X', 2),
(21, 'Ustensiles de ménage appropriés au logement', 'X', 3),
(22, 'Fer et table à repasser', 'X', 2),
(23, 'Placards ou éléments de rangement dans le logement', 'NA', 3),
(24, "Placards ou éléments de rangement dans chaque pièce d\'habitation", 'X', 3),
(25, "Présence d\'une table et d\'assises correspondant à la capacité d\'accueil", 'X', 4),
(26, "Présence d\'un canapé ou fauteuil(s) adapté(s)", 'X', 3),
(27, "Présence d\'une table basse", 'X', 1),
(28, 'Respect des dimensions du ou des lits', 'X', 4),
(29, 'Matelas haute densité ou épaisseur de qualité', 'O', 2),
(30, "Présence d\'oreillers en quantité suffisante", 'X', 2),
(31, 'Deux couvertures ou une couette par lit', 'X', 2),
(32, 'Matelas et oreillers protégés par alaises ou housses amovibles', 'X', 2),
(33, 'Éclairage en tête de lit par personne avec interrupteur individuel', 'X', 2),
(34, "Commande de l\'éclairage central près du lit", 'O', 2),
(35, 'Prise de courant libre située près du lit', 'O', 1),
(36, "Présence d\'une table de chevet par personne", 'X', 2),
(37, "Salle d\'eau privative intérieure", 'X', 2),
(38, "Salle d\'eau privative avec accès indépendant", 'X', 3),
(39, "Salle d\'eau équipée lavabo, douche et/ou baignoire", 'X', 3),
(40, "Salle d\'eau avec équipements supérieurs au standard", 'O', 2),
(41, 'WC privatif intérieur au logement', 'X', 2),
(42, "WC privatif indépendant de la salle d\'eau", 'O', 2),
(43, "Deuxième salle d\'eau privative", 'NA', 5),
(44, "Salle d\'eau supplémentaire équipée", 'NA', 3),
(45, 'WC privatif supplémentaire', 'NA', 2),
(46, 'Deux points lumineux dont un sur le lavabo', 'X', 2),
(47, "Présence de produits d\'accueil", 'X', 3),
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
(95, 'Sanitaires propres et en bon état', 'ONC', 5),
(96, 'Sols, murs et plafonds propres', 'ONC', 5),
(97, 'Mobilier propre et en bon état', 'ONC', 5),
(98, 'Literie propre et en bon état', 'ONC', 5),
(99, 'Cuisine propre et équipements en bon état', 'ONC', 5),
(100, 'Brochures touristiques multilingues', 'X', 3),
(101, "Livret d\'accueil", 'X', 2),
(102, 'Accueil sur place', 'X', 3),
(103, 'Cadeau de bienvenue', 'O', 2),
(104, 'Boîte à clés ou système équivalent', 'O', 2),
(105, 'Draps fournis systématiquement', 'X', 2),
(106, 'Linge de toilette fourni', 'X', 2),
(107, 'Linge de table', 'X', 2),
(108, "Lits faits à l\'arrivée", 'O', 2),
(109, 'Matériel pour bébé sur demande', 'X', 2),
(110, 'Service de ménage proposé', 'X', 2),
(111, "Produits d\'entretien", 'X', 2),
(112, 'Adaptateurs électriques', 'O', 2),
(113, 'Site internet dédié au logement', 'O', 2),
(114, 'Site internet en langue étrangère', 'O', 1),
(115, 'Animaux de compagnie admis', 'O', 2),
(116, "Informations sur l\'accessibilité", 'X', 2),
(117, 'Télécommande adaptée', 'O', 2),
(118, "Siège de douche avec barre d\'appui", 'O', 2),
(119, "WC avec barre d\'appui", 'O', 2),
(120, 'Largeur des portes adaptée', 'O', 2),
(121, 'Document accessible', 'X', 1),
(122, 'Label Tourisme et Handicap', 'O', 3),
(123, "Mesure de réduction de consommation d\'énergie", 'X', 3),
(124, "Mesure supplémentaire de réduction d\'énergie", 'O', 1),
(125, 'Borne de recharge pour véhicules électriques', 'O', 2),
(126, "Mesure de réduction de consommation d\'eau", 'X', 3),
(127, "Mesure supplémentaire de réduction d\'eau", 'O', 1),
(128, 'Tri des déchets', 'X', 1),
(129, 'Composteur', 'O', 1),
(130, 'Sensibilisation environnementale des clients', 'X', 2),
(131, "Produits d\'accueil écologiques", 'O', 2),
(132, "Produits d\'entretien écologiques", 'X', 1),
(133, "Obtention d\'un label environnemental", 'O', 3);

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
  `Dossier_ID` int(11) DEFAULT NULL,
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
  `Utilisateur_ID` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`Dossier_ID`),
  KEY `Utilisateur_ID` (`Utilisateur_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `dossiers`
--

INSERT INTO `dossiers` (`Dossier_ID`, `Dossier_Numero`, `Dossier_Date`, `Dossier_Etoile_Cible`, `Utilisateur_ID`, `status`) VALUES
(1, 'DOS-2025-001', '2025-01-15 10:00:00', 4, 1, 0),
(2, 'DOS-2025-002', '2025-02-10 14:00:00', 3, 1, 1);

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
(1, 1),
(1, 2);

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
(1, '2025-01-18 14:00:00', 'eval_hotel_lumiere.pdf', 'Conforme', 1, 4),
(2, '2025-02-15 10:00:00', 'eval_gite_soleil.pdf', 'Non conforme', 2, 7);

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
(1);

--
-- Déclencheurs `inspecteurs`
--
DROP TRIGGER IF EXISTS `majDossierDelete`;
DELIMITER $$
CREATE TRIGGER `majDossierDelete` BEFORE DELETE ON `inspecteurs` FOR EACH ROW UPDATE dossiers
SET Utilisateur_ID = NULL
WHERE Utilisateur_ID = OLD.Utilisateur_ID
$$
DELIMITER ;

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
(10);

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
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

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
(14, 5, 2, 2);

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
(1, 'photos/hotel_lumiere_1.jpg', 1),
(2, 'photos/hotel_lumiere_2.jpg', 1),
(3, 'photos/gite_soleil_1.jpg', 2),
(100, '/photos/liste_generique.jpg', 1);

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
(1),
(29);

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
  `Utilisateur_Societe` varchar(150) DEFAULT NULL,
  `Utilisateur_Password` varchar(256) DEFAULT NULL,
  `Utilisateur_Mail` varchar(250) NOT NULL,
  `Utilisateur_Telephone` varchar(50) DEFAULT NULL,
  `Utilisateur_Signature` varchar(255) DEFAULT NULL,
  `AdressePostale_ID` int(11) NOT NULL,
  PRIMARY KEY (`Utilisateur_ID`),
  UNIQUE KEY `unique_email` (`Utilisateur_Mail`),
  KEY `AdressePostale_ID` (`AdressePostale_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=ascii COLLATE=ascii_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`Utilisateur_ID`, `Utilisateur_Nom`, `Utilisateur_Prenom`, `Utilisateur_Civilite`, `Utilisateur_Societe`, `Utilisateur_Password`, `Utilisateur_Mail`, `Utilisateur_Telephone`, `Utilisateur_Signature`, `AdressePostale_ID`) VALUES
(1, 'Dupont', 'Marie', 'Madame', 'Vought International', '$2y$10$I1hKFaSD0SBsozEszv8ZAOLujxI09tszX6NcjMRb1sNQrnAgelLfO', 'marie.dupont@mail.com', '0600000001', NULL, 1),
(2, 'Martin', 'Luc', 'Monsieur', 'Maze Bank', '$2y$10$MkNpWi2BTFYWLvFIMJZzuOpCXkpbPnfEGricU6ObaPhYTn0VX4wuO', 'luc.martin@mail.com', '0600000002', NULL, 2),
(3, 'Bernard', 'Julie', 'Madame', 'DedSec', '$2y$10$39cuv2r4W/fEpXvlEQJnb.22bKm0LgU7yos190..B4V57SZ..mbp6', 'julie.bernard@mail.com', '0600000003', NULL, 3),
(4, 'Durand', 'Paul', 'Monsieur', 'Amazon', 'pass123', 'paul.durand@mail.com', '0600000004', NULL, 4),
(29, 'bourdon', 'Angel', 'Monsieur', 'inc', '0123456789', 'anbourdonlopez@stpbb.org', '0769155622', NULL, 42);

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
  ADD CONSTRAINT `biens_ibfk_4` FOREIGN KEY (`Utilisateur_ID_1`) REFERENCES `proprietaires` (`Utilisateur_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `concerne`
--
ALTER TABLE `concerne`
  ADD CONSTRAINT `concerne_ibfk_1` FOREIGN KEY (`Bien_ID`) REFERENCES `biens` (`Bien_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `concerne_ibfk_2` FOREIGN KEY (`Dossier_ID`) REFERENCES `dossiers` (`Dossier_ID`);

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
  ADD CONSTRAINT `dossiers_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `inspecteurs` (`Utilisateur_ID`) ON DELETE CASCADE;

--
-- Contraintes pour la table `effectue`
--
ALTER TABLE `effectue`
  ADD CONSTRAINT `effectue_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `inspecteurs` (`Utilisateur_ID`),
  ADD CONSTRAINT `effectue_ibfk_2` FOREIGN KEY (`Evaluation_ID`) REFERENCES `evaluations` (`Evaluation_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `factures_prixtotal_ibfk_1` FOREIGN KEY (`Devis_ID`) REFERENCES `devis` (`Devis_ID`);

--
-- Contraintes pour la table `inspecteurs`
--
ALTER TABLE `inspecteurs`
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
  ADD CONSTRAINT `proprietaires_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `utilisateurs` (`Utilisateur_ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD CONSTRAINT `utilisateurs_ibfk_1` FOREIGN KEY (`AdressePostale_ID`) REFERENCES `adressespostales` (`AdressePostale_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
