-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 15 sep. 2026 à 09:45
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
(2, 1, 'Tres motive par ce stage, disponible immediatement.', '2026-09-15 11:42:24', 'active');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id_entreprise`, `nom_entreprise`, `activite`, `adresse`, `site_web`) VALUES
(1, 'Acme Corp', 'Informatique', '1 rue du Code, Paris', 'https://acme.example');

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
  `statut` enum('brouillon','publie') NOT NULL DEFAULT 'brouillon',
  PRIMARY KEY (`id_evenement`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id_evenement`, `type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
(1, 'Rencontre', 'Soiree Alumni', 'Networking annuel entre alumni, etudiants et entreprises partenaires.', 'Campus principal, amphi A', 'Aucun', 50, '2026-12-05 18:00:00', 'publie');

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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `forum`
--

INSERT INTO `forum` (`id_forum`, `titre`, `text`, `canal`, `date_heure`, `id_utilisateur`, `id_forum_parent`) VALUES
(1, 'Bienvenue sur le forum', 'Premier message pour lancer les discussions !', 'general', '2026-09-15 11:42:24', 2, NULL),
(2, NULL, 'Merci pour cet accueil, hate d\'echanger avec vous.', 'general', '2026-09-15 11:42:24', 4, 1);

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
(2, 1, '2026-09-15 11:42:24', 'acceptee');

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
  PRIMARY KEY (`id_utilisateur`,`id_evenement`),
  KEY `fk_organise_evenement` (`id_evenement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `organise_evenement`
--

INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES
(3, 1),
(4, 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_utilisateur` varchar(100) NOT NULL,
  `prenom_utilisation` varchar(100) NOT NULL,
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
  KEY `idx_utilisateur_nom` (`nom_utilisateur`,`prenom_utilisation`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id_utilisateur`, `nom_utilisateur`, `prenom_utilisation`, `mail`, `mdp`, `telephone`, `date_naissance`, `role`, `statut_validation`, `cv`, `annee_promo`, `id_formation`, `specialite`, `motif_inscription`, `id_entreprise`, `poste`, `id_gestionnaire_createur`, `date_inscription`) VALUES
(1, 'Admin', 'Root', 'admin@ecole.fr', 'hash_admin', NULL, NULL, 'gestionnaire', 'valide', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-09-15 11:42:24'),
(2, 'Martin', 'Leo', 'leo.martin@mail.fr', 'hash_leo', NULL, NULL, 'etudiant', 'valide', 'cv_leo.pdf', '2026', 1, NULL, NULL, NULL, NULL, NULL, '2026-09-15 11:42:24'),
(3, 'Durand', 'Paul', 'paul.durand@ecole.fr', 'hash_paul', NULL, NULL, 'professeur', 'valide', NULL, NULL, NULL, 'Reseaux', NULL, NULL, NULL, NULL, '2026-09-15 11:42:24'),
(4, 'Petit', 'Sarah', 'sarah.petit@mail.fr', 'hash_sarah', NULL, NULL, 'alumni', 'valide', 'cv_sarah.pdf', '2020', NULL, NULL, NULL, 1, 'Developpeuse', NULL, '2026-09-15 11:42:24'),
(5, 'Bernard', 'Julie', 'julie.bernard@acme.example', 'hash_julie', NULL, NULL, 'partenaire', 'valide', NULL, NULL, NULL, NULL, 'Recrutement de stagiaires', 1, 'Responsable RH', NULL, '2026-09-15 11:42:24');

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
