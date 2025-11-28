-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 28, 2025 at 12:15 AM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_IOT`
--

-- --------------------------------------------------------

--
-- Table structure for table `categorie`
--


--
-- Dumping data for table `categorie`
--

INSERT INTO `categorie` (`ID_Categorie`, `Nom`, `Valeur_critique`) VALUES
(1, 'Ordinateur', 5),
(2, 'Écran', 3),
(3, 'Casque', 2);

-- --------------------------------------------------------

--
-- Table structure for table `concerner`
--


--
-- Dumping data for table `concerner`
--

INSERT INTO `concerner` (`ID_Emprunt`, `ID_Exemplaire`) VALUES
(1, 1),
(2, 3),
(3, 4);

-- --------------------------------------------------------

--
-- Table structure for table `cours`
--



-- --------------------------------------------------------

--
-- Table structure for table `emprunt`
--


--
-- Dumping data for table `emprunt`
--

INSERT INTO `emprunt` (`ID_Emprunt`, `Date_debut`, `Date_fin_prevue`, `Raison_Emprunt`, `Statut`, `Date_retour`, `Retard`, `Degradation_materiel`, `RE_Matricule`, `E_Matricule`, `ID_Projet`) VALUES
(1, '2024-03-01', '2024-03-15', 'Projet de labo de programmation en C.', 'en attente', NULL, 0, 0, 1, 1001, NULL),
(2, '2024-03-05', '2024-03-20', 'Présentation pour un séminaire.', 'en attente', NULL, 0, 0, 2, 1002, NULL),
(3, '2024-03-10', '2024-03-12', 'Utilisation pour enregistrement audio dans un projet perso.', 'en attente', NULL, 0, 0, 1, 1003, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `etudiant`
--



--
-- Dumping data for table `etudiant`
--

INSERT INTO `etudiant` (`E_Matricule`, `Nom`, `Prenom`, `E_mail`, `Mot_de_passe`) VALUES
(1001, 'Dupont', 'Jean', 'jean.dupont@umons.be', 'mdpJean'),
(1002, 'Lambert', 'Marie', 'marie.lambert@umons.be', 'mdpMarie'),
(1003, 'Martin', 'Lucas', 'lucas.martin@umons.be', 'mdpLucas');

-- --------------------------------------------------------

--
-- Table structure for table `exemplaire`
--


--
-- Dumping data for table `exemplaire`
--

INSERT INTO `exemplaire` (`ID_Exemplaire`, `Etat`, `Disponibilite`, `ID_Modele`, `Date_Retrait`, `RE_Matricule_Retrait`, `Date_ajout`, `RE_Matricule_Ajout`) VALUES
(1, 'utilisable', 'disponible', 1, NULL, NULL, '2024-01-10', 1),
(2, 'utilisable', 'disponible', 1, NULL, NULL, '2024-01-15', 1),
(3, 'utilisable', 'disponible', 3, NULL, NULL, '2024-02-01', 2),
(4, 'utilisable', 'disponible', 4, NULL, NULL, '2024-02-10', 2);

-- --------------------------------------------------------

--
-- Table structure for table `lier`
--



-- --------------------------------------------------------

--
-- Table structure for table `modele`
--



--
-- Dumping data for table `modele`
--

INSERT INTO `modele` (`ID_Modele`, `Reference`, `Description`, `ID_Categorie`) VALUES
(1, 'HP ProBook 450', 'PC portable HP pour bureautique', 1),
(2, 'Dell Latitude 5400', 'PC portable Dell pour développement', 1),
(3, 'Samsung 24\" FHD', 'Écran 24 pouces Full HD', 2),
(4, 'Logitech H390', 'Casque USB avec micro', 3);

-- --------------------------------------------------------

--
-- Table structure for table `participer`
--



-- --------------------------------------------------------

--
-- Table structure for table `projet`
--



-- --------------------------------------------------------

--
-- Table structure for table `reparer`
--



-- --------------------------------------------------------

--
-- Table structure for table `responsable_des_equipements`
--



--
-- Dumping data for table `responsable_des_equipements`
--

INSERT INTO `responsable_des_equipements` (`RE_Matricule`, `Nom`, `Prenom`, `E_mail`, `Mot_de_passe`) VALUES
(1, 'Durand', 'Alice', 'alice.durand@umons.be', 'mdpAlice'),
(2, 'Bernard', 'Paul', 'paul.bernard@umons.be', 'mdpPaul');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categorie`
--

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
