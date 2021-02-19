-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le :  mar. 16 fév. 2021 à 16:01
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
(2, 'Demande utilisateur');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `waiting_condition`
--
ALTER TABLE `waiting_condition`
  ADD PRIMARY KEY (`id_waiting_condition`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `waiting_condition`
--
ALTER TABLE `waiting_condition`
  MODIFY `id_waiting_condition` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
