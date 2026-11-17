-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 17 nov. 2026 à 08:34
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `lprs`
--

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

DROP TABLE IF EXISTS `candidature`;
CREATE TABLE IF NOT EXISTS `candidature` (
  `id_utilisateur` int UNSIGNED NOT NULL,
  `id_offre` int UNSIGNED NOT NULL,
  `motivation` text NOT NULL,
  `date_candidature` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut_candidature` enum('active','masquee') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`id_utilisateur`,`id_offre`),
  KEY `fk_candidature_offre` (`id_offre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `candidature`
--

INSERT INTO `candidature` (`id_utilisateur`, `id_offre`, `motivation`, `date_candidature`, `statut_candidature`) VALUES
(2, 1, 'Tres motive par ce stage, disponible immediatement.', '2026-09-15 11:42:24', 'active'),
(12, 1, 'gfdvqdc', '2026-11-10 12:16:57', 'active');

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `id_entreprise` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_entreprise` varchar(200) NOT NULL,
  `activite` varchar(150) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `site_web` varchar(255) NOT NULL,
  PRIMARY KEY (`id_entreprise`),
  UNIQUE KEY `uq_entreprise_site_web` (`site_web`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id_entreprise`, `nom_entreprise`, `activite`, `adresse`, `site_web`) VALUES
(1, 'Acme Corp', 'Informatique', '1 rue du Code, Paris', 'https://acme.example'),
(2, 'NetSecure Solutions', 'Cybersécurité', '12 rue de la République, Paris', 'https://netsecure.example'),
(3, 'DataFlow Systems', 'Informatique', '45 avenue Jean Jaurès, Lyon', 'https://dataflow.example'),
(4, 'MecaPrecision', 'Usinage et mécanique', '8 rue des Ateliers, Saint-Denis', 'https://mecaprecision.example'),
(5, 'TelecomPlus Réseaux', 'Télécommunications', '23 boulevard Voltaire, Paris', 'https://telecomplus.example'),
(6, 'IndusMaint Services', 'Maintenance industrielle', '3 zone industrielle Nord, Gonesse', 'https://indusmaint.example'),
(7, 'CloudNova', 'Hébergement cloud', '67 rue de Rivoli, Paris', 'https://cloudnova.example'),
(8, 'ElectroTech Industrie', 'Électronique', '14 rue Pasteur, Argenteuil', 'https://electrotech.example'),
(9, 'WebFactory Studio', 'Développement web', '9 rue Oberkampf, Paris', 'https://webfactory.example'),
(10, 'Atelier Conception 3D', 'Conception de produits', '31 avenue de la Gare, Sarcelles', 'https://conception3d.example'),
(11, 'InfraLogic', 'Infogérance', '55 rue de Lille, Lille', 'https://infralogic.example'),
(12, 'RoboMeca Automation', 'Automatisme et robotique', '18 rue de l\'Industrie, Cergy', 'https://robomeca.example'),
(13, 'SoftEdu', 'Éditeur de logiciels', '2 place de la Bourse, Bordeaux', 'https://softedu.example'),
(14, 'FibreConnect', 'Réseaux et fibre optique', '76 avenue du Général Leclerc, Créteil', 'https://fibreconnect.example'),
(15, 'TechnoPièces', 'Fabrication de pièces', '5 rue Gustave Eiffel, Bobigny', 'https://technopieces.example'),
(16, 'CyberShield Conseil', 'Audit sécurité', '40 rue La Fayette, Paris', 'https://cybershield.example'),
(17, 'MaintPro Énergie', 'Maintenance des systèmes', '11 chemin des Prés, Pontoise', 'https://maintpro.example'),
(18, 'PixelMobile', 'Applications mobiles', '28 rue Nationale, Nantes', 'https://pixelmobile.example'),
(19, 'Helpdesk Express', 'Support informatique', '6 rue du Commerce, Montreuil', 'https://helpdeskexpress.example'),
(20, 'AéroMéca Sous-traitance', 'Mécanique aéronautique', '90 avenue de l\'Europe, Toulouse', 'https://aeromeca.example'),
(21, 'DataCenter Île-de-France', 'Centre de données', '15 rue des Entrepôts, Aubervilliers', 'https://datacenter-idf.example');

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

