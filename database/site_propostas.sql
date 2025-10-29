-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: db
-- Tempo de geração: 29/10/2025 às 14:11
-- Versão do servidor: 8.0.43
-- Versão do PHP: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `site_propostas`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `formulario`
--

CREATE TABLE `formulario` (
  `id` int NOT NULL,
  `volume` double NOT NULL,
  `unidade_medida` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `formato` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipologia` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `borda` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cor` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `local_uso` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_previsao` date NOT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `cliente` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `obra` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_produto` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `marca` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `embalagem` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `observacao` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Em analise',
  `comentario_Lib_Produto` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `formulario`
--

INSERT INTO `formulario` (`id`, `volume`, `unidade_medida`, `formato`, `tipologia`, `borda`, `cor`, `local_uso`, `data_previsao`, `preco`, `cliente`, `obra`, `nome_produto`, `marca`, `embalagem`, `observacao`, `status`, `comentario_Lib_Produto`) VALUES
(1, 1213, 'PC', '60X120', 'PORCELANATO', 'BOLD', 'BRANCO', 'PISO', '2025-12-31', 241.29, 'RUAN LTDA', 'SOL AMENTOS', 'MARMORE BRANCO GIASSI AC 60X120', 'DECORTILES', 'NÃO', 'CLIENTE DE MUITA IMPORTANCIA, MUITO TEMPO E MUITAS QUANTIDADES VENDIDAS PARA S CONSTRUÇÕES DESSE CLIENTE NA REGIÃO', 'Aprovado 26/10/2025', 'teste123'),
(2, 1111, 'M2', '60X120', 'PORCELANATO', 'RETIFICADO', 'BRANCO', 'PISO', '2025-12-31', 99.99, 'RUAN LTDA', 'GIASSI POUSADAS', 'MUNARI PRETO CASTAGNETI AC 90X90', 'ELIANE', 'NÃO', 'CONTRA O TEMPO, CONCORRENTE TEM EM PORTIFOLIO', 'Rejeitado 26/10/2025', 'asf'),
(3, 35235, 'PC', '60X120', 'PORCELANATO', 'RETIFICADO', 'BRANCO', 'PISO', '2026-02-13', 99.99, 'RUAN COMERCIO', 'CASTANHETI', 'MARMORE BRANCO GIASSI AC 60X120', 'ELIANE', 'NÃO', 'CONTRA O TEMPO, CONCORRENTE TEM EM PORTIFOLIO', 'Aprovado 26/10/2025', 'ssss'),
(4, 1111, 'PC', '60X120', 'PORCELANATO', 'RETIFICADO', 'PRETO', 'PISO', '2027-02-13', 133.00, 'GIASSI LTDA', 'GIASSI POUSADAS', 'MUNARI PRETO CASTAGNETI AC 90X90', 'ELIANE', 'NAO', 'CLIENTE DE MUITA IMPORTANCIA, MUITO TEMPO E MUITAS QUANTIDADES VENDIDAS PARA S CONSTRUÇÕES DESSE CLIENTE NA REGIÃO', 'Rejeitado 26/10/2025', NULL),
(5, 1111, 'M2', '60X120', 'PORCELANATO', 'RETIFICADO', 'BRANCO', 'PISO', '2027-12-31', 133.00, 'RUAN LTDA', 'GIASSI POUSADAS', 'MUNARI PRETO CASTAGNETI AC 90X90', 'ELIANE', 'NÃO', 'CLIENTE DE MUITA IMPORTANCIA, MUITO TEMPO E MUITAS QUANTIDADES VENDIDAS PARA S CONSTRUÇÕES DESSE CLIENTE NA REGIÃO', 'Aprovado 26/10/2025', 'ddd');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int NOT NULL,
  `usuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `usuario`
--

INSERT INTO `usuario` (`id`, `usuario`, `senha`, `data`) VALUES
(1, 'Ruan123', '$2y$10$t/jEtqj5ISA4e3Xmj/8sDOS9ela1UKHw2Zebzs0XsHhPbXaioBXUq', '2025-10-26 20:20:17');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `formulario`
--
ALTER TABLE `formulario`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `formulario`
--
ALTER TABLE `formulario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
