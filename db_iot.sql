-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 30 déc. 2025 à 06:38
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_iot`
--

-- --------------------------------------------------------

--
-- Structure de la table `categorie`
--

CREATE TABLE `categorie` (
  `ID_Categorie` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL,
  `Valeur_critique` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorie`
--

INSERT INTO `categorie` (`ID_Categorie`, `Nom`, `Valeur_critique`) VALUES
(1, 'Moteur', 5),
(2, 'Processeur', 3),
(3, 'Cable', 10),
(4, 'nouveau', 5),
(5, 'noubi', 5);

-- --------------------------------------------------------

--
-- Structure de la table `concerner`
--

CREATE TABLE `concerner` (
  `ID_Emprunt` int(11) NOT NULL,
  `ID_Exemplaire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `concerner`
--

INSERT INTO `concerner` (`ID_Emprunt`, `ID_Exemplaire`) VALUES
(31, 3),
(32, 5);

-- --------------------------------------------------------

--
-- Structure de la table `cours`
--

CREATE TABLE `cours` (
  `ID_Cours` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `cours`
--

INSERT INTO `cours` (`ID_Cours`, `Nom`) VALUES
(1, 'thermique');

-- --------------------------------------------------------

--
-- Structure de la table `emprunt`
--

CREATE TABLE `emprunt` (
  `ID_Emprunt` int(11) NOT NULL,
  `Date_debut` date DEFAULT NULL,
  `Date_fin_prevue` date DEFAULT NULL,
  `Raison_Emprunt` text DEFAULT NULL,
  `Statut` enum('en_attente','refuse','approuve') DEFAULT NULL,
  `Date_retour` date DEFAULT NULL,
  `Retard` tinyint(1) DEFAULT NULL,
  `Degradation_materiel` tinyint(1) DEFAULT NULL,
  `RE_Matricule` int(11) DEFAULT NULL,
  `E_Matricule` int(11) DEFAULT NULL,
  `ID_Projet` int(11) DEFAULT NULL,
  `ID_Modele_demande` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `emprunt`
--

INSERT INTO `emprunt` (`ID_Emprunt`, `Date_debut`, `Date_fin_prevue`, `Raison_Emprunt`, `Statut`, `Date_retour`, `Retard`, `Degradation_materiel`, `RE_Matricule`, `E_Matricule`, `ID_Projet`, `ID_Modele_demande`) VALUES
(31, '2025-12-20', '2025-12-21', 'oekdoed', 'approuve', '2025-12-29', 1, 1, 240227, 240227, 10, 2),
(32, '2025-12-20', '2025-12-21', 'oekdoe', 'approuve', '2025-12-29', 1, 0, 240227, 240227, 9, 2),
(33, '2025-12-21', '2025-12-22', 'fde', 'en_attente', NULL, NULL, NULL, NULL, 240227, 9, 2),
(34, '2025-12-21', '2025-12-22', 'szsad', 'en_attente', NULL, NULL, NULL, NULL, 240227, 9, 2),
(35, '2025-12-21', '2025-12-22', 'adsaa', 'en_attente', NULL, NULL, NULL, NULL, 240227, 10, 2),
(36, '2025-12-26', '2025-12-27', 'dse', 'en_attente', NULL, NULL, NULL, NULL, 111111, 10, 2),
(37, '2025-12-26', '2025-12-27', 'dsaq', 'en_attente', NULL, NULL, NULL, NULL, 111111, 10, 2),
(38, '2025-12-26', '2025-12-28', 'Pas de raison', 'en_attente', NULL, NULL, NULL, NULL, 240227, 17, 4);

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `E_Matricule` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL,
  `Prenom` varchar(150) DEFAULT NULL,
  `E_mail` varchar(150) DEFAULT NULL,
  `Mot_de_passe` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`E_Matricule`, `Nom`, `Prenom`, `E_mail`, `Mot_de_passe`) VALUES
(111111, 'E', 'F', 'gwendehon4@gmail.com', '$2y$10$JKrrXiprBYBRX2ulOQ2tiOLwI9ljw37dKzAH4kvM0fTqLa/IOJGZq'),
(222222, 'D', 'e', 'gwendehon4@gmail.com', '$2y$10$0H0XI1GwknGbGqcPpL4an.8hXi0/wWNP8CfzbuL1Qi9G285QmyIsG'),
(240227, 'E', 'F', 'gwendehon4@gmail.com', '$2y$10$MigwJ5h..YR1GaZ9vB1OS./qezy2BNggGeRO6sCJ/N3kGEA9s.afW');

-- --------------------------------------------------------

--
-- Structure de la table `exemplaire`
--

CREATE TABLE `exemplaire` (
  `ID_Exemplaire` int(11) NOT NULL,
  `Etat` enum('endommage','utilisable') DEFAULT NULL,
  `Disponibilite` enum('disponible','indisponible') DEFAULT NULL,
  `ID_Modele` int(11) DEFAULT NULL,
  `Date_Retrait` date DEFAULT NULL,
  `RE_Matricule_Retrait` int(11) DEFAULT NULL,
  `Date_ajout` date DEFAULT NULL,
  `RE_Matricule_Ajout` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `exemplaire`
--

INSERT INTO `exemplaire` (`ID_Exemplaire`, `Etat`, `Disponibilite`, `ID_Modele`, `Date_Retrait`, `RE_Matricule_Retrait`, `Date_ajout`, `RE_Matricule_Ajout`) VALUES
(1, 'utilisable', 'disponible', 1, NULL, NULL, NULL, NULL),
(2, 'utilisable', 'indisponible', 1, NULL, NULL, NULL, NULL),
(3, 'utilisable', 'indisponible', 2, NULL, NULL, NULL, NULL),
(4, 'utilisable', 'disponible', 1, NULL, NULL, NULL, NULL),
(5, 'utilisable', 'disponible', 2, NULL, NULL, NULL, NULL),
(6, 'utilisable', 'disponible', 1, NULL, NULL, '2025-12-23', 240227),
(7, 'utilisable', 'disponible', 4, NULL, NULL, '2025-12-23', 240227),
(8, 'utilisable', 'disponible', 4, NULL, NULL, '2025-12-23', 240227),
(9, 'utilisable', 'disponible', 5, NULL, NULL, '2025-12-23', 240227),
(10, 'utilisable', 'disponible', 5, NULL, NULL, '2025-12-23', 240227),
(11, 'utilisable', 'disponible', 5, NULL, NULL, '2025-12-23', 240227),
(12, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(13, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(14, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(15, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(16, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(17, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(18, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(19, 'utilisable', 'disponible', 6, NULL, NULL, '2025-12-30', 240227),
(20, 'endommage', 'indisponible', 6, '2025-12-30', 240227, '2025-12-30', 240227),
(21, 'endommage', 'indisponible', 6, '2025-12-30', 240227, '2025-12-30', 240227);

-- --------------------------------------------------------

--
-- Structure de la table `lier`
--

CREATE TABLE `lier` (
  `ID_Cours` int(11) NOT NULL,
  `ID_Projet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `modele`
--

CREATE TABLE `modele` (
  `ID_Modele` int(11) NOT NULL,
  `Reference` varchar(200) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `ID_Categorie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `modele`
--

INSERT INTO `modele` (`ID_Modele`, `Reference`, `Description`, `ID_Categorie`) VALUES
(1, 'Veltor', 'Un moteur pour velo', 1),
(2, 'CPUTORNADO', 'Une CPU qui dechire', 2),
(3, 'CABLEMAG', 'Des cable enchantés', 3),
(4, 'Montont', 'Montent le matin verdoyant', 4),
(5, 'galaxy', 'beautiful stars in the sky', 5),
(6, 'Test', 'testons', 1);

-- --------------------------------------------------------

--
-- Structure de la table `participer`
--

CREATE TABLE `participer` (
  `ID_Projet` int(11) NOT NULL,
  `E_Matricule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `participer`
--

INSERT INTO `participer` (`ID_Projet`, `E_Matricule`) VALUES
(9, 111111),
(9, 240227),
(10, 111111),
(10, 240227),
(13, 240227),
(15, 240227),
(16, 240227),
(17, 111111),
(17, 240227),
(18, 240227),
(19, 240227),
(21, 240227);

-- --------------------------------------------------------

--
-- Structure de la table `projet`
--

CREATE TABLE `projet` (
  `ID_Projet` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL,
  `ID_Cours` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `projet`
--

INSERT INTO `projet` (`ID_Projet`, `Nom`, `ID_Cours`) VALUES
(9, 'projet_1', NULL),
(10, 'projet_2', NULL),
(11, 'ofeofh', NULL),
(12, 'feojf', NULL),
(13, 'new', NULL),
(15, 'nouveau_proj', NULL),
(16, 'encore', NULL),
(17, 'autre', NULL),
(18, 'Projet du cours de thermique', 1),
(19, 'Projet convection', 1),
(21, 'Projet Rayonnement', 1);

-- --------------------------------------------------------

--
-- Structure de la table `reparer`
--

CREATE TABLE `reparer` (
  `RE_Matricule` int(11) NOT NULL,
  `ID_Exemplaire` int(11) NOT NULL,
  `Date_Reparation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reparer`
--

INSERT INTO `reparer` (`RE_Matricule`, `ID_Exemplaire`, `Date_Reparation`) VALUES
(240227, 1, '2025-12-30'),
(240227, 2, '2025-12-30'),
(240227, 3, '2025-12-30'),
(240227, 9, '2025-12-26'),
(240227, 11, '2025-12-26'),
(240227, 18, '2025-12-30'),
(240227, 19, '2025-12-30');

-- --------------------------------------------------------

--
-- Structure de la table `responsable_des_equipements`
--

CREATE TABLE `responsable_des_equipements` (
  `RE_Matricule` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL,
  `Prenom` varchar(150) DEFAULT NULL,
  `E_mail` varchar(150) DEFAULT NULL,
  `Mot_de_passe` varchar(200) DEFAULT NULL,
  `Statut` enum('en_attente','refuse','approuve') NOT NULL,
  `admin` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `responsable_des_equipements`
--

INSERT INTO `responsable_des_equipements` (`RE_Matricule`, `Nom`, `Prenom`, `E_mail`, `Mot_de_passe`, `Statut`, `admin`) VALUES
(111111, 'D', 'T', 'gwendehon4@gmail.com', '$2y$10$j1uQa5UFLXQ4GLoQaZcw2O5csUwWkmpgwer1EKyYqSH9CbOPkpFSm', 'approuve', 0),
(240227, 'E', 'F', 'gwendehon4@gmail.com', '$2y$10$GXU8cxBWxCxSDoeEX3p1juvzWns9W98JfW..Y1oeOrGsU.1R.4URe', 'approuve', 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`ID_Categorie`);

--
-- Index pour la table `concerner`
--
ALTER TABLE `concerner`
  ADD PRIMARY KEY (`ID_Emprunt`,`ID_Exemplaire`),
  ADD KEY `ID_Exemplaire` (`ID_Exemplaire`);

--
-- Index pour la table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`ID_Cours`);

--
-- Index pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD PRIMARY KEY (`ID_Emprunt`),
  ADD KEY `RE_Matricule` (`RE_Matricule`),
  ADD KEY `E_Matricule` (`E_Matricule`),
  ADD KEY `ID_Projet` (`ID_Projet`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`E_Matricule`);

--
-- Index pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD PRIMARY KEY (`ID_Exemplaire`),
  ADD KEY `ID_Modele` (`ID_Modele`),
  ADD KEY `RE_Matricule_Retrait` (`RE_Matricule_Retrait`),
  ADD KEY `RE_Matricule_Ajout` (`RE_Matricule_Ajout`);

--
-- Index pour la table `lier`
--
ALTER TABLE `lier`
  ADD PRIMARY KEY (`ID_Cours`,`ID_Projet`),
  ADD KEY `ID_Projet` (`ID_Projet`);

--
-- Index pour la table `modele`
--
ALTER TABLE `modele`
  ADD PRIMARY KEY (`ID_Modele`),
  ADD KEY `ID_Categorie` (`ID_Categorie`);

--
-- Index pour la table `participer`
--
ALTER TABLE `participer`
  ADD PRIMARY KEY (`ID_Projet`,`E_Matricule`),
  ADD KEY `E_Matricule` (`E_Matricule`);

--
-- Index pour la table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`ID_Projet`),
  ADD KEY `fk_projet_cours` (`ID_Cours`);

--
-- Index pour la table `reparer`
--
ALTER TABLE `reparer`
  ADD PRIMARY KEY (`RE_Matricule`,`ID_Exemplaire`),
  ADD KEY `ID_Exemplaire` (`ID_Exemplaire`);

--
-- Index pour la table `responsable_des_equipements`
--
ALTER TABLE `responsable_des_equipements`
  ADD PRIMARY KEY (`RE_Matricule`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `ID_Categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `cours`
--
ALTER TABLE `cours`
  MODIFY `ID_Cours` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `emprunt`
--
ALTER TABLE `emprunt`
  MODIFY `ID_Emprunt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  MODIFY `ID_Exemplaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `modele`
--
ALTER TABLE `modele`
  MODIFY `ID_Modele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `projet`
--
ALTER TABLE `projet`
  MODIFY `ID_Projet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `concerner`
--
ALTER TABLE `concerner`
  ADD CONSTRAINT `concerner_ibfk_1` FOREIGN KEY (`ID_Emprunt`) REFERENCES `emprunt` (`ID_Emprunt`),
  ADD CONSTRAINT `concerner_ibfk_2` FOREIGN KEY (`ID_Exemplaire`) REFERENCES `exemplaire` (`ID_Exemplaire`);

--
-- Contraintes pour la table `emprunt`
--
ALTER TABLE `emprunt`
  ADD CONSTRAINT `emprunt_ibfk_1` FOREIGN KEY (`RE_Matricule`) REFERENCES `responsable_des_equipements` (`RE_Matricule`),
  ADD CONSTRAINT `emprunt_ibfk_2` FOREIGN KEY (`E_Matricule`) REFERENCES `etudiant` (`E_Matricule`),
  ADD CONSTRAINT `emprunt_ibfk_3` FOREIGN KEY (`ID_Projet`) REFERENCES `projet` (`ID_Projet`);

--
-- Contraintes pour la table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD CONSTRAINT `exemplaire_ibfk_1` FOREIGN KEY (`ID_Modele`) REFERENCES `modele` (`ID_Modele`),
  ADD CONSTRAINT `exemplaire_ibfk_2` FOREIGN KEY (`RE_Matricule_Retrait`) REFERENCES `responsable_des_equipements` (`RE_Matricule`),
  ADD CONSTRAINT `exemplaire_ibfk_3` FOREIGN KEY (`RE_Matricule_Ajout`) REFERENCES `responsable_des_equipements` (`RE_Matricule`);

--
-- Contraintes pour la table `lier`
--
ALTER TABLE `lier`
  ADD CONSTRAINT `lier_ibfk_1` FOREIGN KEY (`ID_Cours`) REFERENCES `cours` (`ID_Cours`),
  ADD CONSTRAINT `lier_ibfk_2` FOREIGN KEY (`ID_Projet`) REFERENCES `projet` (`ID_Projet`);

--
-- Contraintes pour la table `modele`
--
ALTER TABLE `modele`
  ADD CONSTRAINT `modele_ibfk_1` FOREIGN KEY (`ID_Categorie`) REFERENCES `categorie` (`ID_Categorie`);

--
-- Contraintes pour la table `participer`
--
ALTER TABLE `participer`
  ADD CONSTRAINT `participer_ibfk_1` FOREIGN KEY (`ID_Projet`) REFERENCES `projet` (`ID_Projet`),
  ADD CONSTRAINT `participer_ibfk_2` FOREIGN KEY (`E_Matricule`) REFERENCES `etudiant` (`E_Matricule`);

--
-- Contraintes pour la table `projet`
--
ALTER TABLE `projet`
  ADD CONSTRAINT `fk_projet_cours` FOREIGN KEY (`ID_Cours`) REFERENCES `cours` (`ID_Cours`);

--
-- Contraintes pour la table `reparer`
--
ALTER TABLE `reparer`
  ADD CONSTRAINT `reparer_ibfk_1` FOREIGN KEY (`RE_Matricule`) REFERENCES `responsable_des_equipements` (`RE_Matricule`),
  ADD CONSTRAINT `reparer_ibfk_2` FOREIGN KEY (`ID_Exemplaire`) REFERENCES `exemplaire` (`ID_Exemplaire`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
