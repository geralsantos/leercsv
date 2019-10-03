-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-10-2019 a las 06:35:21
-- Versión del servidor: 10.3.16-MariaDB
-- Versión de PHP: 7.3.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `csvdatos`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `csvtabla`
--

CREATE TABLE `csvtabla` (
  `id_reg` int(11) NOT NULL,
  `CreditorReference` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `DCA` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `TransactionDate` date DEFAULT NULL,
  `UploadDate` date DEFAULT NULL,
  `TransactionValue` decimal(8,3) DEFAULT NULL,
  `AdjustmentTypeCode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `AdjustmentDescription` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `PaymentMethodCode` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `SegmentAssignment` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ID` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Estado` int(11) NOT NULL DEFAULT 0,
  `act` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`) VALUES
(1, 'geral', '123');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `csvtabla`
--
ALTER TABLE `csvtabla`
  ADD PRIMARY KEY (`id_reg`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `csvtabla`
--
ALTER TABLE `csvtabla`
  MODIFY `id_reg` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