DROP TABLE IF EXISTS `evenement`;
CREATE TABLE IF NOT EXISTS `evenement` (
  `id_evenement` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type_evenement` varchar(100) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `elements_requis` varchar(255) DEFAULT NULL,
  `nb_places` smallint UNSIGNED DEFAULT NULL,
  `date_evenement` datetime NOT NULL,
  `statut` enum('brouillon','en_attente','publie','refuse') NOT NULL DEFAULT 'brouillon',
  PRIMARY KEY (`id_evenement`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id_evenement`, `type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
(1, 'Rencontre', 'Soiree Alumni', 'Networking annuel entre alumni, etudiants et entreprises partenaires.', 'Campus principal, amphi A', 'Aucun', 50, '2026-12-05 18:00:00', 'publie'),
(2, 'grezgvrev', 'vaervfv', 'vraeqv', 'vreav', 'vreavq', 7, '2026-09-29 12:05:31', 'publie'),
(3, 'test', 'test', 'test', 'test', NULL, 45, '2026-10-13 08:37:55', 'publie'),
(4, 'Conférence', 'Conférence cybersécurité', 'Présentation des métiers de la cybersécurité par un intervenant extérieur.', 'Amphi A', NULL, 80, '2026-10-20 14:00:00', 'publie'),
(5, 'Atelier', 'Atelier CV et lettre de motiv', 'Atelier pratique pour préparer son CV avant la recherche de stage.', 'Salle B12', 'Ordinateur portable', 20, '2026-10-22 10:00:00', 'publie'),
(6, 'Rencontre', 'Afterwork anciens SIO', 'Rencontre informelle entre étudiants SIO et anciens élèves.', 'Cafétéria du lycée', NULL, 40, '2026-11-05 18:00:00', 'publie'),
(7, 'Forum', 'Forum des entreprises', 'Les entreprises partenaires présentent leurs offres de stage et d\'alternance.', 'Gymnase', 'CV imprimés', 200, '2026-11-14 09:00:00', 'publie'),
(8, 'Visite', 'Visite usine partenaire', 'Visite d\'un site de production mécanique pour les élèves TRPM et MEI.', 'Zone industrielle Nord', 'Chaussures de sécurité', 25, '2026-11-20 08:30:00', 'publie'),
(9, 'Atelier', 'Initiation Linux', 'Découverte des commandes de base sous Linux.', 'Salle C03', 'Ordinateur portable', 15, '2026-11-26 13:30:00', 'brouillon'),
(10, 'Conférence', 'Parcours d\'un ancien élève', 'Un alumni raconte son parcours depuis le BTS jusqu\'à son poste actuel.', 'Amphi A', NULL, 100, '2026-12-03 15:00:00', 'publie'),
(11, 'Compétition', 'Hackathon LPRS', 'Défi de programmation en équipe sur 24 heures.', 'Salle informatique', 'Ordinateur portable', 30, '2026-12-12 09:00:00', 'brouillon'),
(12, 'Rencontre', 'Job dating alternance', 'Entretiens rapides avec des entreprises qui recrutent en alternance.', 'Hall principal', 'CV imprimés', 60, '2027-01-15 10:00:00', 'publie'),
(13, 'Portes ouvertes', 'Journée portes ouvertes', 'Présentation des formations aux futurs élèves et à leurs familles.', 'Campus principal', NULL, 300, '2027-02-06 09:00:00', 'publie'),
(14, 'trevfvd', 'vrevev', 'vevrevr', 'dugny', NULL, 15654, '2026-10-13 10:18:30', 'publie'),
(15, 'salutation', 'czece', 'cez', 'vfd', 'svds', 898, '2026-10-13 10:23:15', 'publie'),
(16, 'vfesbf', 'vfvf', 'vevf', 'dvfd', 'qvfd', 15, '2026-11-17 08:44:17', 'refuse'),
(17, 'vfeqbf', 'dqqfdv', 'fqvfqv', 'fqdvfq', 'vfqvqfv', 518, '2027-05-23 15:09:00', 'en_attente'),
(18, 'vfdqvfq', 'vfqdvfqd', 'vqfqdvfq', 'qvfvfdq', 'ft', 1559, '2027-12-15 15:09:00', 'publie');

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

DROP TABLE IF EXISTS `formation`;
CREATE TABLE IF NOT EXISTS `formation` (
  `id_formation` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_formation` varchar(150) NOT NULL,
  `type_formation` enum('bac_pro','bac_techno','bts') NOT NULL,
  PRIMARY KEY (`id_formation`),
  UNIQUE KEY `uq_formation_nom` (`nom_formation`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`id_formation`, `nom_formation`, `type_formation`) VALUES
(1, 'BTS SIO', 'bts'),
(2, 'Bac technologique STI2D', 'bac_techno');

-- --------------------------------------------------------

--
-- Structure de la table `forum`
--

DROP TABLE IF EXISTS `forum`;
CREATE TABLE IF NOT EXISTS `forum` (
  `id_forum` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) DEFAULT NULL,
  `text` text NOT NULL,
  `canal` enum('general','alumni_entreprises','etudiants') NOT NULL,
  `date_heure` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_utilisateur` int UNSIGNED NOT NULL,
  `id_forum_parent` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_forum`),
  KEY `fk_forum_utilisateur` (`id_utilisateur`),
  KEY `fk_forum_parent` (`id_forum_parent`),
  KEY `idx_forum_canal` (`canal`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `forum`
--

INSERT INTO `forum` (`id_forum`, `titre`, `text`, `canal`, `date_heure`, `id_utilisateur`, `id_forum_parent`) VALUES
(1, 'Bienvenue sur le forum', 'Premier message pour lancer les discussions !', 'general', '2026-09-15 11:42:24', 2, NULL),
(3, NULL, 'salut !!', 'general', '2026-11-03 10:02:31', 11, 1),
(4, NULL, 'test', 'general', '2026-11-03 10:41:53', 11, 1),
(6, 'retst', 'vfrzv', 'alumni_entreprises', '2026-11-03 10:45:24', 10, NULL),
(9, 'test', 'dvsrsbfsc', 'alumni_entreprises', '2026-11-10 08:48:00', 11, NULL),
(10, 'vfbfs', 'bfdsbfd', 'general', '2026-11-10 08:48:23', 11, NULL),
(11, NULL, 'salut !!!!', 'general', '2026-11-10 12:17:59', 12, 1);

-- --------------------------------------------------------

--
-- Structure de la table `inscription_evenement`
--

DROP TABLE IF EXISTS `inscription_evenement`;
CREATE TABLE IF NOT EXISTS `inscription_evenement` (
  `id_utilisateur` int UNSIGNED NOT NULL,
  `id_evenement` int UNSIGNED NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut_inscription` enum('acceptee','refusee') NOT NULL DEFAULT 'acceptee',
  PRIMARY KEY (`id_utilisateur`,`id_evenement`),
  KEY `fk_inscription_evenement` (`id_evenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `inscription_evenement`
--

INSERT INTO `inscription_evenement` (`id_utilisateur`, `id_evenement`, `date_inscription`, `statut_inscription`) VALUES
(2, 1, '2026-09-15 11:42:24', 'acceptee'),
(10, 1, '2026-10-13 10:42:18', 'acceptee'),
(10, 2, '2026-10-13 10:42:18', 'acceptee'),
(10, 3, '2026-10-13 10:42:18', 'acceptee'),
(10, 7, '2026-10-13 10:42:18', 'acceptee'),
(10, 12, '2026-10-13 10:42:18', 'refusee'),
(10, 14, '2026-10-28 11:50:20', 'acceptee'),
(10, 15, '2026-11-10 09:20:53', 'acceptee'),
(12, 2, '2026-11-17 08:59:42', 'acceptee'),
(12, 3, '2026-11-10 12:17:15', 'acceptee'),
(12, 14, '2026-11-17 08:59:52', 'acceptee'),
(12, 18, '2026-11-17 09:30:45', 'acceptee');

-- --------------------------------------------------------

--
-- Structure de la table `intervient_formation`
--

DROP TABLE IF EXISTS `intervient_formation`;
CREATE TABLE IF NOT EXISTS `intervient_formation` (
  `id_utilisateur` int UNSIGNED NOT NULL,
  `id_formation` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_utilisateur`,`id_formation`),
  KEY `fk_intervient_formation` (`id_formation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `intervient_formation`
--

INSERT INTO `intervient_formation` (`id_utilisateur`, `id_formation`) VALUES
(3, 1),
(3, 2);

-- --------------------------------------------------------

--
-- Structure de la table `offre`
--

DROP TABLE IF EXISTS `offre`;
CREATE TABLE IF NOT EXISTS `offre` (
  `id_offre` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `missions` text,
  `salaire` decimal(10,2) DEFAULT NULL,
  `type` enum('stage','alternance','cdd','cdi') NOT NULL,
  `statut` enum('ouverte','cloturee') NOT NULL DEFAULT 'ouverte',
  `profil_cible` enum('tous','etudiant','alumni') NOT NULL DEFAULT 'tous',
  `date_publication` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_entreprise` int UNSIGNED NOT NULL,
  `id_utilisateur_auteur` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_offre`),
  KEY `fk_offre_entreprise` (`ref_entreprise`),
  KEY `fk_offre_auteur` (`id_utilisateur_auteur`),
  KEY `idx_offre_statut` (`statut`),
  KEY `idx_offre_type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `offre`
--

INSERT INTO `offre` (`id_offre`, `titre`, `description`, `missions`, `salaire`, `type`, `statut`, `profil_cible`, `date_publication`, `ref_entreprise`, `id_utilisateur_auteur`) VALUES
(1, 'Stage developpeur web', 'Stage de 3 mois au sein de l\'equipe technique.', 'Developpement front-end, tests, code review', 800.00, 'stage', 'ouverte', 'etudiant', '2026-09-15 11:42:24', 1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `organise_evenement`
--

DROP TABLE IF EXISTS `organise_evenement`;
CREATE TABLE IF NOT EXISTS `organise_evenement` (
  `id_utilisateur` int UNSIGNED NOT NULL,
  `id_evenement` int UNSIGNED NOT NULL,
  `statut_participation` enum('en_attente','acceptee','refusee') NOT NULL DEFAULT 'acceptee',
  PRIMARY KEY (`id_utilisateur`,`id_evenement`),
  KEY `fk_organise_evenement` (`id_evenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `organise_evenement`
--

INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`, `statut_participation`) VALUES
(3, 1, 'acceptee'),
(3, 14, 'acceptee'),
(4, 1, 'acceptee'),
(4, 16, 'acceptee'),
(4, 17, 'en_attente'),
(5, 15, 'acceptee'),
(10, 2, 'acceptee'),
(10, 3, 'acceptee'),
(10, 4, 'acceptee'),
(10, 5, 'acceptee'),
(10, 6, 'acceptee'),
(10, 7, 'acceptee'),
(10, 8, 'acceptee'),
(10, 9, 'acceptee'),
(10, 10, 'acceptee'),
(10, 11, 'acceptee'),
(10, 12, 'acceptee'),
(10, 13, 'acceptee'),
(10, 14, 'acceptee'),
(10, 15, 'acceptee'),
(11, 16, 'acceptee'),
(12, 17, 'acceptee'),
(12, 18, 'acceptee'),
(13, 18, 'acceptee');

-- --------------------------------------------------------

--
-- Structure de la table `signalement_forum`
--

DROP TABLE IF EXISTS `signalement_forum`;
CREATE TABLE IF NOT EXISTS `signalement_forum` (
  `id_signalement` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_forum` int UNSIGNED NOT NULL,
  `id_utilisateur` int UNSIGNED NOT NULL,
  `motif` enum('spam','insulte','donnees_personnelles','hors_sujet','autre') NOT NULL,
  `commentaire` varchar(500) DEFAULT NULL,
  `date_signalement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_signalement`),
  UNIQUE KEY `uq_signalement_message_utilisateur` (`id_forum`,`id_utilisateur`),
  KEY `fk_signalement_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `signalement_forum`
--

INSERT INTO `signalement_forum` (`id_signalement`, `id_forum`, `id_utilisateur`, `motif`, `commentaire`, `date_signalement`) VALUES
(4, 4, 12, 'spam', 'vfdvsv', '2026-11-10 12:18:25');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(100) NOT NULL,
  `prenom_utilisateur` varchar(100) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `role` enum('etudiant','alumni','partenaire','professeur','gestionnaire') NOT NULL,
  `statut_validation` enum('en_attente','valide','refuse') NOT NULL DEFAULT 'en_attente',
  `cv` varchar(255) DEFAULT NULL,
  `annee_promo` year DEFAULT NULL,
  `id_formation` int UNSIGNED DEFAULT NULL,
  `specialite` varchar(150) DEFAULT NULL,
  `motif_inscription` varchar(255) DEFAULT NULL,
  `id_entreprise` int UNSIGNED DEFAULT NULL,
  `poste` varchar(150) DEFAULT NULL,
  `id_gestionnaire_createur` int UNSIGNED DEFAULT NULL,
  `date_inscription` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `uq_utilisateur_mail` (`mail`),
  KEY `fk_utilisateur_formation` (`id_formation`),
  KEY `fk_utilisateur_entreprise` (`id_entreprise`),
  KEY `fk_utilisateur_gestionnaire_createur` (`id_gestionnaire_createur`),
  KEY `idx_utilisateur_role` (`role`),
  KEY `idx_utilisateur_annee_promo` (`annee_promo`),
  KEY `idx_utilisateur_nom` (`nom_utilisateur`,`prenom_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom_utilisateur`, `prenom_utilisateur`, `mail`, `mdp`, `telephone`, `date_naissance`, `role`, `statut_validation`, `cv`, `annee_promo`, `id_formation`, `specialite`, `motif_inscription`, `id_entreprise`, `poste`, `id_gestionnaire_createur`, `date_inscription`) VALUES
(1, 'Admin', 'Root', 'admin@ecole.fr', 'hash_admin', NULL, NULL, 'gestionnaire', 'valide', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 11:42:24'),
(2, 'Martin', 'Leo', 'leo.martin@mail.fr', 'hash_leo', NULL, NULL, 'alumni', 'refuse', 'cv_leo.pdf', '2026', 1, NULL, NULL, NULL, NULL, NULL, '2026-09-15 11:42:24'),
(3, 'Durand', 'Paul', 'paul.durand@ecole.fr', 'hash_paul', NULL, NULL, 'professeur', 'valide', NULL, NULL, NULL, 'Reseaux', NULL, NULL, NULL, NULL, '2026-09-15 11:42:24'),
(4, 'Petit', 'Sarah', 'sarah.petit@mail.fr', 'hash_sarah', NULL, NULL, 'professeur', 'valide', 'cv_sarah.pdf', '2020', NULL, NULL, NULL, 1, 'Developpeuse', NULL, '2026-09-15 11:42:24'),
(5, 'Bernard', 'Julie', 'julie.bernard@acme.example', 'hash_julie', NULL, NULL, 'partenaire', 'valide', NULL, NULL, NULL, NULL, 'Recrutement de stagiaires', 1, 'Responsable RH', NULL, '2026-09-15 11:42:24'),
(6, 'test', 'test', 'test@buv.com', '$2y$10$mqjYjb0VZWVRcO38z4EtBeWNZ4zQpc/vInfnU0uZWvrPTh6iTtT/O', '186', NULL, 'etudiant', 'refuse', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-29 10:29:58'),
(10, 'benmechichi', 'Ahmed', 'benmechichi.ahmed@gmail.com', '$2y$10$RlKKjHNWkVYuzMvO8GAbueaY/BUaJiWfoTWhwmXeJALSNF66AwoXi', '0679101512', '2005-10-27', 'gestionnaire', 'valide', NULL, NULL, NULL, 'Maths', 'test', 1, 'dev', NULL, '2026-09-29 11:30:49'),
(11, 'dupont', 'tomas', 'tomas@dupont.fr', '$2y$10$uzf/ZzKON9FQpJk8Ji9gFur9rDX3gnivc3JVBPz5.g62L5qBydtMm', '06554582155', '2004-04-21', 'partenaire', 'valide', NULL, '2026', 1, 'SLAM', NULL, NULL, NULL, NULL, '2026-10-28 12:16:10'),
(12, 'Tomas', 'Mark', 'a.ben-mechichi@lprs.fr', '$2y$10$2zuf3dd2aC8FGc0TbE.pVew3DV/mtFxizGyme7Pniq5A.6b5rQqJS', '459456189', '2004-02-14', 'etudiant', 'valide', NULL, '2023', 1, 'SISR', NULL, NULL, NULL, NULL, '2026-11-10 12:03:21'),
(13, 'lac', 'jon', 'jon@gmail.com', '$2y$10$Rtk/aSzSochVzr2PLinozeF/4u0QiFLaXZT27l.St5WOYUH.Y5bq6', '1515689189', '1988-02-12', 'professeur', 'valide', NULL, NULL, NULL, 'Maths', NULL, NULL, NULL, NULL, '2026-11-17 09:15:56');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `fk_candidature_offre` FOREIGN KEY (`id_offre`) REFERENCES `offre` (`id_offre`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_candidature_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `forum`
--
ALTER TABLE `forum`
  ADD CONSTRAINT `fk_forum_parent` FOREIGN KEY (`id_forum_parent`) REFERENCES `forum` (`id_forum`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_forum_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Contraintes pour la table `inscription_evenement`
--
ALTER TABLE `inscription_evenement`
  ADD CONSTRAINT `fk_inscription_evenement` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscription_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `intervient_formation`
--
ALTER TABLE `intervient_formation`
  ADD CONSTRAINT `fk_intervient_formation` FOREIGN KEY (`id_formation`) REFERENCES `formation` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_intervient_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `fk_offre_auteur` FOREIGN KEY (`id_utilisateur_auteur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_offre_entreprise` FOREIGN KEY (`ref_entreprise`) REFERENCES `entreprise` (`id_entreprise`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `organise_evenement`
--
ALTER TABLE `organise_evenement`
  ADD CONSTRAINT `fk_organise_evenement` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_organise_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `signalement_forum`
--
ALTER TABLE `signalement_forum`
  ADD CONSTRAINT `fk_signalement_forum` FOREIGN KEY (`id_forum`) REFERENCES `forum` (`id_forum`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_signalement_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `fk_utilisateur_entreprise` FOREIGN KEY (`id_entreprise`) REFERENCES `entreprise` (`id_entreprise`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_utilisateur_formation` FOREIGN KEY (`id_formation`) REFERENCES `formation` (`id_formation`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_utilisateur_gestionnaire_createur` FOREIGN KEY (`id_gestionnaire_createur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
