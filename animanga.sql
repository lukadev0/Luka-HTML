-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Gen 09, 2024 alle 21:14
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `animanga`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `commenti`
--

CREATE TABLE `commenti` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `post_id` int(11) NOT NULL,
  `contenuto` text NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `commenti`
--

INSERT INTO `commenti` (`id`, `utente_id`, `username`, `post_id`, `contenuto`, `data_creazione`, `parent_comment_id`) VALUES
(1, 1, 'Lukino', 2, 'Secondo me dovresti dargli un\'altra possibilità, andando avanti con gli episodi diventa molto più carino', '2024-01-09 14:01:33', 0),
(2, 2, 'Leluzzo', 2, 'No.', '2024-01-09 14:02:15', 1),
(3, 1, 'Lukino', 2, '1v1 Nabbo', '2024-01-09 14:02:51', 2),
(7, 2, 'Leluzzo', 2, 'No.', '2024-01-09 18:58:11', 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `post`
--

CREATE TABLE `post` (
  `id` int(11) NOT NULL,
  `utente_id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `posts` text NOT NULL,
  `data_creazione` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `post`
--

INSERT INTO `post` (`id`, `utente_id`, `username`, `posts`, `data_creazione`) VALUES
(1, 1, 'Lukino', 'Ho appena finito di vedere la seconda stagione di Jujutsu Kaisen, l\'ho adorata e voi?', '2024-01-09 02:44:12'),
(2, 2, 'Leluzzo', 'Unpopular Opinion , Haikyuu overrated!', '2024-01-09 02:44:55');

-- --------------------------------------------------------

--
-- Struttura della tabella `utenti`
--

CREATE TABLE `utenti` (
  `id` int(11) NOT NULL,
  `username` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `token` varchar(255) DEFAULT NULL,
  `post_count` int(11) DEFAULT 0,
  `reply_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utenti`
--

INSERT INTO `utenti` (`id`, `username`, `password`, `email`, `token`, `post_count`, `reply_count`) VALUES
(1, 'Lukino', '$2y$10$E1mNR3D/Q.hqakosDLr4/uZa.OsLN15YHme4P6dRImrWF8GZHRGk6', 'lucaciraolo85@gmail.com', NULL, 1, 2),
(2, 'Leluzzo', '$2y$10$EV2AeR0Aucq6Uqn0TYrD/eSta5y8x0UJpfnhQl9bJ8q0h0LRDpeeO', 'lelerusso@gmail.com', NULL, 1, 2);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `commenti`
--
ALTER TABLE `commenti`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente_id_fk` (`utente_id`),
  ADD KEY `post_id_fk` (`post_id`);

--
-- Indici per le tabelle `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente_id_fk` (`utente_id`),
  ADD KEY `post_id_fk` (`post_id`);

--
-- Indici per le tabelle `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utente_id_fk` (`utente_id`);

--
-- Indici per le tabelle `utenti`
--
ALTER TABLE `utenti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `commenti`
--
ALTER TABLE `commenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la tabella `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la tabella `utenti`
--
ALTER TABLE `utenti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `commenti`
--
ALTER TABLE `commenti`
  ADD CONSTRAINT `commenti_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`),
  ADD CONSTRAINT `commenti_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`);

--
-- Limiti per la tabella `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_ibfk_1` FOREIGN KEY (`utente_id`) REFERENCES `utenti` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
