-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 19/09/2025 às 15:59
-- Versão do servidor: 9.1.0
-- Versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `agendamento`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agenda`
--

DROP TABLE IF EXISTS `agenda`;
CREATE TABLE IF NOT EXISTS `agenda` (
  `data` date NOT NULL,
  `horario` time DEFAULT NULL,
  `nome` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `servico` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  `obs` varchar(250) DEFAULT NULL,
  `telefone` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user` int DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios_semana`
--

DROP TABLE IF EXISTS `horarios_semana`;
CREATE TABLE IF NOT EXISTS `horarios_semana` (
  `id` int NOT NULL AUTO_INCREMENT,
  `dia_semana` varchar(10) NOT NULL,
  `horario` time NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `horarios_semana`
--

INSERT INTO `horarios_semana` (`id`, `dia_semana`, `horario`) VALUES
(1, 'segunda', '09:00:00'),
(2, 'segunda', '10:30:00'),
(3, 'segunda', '13:30:00'),
(4, 'segunda', '15:00:00'),
(5, 'segunda', '16:30:00'),
(6, 'segunda', '18:00:00'),
(7, 'terca', '09:00:00'),
(8, 'terca', '10:30:00'),
(9, 'terca', '13:30:00'),
(10, 'terca', '15:00:00'),
(11, 'terca', '16:30:00'),
(12, 'terca', '18:00:00'),
(13, 'quarta', '09:00:00'),
(14, 'quarta', '10:30:00'),
(15, 'quarta', '13:30:00'),
(16, 'quarta', '15:00:00'),
(17, 'quarta', '16:30:00'),
(18, 'quarta', '18:00:00'),
(19, 'quinta', '09:00:00'),
(20, 'quinta', '10:30:00'),
(21, 'quinta', '13:30:00'),
(22, 'quinta', '15:00:00'),
(23, 'quinta', '16:30:00'),
(24, 'quinta', '18:00:00'),
(25, 'sexta', '09:00:00'),
(26, 'sexta', '10:30:00'),
(27, 'sexta', '13:30:00'),
(28, 'sexta', '15:00:00'),
(29, 'sexta', '16:30:00'),
(30, 'sexta', '18:00:00'),
(31, 'sabado', '08:00:00'),
(32, 'sabado', '09:30:00'),
(33, 'sabado', '11:00:00');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `nome` varchar(200) NOT NULL,
  `email` varchar(200) NOT NULL,
  `senha` varchar(200) NOT NULL,
  `telefone` varchar(200) NOT NULL,
  `acesso` varchar(20) NOT NULL,
  `id` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`nome`, `email`, `senha`, `telefone`, `acesso`, `id`) VALUES
('Matheus Santos Ferro', 'matheustos123456@gmail.com', '$2y$10$WYL4SuF/3JQ9Yfo0cN6uUucjZRz/oN22gOHwA.SCuCOPuEUbhsXeO', '(79) 98154-0620', 'admin', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
