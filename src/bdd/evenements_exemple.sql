-- ============================================================
-- Événements d'exemple pour la base LPRS
-- À importer dans phpMyAdmin (onglet "Importer") ou à coller
-- dans l'onglet "SQL", après avoir importé lprs.sql.
--
-- Organisateurs utilisés (utilisateurs de lprs.sql) :
--   2 = Leo Martin (étudiant)
--   3 = Paul Durand (professeur)
--   4 = Sarah Petit (alumni)
--   5 = Julie Bernard (partenaire)
--
-- LAST_INSERT_ID() renvoie l'id de l'événement qu'on vient
-- d'insérer : on s'en sert pour remplir organise_evenement.
-- ============================================================

SET NAMES utf8mb4;

-- ----- Événements publiés (à venir) -----

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Conférence', 'La cybersécurité en entreprise', 'Un expert vient présenter les attaques les plus courantes et les bonnes pratiques pour s''en protéger.', 'Amphi B', 'Aucun', 120, '2026-11-20 14:00:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Atelier', 'Atelier CV et lettre de motivation', 'Des alumni relisent vos CV et vous donnent des conseils pour rédiger une lettre de motivation efficace.', 'Salle 204', 'Apporter son CV imprimé', 20, '2026-11-25 10:00:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (4, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Forum', 'Forum des entreprises', 'Rencontrez les entreprises partenaires qui recherchent des stagiaires et des alternants.', 'Gymnase du lycée', 'CV en plusieurs exemplaires', 200, '2026-12-03 09:00:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (5, LAST_INSERT_ID());
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Atelier', 'Initiation à Git et GitHub', 'Apprenez à versionner votre code et à travailler en équipe sur un projet.', 'Salle informatique 102', 'Ordinateur portable', 25, '2026-12-10 13:30:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Tournoi', 'Tournoi de jeux vidéo', 'Tournoi amical entre étudiants pour finir le trimestre dans la bonne humeur.', 'Foyer des étudiants', 'Aucun', 32, '2026-12-18 16:00:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (2, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Portes ouvertes', 'Journée portes ouvertes', 'Présentation des formations du lycée aux futurs élèves et à leurs parents.', 'Hall principal', NULL, NULL, '2027-01-24 09:00:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Rencontre', 'Témoignages d''anciens élèves', 'Des alumni racontent leur parcours après le BTS : poursuite d''études, premier emploi, création d''entreprise.', 'Amphi A', 'Aucun', 80, '2027-02-05 17:30:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (4, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Visite', 'Visite d''un datacenter', 'Visite guidée du datacenter d''une entreprise partenaire. Départ en car devant le lycée.', 'Départ : parking du lycée', 'Pièce d''identité obligatoire', 30, '2027-03-12 08:30:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (5, LAST_INSERT_ID());

-- ----- Événements passés (déjà publiés) -----

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Rencontre', 'Petit-déjeuner de rentrée', 'Moment convivial pour accueillir les nouveaux étudiants.', 'Cafétéria', NULL, 60, '2026-09-08 08:00:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Hackathon', 'Hackathon 24h', 'Par équipes de 4, développez une application en 24 heures sur un thème imposé.', 'Salles informatiques 101 et 102', 'Ordinateur portable, sac de couchage', 40, '2026-10-16 18:00:00', 'publie');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (2, LAST_INSERT_ID());
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());

-- ----- Événements en attente de validation par un gestionnaire -----

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Atelier', 'Préparer un entretien d''embauche', 'Simulations d''entretiens avec des recruteurs et conseils personnalisés.', 'Salle 210', 'Tenue correcte', 15, '2027-01-14 14:00:00', 'en_attente');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (5, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Sortie', 'Sortie au salon VivaTech', 'Visite du salon de l''innovation et des startups à Paris.', 'Paris Expo Porte de Versailles', 'Autorisation parentale pour les mineurs', 45, '2027-06-11 07:30:00', 'en_attente');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Club', 'Club de programmation', 'Séance hebdomadaire pour s''entraîner aux exercices d''algorithmique.', 'Salle informatique 103', 'Ordinateur portable', 20, '2026-12-02 12:30:00', 'en_attente');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (2, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Conférence', 'Le métier de développeur web', 'Une alumni présente son quotidien de développeuse et les technologies qu''elle utilise.', 'Amphi B', 'Aucun', 100, '2027-02-18 15:00:00', 'en_attente');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (4, LAST_INSERT_ID());

-- ----- Événements refusés -----

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Soirée', 'Soirée de fin d''année', 'Soirée dansante pour fêter la fin de l''année scolaire.', 'Foyer des étudiants', NULL, 150, '2027-06-30 20:00:00', 'refuse');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (2, LAST_INSERT_ID());

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Rencontre', 'Afterwork entreprises', 'Rencontre informelle entre partenaires et alumni après le travail.', 'Bar à proximité du lycée', NULL, 40, '2027-01-29 19:00:00', 'refuse');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (5, LAST_INSERT_ID());

-- ----- Brouillon -----

INSERT INTO `evenement` (`type_evenement`, `titre`, `description`, `lieu`, `elements_requis`, `nb_places`, `date_evenement`, `statut`) VALUES
('Atelier', 'Atelier Linux pour débutants', 'Découverte du terminal et des commandes de base sous Linux.', 'Salle informatique 101', 'Ordinateur portable', 20, '2027-03-04 10:00:00', 'brouillon');
INSERT INTO `organise_evenement` (`id_utilisateur`, `id_evenement`) VALUES (3, LAST_INSERT_ID());
