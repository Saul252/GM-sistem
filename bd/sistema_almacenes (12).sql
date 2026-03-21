-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-03-2026 a las 20:56:53
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_almacenes`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `almacenes`
--

CREATE TABLE `almacenes` (
  `id` int(11) NOT NULL,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacenes`
--

INSERT INTO `almacenes` (`id`, `codigo`, `nombre`, `ubicacion`, `activo`, `fecha_creacion`) VALUES
(1, 'ALM-CM', 'Casa de Materiales', 'Zona Centro', 1, '2026-02-26 14:34:03'),
(2, 'ALM-ER', 'El Rancho', 'Carretera Principal', 1, '2026-02-26 14:34:03'),
(3, 'ALM-TEN', 'Tenango', 'Sucursal Tenango', 1, '2026-02-26 14:34:03'),
(4, 'ALM-VC', 'Valle de Chalco', 'Sucursal Valle de Chalco', 1, '2026-02-26 14:34:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Cementos y Polvos'),
(2, 'Acero y Ferretería'),
(3, 'Herramientas'),
(4, 'Llantas'),
(5, 'Rodillos'),
(6, 'Estructuras Metálicas'),
(7, 'Liquidos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre_comercial` varchar(150) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `rfc` varchar(13) NOT NULL,
  `regimen_fiscal` varchar(3) DEFAULT NULL COMMENT 'Clave del catálogo del SAT',
  `codigo_postal` varchar(5) NOT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `uso_cfdi` varchar(3) DEFAULT 'G03',
  `activo` tinyint(1) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `almacen_id` int(11) DEFAULT NULL,
  `api_token` varchar(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre_comercial`, `razon_social`, `rfc`, `regimen_fiscal`, `codigo_postal`, `correo`, `telefono`, `direccion`, `uso_cfdi`, `activo`, `fecha_registro`, `almacen_id`, `api_token`) VALUES
(1, 'PÚBLICO EN GENERAL', 'PÚBLICO EN GENERAL', 'XAXX010101000', '616', '00000', NULL, NULL, 'VENTAS DE MOSTRADOR', 'S01', 1, '2026-03-12 22:22:53', 1, NULL),
(2, 'PÚBLICO EN GENERAL', 'PÚBLICO EN GENERAL', 'XAXX010101000', '616', '00000', NULL, NULL, 'VENTAS DE MOSTRADOR', 'S01', 1, '2026-03-12 22:22:53', 2, NULL),
(3, 'PÚBLICO EN GENERAL', 'PÚBLICO EN GENERAL', 'XAXX010101000', '616', '00000', NULL, NULL, 'VENTAS DE MOSTRADOR', 'S01', 1, '2026-03-12 22:22:53', 3, NULL),
(4, 'PÚBLICO EN GENERAL', 'PÚBLICO EN GENERAL', 'XAXX010101000', '616', '00000', NULL, NULL, 'VENTAS DE MOSTRADOR', 'S01', 1, '2026-03-12 22:22:53', 4, NULL),
(9, 'materiales centro', 'materiales centro', '11212', '121', '12121', '1212121@gmail.com', '232323232', '323213123123212', 'G03', 1, '2026-03-12 23:58:01', 2, '3e6bca65710bc234cda41054aaf6e153'),
(10, 'Materias primas', 'materas primas', 'MATERIASPRIMA', '601', '12345', 'materiasprimas@gmail.com', '1234567890', 'la cima 11', 'G03', 1, '2026-03-13 02:04:03', 1, 'cc2a46dc4760cebdfc30690c111b74d3'),
(11, 'Cementos Fortaleza', 'cementos fortaleza', 'FORTALEZA123', '601', '12234', 'cementosfortaleza@cf.com', '1234567890', 'cementos fortaleza centro', 'G03', 1, '2026-03-13 02:08:30', 2, '75fab481cd3eef288011e7e25c00827e'),
(12, 'Materiales Garcia', 'Mteriales Garcia', 'MATERIALESGAR', '601', '56623', 'materialesgarcia@mg.com', '5523789029', 'LA CIMA 11', 'G01', 1, '2026-03-13 14:17:43', 3, '71e5fe46783d4b2bb0b7195c0e03009d'),
(13, '123 Materiales', '123Materiales', '1234567890', NULL, '12345', '123materiales@123materiales.com', NULL, NULL, 'G01', 1, '2026-03-21 16:52:56', 1, 'f2f5f17194dec15897682f1aa3b5a6a1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compras`
--

CREATE TABLE `compras` (
  `id` int(11) NOT NULL,
  `folio` varchar(50) NOT NULL,
  `proveedor` varchar(150) NOT NULL,
  `fecha_compra` date NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `documento_url` varchar(255) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_registra_id` int(11) NOT NULL,
  `estado` enum('pendiente','confirmada','cancelada') DEFAULT 'pendiente',
  `tiene_faltantes` tinyint(1) DEFAULT 0,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compras`
--

INSERT INTO `compras` (`id`, `folio`, `proveedor`, `fecha_compra`, `almacen_id`, `total`, `documento_url`, `observaciones`, `usuario_registra_id`, `estado`, `tiene_faltantes`, `fecha_registro`) VALUES
(1, '1', 'Hierros 123', '2026-03-13', 1, 2000.00, 'uploads/compras/compra_F_49_1773444959.pdf', NULL, 1, 'cancelada', 0, '2026-03-13 23:35:59'),
(2, '2', 'Proveedor Materias primas 123', '2026-03-14', 2, 4000.00, 'uploads/compras/compra_F_52_1773499774.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 14:49:34'),
(3, '3', 'Proveedor Materias primas 123', '2026-03-14', 2, 4000.00, 'uploads/compras/compra_F_53_1773499845.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 14:50:45'),
(4, '4', 'Trituradora Maira1234', '2026-03-14', 2, 2000.00, 'uploads/compras/compra_4_1773502863.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 15:41:03'),
(5, '5', 'Cementos Fortaleza', '2026-03-14', 1, 2000.00, 'uploads/compras/compra_5_1773513982.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 18:46:22'),
(6, '6', 'Cementos Fortaleza', '2026-03-17', 1, 4000.00, 'uploads/compras/compra_6_1773757787.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 14:29:47'),
(7, '7', 'Materiales Centro', '2026-03-17', 1, 2000.00, 'uploads/compras/compra_7_1773761828.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 15:37:08'),
(8, '8', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'uploads/compras/compra_8_1773762148.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 15:42:28'),
(9, '9', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'uploads/compras/compra_9_1773764305.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:18:25'),
(10, '10', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'uploads/compras/compra_10_1773764463.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:21:03'),
(11, '11', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'uploads/compras/compra_11_1773765181.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:33:01'),
(12, '12', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'uploads/compras/compra_12_1773765285.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:34:45'),
(13, '13', 'Materiales Centro', '2026-03-17', 1, 1.00, 'uploads/compras/compra_13_1773765648.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:40:48'),
(14, '14', 'Materiales Centro', '2026-03-17', 1, 2000.00, 'uploads/compras/compra_14_1773765701.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:41:41'),
(15, '15', 'Cementos Fortaleza', '2026-03-17', 2, 2000.00, 'uploads/compras/compra_15_1773768485.pdf', NULL, 2, 'cancelada', 0, '2026-03-17 17:28:05'),
(16, '16', 'Cementos Fortaleza', '2026-03-17', 2, 2000.00, 'uploads/compras/compra_16_1773768556.pdf', NULL, 2, 'cancelada', 0, '2026-03-17 17:29:16'),
(17, '17', 'Cementos Fortaleza', '2026-03-17', 2, 200.00, 'uploads/compras/compra_17_1773773906.pdf', NULL, 2, 'confirmada', 0, '2026-03-17 18:58:26'),
(18, '18', 'Cementos Fortaleza', '2026-03-17', 2, 2000.00, 'uploads/compras/compra_18_1773774572.pdf', NULL, 2, 'confirmada', 0, '2026-03-17 19:09:32'),
(19, '19', 'Cementos Fortaleza', '2026-03-17', 2, 20.00, 'uploads/compras/compra_19_1773778747.pdf', NULL, 2, 'confirmada', 0, '2026-03-17 20:19:07'),
(20, '20', 'Cementos Fortaleza', '2026-03-18', 1, 1000.00, 'uploads/compras/compra_20_1773864884.pdf', NULL, 1, 'confirmada', 0, '2026-03-18 20:14:44'),
(21, '21', 'Cementos Fortaleza', '2026-03-18', 1, 1000.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:18:21'),
(22, '22', 'Cementos Fortaleza', '2026-03-18', 2, 1000.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:24:27'),
(23, '23', 'Cementos Fortaleza', '2026-03-18', 1, 10.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:56:02'),
(25, '24', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:56:55'),
(26, '25', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:01:13'),
(32, '26', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:11:01'),
(33, '27', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:11:39'),
(34, '28', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:15:14'),
(35, '29', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:17:01'),
(36, '30', 'Cementos Fortaleza', '2026-03-18', 1, 2000.00, 'uploads/compras/compra_30_1773884590.pdf', NULL, 1, 'confirmada', 0, '2026-03-19 01:43:10'),
(37, '31', 'Cementos Fortaleza', '2026-03-20', 1, 100.00, 'uploads/compras/compra_31_1774061230.pdf', NULL, 1, 'confirmada', 0, '2026-03-21 02:47:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_transmutaciones`
--

CREATE TABLE `config_transmutaciones` (
  `id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `producto_origen_id` int(11) NOT NULL,
  `producto_destino_id` int(11) NOT NULL,
  `rendimiento_teorico` decimal(10,4) NOT NULL COMMENT 'Ej: 1 bulto -> 50.00 kg',
  `usuario_id` int(11) NOT NULL,
  `notas` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `config_transmutaciones`
--

INSERT INTO `config_transmutaciones` (`id`, `almacen_id`, `producto_origen_id`, `producto_destino_id`, `rendimiento_teorico`, `usuario_id`, `notas`, `fecha_registro`) VALUES
(1, 1, 17, 21, 25.0000, 1, '0', '2026-03-13 14:11:06'),
(2, 1, 1, 3, 50.0000, 1, '0', '2026-03-13 16:32:03'),
(3, 2, 14, 4, 25.0000, 2, '0', '2026-03-13 17:54:20'),
(4, 2, 17, 21, 10.0000, 2, '0', '2026-03-13 17:54:53'),
(5, 1, 2, 3, 25.0000, 1, '0', '2026-03-13 19:11:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_compra`
--

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_compra` varchar(20) DEFAULT 'PZA',
  `factor_conversion` decimal(10,2) DEFAULT 1.00,
  `cantidad_faltante` decimal(10,2) DEFAULT 0.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `estado_entrega` enum('completo','incompleto','ajustado') DEFAULT 'completo',
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`id`, `compra_id`, `producto_id`, `cantidad`, `unidad_compra`, `factor_conversion`, `cantidad_faltante`, `precio_unitario`, `estado_entrega`, `subtotal`) VALUES
(44, 1, 4, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(45, 2, 4, 0.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 4000.00),
(46, 3, 21, 0.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 4000.00),
(47, 4, 1, 0.00, 'PZA', 1.00, 0.00, 100.00, 'completo', 2000.00),
(48, 5, 1, 0.00, 'PZA', 1.00, 0.00, 100.00, 'completo', 2000.00),
(49, 6, 1, 0.00, 'PZA', 1.00, 0.00, 200.00, 'completo', 4000.00),
(50, 7, 21, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(51, 8, 21, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(52, 9, 21, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(53, 10, 21, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(54, 11, 4, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(55, 12, 4, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(56, 13, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 'completo', 1.00),
(57, 14, 21, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(58, 15, 21, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(59, 16, 21, 0.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(60, 17, 19, 0.00, 'PZA', 1.00, 0.00, 1.33, 'completo', 200.00),
(61, 18, 19, 0.00, 'PZA', 1.00, 0.00, 13.33, 'completo', 2000.00),
(62, 19, 22, 0.00, 'PZA', 1.00, 0.00, 6.67, 'completo', 20.00),
(63, 20, 4, 10.00, 'PZA', 1.00, 0.00, 100.00, 'completo', 1000.00),
(64, 21, 4, 10.00, 'PZA', 1.00, 0.00, 100.00, 'completo', 1000.00),
(65, 22, 19, 150.00, 'PZA', 1.00, 0.00, 6.67, 'completo', 1000.00),
(66, 23, 4, 1.00, 'PZA', 1.00, 0.00, 10.00, 'completo', 10.00),
(67, 25, 4, 1.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 1.00),
(68, 26, 4, 1.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 1.00),
(69, 32, 4, 1.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 1.00),
(70, 33, 4, 1.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 1.00),
(71, 34, 4, 1.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 1.00),
(72, 35, 4, 1.00, 'PZA', 1.00, 0.00, 1.00, 'completo', 1.00),
(73, 36, 21, 1000.00, 'PZA', 1.00, 0.00, 2.00, 'completo', 2000.00),
(74, 37, 21, 1.00, 'PZA', 1.00, 0.00, 100.00, 'completo', 100.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_entrega`
--

CREATE TABLE `detalle_entrega` (
  `id` int(11) NOT NULL,
  `entrega_id` int(11) NOT NULL,
  `detalle_venta_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_entrega`
--

INSERT INTO `detalle_entrega` (`id`, `entrega_id`, `detalle_venta_id`, `cantidad`) VALUES
(81, 78, 70, 1.00),
(82, 88, 70, 1.00),
(83, 89, 71, 2.00),
(84, 90, 72, 1.00),
(85, 91, 73, 1.00),
(86, 92, 74, 1.00),
(87, 93, 75, 1.00),
(88, 94, 76, 2.00),
(89, 95, 77, 4.00),
(90, 96, 78, 5.00),
(91, 97, 75, 1.00),
(92, 98, 74, 1.00),
(93, 99, 73, 1.00),
(94, 100, 72, 2.00),
(95, 101, 79, 1.00),
(96, 102, 80, 2.00),
(97, 103, 81, 1.00),
(98, 104, 82, 1.00),
(99, 105, 83, 1.00),
(100, 106, 84, 1.00),
(101, 107, 85, 1002.00),
(102, 108, 86, 2.00),
(103, 109, 87, 178.00),
(104, 110, 88, 1.00),
(105, 111, 88, 1.00),
(106, 112, 90, 2899.00),
(107, 113, 91, 899.00),
(108, 114, 92, 1000.00),
(109, 115, 93, 7.00),
(110, 116, 94, 250.00),
(111, 117, 95, 1.00),
(112, 118, 96, 1.00),
(113, 119, 97, 1.00),
(114, 120, 98, 1.00),
(115, 120, 99, 1.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_gasto`
--

CREATE TABLE `detalle_gasto` (
  `id` int(11) NOT NULL,
  `gasto_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL COMMENT '¿En qué se gastó? (Ej. Papelería, Luz, Flete)',
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_gasto`
--

INSERT INTO `detalle_gasto` (`id`, `gasto_id`, `descripcion`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(18, 35, 'Compra de garrafon de agua', 1.00, 10.00, 10.00),
(19, 36, 'gasto de comida de trabajadores', 2.00, 200.00, 400.00),
(20, 37, 'Llanta 3/9 para camión de carga', 1.00, 200.00, 200.00),
(21, 38, 'compra de llanta de camioneta', 1.00, 2000.00, 2000.00),
(22, 39, 'Compra e baterias para la lampara', 2.00, 200.00, 400.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedido_vendedor`
--

CREATE TABLE `detalle_pedido_vendedor` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `notas_producto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_solicitud_compra`
--

CREATE TABLE `detalle_solicitud_compra` (
  `id` int(11) NOT NULL,
  `solicitud_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_solicitud_compra`
--

INSERT INTO `detalle_solicitud_compra` (`id`, `solicitud_id`, `producto_id`, `cantidad`) VALUES
(1, 1, 21, 10000.00),
(5, 5, 4, 10.00),
(7, 7, 19, 150.00),
(8, 8, 4, 1.00),
(9, 9, 21, 1000.00),
(10, 10, 21, 1.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_traspaso`
--

CREATE TABLE `detalle_traspaso` (
  `id` int(11) NOT NULL,
  `traspaso_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_venta`
--

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `cantidad_entregada` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `tipo_precio` enum('minorista','mayorista','distribuidor') NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `estado_entrega` enum('pendiente','parcial','entregado') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_venta`
--

INSERT INTO `detalle_venta` (`id`, `venta_id`, `producto_id`, `cantidad`, `cantidad_entregada`, `precio_unitario`, `tipo_precio`, `subtotal`, `estado_entrega`) VALUES
(70, 1, 21, 2.00, 2.00, 200.00, 'minorista', 400.00, 'parcial'),
(71, 2, 21, 2.00, 2.00, 200.00, 'minorista', 400.00, 'entregado'),
(72, 3, 21, 3.00, 3.00, 200.00, 'minorista', 600.00, 'parcial'),
(73, 4, 21, 2.00, 2.00, 200.00, 'minorista', 400.00, 'parcial'),
(74, 5, 21, 2.00, 2.00, 200.00, 'minorista', 400.00, 'parcial'),
(75, 6, 21, 2.00, 2.00, 200.00, 'minorista', 400.00, 'parcial'),
(76, 7, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(77, 8, 20, 4.00, 4.00, 100.00, 'minorista', 400.00, 'entregado'),
(78, 9, 21, 5.00, 5.00, 20.00, 'minorista', 100.00, 'entregado'),
(79, 10, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(80, 11, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(81, 12, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(82, 13, 3, 1.00, 1.00, 4.80, 'minorista', 4.80, 'entregado'),
(83, 14, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(84, 15, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(85, 16, 21, 1002.00, 1002.00, 200.00, 'minorista', 200400.00, 'entregado'),
(86, 17, 20, 10.00, 2.00, 100.00, 'minorista', 1000.00, 'parcial'),
(87, 18, 21, 1000.00, 178.00, 20.00, 'minorista', 20000.00, 'parcial'),
(88, 19, 4, 2.00, 2.00, 3.00, 'minorista', 6.00, 'entregado'),
(89, 20, 21, 1001.00, 0.00, 20.00, 'minorista', 20020.00, 'pendiente'),
(90, 21, 4, 2899.00, 2899.00, 3.00, 'minorista', 8697.00, 'entregado'),
(91, 22, 4, 899.00, 899.00, 3.00, 'minorista', 2697.00, 'entregado'),
(92, 23, 4, 1000.00, 1000.00, 3.00, 'minorista', 3000.00, 'entregado'),
(93, 24, 19, 8.00, 7.00, 200.00, 'minorista', 1600.00, 'parcial'),
(94, 25, 19, 250.00, 250.00, 200.00, 'minorista', 50000.00, 'entregado'),
(95, 26, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(96, 27, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(97, 28, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(98, 29, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(99, 29, 7, 1.00, 1.00, 0.00, 'minorista', 0.00, 'entregado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entregas_venta`
--

CREATE TABLE `entregas_venta` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entregas_venta`
--

INSERT INTO `entregas_venta` (`id`, `venta_id`, `usuario_id`, `fecha`, `observaciones`) VALUES
(78, 1, 3, '2026-03-12 16:23:33', 'Entrega inicial generada en venta'),
(88, 1, 3, '2026-03-12 16:24:18', NULL),
(89, 2, 3, '2026-03-12 16:24:39', 'Entrega inicial generada en venta'),
(90, 3, 3, '2026-03-12 16:25:08', 'Entrega inicial generada en venta'),
(91, 4, 3, '2026-03-12 16:32:55', 'Entrega inicial generada en venta'),
(92, 5, 1, '2026-03-12 16:35:04', 'Entrega inicial generada en venta'),
(93, 6, 1, '2026-03-12 16:39:36', 'Entrega inicial generada en venta'),
(94, 7, 3, '2026-03-12 17:41:05', 'Entrega inicial generada en venta'),
(95, 8, 3, '2026-03-12 17:41:27', 'Entrega inicial generada en venta'),
(96, 9, 1, '2026-03-12 17:41:57', 'Entrega inicial generada en venta'),
(97, 6, 1, '2026-03-12 17:43:42', NULL),
(98, 5, 1, '2026-03-12 17:43:50', NULL),
(99, 4, 1, '2026-03-12 17:43:57', NULL),
(100, 3, 1, '2026-03-12 17:44:04', NULL),
(101, 10, 1, '2026-03-12 22:04:21', 'Entrega inicial generada en venta'),
(102, 11, 3, '2026-03-13 12:05:21', 'Entrega inicial generada en venta'),
(103, 12, 1, '2026-03-13 16:30:14', 'Entrega inicial generada en venta'),
(104, 13, 1, '2026-03-13 16:33:04', 'Entrega inicial generada en venta'),
(105, 14, 3, '2026-03-13 17:34:43', 'Entrega inicial generada en venta'),
(106, 15, 3, '2026-03-13 17:36:48', 'Entrega inicial generada en venta'),
(107, 16, 2, '2026-03-14 08:45:31', 'Entrega inicial generada en venta'),
(108, 17, 1, '2026-03-14 09:13:13', 'Entrega inicial generada en venta. Folio: V-260314091313'),
(109, 18, 1, '2026-03-14 09:14:56', 'Entrega inicial generada en venta. Folio: V-260314091456'),
(110, 19, 1, '2026-03-14 09:16:39', 'Entrega inicial generada en venta. Folio: V-260314091639'),
(111, 19, 1, '2026-03-14 11:28:12', 'Entrega desde edición'),
(112, 21, 3, '2026-03-17 10:50:27', 'Entrega inicial generada en venta. Folio: V-260317105027'),
(113, 22, 1, '2026-03-17 10:53:20', 'Entrega inicial generada en venta. Folio: V-260317105320'),
(114, 23, 2, '2026-03-17 11:42:54', 'Entrega inicial generada en venta. Folio: V-260317114254'),
(115, 24, 2, '2026-03-17 12:55:28', 'Entrega inicial generada en venta. Folio: V-260317125528'),
(116, 25, 2, '2026-03-17 13:09:48', 'Entrega inicial generada en venta. Folio: V-260317130948'),
(117, 26, 1, '2026-03-19 16:01:55', 'Entrega inicial generada en venta. Folio: V-260319160155'),
(118, 27, 1, '2026-03-20 17:51:22', 'Entrega inicial generada en venta. Folio: V-260320175122'),
(119, 28, 1, '2026-03-21 11:34:25', 'Entrega inicial generada en venta. Folio: V-260321113425'),
(120, 29, 1, '2026-03-21 11:35:18', 'Entrega inicial generada en venta. Folio: V-260321113518');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `faltantes_ingreso`
--

CREATE TABLE `faltantes_ingreso` (
  `id` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_pendiente` decimal(10,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `folio` varchar(50) NOT NULL,
  `fecha_gasto` date NOT NULL,
  `almacen_id` int(11) NOT NULL COMMENT 'Almacén al que se carga el gasto',
  `usuario_registra_id` int(11) NOT NULL,
  `beneficiario` varchar(150) NOT NULL COMMENT 'Quién recibió el dinero',
  `metodo_pago` varchar(50) DEFAULT 'Efectivo',
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `documento_url` varchar(255) DEFAULT NULL COMMENT 'Comprobante o factura',
  `observaciones` text DEFAULT NULL,
  `estado` enum('pendiente','pagado','cancelado') NOT NULL DEFAULT 'pagado',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos`
--

INSERT INTO `gastos` (`id`, `folio`, `fecha_gasto`, `almacen_id`, `usuario_registra_id`, `beneficiario`, `metodo_pago`, `total`, `documento_url`, `observaciones`, `estado`, `fecha_registro`) VALUES
(35, '1', '2026-03-14', 2, 1, 'Purificadora Juanita', 'Efectivo', 10.00, 'GASTO_1773513216_69b5aa00431aa.pdf', 'Se habia acabo el agua', 'pagado', '2026-03-14 18:33:36'),
(36, '2', '2026-03-14', 1, 1, 'Gastos de alimentacion para viajes', 'Efectivo', 400.00, 'GASTO_1773513888_69b5aca0833b5.pdf', 'Se compro comoda apara vaiaje a Puebla', 'pagado', '2026-03-14 18:44:48'),
(37, '3', '2026-03-17', 1, 1, 'Gasolina', 'Efectivo', 200.00, NULL, 'compra de gasolina\n*** CANCELADO por casa el 2026-03-17 18:23 ***\nRAZÓN: me equivoque de concepto', 'cancelado', '2026-03-17 17:13:28'),
(38, '4', '2026-03-17', 1, 1, 'Llanta para camión', 'Efectivo', 2000.00, 'GASTO_1773768248_69b98e38871c2.pdf', '\n*** CANCELADO por casa el 2026-03-17 18:24 ***\nRAZÓN: me equivoque llanta', 'cancelado', '2026-03-17 17:24:08'),
(39, '5', '2026-03-17', 2, 1, 'Papleria el caminito de la escuela', 'Efectivo', 400.00, 'GASTO_1773768606_69b98f9ec89f1.pdf', '\n*** CANCELADO por juan el 2026-03-17 18:30 ***\nRAZÓN: eran de otra medida', 'cancelado', '2026-03-17 17:30:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_pagos`
--

CREATE TABLE `historial_pagos` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_pagos`
--

INSERT INTO `historial_pagos` (`id`, `venta_id`, `usuario_id`, `monto`, `metodo_pago`, `referencia`, `fecha`) VALUES
(61, 1, 3, 400.00, 'Efectivo', NULL, '2026-03-12 16:23:33'),
(62, 2, 3, 400.00, 'Efectivo', NULL, '2026-03-12 16:24:39'),
(63, 3, 3, 600.00, 'Efectivo', NULL, '2026-03-12 16:25:08'),
(64, 4, 3, 400.00, 'Efectivo', NULL, '2026-03-12 16:32:55'),
(65, 5, 1, 400.00, 'Efectivo', NULL, '2026-03-12 16:35:04'),
(66, 6, 1, 400.00, 'Efectivo', NULL, '2026-03-12 16:39:36'),
(67, 7, 3, 40.00, 'Efectivo', NULL, '2026-03-12 17:41:05'),
(68, 8, 3, 400.00, 'Efectivo', NULL, '2026-03-12 17:41:27'),
(69, 9, 1, 100.00, 'Efectivo', NULL, '2026-03-12 17:41:57'),
(70, 10, 1, 20.00, 'Efectivo', NULL, '2026-03-12 22:04:21'),
(71, 11, 3, 40.00, 'Efectivo', NULL, '2026-03-13 12:05:21'),
(72, 12, 1, 20.00, 'Efectivo', NULL, '2026-03-13 16:30:14'),
(73, 13, 1, 4.80, 'Efectivo', NULL, '2026-03-13 16:33:04'),
(74, 14, 3, 20.00, 'Efectivo', NULL, '2026-03-13 17:34:43'),
(75, 15, 3, 3.00, 'Efectivo', NULL, '2026-03-13 17:36:48'),
(76, 16, 2, 200400.00, 'Efectivo', NULL, '2026-03-14 08:45:31'),
(77, 17, 1, 1000.00, 'Efectivo', NULL, '2026-03-14 09:13:13'),
(78, 18, 1, 20000.00, 'Efectivo', NULL, '2026-03-14 09:14:56'),
(79, 19, 1, 3.00, 'Efectivo', NULL, '2026-03-14 09:16:39'),
(80, 20, 3, 20020.00, 'Efectivo', NULL, '2026-03-14 15:15:46'),
(81, 21, 3, 8697.00, 'Efectivo', NULL, '2026-03-17 10:50:27'),
(82, 22, 1, 2697.00, 'Efectivo', NULL, '2026-03-17 10:53:20'),
(83, 23, 2, 3000.00, 'Efectivo', NULL, '2026-03-17 11:42:54'),
(84, 24, 2, 1600.00, 'Efectivo', NULL, '2026-03-17 12:55:28'),
(85, 25, 2, 50000.00, 'Efectivo', NULL, '2026-03-17 13:09:48'),
(86, 26, 1, 20.00, 'Efectivo', NULL, '2026-03-19 16:01:55'),
(87, 27, 1, 200.00, 'Efectivo', NULL, '2026-03-20 17:51:22'),
(88, 28, 1, 20.00, 'Efectivo', NULL, '2026-03-21 11:34:25'),
(89, 29, 1, 20.00, 'Efectivo', NULL, '2026-03-21 11:35:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `stock` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `stock_maximo` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `almacen_id`, `producto_id`, `stock`, `stock_minimo`, `stock_maximo`) VALUES
(32, 1, 1, 45.00, 10.00, 500.00),
(33, 2, 1, 31.00, 10.00, 500.00),
(34, 3, 1, 10.00, 10.00, 500.00),
(35, 4, 1, 10.00, 10.00, 500.00),
(36, 1, 2, 6.00, 10.00, 500.00),
(37, 2, 2, 11.00, 10.00, 500.00),
(38, 3, 2, 10.00, 10.00, 500.00),
(39, 4, 2, 10.00, 10.00, 500.00),
(40, 1, 3, 84.00, 10.00, 500.00),
(41, 2, 3, 10.00, 10.00, 500.00),
(42, 3, 3, 10.00, 10.00, 500.00),
(43, 4, 3, 10.00, 10.00, 500.00),
(44, 1, 4, 25.00, 10.00, 500.00),
(45, 2, 4, 3009.00, 10.00, 500.00),
(46, 3, 4, 10.00, 10.00, 500.00),
(47, 4, 4, 10.00, 10.00, 500.00),
(48, 1, 5, 9.00, 10.00, 500.00),
(49, 2, 5, 10.00, 10.00, 500.00),
(50, 3, 5, 10.00, 10.00, 500.00),
(51, 4, 5, 10.00, 10.00, 500.00),
(63, 1, 6, 9.00, 0.00, 0.00),
(64, 2, 6, 10.00, 1.00, 0.00),
(65, 3, 6, 10.00, 0.00, 0.00),
(66, 4, 6, 10.00, 0.00, 0.00),
(67, 1, 7, 9.00, 0.00, 0.00),
(68, 2, 7, 10.00, 0.00, 0.00),
(69, 1, 8, 10.00, 0.00, 0.00),
(70, 2, 8, 10.00, 0.00, 0.00),
(71, 1, 9, 10.00, 0.00, 0.00),
(72, 2, 9, 10.00, 0.00, 0.00),
(91, 1, 10, 10.00, 0.00, 0.00),
(92, 2, 10, 10.00, 0.00, 0.00),
(112, 1, 17, 4.00, 0.00, 0.00),
(113, 2, 17, 6.00, 0.00, 0.00),
(115, 1, 18, 10.00, 0.00, 0.00),
(134, 1, 19, 0.00, 0.00, 0.00),
(139, 1, 20, 0.00, 0.00, 0.00),
(140, 2, 20, 5.00, 0.00, 0.00),
(141, 3, 20, 10.00, 0.00, 0.00),
(142, 4, 20, 10.00, 0.00, 0.00),
(153, 2, 19, 200.00, 0.00, 0.00),
(163, 1, 21, 998.00, 0.00, 0.00),
(164, 2, 21, 3386.00, 0.00, 0.00),
(165, 3, 21, 246.00, 0.00, 0.00),
(166, 4, 21, 246.00, 0.00, 0.00),
(186, 2, 22, 3.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_ingresos_detalle`
--

CREATE TABLE `lotes_ingresos_detalle` (
  `id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `detalle_compra_id` int(11) NOT NULL,
  `cantidad_recibida` decimal(10,2) NOT NULL,
  `costo_aplicado` decimal(10,2) NOT NULL COMMENT 'Costo pactado en la compra para este lote',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotes_ingresos_detalle`
--

INSERT INTO `lotes_ingresos_detalle` (`id`, `lote_id`, `detalle_compra_id`, `cantidad_recibida`, `costo_aplicado`, `fecha_registro`) VALUES
(2, 144, 44, 1000.00, 2.00, '2026-03-13 23:35:59'),
(3, 147, 45, 4000.00, 1.00, '2026-03-14 14:49:34'),
(4, 148, 46, 4000.00, 1.00, '2026-03-14 14:50:45'),
(5, 149, 47, 20.00, 100.00, '2026-03-14 15:41:03'),
(6, 150, 48, 20.00, 100.00, '2026-03-14 18:46:22'),
(18, 163, 60, 150.00, 1.33, '2026-03-17 18:58:26'),
(19, 164, 61, 150.00, 13.33, '2026-03-17 19:09:32'),
(20, 165, 62, 3.00, 6.67, '2026-03-17 20:19:07'),
(21, 166, 63, 10.00, 100.00, '2026-03-18 20:14:45'),
(22, 167, 64, 10.00, 100.00, '2026-03-18 22:18:21'),
(23, 168, 65, 150.00, 6.67, '2026-03-18 22:24:27'),
(24, 169, 66, 1.00, 10.00, '2026-03-18 22:56:02'),
(25, 170, 67, 1.00, 1.00, '2026-03-18 22:56:55'),
(26, 171, 68, 1.00, 1.00, '2026-03-18 23:01:13'),
(27, 172, 69, 1.00, 1.00, '2026-03-18 23:11:01'),
(28, 173, 70, 1.00, 1.00, '2026-03-18 23:11:39'),
(29, 174, 71, 1.00, 1.00, '2026-03-18 23:15:14'),
(30, 175, 72, 1.00, 1.00, '2026-03-18 23:17:01'),
(31, 176, 73, 1000.00, 2.00, '2026-03-19 01:43:10'),
(32, 177, 74, 1.00, 100.00, '2026-03-21 02:47:10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_movimientos_salida`
--

CREATE TABLE `lotes_movimientos_salida` (
  `id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `entrega_venta_id` int(11) NOT NULL COMMENT 'Referencia a tu tabla entregas_venta',
  `detalle_venta_id` int(11) NOT NULL COMMENT 'Referencia al producto vendido originalmente',
  `cantidad_salida` decimal(10,2) NOT NULL,
  `costo_compra_historico` decimal(10,2) NOT NULL COMMENT 'Lo que nos costó a nosotros el lote',
  `precio_venta_pactado` decimal(10,2) NOT NULL COMMENT 'A cuánto se le vendió al cliente (aunque sea hace un año)',
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotes_movimientos_salida`
--

INSERT INTO `lotes_movimientos_salida` (`id`, `lote_id`, `entrega_venta_id`, `detalle_venta_id`, `cantidad_salida`, `costo_compra_historico`, `precio_venta_pactado`, `fecha_movimiento`) VALUES
(31, 120, 78, 70, 1.00, 0.00, 200.00, '2026-03-12 23:47:13'),
(32, 120, 94, 76, 2.00, 0.00, 20.00, '2026-03-12 23:47:37'),
(33, 120, 96, 78, 5.00, 0.00, 20.00, '2026-03-13 01:17:33'),
(34, 120, 101, 79, 1.00, 0.00, 20.00, '2026-03-13 04:05:17'),
(35, 120, 102, 80, 2.00, 0.00, 20.00, '2026-03-13 18:06:22'),
(36, 144, 106, 84, 1.00, 2.00, 3.00, '2026-03-13 23:38:19'),
(37, 93, 114, 92, 7.00, 1200.00, 3.00, '2026-03-17 17:43:20'),
(38, 147, 114, 92, 993.00, 1.00, 3.00, '2026-03-17 17:43:20'),
(39, 119, 115, 93, 7.00, 10.00, 200.00, '2026-03-17 19:02:34'),
(40, 121, 107, 85, 248.00, 5.50, 200.00, '2026-03-17 19:08:24'),
(41, 146, 107, 85, 40.00, 25.00, 200.00, '2026-03-17 19:08:24'),
(42, 148, 107, 85, 714.00, 1.00, 200.00, '2026-03-17 19:08:24'),
(43, 163, 116, 94, 150.00, 1.33, 200.00, '2026-03-17 19:10:36'),
(44, 164, 116, 94, 100.00, 13.33, 200.00, '2026-03-17 19:10:36'),
(45, 144, 113, 91, 899.00, 2.00, 3.00, '2026-03-17 19:35:30'),
(46, 120, 117, 95, 1.00, 5.50, 20.00, '2026-03-19 22:02:14'),
(47, 120, 103, 81, 1.00, 5.50, 20.00, '2026-03-20 22:49:36'),
(48, 89, 104, 82, 1.00, 85.00, 4.80, '2026-03-20 23:33:33'),
(49, 123, 118, 96, 1.00, 5.50, 200.00, '2026-03-20 23:52:07'),
(50, 120, 119, 97, 1.00, 5.50, 20.00, '2026-03-21 17:40:48'),
(51, 120, 120, 98, 1.00, 5.50, 20.00, '2026-03-21 17:55:14'),
(52, 104, 120, 99, 1.00, 120.00, 0.00, '2026-03-21 17:56:48'),
(53, 115, 108, 86, 2.00, 10.00, 100.00, '2026-03-21 18:01:59');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lotes_stock`
--

CREATE TABLE `lotes_stock` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `codigo_lote` varchar(50) NOT NULL COMMENT 'Código único para rastrear este grupo de productos',
  `cantidad_inicial` decimal(10,2) NOT NULL COMMENT 'Lo que entró originalmente',
  `cantidad_actual` decimal(10,2) NOT NULL COMMENT 'Lo que queda disponible para entregar',
  `precio_compra_unitario` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Costo de adquisición real de este lote',
  `fecha_ingreso` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_lote` enum('activo','agotado','bloqueado') DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lotes_stock`
--

INSERT INTO `lotes_stock` (`id`, `producto_id`, `almacen_id`, `codigo_lote`, `cantidad_inicial`, `cantidad_actual`, `precio_compra_unitario`, `fecha_ingreso`, `estado_lote`) VALUES
(81, 1, 1, 'LOTE-INI-32', 6.00, 5.00, 4.00, '2026-03-12 23:46:54', 'activo'),
(82, 1, 2, 'LOTE-INI-33', 12.00, 11.00, 450.00, '2026-03-12 23:46:54', 'activo'),
(83, 1, 3, 'LOTE-INI-34', 10.00, 10.00, 450.00, '2026-03-12 23:46:54', 'activo'),
(84, 1, 4, 'LOTE-INI-35', 10.00, 10.00, 450.00, '2026-03-12 23:46:54', 'activo'),
(85, 2, 1, 'LOTE-INI-36', 9.00, 6.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(86, 2, 2, 'LOTE-INI-37', 11.00, 11.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(87, 2, 3, 'LOTE-INI-38', 10.00, 10.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(88, 2, 4, 'LOTE-INI-39', 10.00, 10.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(89, 3, 1, 'LOTE-INI-40', 10.00, 84.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(90, 3, 2, 'LOTE-INI-41', 10.00, 10.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(91, 3, 3, 'LOTE-INI-42', 10.00, 10.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(92, 3, 4, 'LOTE-INI-43', 10.00, 10.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(93, 4, 2, 'LOTE-INI-45', 7.00, 0.00, 1200.00, '2026-03-12 23:46:54', 'agotado'),
(94, 4, 3, 'LOTE-INI-46', 10.00, 10.00, 1200.00, '2026-03-12 23:46:54', 'activo'),
(95, 4, 4, 'LOTE-INI-47', 10.00, 10.00, 1200.00, '2026-03-12 23:46:54', 'activo'),
(96, 5, 1, 'LOTE-INI-48', 10.00, 9.00, 35.00, '2026-03-12 23:46:54', 'activo'),
(97, 5, 2, 'LOTE-INI-49', 10.00, 10.00, 35.00, '2026-03-12 23:46:54', 'activo'),
(98, 5, 3, 'LOTE-INI-50', 10.00, 10.00, 35.00, '2026-03-12 23:46:54', 'activo'),
(99, 5, 4, 'LOTE-INI-51', 10.00, 10.00, 35.00, '2026-03-12 23:46:54', 'activo'),
(100, 6, 1, 'LOTE-INI-63', 10.00, 9.00, 55.00, '2026-03-12 23:46:54', 'activo'),
(101, 6, 2, 'LOTE-INI-64', 10.00, 10.00, 55.00, '2026-03-12 23:46:54', 'activo'),
(102, 6, 3, 'LOTE-INI-65', 10.00, 10.00, 55.00, '2026-03-12 23:46:54', 'activo'),
(103, 6, 4, 'LOTE-INI-66', 10.00, 10.00, 55.00, '2026-03-12 23:46:54', 'activo'),
(104, 7, 1, 'LOTE-INI-67', 10.00, 8.00, 120.00, '2026-03-12 23:46:54', 'activo'),
(105, 7, 2, 'LOTE-INI-68', 10.00, 10.00, 120.00, '2026-03-12 23:46:54', 'activo'),
(106, 8, 1, 'LOTE-INI-69', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(107, 8, 2, 'LOTE-INI-70', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(108, 9, 1, 'LOTE-INI-71', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(109, 9, 2, 'LOTE-INI-72', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(110, 10, 1, 'LOTE-INI-91', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(111, 10, 2, 'LOTE-INI-92', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(112, 17, 1, 'LOTE-INI-112', 10.00, 4.00, 250.00, '2026-03-12 23:46:54', 'activo'),
(113, 17, 2, 'LOTE-INI-113', 10.00, 6.00, 250.00, '2026-03-12 23:46:54', 'activo'),
(114, 18, 1, 'LOTE-INI-115', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(115, 20, 1, 'LOTE-INI-139', 2.00, 0.00, 10.00, '2026-03-12 23:46:54', 'agotado'),
(116, 20, 2, 'LOTE-INI-140', 5.00, 5.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(117, 20, 3, 'LOTE-INI-141', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(118, 20, 4, 'LOTE-INI-142', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(119, 19, 2, 'LOTE-INI-153', 7.00, 0.00, 10.00, '2026-03-12 23:46:54', 'agotado'),
(120, 21, 1, 'LOTE-INI-163', 233.00, 68.00, 5.50, '2026-03-12 23:46:54', 'activo'),
(121, 21, 2, 'LOTE-INI-164', 248.00, 0.00, 5.50, '2026-03-12 23:46:54', 'agotado'),
(122, 21, 3, 'LOTE-INI-165', 246.00, 246.00, 5.50, '2026-03-12 23:46:54', 'activo'),
(123, 21, 4, 'LOTE-INI-166', 247.00, 246.00, 5.50, '2026-03-12 23:46:54', 'activo'),
(144, 4, 1, 'LOTE-1-4-1', 1000.00, 0.00, 2.00, '2026-03-13 23:35:59', 'agotado'),
(145, 21, 1, 'TR-260314-BCA9', 100.00, 100.00, 10.00, '2026-03-13 23:39:59', 'activo'),
(146, 21, 2, 'TR-260314-A1EE', 40.00, 0.00, 25.00, '2026-03-13 23:55:29', 'agotado'),
(147, 4, 2, 'LOTE-2-4-2', 4000.00, 3007.00, 1.00, '2026-03-14 14:49:34', 'activo'),
(148, 21, 2, 'LOTE-3-21-2', 4000.00, 3286.00, 1.00, '2026-03-14 14:50:45', 'activo'),
(149, 1, 2, 'LOTE-4-1-2', 20.00, 20.00, 100.00, '2026-03-14 15:41:03', 'activo'),
(150, 1, 1, 'LOTE-5-1-1', 20.00, 20.00, 100.00, '2026-03-14 18:46:22', 'activo'),
(162, 21, 2, 'L-TR-64-183813', 100.00, 100.00, 5.50, '2026-03-17 17:38:13', 'activo'),
(163, 19, 2, 'LOTE-17-19-2', 150.00, 0.00, 1.33, '2026-03-17 18:58:26', 'agotado'),
(164, 19, 2, 'LOTE-18-19-2', 150.00, 50.00, 13.33, '2026-03-17 19:09:32', 'activo'),
(165, 22, 2, 'LOTE-19-22-2', 3.00, 3.00, 6.67, '2026-03-17 20:19:07', 'activo'),
(166, 4, 1, 'LOTE-20-4-1', 10.00, 8.00, 100.00, '2026-03-18 20:14:44', 'activo'),
(167, 4, 1, 'LOTE-21-4-1', 10.00, 10.00, 100.00, '2026-03-18 22:18:21', 'activo'),
(168, 19, 2, 'LOTE-22-19-2', 150.00, 150.00, 6.67, '2026-03-18 22:24:27', 'activo'),
(169, 4, 1, 'LOTE-23-4-1', 1.00, 1.00, 10.00, '2026-03-18 22:56:02', 'activo'),
(170, 4, 1, 'LOTE-25-4-1', 1.00, 1.00, 1.00, '2026-03-18 22:56:55', 'activo'),
(171, 4, 1, 'LOTE-26-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:01:13', 'activo'),
(172, 4, 1, 'LOTE-32-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:11:01', 'activo'),
(173, 4, 1, 'LOTE-33-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:11:39', 'activo'),
(174, 4, 1, 'LOTE-34-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:15:14', 'activo'),
(175, 4, 1, 'LOTE-35-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:17:01', 'activo'),
(176, 21, 1, 'LOTE-36-21-1', 1000.00, 1000.00, 2.00, '2026-03-19 01:43:10', 'activo'),
(177, 21, 1, 'LOTE-37-21-1', 1.00, 1.00, 100.00, '2026-03-21 02:47:10', 'activo'),
(178, 4, 2, 'L-TR-130-163810', 2.00, 2.00, 100.00, '2026-03-21 15:38:10', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mermas`
--

CREATE TABLE `mermas` (
  `id` int(11) NOT NULL,
  `movimiento_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `tipo_merma` enum('robo','daño','caducidad','otro') DEFAULT 'otro',
  `responsable_declaracion` varchar(150) DEFAULT NULL,
  `descripcion_suceso` text NOT NULL,
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mermas`
--

INSERT INTO `mermas` (`id`, `movimiento_id`, `almacen_id`, `producto_id`, `lote_id`, `cantidad`, `tipo_merma`, `responsable_declaracion`, `descripcion_suceso`, `fecha_reporte`) VALUES
(1, 28, 1, 7, 104, 1.00, 'robo', 'Administrador General', 'Se cayo de el camión', '2026-03-13 17:59:13'),
(2, 29, 1, 6, 100, 1.00, 'robo', 'Administrador General', 'Se cayo de el camion', '2026-03-13 18:01:53'),
(3, 30, 1, 5, 96, 1.00, 'robo', 'casa', 'Se cayo de el camion', '2026-03-13 18:03:55'),
(4, 50, 1, 2, 85, 1.00, 'caducidad', 'Administrador General', '', '2026-03-13 23:14:23'),
(5, 51, 1, 2, 85, 1.00, 'caducidad', 'Administrador General', '', '2026-03-13 23:18:19'),
(6, 52, 1, 21, 120, 100.00, 'daño', 'Administrador General', 'se rompio el alambre', '2026-03-13 23:32:24'),
(7, 56, 1, 4, 144, 100.00, 'daño', 'casa', 'el alambre se mojo y se rompio', '2026-03-13 23:37:49'),
(8, 63, 2, 1, 82, 1.00, 'daño', 'Administrador General', 'un carro roso el cargamento y se rompio un bulto', '2026-03-14 01:14:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `identificador` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT 'fas fa-box',
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`id`, `nombre`, `identificador`, `icono`, `orden`, `activo`) VALUES
(1, 'Inicio', 'inicio', 'bi bi-house-door', 1, 1),
(2, 'Ventas', 'ventas', 'bi bi-cart-check', 1, 1),
(3, 'Compras', 'compras', 'bi bi-bag-plus', 3, 1),
(4, 'Almacenes', 'almacenes', 'bi bi-buildings', 1, 1),
(5, 'Clientes', 'clientes', 'bi bi-people', 1, 1),
(6, 'movimientos', 'movimientos', 'bi bi-arrow-left-right', 1, 1),
(8, 'Usuarios', 'usuarios', 'bi bi-person-gear', 1, 1),
(9, 'Finanzas', 'finanzas', 'bi bi-graph-down-arrow', 9, 1),
(10, 'Mermas', 'Mermas', 'bi bi-trash', 1, 1),
(11, 'Proveedores', 'proveedores', 'bi bi-truck-flatbed', 11, 1),
(12, 'Corte de Caja', 'corteCaja', 'bi bi-cash-stack', 1, 1),
(13, 'Entregas', 'entregas', '', 1, 1),
(14, 'Clientes Estatus', 'clientesEstatus', '', 1, 1),
(15, 'configuracion de acceso', 'Configuracion', 'bi bi-gear-fill', 1, 1),
(16, 'Transmutaciones', 'transmutaciones', '', 1, 1),
(17, 'solicitudes de Compra', 'solicitudesCompra', '', 1, 1),
(18, 'Trabajadores', 'trabajadores', '', 1, 1),
(19, 'Vehiculos', 'vehiculos', '', 1, 1),
(20, 'Repartos', 'repartos', '', 1, 1),
(22, 'Historial de ventas', 'ventashistorial', '', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `tipo` enum('entrada','salida','traspaso','ajuste') NOT NULL,
  `cantidad` decimal(10,2) DEFAULT NULL,
  `almacen_origen_id` int(11) DEFAULT NULL,
  `almacen_destino_id` int(11) DEFAULT NULL,
  `usuario_registra_id` int(11) NOT NULL,
  `usuario_autoriza_id` int(11) DEFAULT NULL,
  `usuario_envia_id` int(11) DEFAULT NULL,
  `usuario_recibe_id` int(11) DEFAULT NULL,
  `responsable_movimiento` varchar(150) DEFAULT NULL,
  `referencia_id` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `producto_id`, `tipo`, `cantidad`, `almacen_origen_id`, `almacen_destino_id`, `usuario_registra_id`, `usuario_autoriza_id`, `usuario_envia_id`, `usuario_recibe_id`, `responsable_movimiento`, `referencia_id`, `observaciones`, `fecha`) VALUES
(1, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 1, 'Salida por venta folio: V-260312162333 (Cant. Entregada: 1)', '2026-03-12 22:23:33'),
(2, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 1, 'Salida por entrega parcial. Folio Venta: V-260312162333', '2026-03-12 22:24:18'),
(3, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 2, 'Salida por venta folio: V-260312162439 (Cant. Entregada: 2)', '2026-03-12 22:24:39'),
(4, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 3, 'Salida por venta folio: V-260312162508 (Cant. Entregada: 1)', '2026-03-12 22:25:08'),
(5, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 4, 'Salida por venta folio: V-260312163255 (Cant. Entregada: 1)', '2026-03-12 22:32:55'),
(6, 21, 'salida', 1.00, 3, NULL, 1, NULL, NULL, NULL, NULL, 5, 'Salida por venta folio: V-260312163504 (Cant. Entregada: 1)', '2026-03-12 22:35:04'),
(7, 21, 'salida', 1.00, 4, NULL, 1, NULL, NULL, NULL, NULL, 6, 'Salida por venta folio: V-260312163936 (Cant. Entregada: 1)', '2026-03-12 22:39:36'),
(8, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 7, 'Salida por venta folio: V-260312174105 (Cant. Entregada: 2)', '2026-03-12 23:41:05'),
(9, 20, 'salida', 4.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 8, 'Salida por venta folio: V-260312174127 (Cant. Entregada: 4)', '2026-03-12 23:41:27'),
(10, 21, 'salida', 5.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 9, 'Salida por venta folio: V-260312174157 (Cant. Entregada: 5)', '2026-03-12 23:41:57'),
(11, 21, 'salida', 1.00, 4, NULL, 1, NULL, NULL, NULL, NULL, 6, 'Salida por entrega parcial. Folio Venta: V-260312163936', '2026-03-12 23:43:42'),
(12, 21, 'salida', 1.00, 3, NULL, 1, NULL, NULL, NULL, NULL, 5, 'Salida por entrega parcial. Folio Venta: V-260312163504', '2026-03-12 23:43:50'),
(13, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 4, 'Salida por entrega parcial. Folio Venta: V-260312163255', '2026-03-12 23:43:57'),
(14, 21, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 3, 'Salida por entrega parcial. Folio Venta: V-260312162508', '2026-03-12 23:44:04'),
(15, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 10, 'Salida por venta folio: V-260312220421 (Cant. Entregada: 1)', '2026-03-13 04:04:21'),
(28, 7, 'ajuste', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Se cayo de el camión', '2026-03-13 17:59:13'),
(29, 6, 'ajuste', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Se cayo de el camion', '2026-03-13 18:01:53'),
(30, 5, 'ajuste', 1.00, 1, NULL, 3, NULL, NULL, NULL, 'casa', NULL, 'Se cayo de el camion', '2026-03-13 18:03:55'),
(31, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 11, 'Salida por venta folio: V-260313120521 (Cant. Entregada: 2)', '2026-03-13 18:05:21'),
(44, 17, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Salida Transmutación #18', '2026-03-13 22:23:31'),
(45, 21, 'entrada', 50.00, NULL, 1, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Entrada Transmutación #18', '2026-03-13 22:23:31'),
(46, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 12, 'Salida por venta folio: V-260313163014 (Cant. Entregada: 1)', '2026-03-13 22:30:14'),
(47, 1, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Salida Transmutación #19', '2026-03-13 22:32:42'),
(48, 3, 'entrada', 50.00, NULL, 1, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Entrada Transmutación #19', '2026-03-13 22:32:42'),
(49, 3, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 13, 'Salida por venta folio: V-260313163304 (Cant. Entregada: 1)', '2026-03-13 22:33:04'),
(50, 2, 'ajuste', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, '', '2026-03-13 23:14:23'),
(51, 2, 'ajuste', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, '', '2026-03-13 23:18:19'),
(52, 21, 'ajuste', 100.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'se rompio el alambre', '2026-03-13 23:32:24'),
(53, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 14, 'Salida por venta folio: V-260313173443 (Cant. Entregada: 1)', '2026-03-13 23:34:43'),
(54, 4, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 1, 'Compra Folio: F-49 (Lote: LOTE-1-4-1)', '2026-03-13 23:35:59'),
(55, 4, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 15, 'Salida por venta folio: V-260313173648 (Cant. Entregada: 1)', '2026-03-13 23:36:48'),
(56, 4, 'ajuste', 100.00, 1, NULL, 3, NULL, NULL, NULL, 'casa', NULL, 'el alambre se mojo y se rompio', '2026-03-13 23:37:49'),
(57, 17, 'salida', 4.00, 1, NULL, 3, NULL, NULL, NULL, 'casa', NULL, 'Salida Transmutación #20', '2026-03-13 23:39:59'),
(58, 21, 'entrada', 100.00, NULL, 1, 3, NULL, NULL, NULL, 'casa', NULL, 'Entrada Transmutación #20', '2026-03-13 23:39:59'),
(59, 17, 'salida', 4.00, 2, NULL, 2, NULL, NULL, NULL, 'juan', NULL, 'Salida Transmutación #21', '2026-03-13 23:55:29'),
(60, 21, 'entrada', 40.00, NULL, 2, 2, NULL, NULL, NULL, 'juan', NULL, 'Entrada Transmutación #21', '2026-03-13 23:55:29'),
(61, 2, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Salida Transmutación #22', '2026-03-14 01:12:21'),
(62, 3, 'entrada', 25.00, NULL, 1, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Entrada Transmutación #22', '2026-03-14 01:12:21'),
(63, 1, 'ajuste', 1.00, 2, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'un carro roso el cargamento y se rompio un bulto', '2026-03-14 01:14:20'),
(64, 21, 'traspaso', 100.00, 1, 2, 1, NULL, 1, 2, NULL, NULL, '', '2026-03-17 17:38:13'),
(65, 21, 'salida', 1002.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 16, 'Salida por venta folio: V-260314084531 (Cant. Entregada: 1002)', '2026-03-14 14:45:31'),
(66, 4, 'entrada', 4000.00, NULL, 2, 1, NULL, NULL, NULL, NULL, 2, 'Compra Folio: F-52 (Lote: LOTE-2-4-2)', '2026-03-14 14:49:34'),
(67, 21, 'entrada', 4000.00, NULL, 2, 1, NULL, NULL, NULL, NULL, 3, 'Compra Folio: F-53 (Lote: LOTE-3-21-2)', '2026-03-14 14:50:45'),
(68, 20, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 17, 'Salida por venta folio: V-260314091313. Entregado real: 2 de 10', '2026-03-14 15:13:13'),
(69, 21, 'salida', 178.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 18, 'Salida por venta folio: V-260314091456. Entregado real: 178 de 1000', '2026-03-14 15:14:56'),
(70, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 19, 'Salida por venta folio: V-260314091639. Entregado real: 1 de 1', '2026-03-14 15:16:39'),
(71, 1, 'entrada', 20.00, NULL, 2, 1, NULL, NULL, NULL, NULL, 4, 'Compra Folio: 4 (Lote: LOTE-4-1-2)', '2026-03-14 15:41:03'),
(72, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 19, 'Entrega parcial - Folio: V-260314091639', '2026-03-14 17:28:12'),
(73, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 5, 'Compra Folio: 5 (Lote: LOTE-5-1-1)', '2026-03-14 18:46:22'),
(75, 4, 'entrada', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 19, 'REINGRESO POR CANCELACIÓN - Folio: V-260314091639. Motivo: movimiento de prueba', '2026-03-14 19:19:12'),
(76, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 6, 'Compra Folio: 6 (Lote: LOTE-6-1-1)', '2026-03-17 14:29:47'),
(82, 4, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 1, 'Anulación Compra Folio: 1', '2026-03-17 15:35:56'),
(83, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 7, 'Compra Folio: 7 (Lote: LOTE-7-21-1)', '2026-03-17 15:37:08'),
(84, 4, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 1, 'Anulación Compra Folio: 1', '2026-03-17 15:37:24'),
(85, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 8, 'Compra Folio: 8 (Lote: LOTE-8-21-1)', '2026-03-17 15:42:28'),
(86, 4, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 1, 'Anulación Compra Folio: 1', '2026-03-17 15:56:42'),
(87, 4, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 1, 'Anulación Compra Folio: 1', '2026-03-17 16:02:30'),
(88, 21, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 8, 'ANULACIÓN AUTOMÁTICA COMPRA - FOLIO: 8', '2026-03-17 16:09:35'),
(89, 21, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 7, 'ANULACIÓN AUTOMÁTICA COMPRA - FOLIO: 7', '2026-03-17 16:10:01'),
(90, 1, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 6, 'ANULACIÓN AUTOMÁTICA COMPRA - FOLIO: 6', '2026-03-17 16:10:07'),
(91, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 9, 'Compra Folio: 9 (Lote: LOTE-9-21-1)', '2026-03-17 16:18:25'),
(92, 21, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 9, 'REVERSIÓN COMPRA FOLIO: 9', '2026-03-17 16:18:42'),
(93, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 10, 'Compra Folio: 10 (Lote: LOTE-10-21-1)', '2026-03-17 16:21:03'),
(94, 21, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 10, 'CANCELACIÓN COMPRA: 10 - REVERSIÓN EN ALMACÉN ID: 1', '2026-03-17 16:25:44'),
(95, 4, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 11, 'Compra Folio: 11 (Lote: LOTE-11-4-1)', '2026-03-17 16:33:01'),
(96, 4, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 12, 'Compra Folio: 12 (Lote: LOTE-12-4-1)', '2026-03-17 16:34:45'),
(97, 4, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 12, 'CANCELACIÓN COMPRA: 12 - REVERSIÓN EN ALMACÉN ID: 1', '2026-03-17 16:34:57'),
(98, 4, 'ajuste', 0.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 11, 'CANCELACIÓN COMPRA: 11 - REVERSIÓN EN ALMACÉN ID: 1', '2026-03-17 16:35:50'),
(99, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 13, 'Compra Folio: 13 (Lote: LOTE-13-21-1)', '2026-03-17 16:40:48'),
(100, 21, 'ajuste', 1000.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 13, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 13', '2026-03-17 16:40:54'),
(101, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 14, 'Compra Folio: 14 (Lote: LOTE-14-21-1)', '2026-03-17 16:41:41'),
(102, 21, 'ajuste', 1000.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 14, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 14', '2026-03-17 16:41:50'),
(103, 4, 'salida', 2899.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 21, 'Salida por venta folio: V-260317105027. Entregado real: 2899 de 2899', '2026-03-17 16:50:27'),
(104, 4, 'entrada', 2899.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 21, 'REINGRESO POR CANCELACIÓN - Folio: V-260317105027. Motivo: insuficuencia de stock', '2026-03-17 16:51:12'),
(105, 4, 'salida', 899.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 22, 'Salida por venta folio: V-260317105320. Entregado real: 899 de 899', '2026-03-17 16:53:20'),
(106, 21, 'entrada', 1000.00, NULL, 2, 2, NULL, NULL, NULL, NULL, 15, 'Compra Folio: 15 (Lote: LOTE-15-21-2)', '2026-03-17 17:28:05'),
(107, 21, 'ajuste', 1000.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 15, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 15', '2026-03-17 17:28:19'),
(108, 21, 'entrada', 1000.00, NULL, 2, 2, NULL, NULL, NULL, NULL, 16, 'Compra Folio: 16 (Lote: LOTE-16-21-2)', '2026-03-17 17:29:16'),
(109, 21, 'ajuste', 1000.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 16, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 16', '2026-03-17 17:29:28'),
(110, 4, 'salida', 1000.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 23, 'Salida por venta folio: V-260317114254. Entregado real: 1000 de 1000', '2026-03-17 17:42:54'),
(111, 19, 'salida', 7.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 24, 'Salida por venta folio: V-260317125528. Entregado real: 7 de 8', '2026-03-17 18:55:28'),
(112, 19, 'entrada', 150.00, NULL, 2, 2, NULL, NULL, NULL, NULL, 17, 'Compra Folio: 17 (Lote: LOTE-17-19-2)', '2026-03-17 18:58:26'),
(113, 19, 'entrada', 150.00, NULL, 2, 2, NULL, NULL, NULL, NULL, 18, 'Compra Folio: 18 (Lote: LOTE-18-19-2)', '2026-03-17 19:09:32'),
(114, 19, 'salida', 250.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 25, 'Salida por venta folio: V-260317130948. Entregado real: 250 de 250', '2026-03-17 19:09:49'),
(115, 22, 'entrada', 3.00, NULL, 2, 2, NULL, NULL, NULL, NULL, 19, 'Compra Folio: 19 (Lote: LOTE-19-22-2)', '2026-03-17 20:19:07'),
(116, 4, 'entrada', 10.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 20, 'Compra Folio: 20 (Lote: LOTE-20-4-1)', '2026-03-18 20:14:45'),
(117, 4, 'entrada', 10.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 21, 'Compra Folio: 21 (Lote: LOTE-21-4-1)', '2026-03-18 22:18:21'),
(118, 19, 'entrada', 150.00, NULL, 2, 1, NULL, NULL, NULL, NULL, 22, 'Compra Folio: 22 (Lote: LOTE-22-19-2)', '2026-03-18 22:24:27'),
(119, 4, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 23, 'Compra Folio: 23 (Lote: LOTE-23-4-1)', '2026-03-18 22:56:02'),
(120, 4, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 25, 'Compra Folio: 24 (Lote: LOTE-25-4-1)', '2026-03-18 22:56:55'),
(121, 4, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 26, 'Compra Folio: 25 (Lote: LOTE-26-4-1)', '2026-03-18 23:01:13'),
(122, 4, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 32, 'Compra Folio: 26 (Lote: LOTE-32-4-1)', '2026-03-18 23:11:01'),
(123, 4, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 33, 'Compra Folio: 27 (Lote: LOTE-33-4-1)', '2026-03-18 23:11:39'),
(124, 4, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 34, 'Compra Folio: 28 (Lote: LOTE-34-4-1)', '2026-03-18 23:15:14'),
(125, 4, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 35, 'Compra Folio: 29 (Lote: LOTE-35-4-1)', '2026-03-18 23:17:01'),
(126, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 36, 'Compra Folio: 30 (Lote: LOTE-36-21-1)', '2026-03-19 01:43:10'),
(127, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 26, 'Salida por venta folio: V-260319160155. Entregado real: 1 de 1', '2026-03-19 22:01:55'),
(128, 21, 'salida', 1.00, 4, NULL, 1, NULL, NULL, NULL, NULL, 27, 'Salida por venta folio: V-260320175122. Entregado real: 1 de 1', '2026-03-20 23:51:22'),
(129, 21, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 37, 'Compra Folio: 31 (Lote: LOTE-37-21-1)', '2026-03-21 02:47:10'),
(130, 4, 'traspaso', 2.00, 1, 2, 1, 1, 1, 1, NULL, NULL, '', '2026-03-21 15:38:10'),
(131, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 28, 'Salida por venta folio: V-260321113425. Entregado real: 1 de 1', '2026-03-21 17:34:25'),
(132, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 29, 'Salida por venta folio: V-260321113518. Entregado real: 1 de 1', '2026-03-21 17:35:18'),
(133, 7, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 29, 'Salida por venta folio: V-260321113518. Entregado real: 1 de 1', '2026-03-21 17:35:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos_vendedores`
--

CREATE TABLE `pedidos_vendedores` (
  `id` int(11) NOT NULL,
  `folio` varchar(20) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `prioridad` enum('Baja','Media','Alta') DEFAULT 'Media',
  `estatus` tinyint(1) NOT NULL DEFAULT 1,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `permisos_roles`
--

CREATE TABLE `permisos_roles` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `permisos_roles`
--

INSERT INTO `permisos_roles` (`id`, `rol_id`, `modulo`) VALUES
(1210, 1, 'almacenes'),
(1211, 1, 'clientes'),
(1200, 1, 'clientesEstatus'),
(1215, 1, 'compras'),
(1201, 1, 'Configuracion'),
(1207, 1, 'corteCaja'),
(1199, 1, 'entregas'),
(1216, 1, 'finanzas'),
(1198, 1, 'inicio'),
(1214, 1, 'Mermas'),
(1212, 1, 'movimientos'),
(1217, 1, 'proveedores'),
(1206, 1, 'repartos'),
(1203, 1, 'solicitudesCompra'),
(1204, 1, 'trabajadores'),
(1202, 1, 'transmutaciones'),
(1213, 1, 'usuarios'),
(1205, 1, 'vehiculos'),
(1209, 1, 'ventas'),
(1208, 1, 'ventashistorial'),
(1230, 2, 'almacenes'),
(1231, 2, 'clientes'),
(1220, 2, 'clientesEstatus'),
(1235, 2, 'compras'),
(1221, 2, 'Configuracion'),
(1227, 2, 'corteCaja'),
(1219, 2, 'entregas'),
(1236, 2, 'finanzas'),
(1218, 2, 'inicio'),
(1234, 2, 'Mermas'),
(1232, 2, 'movimientos'),
(1237, 2, 'proveedores'),
(1226, 2, 'repartos'),
(1223, 2, 'solicitudesCompra'),
(1224, 2, 'trabajadores'),
(1222, 2, 'transmutaciones'),
(1233, 2, 'usuarios'),
(1225, 2, 'vehiculos'),
(1229, 2, 'ventas'),
(1228, 2, 'ventashistorial'),
(1249, 3, 'almacenes'),
(1250, 3, 'clientes'),
(1240, 3, 'clientesEstatus'),
(1254, 3, 'compras'),
(1241, 3, 'Configuracion'),
(1246, 3, 'corteCaja'),
(1239, 3, 'entregas'),
(1255, 3, 'finanzas'),
(1238, 3, 'inicio'),
(1253, 3, 'Mermas'),
(1251, 3, 'movimientos'),
(1256, 3, 'proveedores'),
(1245, 3, 'repartos'),
(1243, 3, 'trabajadores'),
(1242, 3, 'transmutaciones'),
(1252, 3, 'usuarios'),
(1244, 3, 'vehiculos'),
(1248, 3, 'ventas'),
(1247, 3, 'ventashistorial'),
(1257, 4, 'clientesEstatus');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `precios_producto`
--

CREATE TABLE `precios_producto` (
  `id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `precio_minorista` decimal(10,2) NOT NULL,
  `precio_mayorista` decimal(10,2) NOT NULL,
  `precio_distribuidor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `precios_producto`
--

INSERT INTO `precios_producto` (`id`, `producto_id`, `almacen_id`, `precio_minorista`, `precio_mayorista`, `precio_distribuidor`) VALUES
(108, 4, 1, 3.00, 2.88, 2.75),
(109, 4, 2, 3.00, 2.88, 2.75),
(110, 4, 3, 3.00, 2.88, 2.75),
(111, 4, 4, 3.00, 2.88, 2.75),
(112, 2, 1, 120.00, 115.00, 110.00),
(113, 2, 2, 120.00, 115.00, 110.00),
(114, 2, 3, 120.00, 115.00, 110.00),
(115, 2, 4, 120.00, 115.00, 110.00),
(116, 1, 1, 216.00, 207.00, 198.00),
(117, 1, 2, 216.00, 207.00, 198.00),
(118, 1, 3, 216.00, 207.00, 198.00),
(119, 1, 4, 216.00, 207.00, 198.00),
(120, 3, 1, 4.80, 4.60, 4.40),
(121, 3, 2, 4.80, 4.60, 4.40),
(122, 3, 3, 4.80, 4.60, 4.40),
(123, 3, 4, 4.80, 4.60, 4.40),
(124, 5, 1, 42.00, 40.25, 38.50),
(125, 5, 2, 42.00, 40.25, 38.50),
(126, 5, 3, 42.00, 40.25, 38.50),
(127, 5, 4, 42.00, 40.25, 38.50),
(139, 6, 1, 10.00, 9.00, 8.00),
(140, 6, 2, 10.00, 9.00, 8.00),
(141, 6, 3, 10.00, 9.00, 8.00),
(142, 6, 4, 10.00, 9.00, 8.00),
(143, 7, 1, 0.00, 0.00, 0.00),
(144, 7, 2, 0.00, 0.00, 0.00),
(145, 8, 1, 0.00, 0.00, 0.00),
(146, 8, 2, 0.00, 0.00, 0.00),
(147, 9, 1, 0.00, 0.00, 0.00),
(148, 9, 2, 0.00, 0.00, 0.00),
(149, 10, 1, 0.00, 0.00, 0.00),
(150, 10, 2, 0.00, 0.00, 0.00),
(151, 11, 1, 200.00, 150.00, 140.00),
(152, 11, 2, 200.00, 150.00, 140.00),
(153, 11, 3, 200.00, 150.00, 140.00),
(154, 11, 4, 200.00, 150.00, 140.00),
(155, 12, 1, 100.00, 90.00, 80.00),
(156, 12, 2, 100.00, 90.00, 80.00),
(157, 12, 3, 100.00, 90.00, 80.00),
(158, 12, 4, 100.00, 90.00, 80.00),
(159, 13, 1, 100.00, 90.00, 80.00),
(160, 13, 2, 100.00, 90.00, 80.00),
(161, 13, 3, 100.00, 90.00, 80.00),
(162, 13, 4, 100.00, 90.00, 80.00),
(163, 14, 1, 110.00, 100.00, 90.00),
(164, 14, 2, 110.00, 100.00, 90.00),
(165, 14, 3, 110.00, 100.00, 90.00),
(166, 14, 4, 110.00, 100.00, 90.00),
(167, 15, 1, 90.00, 80.00, 70.00),
(168, 15, 2, 90.00, 80.00, 70.00),
(169, 15, 3, 90.00, 80.00, 70.00),
(170, 15, 4, 90.00, 80.00, 70.00),
(171, 16, 1, 100.00, 90.00, 80.00),
(172, 16, 2, 100.00, 90.00, 80.00),
(173, 16, 3, 100.00, 90.00, 80.00),
(174, 16, 4, 100.00, 90.00, 80.00),
(175, 17, 1, 100.00, 90.00, 80.00),
(176, 17, 2, 100.00, 90.00, 80.00),
(177, 17, 3, 100.00, 90.00, 80.00),
(178, 17, 4, 100.00, 90.00, 80.00),
(179, 18, 1, 0.00, 0.00, 0.00),
(180, 19, 1, 200.00, 190.00, 180.00),
(181, 19, 2, 200.00, 190.00, 180.00),
(182, 19, 3, 200.00, 190.00, 180.00),
(183, 19, 4, 200.00, 190.00, 180.00),
(184, 20, 1, 100.00, 90.00, 80.00),
(185, 20, 2, 100.00, 90.00, 80.00),
(186, 20, 3, 100.00, 90.00, 80.00),
(187, 20, 4, 100.00, 90.00, 80.00),
(188, 21, 1, 20.00, 18.00, 17.00),
(189, 21, 2, 200.00, 180.00, 170.00),
(190, 21, 3, 200.00, 180.00, 170.00),
(191, 21, 4, 200.00, 180.00, 170.00),
(192, 22, 1, 10.00, 9.00, 8.00),
(193, 22, 2, 10.00, 9.00, 8.00),
(194, 22, 3, 10.00, 9.00, 8.00),
(195, 22, 4, 10.00, 9.00, 8.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id` int(11) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad_medida` varchar(50) DEFAULT NULL,
  `unidad_reporte` varchar(20) DEFAULT NULL,
  `factor_conversion` decimal(10,2) DEFAULT 1.00,
  `fiscal_clave_prod` varchar(20) DEFAULT NULL,
  `fiscal_clave_unidad` varchar(20) DEFAULT NULL,
  `precio_adquisicion` decimal(10,2) NOT NULL,
  `impuesto_iva` decimal(5,2) DEFAULT 16.00,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id`, `sku`, `nombre`, `descripcion`, `unidad_medida`, `unidad_reporte`, `factor_conversion`, `fiscal_clave_prod`, `fiscal_clave_unidad`, `precio_adquisicion`, `impuesto_iva`, `activo`, `fecha_creacion`, `categoria_id`) VALUES
(1, 'CEM-50', 'Cemento Gris 50kg', NULL, 'Bulto', 'Tonelada', 20.00, NULL, NULL, 180.00, 16.00, 1, '2026-03-04 14:34:52', 1),
(2, 'CEM-25', 'Cemento Gris 25kg', NULL, 'Bulto', 'Tonelada', 40.00, NULL, NULL, 100.00, 16.00, 1, '2026-03-04 14:34:52', 1),
(3, 'CEM-KG', 'Cemento Suelto (Granel)', NULL, 'Kg', 'Kg', 1.00, NULL, NULL, 4.50, 16.00, 1, '2026-03-04 14:34:52', 1),
(4, 'AN-10', 'Anillo/Estribo 10x10', '', 'PZA', 'Millar', 1000.00, '0', '', 3.00, 16.00, 1, '2026-03-04 14:34:52', 2),
(5, 'CL-1P', 'Clavo Estándar 1 pulgada', NULL, 'Kg', 'Caja', 20.00, NULL, NULL, 35.00, 16.00, 1, '2026-03-04 14:34:52', 2),
(6, 'T001', 'Tornillo', 'Tornillo 1', 'kg', 'Bote', 20.00, '2121', '', 45.00, 16.00, 1, '2026-03-04 15:34:24', 2),
(7, 'MC01', 'Mortero', 'Mortero', 'Bulto', 'Tonelada', 20.00, '2121232', '3232', 98.00, 16.00, 1, '2026-03-04 15:39:32', 2),
(8, 'Yeso-25', 'Yeso 45 kg', 'Yeso blanco', 'Bulto', 'Tonelada', 40.00, '32312313', '2323', 68.00, 8.00, 1, '2026-03-04 17:24:48', 1),
(9, 'T004', 'Tornillo 04', 'tornillos 04', 'PZA', 'Kilo', 100.00, '23221', '232', 50.00, 16.00, 1, '2026-03-04 18:10:51', 2),
(10, 'llanC01', 'LLanta de carretilla', 'Llanta para carretilla', 'PZA', '1', 1.00, '12344', 'h23', 55.00, 16.00, 1, '2026-03-05 22:59:22', 4),
(11, 'cmb-50', 'Cemento blanco 50kg', '', 'Bulto', 'Tonelada', 20.00, '', '', 0.00, 16.00, 1, '2026-03-06 20:04:54', 1),
(12, 'Yeso-G25', 'Yeso Gris 25 Kg', '', 'Bulto', 'Tonelada', 40.00, '', '', 0.00, 16.00, 1, '2026-03-06 20:10:59', 1),
(13, 'YESO-B50', 'yeso 50kg', '', 'Bulto', 'Tonelada', 20.00, '', '', 0.00, 16.00, 1, '2026-03-06 20:27:03', 1),
(14, 'Varilla-1/4', 'Varilla 1/4 pulgadas', '', 'PZA', 'Tonelada', 10.00, '', '', 0.00, 16.00, 1, '2026-03-06 20:28:32', 2),
(15, 'YESO-25 -G', 'YESO GRIS 25KG', '', 'BUlto', 'Tonelada', 20.00, '', '', 68.00, 16.00, 1, '2026-03-06 20:30:16', 1),
(16, 'Varilla-2/4', 'Varilla 2/4 pulgadas', '', 'PZA', 'Tonelada', 10.00, '', '', 0.00, 16.00, 1, '2026-03-06 20:33:34', 1),
(17, 'Varilla3/4', 'Varilla 3/4 pulgadas', '', 'PZA', 'Tonelada', 10.00, '12345', '', 165.00, 16.00, 1, '2026-03-06 20:36:04', 2),
(18, 'T001T', 'Kit T3 Libre', 'Kit de desarmadores truper', 'PZA', '1', 10.00, '2121', '1212', 45.00, 16.00, 1, '2026-03-06 22:54:07', 2),
(19, 'CT-ARM10', 'Castillo Armex 10x10', '', 'PZA', 'Tonelada', 150.00, '', '', 125.00, 16.00, 1, '2026-03-10 17:22:16', 6),
(20, 'CAM-VA2', 'Castillo armex varilla solida', '', 'PZA', 'PZA', 1.00, 'ARM12', '', 310.00, 16.00, 1, '2026-03-10 18:43:11', 6),
(21, 'Ani-40', 'anillo 40x40', 'Anillo de 40x40', 'Kg', 'Tonelada', 1000.00, '1234', 'h123', 20000.00, 16.00, 1, '2026-03-12 15:05:50', 2),
(22, 'SOL-01', 'Solvente para pintura', '', 'Ltr', 'Galon', 3.00, '', '', 0.00, 16.00, 1, '2026-03-17 20:18:31', 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre_comercial` varchar(150) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre_comercial`, `razon_social`, `rfc`, `correo`, `telefono`, `direccion`, `activo`, `creado_at`) VALUES
(2, 'Materiales Centro', 'Materiales Centro', 'ASDFG1234567', 'MATERIALESCENTRO@GMAIL.COM', '1234567890', NULL, 1, '2026-03-14 17:35:45'),
(3, 'TECNOCENTRO', 'COMERCIALIZADORA TECNOLÓGICA DEL CENTRO SA DE CV', 'AATS980713', '', '1234567890', NULL, 1, '2026-03-14 17:47:10'),
(4, 'Cementos Fortaleza', 'cementos fortaleza', 'TCC010101ABC', '', '1234567890', NULL, 1, '2026-03-14 17:52:23'),
(5, 'TechNorte', 'COMERCIALIZADORA TECNOLÓGICA DEL CENTRO Norte SA DE CV', 'TCC010101ABC', '', '5523789029', NULL, 1, '2026-03-14 18:00:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_salida_lotes`
--

CREATE TABLE `registro_salida_lotes` (
  `id` int(11) NOT NULL,
  `movimiento_id` int(11) NOT NULL,
  `usuario_patio_id` int(11) NOT NULL,
  `fecha_despacho` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_despacho_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `registro_salida_lotes`
--

INSERT INTO `registro_salida_lotes` (`id`, `movimiento_id`, `usuario_patio_id`, `fecha_despacho`, `usuario_despacho_id`) VALUES
(14, 1, 1, '2026-03-12 23:47:13', 1),
(15, 8, 1, '2026-03-12 23:47:37', 1),
(16, 10, 1, '2026-03-13 01:17:33', 1),
(17, 15, 1, '2026-03-13 04:05:17', 1),
(18, 31, 3, '2026-03-13 18:06:22', 3),
(19, 55, 3, '2026-03-13 23:38:19', 3),
(20, 110, 2, '2026-03-17 17:43:20', 2),
(21, 111, 2, '2026-03-17 19:02:34', 2),
(22, 65, 2, '2026-03-17 19:08:24', 2),
(23, 114, 2, '2026-03-17 19:10:36', 2),
(24, 105, 1, '2026-03-17 19:35:30', 1),
(25, 127, 1, '2026-03-19 22:02:14', 1),
(26, 46, 1, '2026-03-20 22:49:36', 1),
(27, 49, 1, '2026-03-20 23:33:33', 1),
(28, 128, 1, '2026-03-20 23:52:07', 1),
(29, 131, 1, '2026-03-21 17:40:48', 1),
(30, 132, 1, '2026-03-21 17:55:14', 1),
(31, 133, 1, '2026-03-21 17:56:48', 1),
(32, 68, 1, '2026-03-21 18:01:59', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre`) VALUES
(1, 'administrador'),
(2, 'gestor_almacen'),
(4, 'Repartidor'),
(3, 'supervisor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_compra`
--

CREATE TABLE `solicitudes_compra` (
  `id` int(11) NOT NULL,
  `administrador_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','ordenado','recibido','cancelado') DEFAULT 'pendiente',
  `compra_id_final` int(11) DEFAULT NULL COMMENT 'Relación con la compra real una vez ejecutada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitudes_compra`
--

INSERT INTO `solicitudes_compra` (`id`, `administrador_id`, `almacen_id`, `proveedor_id`, `fecha_creacion`, `estado`, `compra_id_final`) VALUES
(1, 1, 1, 4, '2026-03-17 23:31:15', 'pendiente', NULL),
(5, 1, 1, 4, '2026-03-17 23:54:18', 'pendiente', NULL),
(7, 1, 2, 4, '2026-03-18 22:22:18', 'pendiente', NULL),
(8, 1, 1, 4, '2026-03-18 22:32:50', 'recibido', NULL),
(9, 1, 1, 4, '2026-03-19 01:42:40', 'recibido', NULL),
(10, 1, 1, 4, '2026-03-21 02:46:28', 'recibido', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitudes_pedidos`
--

CREATE TABLE `solicitudes_pedidos` (
  `id` int(11) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','en_compra','listo_entrega','finalizado','cancelado') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `rol` enum('administrador','vendedor','chofer','almacenista','cargador') NOT NULL DEFAULT 'vendedor',
  `estado` enum('activo','inactivo','vacaciones','en_ruta') NOT NULL DEFAULT 'activo',
  `almacen_id` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `nombre`, `telefono`, `rol`, `estado`, `almacen_id`, `fecha_registro`) VALUES
(1, 'Juan', '12345678', 'chofer', 'activo', 1, '2026-03-19 16:31:51'),
(2, 'Manuel', '123456789', 'cargador', 'activo', 2, '2026-03-19 22:37:53'),
(3, 'Patroclo', '123456789', 'cargador', 'activo', 2, '2026-03-20 18:49:33'),
(4, 'Arnulfo', '1234567890', 'cargador', 'activo', 4, '2026-03-21 17:10:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transmutaciones`
--

CREATE TABLE `transmutaciones` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transmutaciones`
--

INSERT INTO `transmutaciones` (`id`, `usuario_id`, `almacen_id`, `fecha`, `observaciones`) VALUES
(18, 1, 1, '2026-03-13 22:23:31', 'tranformacion del material'),
(19, 1, 1, '2026-03-13 22:32:42', 'se rompio el bulto'),
(20, 3, 1, '2026-03-13 23:39:59', 'necesitamos anillos'),
(21, 2, 2, '2026-03-13 23:55:29', ''),
(22, 1, 1, '2026-03-14 01:12:21', 'se rompio el bulto se recupero');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transmutacion_detalle`
--

CREATE TABLE `transmutacion_detalle` (
  `id` int(11) NOT NULL,
  `transmutacion_id` int(11) NOT NULL,
  `movimiento_id` int(11) NOT NULL,
  `tipo` enum('salida','entrada') NOT NULL,
  `producto_id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario_historico` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transmutacion_detalle`
--

INSERT INTO `transmutacion_detalle` (`id`, `transmutacion_id`, `movimiento_id`, `tipo`, `producto_id`, `lote_id`, `cantidad`, `costo_unitario_historico`, `costo_unitario`) VALUES
(1, 18, 44, 'salida', 17, 112, 2.00, 250.00, 250.00),
(2, 18, 45, 'entrada', 21, 120, 50.00, 5.50, 5.50),
(3, 19, 47, 'salida', 1, 81, 1.00, 4.00, 4.00),
(4, 19, 48, 'entrada', 3, 89, 50.00, 85.00, 85.00),
(5, 20, 57, 'salida', 17, 112, 4.00, 250.00, 250.00),
(6, 20, 58, 'entrada', 21, 145, 100.00, 10.00, 10.00),
(7, 21, 59, 'salida', 17, 113, 4.00, 250.00, 250.00),
(8, 21, 60, 'entrada', 21, 146, 40.00, 25.00, 25.00),
(9, 22, 61, 'salida', 2, 85, 1.00, 12.50, 12.50),
(10, 22, 62, 'entrada', 3, 89, 25.00, 85.00, 85.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transporte_consolidacion`
--

CREATE TABLE `transporte_consolidacion` (
  `id` int(11) NOT NULL,
  `viaje_folio` varchar(50) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `reparto_id` int(11) NOT NULL,
  `estatus_consolidado` enum('abierto','cerrado') DEFAULT 'abierto',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transporte_consolidacion`
--

INSERT INTO `transporte_consolidacion` (`id`, `viaje_folio`, `vehiculo_id`, `reparto_id`, `estatus_consolidado`, `fecha_creacion`) VALUES
(1, 'RUT-260320-01-12', 1, 7, 'cerrado', '2026-03-20 17:50:44'),
(2, 'RUT-260320-01-12', 1, 8, 'cerrado', '2026-03-20 17:50:58'),
(3, 'RUT-260320-01-12', 1, 9, 'cerrado', '2026-03-20 17:51:14'),
(4, 'RUT-260320-02-84', 2, 10, 'cerrado', '2026-03-20 17:53:12'),
(5, 'RUT-260320-02-20', 2, 11, 'cerrado', '2026-03-20 19:31:26'),
(6, 'RUT-260320-02-14', 2, 12, 'cerrado', '2026-03-20 22:49:08'),
(7, 'RUT-260320-02-14', 2, 13, 'cerrado', '2026-03-20 22:49:24'),
(8, 'RUT-260320-02-14', 2, 14, 'cerrado', '2026-03-20 22:50:08'),
(9, 'RUT-260320-02-14', 2, 15, 'cerrado', '2026-03-20 23:33:40'),
(13, 'RUT-260321-02-36', 2, 27, 'abierto', '2026-03-21 18:52:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transporte_repartos_maestro`
--

CREATE TABLE `transporte_repartos_maestro` (
  `id` int(11) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `usuario_encargado_id` int(11) NOT NULL COMMENT 'El chofer o responsable del reparto',
  `entrega_venta_id` int(11) DEFAULT NULL COMMENT 'Relación con la entrega (si aplica)',
  `fecha_programada` date NOT NULL,
  `hora_salida_real` datetime DEFAULT NULL,
  `hora_llegada_real` datetime DEFAULT NULL,
  `km_inicial` int(11) DEFAULT NULL,
  `km_final` int(11) DEFAULT NULL,
  `estado_reparto` enum('preparacion','en_transito','completado','cancelado') DEFAULT 'preparacion',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transporte_repartos_maestro`
--

INSERT INTO `transporte_repartos_maestro` (`id`, `vehiculo_id`, `usuario_encargado_id`, `entrega_venta_id`, `fecha_programada`, `hora_salida_real`, `hora_llegada_real`, `km_inicial`, `km_final`, `estado_reparto`, `observaciones`) VALUES
(2, 1, 1, 127, '2026-03-19', NULL, NULL, 0, 0, 'completado', NULL),
(3, 1, 1, 105, '2026-03-20', NULL, NULL, NULL, NULL, 'completado', NULL),
(4, 1, 1, 114, '2026-03-20', NULL, NULL, NULL, NULL, 'completado', NULL),
(5, 1, 1, 65, '2026-03-20', NULL, NULL, NULL, NULL, 'completado', NULL),
(6, 1, 1, 111, '2026-03-20', NULL, NULL, NULL, NULL, 'completado', NULL),
(7, 1, 1, 110, '2026-03-20', NULL, '2026-03-20 13:31:10', NULL, NULL, 'completado', NULL),
(8, 1, 1, 55, '2026-03-20', NULL, '2026-03-20 13:31:10', NULL, NULL, 'completado', NULL),
(9, 1, 1, 31, '2026-03-20', NULL, '2026-03-20 13:31:10', NULL, NULL, 'completado', NULL),
(10, 2, 2, 15, '2026-03-20', NULL, '2026-03-20 13:31:15', NULL, NULL, 'completado', NULL),
(11, 2, 1, 10, '2026-03-20', NULL, '2026-03-20 16:47:34', NULL, NULL, 'completado', NULL),
(12, 2, 1, 8, '2026-03-20', NULL, '2026-03-21 11:56:28', NULL, NULL, 'completado', NULL),
(13, 2, 1, 1, '2026-03-20', NULL, '2026-03-21 11:56:28', NULL, NULL, 'completado', NULL),
(14, 2, 1, 46, '2026-03-20', NULL, '2026-03-21 11:56:28', NULL, NULL, 'completado', NULL),
(15, 2, 1, 49, '2026-03-20', NULL, '2026-03-21 11:56:28', NULL, NULL, 'completado', NULL),
(21, 999, 1, 128, '2026-03-21', NULL, '2026-03-21 11:33:52', NULL, NULL, 'completado', NULL),
(22, 999, 1, 131, '2026-03-21', NULL, '2026-03-21 11:54:54', NULL, NULL, 'completado', NULL),
(23, 999, 1, 132, '2026-03-21', NULL, '2026-03-21 11:55:21', NULL, NULL, 'completado', NULL),
(27, 2, 1, 68, '2026-03-21', NULL, NULL, NULL, NULL, 'en_transito', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transporte_rutas_puntos`
--

CREATE TABLE `transporte_rutas_puntos` (
  `id` int(11) NOT NULL,
  `reparto_id` int(11) NOT NULL,
  `orden_visita` int(11) NOT NULL DEFAULT 1,
  `descripcion_punto` varchar(255) NOT NULL COMMENT 'Ej. Bodega Central o Domicilio Cliente X',
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `llegada_estimada` datetime DEFAULT NULL,
  `llegada_real` datetime DEFAULT NULL,
  `estado_punto` enum('pendiente','visitado','omitido') DEFAULT 'pendiente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transporte_rutas_puntos`
--

INSERT INTO `transporte_rutas_puntos` (`id`, `reparto_id`, `orden_visita`, `descripcion_punto`, `latitud`, `longitud`, `llegada_estimada`, `llegada_real`, `estado_punto`) VALUES
(2, 2, 1, 'cementos fortaleza centro', NULL, NULL, NULL, NULL, 'pendiente'),
(3, 3, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(4, 4, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(5, 5, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(6, 6, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(7, 7, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(8, 8, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(9, 9, 1, 'la cima 11', NULL, NULL, NULL, NULL, 'pendiente'),
(10, 10, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(11, 11, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(12, 12, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(13, 13, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(14, 14, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(15, 15, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(16, 21, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(17, 22, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(18, 23, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(22, 27, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transporte_tripulantes_detalle`
--

CREATE TABLE `transporte_tripulantes_detalle` (
  `id` int(11) NOT NULL,
  `reparto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'Ayudante o personal de apoyo',
  `rol_secundario` varchar(50) DEFAULT 'Ayudante' COMMENT 'Ej. Estibador, Copiloto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transporte_tripulantes_detalle`
--

INSERT INTO `transporte_tripulantes_detalle` (`id`, `reparto_id`, `usuario_id`, `rol_secundario`) VALUES
(2, 2, 2, 'Ayudante'),
(3, 3, 2, 'Ayudante'),
(4, 4, 2, 'Ayudante'),
(5, 5, 2, 'Ayudante'),
(6, 6, 2, 'Ayudante'),
(7, 9, 2, 'Ayudante'),
(8, 10, 1, 'Ayudante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `transporte_vehiculos`
--

CREATE TABLE `transporte_vehiculos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL COMMENT 'Ej. Torton Internacional',
  `placas` varchar(20) NOT NULL,
  `serie_vin` varchar(50) DEFAULT NULL,
  `modelo_año` int(4) DEFAULT NULL,
  `capacidad_carga_kg` decimal(10,2) DEFAULT NULL,
  `estado_unidad` enum('disponible','en_ruta','mantenimiento','fuera_servicio') DEFAULT 'disponible',
  `activo` tinyint(1) DEFAULT 1,
  `almacen_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `transporte_vehiculos`
--

INSERT INTO `transporte_vehiculos` (`id`, `nombre`, `placas`, `serie_vin`, `modelo_año`, `capacidad_carga_kg`, `estado_unidad`, `activo`, `almacen_id`) VALUES
(1, 'Kenwork', '1234567', '123456789', 2016, 1200.00, 'disponible', 1, 2),
(2, 'Freightliner Cascadia', '12345678', '123456789', 2021, 9000.00, 'en_ruta', 1, 1),
(3, 'jeap', '123456789', '123456789', 2022, 1.00, 'disponible', 1, 2),
(999, 'MOSTRADOR / PATIO', 'CLIENTE', 'ENTREGA-DIRECTA', 2026, 0.00, 'disponible', 1, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `traspasos`
--

CREATE TABLE `traspasos` (
  `id` int(11) NOT NULL,
  `almacen_origen_id` int(11) NOT NULL,
  `almacen_destino_id` int(11) NOT NULL,
  `usuario_solicita_id` int(11) NOT NULL,
  `usuario_autoriza_id` int(11) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','cancelado') DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_autorizacion` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `almacen_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `nombre`, `password`, `rol_id`, `almacen_id`, `activo`, `fecha_creacion`) VALUES
(1, 'admin', 'Administrador General', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NULL, 1, '2026-02-23 23:52:17'),
(2, 'juan', 'juan', '$2y$10$7wQp8D.mUvvpuRviPO6G0.8chLmuwjP.ckbkCTaFEjEMBjEgDLHVW', 2, 2, 1, '2026-02-28 14:38:54'),
(3, 'casa', 'casa', '$2y$10$GSEt/ZVPPLDwQrPY4Ams8eTS1z27IFxtsFkH9kgVtUPTftcrnKhsC', 2, 1, 1, '2026-03-02 22:31:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL,
  `folio` varchar(50) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `subtotal` decimal(12,2) NOT NULL DEFAULT 0.00,
  `descuento` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL DEFAULT 0.00,
  `estado_pago` enum('pendiente','parcial','pagado') DEFAULT 'pagado',
  `estado_entrega` enum('pendiente','parcial','entregado') DEFAULT 'pendiente',
  `estado_general` enum('activa','cancelada') DEFAULT 'activa',
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id`, `folio`, `id_cliente`, `almacen_id`, `usuario_id`, `fecha`, `subtotal`, `descuento`, `total`, `estado_pago`, `estado_entrega`, `estado_general`, `observaciones`) VALUES
(1, 'V-260312162333', 1, 1, 3, '2026-03-12 16:23:33', 400.00, 0.00, 400.00, 'pagado', 'entregado', 'activa', ''),
(2, 'V-260312162439', 1, 1, 3, '2026-03-12 16:24:39', 400.00, 0.00, 400.00, 'pagado', 'entregado', 'activa', ''),
(3, 'V-260312162508', 1, 1, 3, '2026-03-12 16:25:08', 600.00, 0.00, 600.00, 'pagado', 'entregado', 'activa', ''),
(4, 'V-260312163255', 1, 1, 3, '2026-03-12 16:32:55', 400.00, 0.00, 400.00, 'pagado', 'entregado', 'activa', ''),
(5, 'V-260312163504', 1, 3, 1, '2026-03-12 16:35:04', 400.00, 0.00, 400.00, 'pagado', 'entregado', 'activa', ''),
(6, 'V-260312163936', 1, 4, 1, '2026-03-12 16:39:36', 400.00, 0.00, 400.00, 'pagado', 'entregado', 'activa', ''),
(7, 'V-260312174105', 1, 1, 3, '2026-03-12 17:41:05', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'activa', ''),
(8, 'V-260312174127', 1, 1, 3, '2026-03-12 17:41:27', 400.00, 0.00, 400.00, 'pagado', 'entregado', 'activa', ''),
(9, 'V-260312174157', 1, 1, 1, '2026-03-12 17:41:57', 100.00, 0.00, 100.00, 'pagado', 'entregado', 'activa', ''),
(10, 'V-260312220421', 1, 1, 1, '2026-03-12 22:04:21', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(11, 'V-260313120521', 10, 1, 3, '2026-03-13 12:05:21', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'activa', ''),
(12, 'V-260313163014', 1, 1, 1, '2026-03-13 16:30:14', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(13, 'V-260313163304', 1, 1, 1, '2026-03-13 16:33:04', 4.80, 0.00, 4.80, 'pagado', 'entregado', 'activa', ''),
(14, 'V-260313173443', 1, 1, 3, '2026-03-13 17:34:43', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(15, 'V-260313173648', 1, 1, 3, '2026-03-13 17:36:48', 3.00, 0.00, 3.00, 'pagado', 'entregado', 'activa', ''),
(16, 'V-260314084531', 2, 2, 2, '2026-03-14 08:45:31', 200400.00, 0.00, 200400.00, 'pagado', 'entregado', 'activa', ''),
(17, 'V-260314091313', 1, 1, 1, '2026-03-14 09:13:13', 1000.00, 0.00, 1000.00, 'pagado', 'parcial', 'activa', ''),
(18, 'V-260314091456', 1, 1, 1, '2026-03-14 09:14:56', 20000.00, 0.00, 20000.00, 'pagado', 'parcial', 'activa', ''),
(19, 'V-260314091639', 1, 1, 1, '2026-03-14 09:16:39', 6.00, 0.00, 6.00, 'parcial', 'entregado', 'cancelada', ''),
(20, 'V-260314151546', 1, 1, 3, '2026-03-14 15:15:46', 20020.00, 0.00, 20020.00, 'pagado', 'pendiente', 'activa', ''),
(21, 'V-260317105027', 1, 1, 3, '2026-03-17 10:50:27', 8697.00, 0.00, 8697.00, 'pagado', 'entregado', 'cancelada', ''),
(22, 'V-260317105320', 1, 1, 1, '2026-03-17 10:53:20', 2697.00, 0.00, 2697.00, 'pagado', 'entregado', 'activa', ''),
(23, 'V-260317114254', 2, 2, 2, '2026-03-17 11:42:54', 3000.00, 0.00, 3000.00, 'pagado', 'entregado', 'activa', ''),
(24, 'V-260317125528', 2, 2, 2, '2026-03-17 12:55:28', 1600.00, 0.00, 1600.00, 'pagado', 'parcial', 'activa', ''),
(25, 'V-260317130948', 2, 2, 2, '2026-03-17 13:09:48', 50000.00, 0.00, 50000.00, 'pagado', 'entregado', 'activa', ''),
(26, 'V-260319160155', 11, 1, 1, '2026-03-19 16:01:55', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(27, 'V-260320175122', 1, 4, 1, '2026-03-20 17:51:22', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(28, 'V-260321113425', 1, 1, 1, '2026-03-21 11:34:25', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(29, 'V-260321113518', 1, 1, 1, '2026-03-21 11:35:18', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', '');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_api_token` (`api_token`),
  ADD UNIQUE KEY `rfc_almacen_unique` (`rfc`,`almacen_id`),
  ADD KEY `idx_cliente_almacen` (`almacen_id`);

--
-- Indices de la tabla `compras`
--
ALTER TABLE `compras`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `almacen_id` (`almacen_id`),
  ADD KEY `usuario_registra_id` (`usuario_registra_id`);

--
-- Indices de la tabla `config_transmutaciones`
--
ALTER TABLE `config_transmutaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_almacen_origen_destino` (`almacen_id`,`producto_origen_id`,`producto_destino_id`),
  ADD KEY `fk_config_trans_origen` (`producto_origen_id`),
  ADD KEY `fk_config_trans_destino` (`producto_destino_id`);

--
-- Indices de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_id` (`compra_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `detalle_entrega`
--
ALTER TABLE `detalle_entrega`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entrega_id` (`entrega_id`),
  ADD KEY `detalle_venta_id` (`detalle_venta_id`);

--
-- Indices de la tabla `detalle_gasto`
--
ALTER TABLE `detalle_gasto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gasto_id` (`gasto_id`);

--
-- Indices de la tabla `detalle_pedido_vendedor`
--
ALTER TABLE `detalle_pedido_vendedor`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_detalle_pedido` (`pedido_id`);

--
-- Indices de la tabla `detalle_solicitud_compra`
--
ALTER TABLE `detalle_solicitud_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_det_sol_cabecera` (`solicitud_id`),
  ADD KEY `fk_det_sol_producto` (`producto_id`);

--
-- Indices de la tabla `detalle_traspaso`
--
ALTER TABLE `detalle_traspaso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `traspaso_id` (`traspaso_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `entregas_venta`
--
ALTER TABLE `entregas_venta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `faltantes_ingreso`
--
ALTER TABLE `faltantes_ingreso`
  ADD PRIMARY KEY (`id`),
  ADD KEY `compra_id` (`compra_id`);

--
-- Indices de la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `almacen_id` (`almacen_id`),
  ADD KEY `usuario_registra_id` (`usuario_registra_id`);

--
-- Indices de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `venta_id` (`venta_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `almacen_id` (`almacen_id`,`producto_id`),
  ADD KEY `producto_id` (`producto_id`);

--
-- Indices de la tabla `lotes_ingresos_detalle`
--
ALTER TABLE `lotes_ingresos_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ingreso_lote` (`lote_id`),
  ADD KEY `idx_ingreso_compra` (`detalle_compra_id`);

--
-- Indices de la tabla `lotes_movimientos_salida`
--
ALTER TABLE `lotes_movimientos_salida`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_salida_lote` (`lote_id`),
  ADD KEY `idx_salida_entrega` (`entrega_venta_id`),
  ADD KEY `idx_salida_detalle_venta` (`detalle_venta_id`);

--
-- Indices de la tabla `lotes_stock`
--
ALTER TABLE `lotes_stock`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_lote_producto` (`producto_id`),
  ADD KEY `idx_lote_almacen` (`almacen_id`);

--
-- Indices de la tabla `mermas`
--
ALTER TABLE `mermas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_merma_movimiento` (`movimiento_id`),
  ADD KEY `fk_merma_almacen` (`almacen_id`),
  ADD KEY `fk_merma_producto` (`producto_id`),
  ADD KEY `fk_merma_lote` (`lote_id`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `identificador` (`identificador`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `producto_id` (`producto_id`),
  ADD KEY `almacen_origen_id` (`almacen_origen_id`),
  ADD KEY `almacen_destino_id` (`almacen_destino_id`),
  ADD KEY `usuario_registra_id` (`usuario_registra_id`),
  ADD KEY `usuario_autoriza_id` (`usuario_autoriza_id`),
  ADD KEY `usuario_envia_id` (`usuario_envia_id`),
  ADD KEY `usuario_recibe_id` (`usuario_recibe_id`);

--
-- Indices de la tabla `pedidos_vendedores`
--
ALTER TABLE `pedidos_vendedores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `fk_pedido_vendedor` (`vendedor_id`),
  ADD KEY `fk_pedido_almacen` (`almacen_id`);

--
-- Indices de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rol_modulo` (`rol_id`,`modulo`);

--
-- Indices de la tabla `precios_producto`
--
ALTER TABLE `precios_producto`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `producto_id` (`producto_id`,`almacen_id`),
  ADD KEY `almacen_id` (`almacen_id`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `fk_categoria` (`categoria_id`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `registro_salida_lotes`
--
ALTER TABLE `registro_salida_lotes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_movimiento_unico` (`movimiento_id`),
  ADD KEY `fk_reg_salida_user` (`usuario_patio_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `solicitudes_compra`
--
ALTER TABLE `solicitudes_compra`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sol_comp_admin` (`administrador_id`),
  ADD KEY `fk_sol_comp_compra` (`compra_id_final`);

--
-- Indices de la tabla `solicitudes_pedidos`
--
ALTER TABLE `solicitudes_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sol_ped_vendedor` (`vendedor_id`),
  ADD KEY `fk_sol_ped_cliente` (`cliente_id`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trabajador_almacen` (`almacen_id`);

--
-- Indices de la tabla `transmutaciones`
--
ALTER TABLE `transmutaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trans_usuario` (`usuario_id`),
  ADD KEY `fk_trans_almacen` (`almacen_id`);

--
-- Indices de la tabla `transmutacion_detalle`
--
ALTER TABLE `transmutacion_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_det_trans_cabecera` (`transmutacion_id`),
  ADD KEY `fk_det_trans_prod` (`producto_id`),
  ADD KEY `fk_det_trans_lote` (`lote_id`),
  ADD KEY `fk_det_trans_mov` (`movimiento_id`);

--
-- Indices de la tabla `transporte_consolidacion`
--
ALTER TABLE `transporte_consolidacion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_vehiculo_activo` (`vehiculo_id`,`estatus_consolidado`);

--
-- Indices de la tabla `transporte_repartos_maestro`
--
ALTER TABLE `transporte_repartos_maestro`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_reparto_vehiculo` (`vehiculo_id`),
  ADD KEY `idx_reparto_encargado` (`usuario_encargado_id`);

--
-- Indices de la tabla `transporte_rutas_puntos`
--
ALTER TABLE `transporte_rutas_puntos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ruta_punto_reparto` (`reparto_id`);

--
-- Indices de la tabla `transporte_tripulantes_detalle`
--
ALTER TABLE `transporte_tripulantes_detalle`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_trip_reparto` (`reparto_id`),
  ADD KEY `fk_trip_usuario` (`usuario_id`);

--
-- Indices de la tabla `transporte_vehiculos`
--
ALTER TABLE `transporte_vehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placas` (`placas`),
  ADD KEY `fk_vehiculo_almacen` (`almacen_id`);

--
-- Indices de la tabla `traspasos`
--
ALTER TABLE `traspasos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `almacen_origen_id` (`almacen_origen_id`),
  ADD KEY `almacen_destino_id` (`almacen_destino_id`),
  ADD KEY `usuario_solicita_id` (`usuario_solicita_id`),
  ADD KEY `usuario_autoriza_id` (`usuario_autoriza_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `fk_usuario_almacen` (`almacen_id`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folio` (`folio`),
  ADD KEY `almacen_id` (`almacen_id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `almacenes`
--
ALTER TABLE `almacenes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT de la tabla `config_transmutaciones`
--
ALTER TABLE `config_transmutaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `detalle_entrega`
--
ALTER TABLE `detalle_entrega`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de la tabla `detalle_gasto`
--
ALTER TABLE `detalle_gasto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido_vendedor`
--
ALTER TABLE `detalle_pedido_vendedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_solicitud_compra`
--
ALTER TABLE `detalle_solicitud_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `detalle_traspaso`
--
ALTER TABLE `detalle_traspaso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `entregas_venta`
--
ALTER TABLE `entregas_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT de la tabla `faltantes_ingreso`
--
ALTER TABLE `faltantes_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- AUTO_INCREMENT de la tabla `lotes_ingresos_detalle`
--
ALTER TABLE `lotes_ingresos_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `lotes_movimientos_salida`
--
ALTER TABLE `lotes_movimientos_salida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `lotes_stock`
--
ALTER TABLE `lotes_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=179;

--
-- AUTO_INCREMENT de la tabla `mermas`
--
ALTER TABLE `mermas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT de la tabla `pedidos_vendedores`
--
ALTER TABLE `pedidos_vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1258;

--
-- AUTO_INCREMENT de la tabla `precios_producto`
--
ALTER TABLE `precios_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `registro_salida_lotes`
--
ALTER TABLE `registro_salida_lotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `solicitudes_compra`
--
ALTER TABLE `solicitudes_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `solicitudes_pedidos`
--
ALTER TABLE `solicitudes_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `transmutaciones`
--
ALTER TABLE `transmutaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `transmutacion_detalle`
--
ALTER TABLE `transmutacion_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `transporte_consolidacion`
--
ALTER TABLE `transporte_consolidacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `transporte_repartos_maestro`
--
ALTER TABLE `transporte_repartos_maestro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `transporte_rutas_puntos`
--
ALTER TABLE `transporte_rutas_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `transporte_tripulantes_detalle`
--
ALTER TABLE `transporte_tripulantes_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `transporte_vehiculos`
--
ALTER TABLE `transporte_vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1000;

--
-- AUTO_INCREMENT de la tabla `traspasos`
--
ALTER TABLE `traspasos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_cliente_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `compras`
--
ALTER TABLE `compras`
  ADD CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`usuario_registra_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `config_transmutaciones`
--
ALTER TABLE `config_transmutaciones`
  ADD CONSTRAINT `fk_config_trans_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `fk_config_trans_destino` FOREIGN KEY (`producto_destino_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `fk_config_trans_origen` FOREIGN KEY (`producto_origen_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  ADD CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_entrega`
--
ALTER TABLE `detalle_entrega`
  ADD CONSTRAINT `detalle_entrega_ibfk_1` FOREIGN KEY (`entrega_id`) REFERENCES `entregas_venta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_entrega_ibfk_2` FOREIGN KEY (`detalle_venta_id`) REFERENCES `detalle_venta` (`id`);

--
-- Filtros para la tabla `detalle_gasto`
--
ALTER TABLE `detalle_gasto`
  ADD CONSTRAINT `fk_detalle_gasto_cabecera` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_pedido_vendedor`
--
ALTER TABLE `detalle_pedido_vendedor`
  ADD CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos_vendedores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `detalle_solicitud_compra`
--
ALTER TABLE `detalle_solicitud_compra`
  ADD CONSTRAINT `fk_det_sol_cabecera` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_compra` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_det_sol_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_traspaso`
--
ALTER TABLE `detalle_traspaso`
  ADD CONSTRAINT `detalle_traspaso_ibfk_1` FOREIGN KEY (`traspaso_id`) REFERENCES `traspasos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_traspaso_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  ADD CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `entregas_venta`
--
ALTER TABLE `entregas_venta`
  ADD CONSTRAINT `entregas_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `entregas_venta_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `gastos`
--
ALTER TABLE `gastos`
  ADD CONSTRAINT `fk_gasto_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `fk_gasto_usuario` FOREIGN KEY (`usuario_registra_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  ADD CONSTRAINT `historial_pagos_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historial_pagos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `lotes_ingresos_detalle`
--
ALTER TABLE `lotes_ingresos_detalle`
  ADD CONSTRAINT `fk_ingreso_detalle_compra` FOREIGN KEY (`detalle_compra_id`) REFERENCES `detalle_compra` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ingreso_lote_ref` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `lotes_movimientos_salida`
--
ALTER TABLE `lotes_movimientos_salida`
  ADD CONSTRAINT `fk_salida_detalle_venta` FOREIGN KEY (`detalle_venta_id`) REFERENCES `detalle_venta` (`id`),
  ADD CONSTRAINT `fk_salida_entrega_venta` FOREIGN KEY (`entrega_venta_id`) REFERENCES `entregas_venta` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_salida_lote_ref` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`);

--
-- Filtros para la tabla `lotes_stock`
--
ALTER TABLE `lotes_stock`
  ADD CONSTRAINT `fk_lotes_stock_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `fk_lotes_stock_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `mermas`
--
ALTER TABLE `mermas`
  ADD CONSTRAINT `fk_merma_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`),
  ADD CONSTRAINT `fk_merma_movimiento` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_3` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_4` FOREIGN KEY (`usuario_registra_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_5` FOREIGN KEY (`usuario_autoriza_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_6` FOREIGN KEY (`usuario_envia_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `movimientos_ibfk_7` FOREIGN KEY (`usuario_recibe_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `pedidos_vendedores`
--
ALTER TABLE `pedidos_vendedores`
  ADD CONSTRAINT `fk_pedido_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `fk_pedido_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  ADD CONSTRAINT `fk_permisos_rol_db` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `precios_producto`
--
ALTER TABLE `precios_producto`
  ADD CONSTRAINT `precios_producto_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  ADD CONSTRAINT `precios_producto_ibfk_2` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`);

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`);

--
-- Filtros para la tabla `registro_salida_lotes`
--
ALTER TABLE `registro_salida_lotes`
  ADD CONSTRAINT `fk_reg_salida_mov` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`),
  ADD CONSTRAINT `fk_reg_salida_user` FOREIGN KEY (`usuario_patio_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `solicitudes_compra`
--
ALTER TABLE `solicitudes_compra`
  ADD CONSTRAINT `fk_sol_comp_admin` FOREIGN KEY (`administrador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_sol_comp_compra` FOREIGN KEY (`compra_id_final`) REFERENCES `compras` (`id`);

--
-- Filtros para la tabla `solicitudes_pedidos`
--
ALTER TABLE `solicitudes_pedidos`
  ADD CONSTRAINT `fk_sol_ped_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `fk_sol_ped_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD CONSTRAINT `fk_trabajador_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `transmutaciones`
--
ALTER TABLE `transmutaciones`
  ADD CONSTRAINT `fk_trans_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `fk_trans_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `transmutacion_detalle`
--
ALTER TABLE `transmutacion_detalle`
  ADD CONSTRAINT `fk_det_trans_cabecera` FOREIGN KEY (`transmutacion_id`) REFERENCES `transmutaciones` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_det_trans_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`),
  ADD CONSTRAINT `fk_det_trans_mov` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`),
  ADD CONSTRAINT `fk_det_trans_prod` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`);

--
-- Filtros para la tabla `transporte_repartos_maestro`
--
ALTER TABLE `transporte_repartos_maestro`
  ADD CONSTRAINT `fk_reparto_encargado` FOREIGN KEY (`usuario_encargado_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_reparto_vehiculo` FOREIGN KEY (`vehiculo_id`) REFERENCES `transporte_vehiculos` (`id`);

--
-- Filtros para la tabla `transporte_rutas_puntos`
--
ALTER TABLE `transporte_rutas_puntos`
  ADD CONSTRAINT `fk_ruta_punto_reparto` FOREIGN KEY (`reparto_id`) REFERENCES `transporte_repartos_maestro` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `transporte_tripulantes_detalle`
--
ALTER TABLE `transporte_tripulantes_detalle`
  ADD CONSTRAINT `fk_trip_reparto_cab` FOREIGN KEY (`reparto_id`) REFERENCES `transporte_repartos_maestro` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_trip_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `transporte_vehiculos`
--
ALTER TABLE `transporte_vehiculos`
  ADD CONSTRAINT `fk_vehiculo_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `traspasos`
--
ALTER TABLE `traspasos`
  ADD CONSTRAINT `traspasos_ibfk_1` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `traspasos_ibfk_2` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `traspasos_ibfk_3` FOREIGN KEY (`usuario_solicita_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `traspasos_ibfk_4` FOREIGN KEY (`usuario_autoriza_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`);

--
-- Filtros para la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  ADD CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
