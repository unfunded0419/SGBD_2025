-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 10:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_iot`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--

CREATE TABLE `categorie` (
  `ID_Categorie` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL,
  `Valeur_critique` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`ID_Categorie`, `Nom`, `Valeur_critique`) VALUES
(1, 'Moteur', 5),
(2, 'Processeur', 3),
(3, 'Cable', 10);

-- --------------------------------------------------------

--
-- Table structure for table `concerner`
--

CREATE TABLE `concerner` (
  `ID_Emprunt` int(11) NOT NULL,
  `ID_Exemplaire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cours`
--

CREATE TABLE `cours` (
  `ID_Cours` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emprunt`
--

CREATE TABLE `emprunt` (
  `ID_Emprunt` int(11) NOT NULL,
  `Date_debut` date DEFAULT NULL,
  `Date_fin_prevue` date DEFAULT NULL,
  `Raison_Emprunt` text DEFAULT NULL,
  `Statut` enum('accepté','refusé','en attente') DEFAULT NULL,
  `Date_retour` date DEFAULT NULL,
  `Retard` tinyint(1) DEFAULT NULL,
  `Degradation_materiel` tinyint(1) DEFAULT NULL,
  `RE_Matricule` int(11) DEFAULT NULL,
  `E_Matricule` int(11) DEFAULT NULL,
  `ID_Projet` int(11) DEFAULT NULL,
  `ID_Modele_demande` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `etudiant`
--

CREATE TABLE `etudiant` (
  `E_Matricule` int(6) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL,
  `Prenom` varchar(150) DEFAULT NULL,
  `E_mail` varchar(150) DEFAULT NULL,
  `Mot_de_passe` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `etudiant`
--

INSERT INTO `etudiant` (`E_Matricule`, `Nom`, `Prenom`, `E_mail`, `Mot_de_passe`) VALUES
(242424, 'user', 'user', 'user', 'user');

-- --------------------------------------------------------

--
-- Table structure for table `exemplaire`
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
-- Dumping data for table `exemplaire`
--

INSERT INTO `exemplaire` (`ID_Exemplaire`, `Etat`, `Disponibilite`, `ID_Modele`, `Date_Retrait`, `RE_Matricule_Retrait`, `Date_ajout`, `RE_Matricule_Ajout`) VALUES
(1, 'utilisable', 'disponible', 1, NULL, NULL, NULL, NULL),
(2, 'utilisable', 'disponible', 1, NULL, NULL, NULL, NULL),
(3, 'utilisable', 'disponible', 2, NULL, NULL, NULL, NULL),
(4, 'utilisable', 'disponible', 1, NULL, NULL, NULL, NULL),
(5, 'utilisable', 'disponible', 2, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lier`
--

CREATE TABLE `lier` (
  `ID_Cours` int(11) NOT NULL,
  `ID_Projet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modele`
--

CREATE TABLE `modele` (
  `ID_Modele` int(11) NOT NULL,
  `Reference` varchar(200) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `ID_Categorie` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modele`
--

INSERT INTO `modele` (`ID_Modele`, `Reference`, `Description`, `ID_Categorie`) VALUES
(1, 'Veltor', 'Un moteur pour velo', 1),
(2, 'CPUTORNADO', 'Une CPU qui dechire', 2),
(3, 'CABLEMAG', 'Des cable enchantés', 3);

-- --------------------------------------------------------

--
-- Table structure for table `participer`
--

CREATE TABLE `participer` (
  `ID_Projet` int(11) NOT NULL,
  `E_Matricule` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `participer`
--

INSERT INTO `participer` (`ID_Projet`, `E_Matricule`) VALUES
(1, 242424);

-- --------------------------------------------------------

--
-- Table structure for table `projet`
--

CREATE TABLE `projet` (
  `ID_Projet` int(11) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `projet`
--

INSERT INTO `projet` (`ID_Projet`, `Nom`) VALUES
(1, 'blob');

-- --------------------------------------------------------

--
-- Table structure for table `reparer`
--

CREATE TABLE `reparer` (
  `RE_Matricule` int(6) NOT NULL,
  `ID_Exemplaire` int(11) NOT NULL,
  `Date_Reparation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `responsable_des_equipements`
--

CREATE TABLE `responsable_des_equipements` (
  `RE_Matricule` int(6) NOT NULL,
  `Nom` varchar(150) DEFAULT NULL,
  `Prenom` varchar(150) DEFAULT NULL,
  `E_mail` varchar(150) DEFAULT NULL,
  `Mot_de_passe` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responsable_des_equipements`
--

INSERT INTO `responsable_des_equipements` (`RE_Matricule`, `Nom`, `Prenom`, `E_mail`, `Mot_de_passe`) VALUES
(252525, 'admin', 'admin', 'admin', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorie`
--
ALTER TABLE `categorie`
  ADD PRIMARY KEY (`ID_Categorie`);

--
-- Indexes for table `concerner`
--
ALTER TABLE `concerner`
  ADD PRIMARY KEY (`ID_Emprunt`,`ID_Exemplaire`),
  ADD KEY `ID_Exemplaire` (`ID_Exemplaire`);

--
-- Indexes for table `cours`
--
ALTER TABLE `cours`
  ADD PRIMARY KEY (`ID_Cours`);

--
-- Indexes for table `emprunt`
--
ALTER TABLE `emprunt`
  ADD PRIMARY KEY (`ID_Emprunt`),
  ADD KEY `RE_Matricule` (`RE_Matricule`),
  ADD KEY `E_Matricule` (`E_Matricule`),
  ADD KEY `ID_Projet` (`ID_Projet`),
  ADD KEY `emprunt_ibfk_4` (`ID_Modele_demande`);

--
-- Indexes for table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`E_Matricule`);

--
-- Indexes for table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD PRIMARY KEY (`ID_Exemplaire`),
  ADD KEY `ID_Modele` (`ID_Modele`),
  ADD KEY `RE_Matricule_Retrait` (`RE_Matricule_Retrait`),
  ADD KEY `RE_Matricule_Ajout` (`RE_Matricule_Ajout`);

--
-- Indexes for table `lier`
--
ALTER TABLE `lier`
  ADD PRIMARY KEY (`ID_Cours`,`ID_Projet`),
  ADD KEY `ID_Projet` (`ID_Projet`);

--
-- Indexes for table `modele`
--
ALTER TABLE `modele`
  ADD PRIMARY KEY (`ID_Modele`),
  ADD KEY `ID_Categorie` (`ID_Categorie`);

--
-- Indexes for table `participer`
--
ALTER TABLE `participer`
  ADD PRIMARY KEY (`ID_Projet`,`E_Matricule`),
  ADD KEY `E_Matricule` (`E_Matricule`);

--
-- Indexes for table `projet`
--
ALTER TABLE `projet`
  ADD PRIMARY KEY (`ID_Projet`);

--
-- Indexes for table `reparer`
--
ALTER TABLE `reparer`
  ADD PRIMARY KEY (`RE_Matricule`,`ID_Exemplaire`),
  ADD KEY `ID_Exemplaire` (`ID_Exemplaire`);

--
-- Indexes for table `responsable_des_equipements`
--
ALTER TABLE `responsable_des_equipements`
  ADD PRIMARY KEY (`RE_Matricule`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categorie`
--
ALTER TABLE `categorie`
  MODIFY `ID_Categorie` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `cours`
--
ALTER TABLE `cours`
  MODIFY `ID_Cours` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emprunt`
--
ALTER TABLE `emprunt`
  MODIFY `ID_Emprunt` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exemplaire`
--
ALTER TABLE `exemplaire`
  MODIFY `ID_Exemplaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `modele`
--
ALTER TABLE `modele`
  MODIFY `ID_Modele` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projet`
--
ALTER TABLE `projet`
  MODIFY `ID_Projet` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `concerner`
--
ALTER TABLE `concerner`
  ADD CONSTRAINT `concerner_ibfk_1` FOREIGN KEY (`ID_Emprunt`) REFERENCES `emprunt` (`ID_Emprunt`),
  ADD CONSTRAINT `concerner_ibfk_2` FOREIGN KEY (`ID_Exemplaire`) REFERENCES `exemplaire` (`ID_Exemplaire`);

--
-- Constraints for table `emprunt`
--
ALTER TABLE `emprunt`
  ADD CONSTRAINT `emprunt_ibfk_1` FOREIGN KEY (`RE_Matricule`) REFERENCES `responsable_des_equipements` (`RE_Matricule`),
  ADD CONSTRAINT `emprunt_ibfk_2` FOREIGN KEY (`E_Matricule`) REFERENCES `etudiant` (`E_Matricule`),
  ADD CONSTRAINT `emprunt_ibfk_3` FOREIGN KEY (`ID_Projet`) REFERENCES `projet` (`ID_Projet`),
  ADD CONSTRAINT `emprunt_ibfk_4` FOREIGN KEY (`ID_Modele_demande`) REFERENCES `modele` (`ID_Modele`);

--
-- Constraints for table `exemplaire`
--
ALTER TABLE `exemplaire`
  ADD CONSTRAINT `exemplaire_ibfk_1` FOREIGN KEY (`ID_Modele`) REFERENCES `modele` (`ID_Modele`),
  ADD CONSTRAINT `exemplaire_ibfk_2` FOREIGN KEY (`RE_Matricule_Retrait`) REFERENCES `responsable_des_equipements` (`RE_Matricule`),
  ADD CONSTRAINT `exemplaire_ibfk_3` FOREIGN KEY (`RE_Matricule_Ajout`) REFERENCES `responsable_des_equipements` (`RE_Matricule`);

--
-- Constraints for table `lier`
--
ALTER TABLE `lier`
  ADD CONSTRAINT `lier_ibfk_1` FOREIGN KEY (`ID_Cours`) REFERENCES `cours` (`ID_Cours`),
  ADD CONSTRAINT `lier_ibfk_2` FOREIGN KEY (`ID_Projet`) REFERENCES `projet` (`ID_Projet`);

--
-- Constraints for table `modele`
--
ALTER TABLE `modele`
  ADD CONSTRAINT `modele_ibfk_1` FOREIGN KEY (`ID_Categorie`) REFERENCES `categorie` (`ID_Categorie`);

--
-- Constraints for table `participer`
--
ALTER TABLE `participer`
  ADD CONSTRAINT `participer_ibfk_1` FOREIGN KEY (`ID_Projet`) REFERENCES `projet` (`ID_Projet`),
  ADD CONSTRAINT `participer_ibfk_2` FOREIGN KEY (`E_Matricule`) REFERENCES `etudiant` (`E_Matricule`);

--
-- Constraints for table `reparer`
--
ALTER TABLE `reparer`
  ADD CONSTRAINT `reparer_ibfk_1` FOREIGN KEY (`RE_Matricule`) REFERENCES `responsable_des_equipements` (`RE_Matricule`),
  ADD CONSTRAINT `reparer_ibfk_2` FOREIGN KEY (`ID_Exemplaire`) REFERENCES `exemplaire` (`ID_Exemplaire`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
