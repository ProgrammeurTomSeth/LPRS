-- =========================================================
-- Mise à jour de la base lprs : signalements du forum
-- À importer UNE fois dans phpMyAdmin (onglet Importer) sur une base
-- déjà créée avec lprs.sql. (lprs.sql contient déjà cette table
-- pour une installation neuve.)
-- =========================================================

-- Un signalement = un utilisateur signale un message du forum (post ou réponse)
-- au gestionnaire, avec un motif et un commentaire facultatif.
CREATE TABLE IF NOT EXISTS `signalement_forum` (
  `id_signalement` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_forum` int UNSIGNED NOT NULL,
  `id_utilisateur` int UNSIGNED NOT NULL,
  `motif` enum('spam','insulte','donnees_personnelles','hors_sujet','autre') NOT NULL,
  `commentaire` varchar(500) DEFAULT NULL,
  `date_signalement` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_signalement`),
  -- un utilisateur ne peut signaler le même message qu'une seule fois
  UNIQUE KEY `uq_signalement_message_utilisateur` (`id_forum`,`id_utilisateur`),
  KEY `fk_signalement_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Si le message est supprimé par le gestionnaire, ses signalements
-- disparaissent avec lui (ON DELETE CASCADE)
ALTER TABLE `signalement_forum`
  ADD CONSTRAINT `fk_signalement_forum` FOREIGN KEY (`id_forum`) REFERENCES `forum` (`id_forum`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_signalement_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateur` (`id_utilisateur`) ON DELETE CASCADE ON UPDATE CASCADE;
