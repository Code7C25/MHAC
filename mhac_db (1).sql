-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-09-2025 a las 03:43:09
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `mhac_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adopciones`
--

CREATE TABLE `adopciones` (
  `id` int(11) NOT NULL,
  `mascota_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicilio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `edad` int(11) NOT NULL,
  `vivienda` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `experiencia` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL,
  `estado` enum('pendiente','aprobada','rechazada','finalizada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `adopciones`
--

INSERT INTO `adopciones` (`id`, `mascota_id`, `usuario_id`, `nombre`, `email`, `telefono`, `domicilio`, `edad`, `vivienda`, `experiencia`, `fecha_solicitud`, `estado`) VALUES
(1, 10, 7, 'Conrad Fisher', 'conrad.fisher@gmail.com', '01000010', '23 Cornelia Street, New York, NY 10014', 23, 'Casa', 'Si', '2025-08-27 17:26:00', 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `campañas`
--

CREATE TABLE `campañas` (
  `id` int(11) NOT NULL,
  `titulo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `lugar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `organizador_id` int(11) DEFAULT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('proxima','en_curso','finalizada') COLLATE utf8mb4_unicode_ci DEFAULT 'proxima'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `denuncias`
--

CREATE TABLE `denuncias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_denuncia` datetime DEFAULT current_timestamp(),
  `estado` enum('pendiente','en_proceso','cerrada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `likes`
--

INSERT INTO `likes` (`id`, `post_id`, `usuario_id`, `fecha`) VALUES
(7, 2, 1, '2025-09-07 19:51:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mascotas`
--

CREATE TABLE `mascotas` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `especie` enum('perro','gato','otro') COLLATE utf8mb4_unicode_ci DEFAULT 'perro',
  `raza` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sexo` enum('macho','hembra','desconocido') COLLATE utf8mb4_unicode_ci DEFAULT 'desconocido',
  `edad_categoria` enum('cachorro','joven','adulto','mayor') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tamano` enum('pequeño','mediano','grande','desconocido') COLLATE utf8mb4_unicode_ci DEFAULT 'desconocido',
  `pelaje` enum('sin_pelo','corto','medio','largo') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comportamiento` enum('entrenado','cuidados_especiales','ninguno') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dias_mhac` int(11) DEFAULT 0,
  `descripcion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('en_adopcion','adoptado','perdido','encontrado') COLLATE utf8mb4_unicode_ci DEFAULT 'en_adopcion',
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refugio_id` int(11) DEFAULT NULL,
  `fecha_alta` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `mascotas`
--

INSERT INTO `mascotas` (`id`, `nombre`, `especie`, `raza`, `sexo`, `edad_categoria`, `tamano`, `pelaje`, `color`, `comportamiento`, `dias_mhac`, `descripcion`, `estado`, `foto`, `refugio_id`, `fecha_alta`) VALUES
(8, 'Chelo', 'perro', 'Mestizo', 'macho', 'adulto', 'mediano', 'corto', 'dorado', 'ninguno', 0, 'Es un perro guardián, pero muy amable con quienes se gana su confianza. Le encantan los niños y no para de jugar.', 'en_adopcion', '1755824286_chelo_perrito.jpg', 1, '2025-08-22 02:58:06'),
(9, 'Cookie', 'perro', 'Mestizo', 'macho', 'joven', 'mediano', 'medio', 'apricot', 'ninguno', 0, 'Perro gruñón y de gran apetito. No le gusta el cariño, excepto cuando él lo busca. Es algo arisco.', 'en_adopcion', '1755824470_cookie_perrito.jpg', 1, '2025-08-22 03:01:10'),
(10, 'Floppy', 'gato', 'Mestizo', 'hembra', 'joven', 'pequeño', 'corto', 'bicolor', 'ninguno', 0, 'Floppy es tranquila, con un carácter dulce e independiente. Le encanta explorar y descansar en lugares soleados, siempre lista para un buen momento de compañía.', 'en_adopcion', '1755827201_floppy_gatito.jpeg', 2, '2025-08-22 03:46:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id` int(11) NOT NULL,
  `emisor_id` int(11) NOT NULL,
  `receptor_id` int(11) NOT NULL,
  `mensaje` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_envio` datetime DEFAULT current_timestamp(),
  `leido` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `participaciones`
--

CREATE TABLE `participaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `campaña_id` int(11) NOT NULL,
  `fecha_participacion` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `posts`
--

INSERT INTO `posts` (`id`, `usuario_id`, `contenido`, `imagen`, `fecha`) VALUES
(1, 1, 'CON MIS CACHORRITOS ❤❤❤❤', '1757280750_Porquinhos.jpg', '2025-09-07 18:32:30'),
(2, 7, 'en Cousins con mis wachas', '1757281066_seagulls__1989__blue.jpg', '2025-09-07 18:37:46'),
(3, 1, 'LES PRESENTO A MIS BEBES, Pimienta, Rosa y Mora 💘', '1757284770_My_babies_.jpg', '2025-09-07 19:39:30'),
(4, 1, 'otro día más con mis hermosas 🥰', '1757287762_descarga.jpg', '2025-09-07 20:29:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `refugios`
--

CREATE TABLE `refugios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre_refugio` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `refugios`
--

INSERT INTO `refugios` (`id`, `usuario_id`, `nombre_refugio`, `direccion`, `telefono`, `email`, `descripcion`) VALUES
(1, 4, 'Raíces', NULL, NULL, NULL, NULL),
(2, 5, 'Sombra del Trébol', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('adoptante','refugio','voluntario','hogar_transito','veterinario','donante','admin','dador') COLLATE utf8mb4_unicode_ci DEFAULT 'adoptante',
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `estado` enum('activo','inactivo','baneado') COLLATE utf8mb4_unicode_ci DEFAULT 'activo',
  `foto_perfil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `apellido`, `email`, `telefono`, `password_hash`, `rol`, `fecha_registro`, `estado`, `foto_perfil`) VALUES
(1, 'Sol', 'Barrionuevo', 'solbarrionuevoo23@gmail.com', '3547564695', '$2y$10$PEQf2kQ2jTQwa1jxMGpq9OtZy1y9rc950xxXNT8BI67ic3gZkoeI2', 'adoptante', '2025-08-05 19:30:47', 'activo', 'imagenes/68a51078b695f_si.jpg'),
(2, 'Taylor', 'Swift', 'taylorswift13@gmail.com', '', '$2y$10$xKc1mNDesdSO86sV0GTVR.d7W3E6uVyW5iZNdTfv2Hk7CcEnMc1GS', 'donante', '2025-08-05 21:14:14', 'activo', NULL),
(3, 'AARON', 'WARNER', 'aaronwarner@gmail.com', '', '$2y$10$FNYbKRpo0itoCgtFyez4MOOYAkK9GMuB5g1TDtLKHRecoK1M9MR62', 'adoptante', '2025-08-05 21:19:53', 'activo', NULL),
(4, 'Raíces', 'y Patas', 'raicesypatas@gmail.com', '', '$2y$10$2EThdLPZEgRvi/RFv8eD5.IepkG0OJhfKgdKnVRP.OdruLs7LQ1da', 'refugio', '2025-08-19 22:49:20', 'activo', NULL),
(5, 'Sombra del Trébol', '', 'sombra.trebol@gmail.com', '3519874020', '$2y$10$amP4U/osAA9/pezMcXp4wuwVyfoPAEam4E5jFEAIO4fRTPdz0DFxe', 'refugio', '2025-08-21 22:39:47', 'activo', NULL),
(6, 'Rama', 'Ledesma', 'ramiroledesma@gmail.com', '3547635267', '$2y$10$xAt6VDOepfvL3NqWP86wOuWEj6CkWJQ8H7k67GDm1UKBEg/JRsyxK', 'veterinario', '2025-08-25 09:36:57', 'activo', NULL),
(7, 'Conrad', 'Fisher', 'conrad.fisher@gmail.com', '01000010', '$2y$10$ZcgnESmxNk0nRepSBv7txuaNMHSg3fhHTREWp/PuQuc6l3bOA4Pxi', 'adoptante', '2025-08-27 17:02:48', 'activo', NULL),
(8, 'Daniela', 'Cañete Gaitan', 'canetegaitanlula@gmail.com', '03547457154', '$2y$10$U5iZbxbcwby6aIyU7HBzVeSxZ8O5odQVlcbkBTx2I67mKfhrB8JEO', 'adoptante', '2025-09-07 21:37:57', 'activo', NULL),
(10, 'Daniela', 'Cañete', 'canetegaitandaniela@gmail.com', '03547457154', '$2y$10$MK/PZp09m0XFoeOmgK0iGO7Z3rj/4WT7sOv/usDz3c/p0xoz5OYFy', 'dador', '2025-09-07 22:40:34', 'activo', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adopciones`
--
ALTER TABLE `adopciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mascota_id` (`mascota_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `campañas`
--
ALTER TABLE `campañas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `organizador_id` (`organizador_id`);

--
-- Indices de la tabla `denuncias`
--
ALTER TABLE `denuncias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `post_id` (`post_id`,`usuario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `refugio_id` (`refugio_id`);

--
-- Indices de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emisor_id` (`emisor_id`),
  ADD KEY `receptor_id` (`receptor_id`);

--
-- Indices de la tabla `participaciones`
--
ALTER TABLE `participaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `campaña_id` (`campaña_id`);

--
-- Indices de la tabla `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `refugios`
--
ALTER TABLE `refugios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adopciones`
--
ALTER TABLE `adopciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `campañas`
--
ALTER TABLE `campañas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `denuncias`
--
ALTER TABLE `denuncias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `mascotas`
--
ALTER TABLE `mascotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `mensajes`
--
ALTER TABLE `mensajes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `participaciones`
--
ALTER TABLE `participaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `refugios`
--
ALTER TABLE `refugios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adopciones`
--
ALTER TABLE `adopciones`
  ADD CONSTRAINT `adopciones_ibfk_1` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `adopciones_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `campañas`
--
ALTER TABLE `campañas`
  ADD CONSTRAINT `campañas_ibfk_1` FOREIGN KEY (`organizador_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `mascotas`
--
ALTER TABLE `mascotas`
  ADD CONSTRAINT `mascotas_ibfk_1` FOREIGN KEY (`refugio_id`) REFERENCES `refugios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `mensajes`
--
ALTER TABLE `mensajes`
  ADD CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`emisor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`receptor_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `participaciones`
--
ALTER TABLE `participaciones`
  ADD CONSTRAINT `participaciones_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `participaciones_ibfk_2` FOREIGN KEY (`campaña_id`) REFERENCES `campañas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `refugios`
--
ALTER TABLE `refugios`
  ADD CONSTRAINT `refugios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
