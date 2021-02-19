-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  mar. 16 fév. 2021 à 17:01
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
(9, ' ', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `method_demo`
--

CREATE TABLE `method_demo` (
  `id_method_demo` int(10) NOT NULL,
  `A1` int(10) DEFAULT NULL,
  `A2` int(10) DEFAULT NULL,
  `A3` int(10) DEFAULT NULL,
  `A4` int(10) DEFAULT NULL,
  `A5` int(10) DEFAULT NULL,
  `B1` int(10) DEFAULT NULL,
  `B2` int(10) DEFAULT NULL,
  `B3` int(10) DEFAULT NULL,
  `B4` int(10) DEFAULT NULL,
  `B5` int(10) DEFAULT NULL,
  `C1` int(10) DEFAULT NULL,
  `C2` int(10) DEFAULT NULL,
  `C3` int(10) DEFAULT NULL,
  `C4` int(10) DEFAULT NULL,
  `C5` int(10) DEFAULT NULL,
  `pump` int(10) DEFAULT NULL,
  `oven` int(10) DEFAULT NULL,
  `lifter` float DEFAULT NULL,
  `id_waiting_condition` int(10) DEFAULT NULL,
  `waiting_period` int(10) DEFAULT NULL,
  `measure` float DEFAULT NULL,
  `id_measure_type` int(10) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `id_method_name` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `method_name`
--

CREATE TABLE `method_name` (
  `id_method_name` int(10) NOT NULL,
  `method_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `method_name`
--

INSERT INTO `method_name` (`id_method_name`, `method_name`) VALUES
(1, 'pr_test222');

-- --------------------------------------------------------

--
-- Structure de la table `pr_test221`
--

CREATE TABLE `pr_test221` (
  `id_method_demo` int(10) NOT NULL,
  `A1` int(10) DEFAULT NULL,
  `A2` int(10) DEFAULT NULL,
  `A3` int(10) DEFAULT NULL,
  `A4` int(10) DEFAULT NULL,
  `A5` int(10) DEFAULT NULL,
  `B1` int(10) DEFAULT NULL,
  `B2` int(10) DEFAULT NULL,
  `B3` int(10) DEFAULT NULL,
  `B4` int(10) DEFAULT NULL,
  `B5` int(10) DEFAULT NULL,
  `C1` int(10) DEFAULT NULL,
  `C2` int(10) DEFAULT NULL,
  `C3` int(10) DEFAULT NULL,
  `C4` int(10) DEFAULT NULL,
  `C5` int(10) DEFAULT NULL,
  `pump` int(10) DEFAULT NULL,
  `oven` int(10) DEFAULT NULL,
  `lifter` float DEFAULT NULL,
  `id_waiting_condition` int(10) DEFAULT NULL,
  `waiting_period` int(10) DEFAULT NULL,
  `measure` float DEFAULT NULL,
  `id_measure_type` int(10) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `pr_test222`
--

CREATE TABLE `pr_test222` (
  `id_method` int(10) NOT NULL,
  `A1` int(10) DEFAULT NULL,
  `A2` int(10) DEFAULT NULL,
  `A3` int(10) DEFAULT NULL,
  `A4` int(10) DEFAULT NULL,
  `A5` int(10) DEFAULT NULL,
  `B1` int(10) DEFAULT NULL,
  `B2` int(10) DEFAULT NULL,
  `B3` int(10) DEFAULT NULL,
  `B4` int(10) DEFAULT NULL,
  `B5` int(10) DEFAULT NULL,
  `C1` int(10) DEFAULT NULL,
  `C2` int(10) DEFAULT NULL,
  `C3` int(10) DEFAULT NULL,
  `C4` int(10) DEFAULT NULL,
  `C5` int(10) DEFAULT NULL,
  `pump` int(10) DEFAULT NULL,
  `oven` int(10) DEFAULT NULL,
  `lifter` float DEFAULT NULL,
  `id_waiting_condition` int(10) DEFAULT NULL,
  `waiting_period` int(10) DEFAULT NULL,
  `measure` float DEFAULT NULL,
  `id_measure_type` int(10) DEFAULT NULL,
  `description` varchar(50) DEFAULT NULL,
  `id_method_name` int(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `pr_test222`
--

INSERT INTO `pr_test222` (`id_method`, `A1`, `A2`, `A3`, `A4`, `A5`, `B1`, `B2`, `B3`, `B4`, `B5`, `C1`, `C2`, `C3`, `C4`, `C5`, `pump`, `oven`, `lifter`, `id_waiting_condition`, `waiting_period`, `measure`, `id_measure_type`, `description`, `id_method_name`) VALUES
(4, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 3, 3, 0, 0, 9, 'initial State', 1),
(5, 0, 1, 0, 0, 0, 0, 0, 0, 0, 3, 3, 1, 0, 0, 3, 250, 0, 90, 1, 60, 0, 9, 'Fill HPLC loop with Standard NH4+', 1);

-- --------------------------------------------------------

--
-- Structure de la table `waiting_condition`
--

CREATE TABLE `waiting_condition` (
  `id_waiting_condition` int(10) NOT NULL,
  `waiting_condition` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `waiting_condition`
--

INSERT INTO `waiting_condition` (`id_waiting_condition`, `waiting_condition`) VALUES
(1, 'Delai fixé'),
(2, 'Demande utilisateur'),
(3, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `measure_type`
--
ALTER TABLE `measure_type`
  ADD PRIMARY KEY (`id_measure_type`);

--
-- Index pour la table `method_demo`
--
ALTER TABLE `method_demo`
  ADD PRIMARY KEY (`id_method_demo`),
  ADD KEY `id_waiting_condition` (`id_waiting_condition`),
  ADD KEY `id_measure_type` (`id_measure_type`),
  ADD KEY `id_method_name` (`id_method_name`);

--
-- Index pour la table `method_name`
--
ALTER TABLE `method_name`
  ADD PRIMARY KEY (`id_method_name`);

--
-- Index pour la table `pr_test221`
--
ALTER TABLE `pr_test221`
  ADD PRIMARY KEY (`id_method_demo`),
  ADD KEY `id_waiting_condition` (`id_waiting_condition`),
  ADD KEY `id_measure_type` (`id_measure_type`);

--
-- Index pour la table `pr_test222`
--
ALTER TABLE `pr_test222`
  ADD PRIMARY KEY (`id_method`),
  ADD KEY `id_waiting_condition` (`id_waiting_condition`),
  ADD KEY `id_measure_type` (`id_measure_type`),
  ADD KEY `id_method_name` (`id_method_name`);

--
-- Index pour la table `waiting_condition`
--
ALTER TABLE `waiting_condition`
  ADD PRIMARY KEY (`id_waiting_condition`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `measure_type`
--
ALTER TABLE `measure_type`
  MODIFY `id_measure_type` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `method_demo`
--
ALTER TABLE `method_demo`
  MODIFY `id_method_demo` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `method_name`
--
ALTER TABLE `method_name`
  MODIFY `id_method_name` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `pr_test221`
--
ALTER TABLE `pr_test221`
  MODIFY `id_method_demo` int(10) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pr_test222`
--
ALTER TABLE `pr_test222`
  MODIFY `id_method` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `waiting_condition`
--
ALTER TABLE `waiting_condition`
  MODIFY `id_waiting_condition` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `method_demo`
--
ALTER TABLE `method_demo`
  ADD CONSTRAINT `method_demo_ibfk_1` FOREIGN KEY (`id_waiting_condition`) REFERENCES `waiting_condition` (`id_waiting_condition`),
  ADD CONSTRAINT `method_demo_ibfk_2` FOREIGN KEY (`id_measure_type`) REFERENCES `measure_type` (`id_measure_type`),
  ADD CONSTRAINT `method_demo_ibfk_3` FOREIGN KEY (`id_method_name`) REFERENCES `method_name` (`id_method_name`);

--
-- Contraintes pour la table `pr_test221`
--
ALTER TABLE `pr_test221`
  ADD CONSTRAINT `pr_test221_ibfk_1` FOREIGN KEY (`id_waiting_condition`) REFERENCES `waiting_condition` (`id_waiting_condition`),
  ADD CONSTRAINT `pr_test221_ibfk_2` FOREIGN KEY (`id_measure_type`) REFERENCES `measure_type` (`id_measure_type`);

--
-- Contraintes pour la table `pr_test222`
--
ALTER TABLE `pr_test222`
  ADD CONSTRAINT `pr_test222_ibfk_1` FOREIGN KEY (`id_waiting_condition`) REFERENCES `waiting_condition` (`id_waiting_condition`),
  ADD CONSTRAINT `pr_test222_ibfk_2` FOREIGN KEY (`id_measure_type`) REFERENCES `measure_type` (`id_measure_type`),
  ADD CONSTRAINT `pr_test222_ibfk_3` FOREIGN KEY (`id_method_name`) REFERENCES `method_name` (`id_method_name`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
