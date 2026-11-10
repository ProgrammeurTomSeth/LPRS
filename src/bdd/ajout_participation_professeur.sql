-- ============================================================
-- Mise à jour de la base : accord du professeur
--
-- Quand un étudiant crée un événement, chaque professeur qu'il
-- ajoute comme organisateur doit accepter avant que le
-- gestionnaire puisse publier l'événement.
--
-- Nouvelle colonne statut_participation dans organise_evenement :
--   'en_attente' = le professeur n'a pas encore répondu
--   'acceptee'   = il a accepté (valeur par défaut, donc les
--                  organisateurs déjà présents restent valides)
--   'refusee'    = il a refusé
--
-- À exécuter UNE SEULE FOIS dans l'onglet SQL de phpMyAdmin.
-- ============================================================

ALTER TABLE `organise_evenement`
  ADD `statut_participation` enum('en_attente','acceptee','refusee') NOT NULL DEFAULT 'acceptee';
