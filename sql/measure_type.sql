-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  mar. 16 fév. 2021 à 15:58
-- Version du serveur :  10.3.16-MariaDB
-- Version de PHP :  7.1.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gaia`
--

-- --------------------------------------------------------

--
-- Structure de la table `measure_type`
--

CREATE TABLE `measure_type` (
  `id_measure_type` int(10) NOT NULL,
  `measure_type` varchar(50) DEFAULT NULL,
  `unity` varchar(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `measure_type`
--

INSERT INTO `measure_type` (`id_measure_type`, `measure_type`, `unity`) VALUES
(1, 'Gaia Atmospheric Pressure', 'mbar'),
(2, 'Service Atmospheric Pressure', 'mbar'),
(3, 'Pump Speed Stability', 'rpm'),
(4, 'Gaia Overpressure I', 'mbar'),
(5, 'Service Overpressure I', 'mbar'),
(6, 'Pressure Compensation', 'mbar'),
(7, 'Reactor Temperature', '°C'),
(8, 'Service Temperature', '°C'),
(9, ' ', ' ');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `measure_type`
--
ALTER TABLE `measure_type`
  ADD PRIMARY KEY (`id_measure_type`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `measure_type`
--
ALTER TABLE `measure_type`
  MODIFY `id_measure_type` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
