-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 24-04-2026 a las 01:54:24
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
  `hora_cierre_programada` time DEFAULT '18:00:00',
  `ubicacion` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `almacenes`
--

INSERT INTO `almacenes` (`id`, `codigo`, `nombre`, `hora_cierre_programada`, `ubicacion`, `activo`, `fecha_creacion`) VALUES
(1, 'ALM-CM', 'Casa de Materiales', '11:48:00', 'Zona Centro', 1, '2026-02-26 14:34:03'),
(2, 'ALM-ER', 'El Rancho', '18:00:00', 'Carretera Principal', 1, '2026-02-26 14:34:03'),
(3, 'ALM-TEN', 'Tenango', '18:00:00', 'Sucursal Tenango', 1, '2026-02-26 14:34:03'),
(4, 'ALM-VC', 'Valle de Chalco', '18:00:00', 'Sucursal Valle de Chalco', 1, '2026-02-26 14:34:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cajas_fuertes`
--

CREATE TABLE `cajas_fuertes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `almacen_id` int(11) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT 1,
  `Saldo` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cajas_fuertes`
--

INSERT INTO `cajas_fuertes` (`id`, `nombre`, `almacen_id`, `descripcion`, `estatus`, `Saldo`) VALUES
(1, 'Caja Principal A1', 1, 'Caja fuerte del almacén 1', 1, 2299.5),
(2, 'Caja Principal A2', 2, 'Caja fuerte del almacén 2', 1, 10),
(3, 'Caja Principal A3', 3, 'Caja fuerte del almacén 3', 1, 0),
(4, 'Caja Principal A4', 4, 'Caja fuerte del almacén 4', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `capital_categorias`
--

CREATE TABLE `capital_categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo_operacion` enum('entrada','salida','traspaso') NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `capital_categorias`
--

INSERT INTO `capital_categorias` (`id`, `nombre`, `tipo_operacion`, `descripcion`, `estatus`) VALUES
(1, 'Apertura de Caja / Fondo', 'entrada', 'Dinero inicial para dar cambio al abrir el turno', 1),
(2, 'Traspaso entre Almacenes', 'traspaso', 'Movimiento de efectivo enviado a otra sucursal', 1),
(3, 'Préstamo a Socio / Dueño', 'salida', 'Retiro de efectivo para uso personal de los socios', 1),
(4, 'Inyección de Capital', 'entrada', 'Aportación extra de socios para flujo de efectivo', 1),
(5, 'Movimiento a Caja Fuerte', 'salida', 'Retiro de excedente de caja para resguardo', 1),
(6, 'Pago de Nómina (Efectivo)', 'salida', 'Pago de sueldos realizado directamente desde el cajón', 1),
(7, 'Faltante de Caja (Discrepancia)', 'salida', 'Ajuste negativo por dinero perdido o faltante en arqueo', 1),
(8, 'Sobrante de Caja (Discrepancia)', 'entrada', 'Ajuste positivo por dinero sobrante encontrado en arqueo', 1),
(9, 'Préstamo Bancario', 'entrada', 'Ingreso de capital por créditos o instituciones externas', 1),
(10, 'Ingreso desde Caja Fuerte', 'entrada', 'Reingreso de efectivo guardado a la caja operativa', 1),
(11, 'Retiro de Banco (Efectivo)', 'entrada', 'Dinero que sale del banco y entra a la caja de la sucursal', 1),
(12, 'Traspaso entre Bancos', 'traspaso', 'Movimiento de fondos entre dos cuentas bancarias propias', 1),
(13, 'Ingreso por pago de préstamo', 'entrada', 'Ingreso de dinero por pago de préstamo', 1);

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
(13, '123 Materiales', '123Materiales', '1234567890', NULL, '12345', '123materiales@123materiales.com', NULL, 'Materiales 123', 'G01', 1, '2026-03-21 16:52:56', 1, 'f2f5f17194dec15897682f1aa3b5a6a1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_saldos`
--

CREATE TABLE `clientes_saldos` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `saldo_a_favor` decimal(12,2) DEFAULT 0.00 COMMENT 'Dinero real que el cliente tiene para usar',
  `saldo_en_contra` decimal(12,2) DEFAULT 0.00 COMMENT 'Deuda pendiente del cliente',
  `ultima_venta_id` int(11) DEFAULT NULL,
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `clientes_saldos`
--

INSERT INTO `clientes_saldos` (`id`, `cliente_id`, `saldo_a_favor`, `saldo_en_contra`, `ultima_venta_id`, `ultima_actualizacion`) VALUES
(27, 1, 88.00, 0.00, 265, '2026-04-22 02:35:10'),
(30, 13, 0.00, 0.00, 204, '2026-04-17 07:01:49'),
(147, 10, 4366.00, 0.00, 241, '2026-04-09 07:17:09'),
(232, 11, 0.00, 0.00, 208, '2026-04-08 04:26:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_saldos_log`
--

CREATE TABLE `clientes_saldos_log` (
  `id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `venta_id` int(11) DEFAULT NULL,
  `tipo_movimiento` enum('cargo','abono') NOT NULL COMMENT 'cargo: aumenta deuda o quita saldo, abono: paga deuda o aumenta saldo a favor',
  `monto` decimal(12,2) NOT NULL,
  `monto_operacion_total` decimal(12,2) DEFAULT 0.00,
  `monto_pagado_momento` decimal(12,2) DEFAULT 0.00,
  `referencia_tipo` enum('venta','cancelacion','devolucion','pago_manual','pago_credito') NOT NULL,
  `referencia_id` int(11) DEFAULT NULL COMMENT 'ID de la venta o movimiento que originó esto',
  `observaciones` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Monto total de la compra vs lo que pagó en ese instante';

--
-- Volcado de datos para la tabla `clientes_saldos_log`
--

INSERT INTO `clientes_saldos_log` (`id`, `cliente_id`, `venta_id`, `tipo_movimiento`, `monto`, `monto_operacion_total`, `monto_pagado_momento`, `referencia_tipo`, `referencia_id`, `observaciones`, `fecha_registro`, `usuario_id`) VALUES
(25, 1, 116, 'abono', 20.00, 20.00, 20.00, 'pago_manual', 116, 'Abono manual vía Efectivo. Ref Venta: #116', '2026-03-31 15:06:56', 3),
(26, 1, 117, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 15:16:23', 3),
(27, 13, 118, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 15:27:12', 3),
(28, 1, 119, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 15:55:39', 3),
(29, 1, 119, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 119, 'Abono manual vía AJUSTE. Ref Venta: #119', '2026-03-31 23:55:51', 3),
(30, 1, 120, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:02:10', 3),
(31, 1, 120, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 120, 'Abono manual vía AJUSTE. Ref Venta: #120', '2026-04-01 00:02:20', 3),
(32, 1, 121, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:22:03', 3),
(33, 1, 121, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 121, 'Abono manual vía AJUSTE. Ref Venta: #121', '2026-04-01 00:24:36', 3),
(34, 1, 122, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:28:06', 3),
(35, 1, 122, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 122, 'Abono manual vía AJUSTE. Ref Venta: #122', '2026-04-01 00:28:24', 3),
(36, 1, 123, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:28:45', 3),
(37, 1, 124, 'abono', 0.00, 43.00, 43.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:52:32', 3),
(38, 1, 125, 'abono', 0.00, 43.00, 43.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:54:34', 3),
(39, 1, 125, 'abono', 3.00, 0.00, 3.00, 'pago_manual', 125, 'Abono manual vía ABONO_EDICION. Ref Venta: #125', '2026-04-01 00:54:54', 3),
(40, 1, 126, 'abono', 0.00, 40.00, 40.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:55:53', 3),
(41, 1, 126, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 126, 'Abono manual vía AJUSTE. Ref Venta: #126', '2026-04-01 00:56:13', 3),
(42, 1, 127, 'abono', 0.00, 40.00, 40.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:56:48', 3),
(43, 1, 127, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 127, 'Abono manual vía AJUSTE. Ref Venta: #127', '2026-04-01 00:57:03', 3),
(44, 1, 128, 'abono', 0.00, 40.00, 40.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 16:59:11', 3),
(45, 1, 128, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 128, 'Abono manual vía AJUSTE. Ref Venta: #128', '2026-04-01 00:59:27', 3),
(46, 1, 125, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 125, 'Abono manual vía ABONO_EDICION. Ref Venta: #125', '2026-04-01 01:00:02', 3),
(47, 1, 129, 'abono', 0.00, 60.00, 60.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 17:10:41', 3),
(48, 1, 130, 'abono', 0.00, 60.00, 60.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 17:23:15', 1),
(49, 1, 130, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 130, 'Abono manual vía ABONO_EDICION. Ref Venta: #130', '2026-04-01 01:31:00', 1),
(50, 1, 130, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 130, 'Abono manual vía CARGO_EDICION. Ref Venta: #130', '2026-04-01 02:01:35', 1),
(51, 1, 130, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 130, 'Abono manual vía CARGO_EDICION. Ref Venta: #130', '2026-04-01 02:13:09', 1),
(52, 1, 131, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 18:45:59', 1),
(53, 1, 132, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 18:54:12', 1),
(54, 1, 133, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 18:54:33', 1),
(55, 1, 140, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 19:28:17', 1),
(56, 1, 141, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 19:30:33', 1),
(57, 1, 141, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 141, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #141', '2026-04-01 03:30:33', 1),
(58, 1, 142, 'cargo', 60.00, 60.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 19:30:57', 1),
(59, 1, 143, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 20:28:41', 1),
(60, 1, 144, 'cargo', 10.00, 20.00, 10.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 21:26:53', 1),
(61, 1, 144, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 144, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #144', '2026-04-01 05:26:53', 1),
(62, 1, 145, 'cargo', 10.00, 20.00, 10.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 21:29:29', 1),
(63, 1, 145, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 145, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #145', '2026-04-01 05:29:29', 1),
(64, 1, 146, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 21:34:29', 1),
(65, 1, 146, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 146, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #146', '2026-04-01 05:34:29', 1),
(66, 1, 147, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 21:35:40', 1),
(67, 1, 147, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 147, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #147', '2026-04-01 05:35:40', 1),
(68, 1, 147, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 147, 'Abono manual vía AJUSTE. Ref Venta: #147', '2026-04-01 05:41:08', 1),
(69, 1, 146, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 146, 'Abono manual vía AJUSTE. Ref Venta: #146', '2026-04-01 05:41:16', 1),
(70, 1, 144, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 144, 'Abono manual vía Efectivo. Ref Venta: #144', '2026-03-31 21:41:23', 1),
(71, 1, 145, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 145, 'Abono manual vía Efectivo. Ref Venta: #145', '2026-03-31 21:41:33', 1),
(72, 1, 130, 'abono', 80.00, 0.00, 80.00, 'pago_manual', 130, 'Abono manual vía Efectivo. Ref Venta: #130', '2026-03-31 21:41:48', 1),
(73, 1, 148, 'cargo', 60.00, 60.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 21:44:55', 1),
(74, 1, 148, 'abono', 60.00, 0.00, 60.00, 'pago_manual', 148, 'Abono manual vía Efectivo. Ref Venta: #148', '2026-03-31 21:45:10', 1),
(75, 1, 149, 'cargo', 40.00, 40.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 21:46:09', 1),
(76, 1, 149, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 149, 'Abono manual vía Efectivo. Ref Venta: #149', '2026-03-31 21:49:32', 1),
(77, 1, 150, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 21:50:37', 1),
(78, 1, 151, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 21:55:55', 1),
(79, 1, 151, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 151, 'Abono manual vía Efectivo. Ref Venta: #151', '2026-03-31 21:56:11', 1),
(80, 1, 152, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:01:25', 1),
(81, 1, 152, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 152, 'Abono manual vía AJUSTE. Ref Venta: #152', '2026-04-01 06:01:36', 1),
(82, 1, 153, 'abono', 0.00, 40.00, 40.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:02:17', 1),
(83, 1, 153, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 153, 'Abono manual vía AJUSTE. Ref Venta: #153', '2026-04-01 06:02:27', 1),
(84, 1, 154, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 22:04:15', 1),
(85, 1, 154, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 154, 'Abono manual vía Efectivo. Ref Venta: #154', '2026-03-31 22:04:26', 1),
(86, 1, 154, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 154, 'Abono manual vía Efectivo. Ref Venta: #154', '2026-03-31 22:04:31', 1),
(87, 1, 154, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 154, 'Abono manual vía Efectivo. Ref Venta: #154', '2026-03-31 22:04:51', 1),
(88, 1, 155, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 22:05:35', 1),
(89, 1, 155, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 155, 'Abono manual vía Efectivo. Ref Venta: #155', '2026-03-31 22:05:46', 1),
(90, 1, 156, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-03-31 22:06:13', 1),
(91, 1, 157, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:17:04', 1),
(92, 1, 158, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:19:03', 1),
(93, 1, 159, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:21:15', 1),
(94, 1, 160, 'abono', 0.00, 100.00, 100.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:21:33', 1),
(95, 1, 159, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 159, 'Abono manual vía AJUSTE. Ref Venta: #159', '2026-04-01 06:36:56', 1),
(96, 1, 149, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 149, 'Abono manual vía ABONO_EDICION. Ref Venta: #149', '2026-04-01 06:51:43', 1),
(97, 1, 148, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 148, 'Abono manual vía ABONO_EDICION. Ref Venta: #148', '2026-04-01 06:53:03', 1),
(98, 1, 161, 'abono', 0.00, 100.00, 100.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:53:37', 1),
(99, 1, 161, 'abono', 80.00, 0.00, 80.00, 'pago_manual', 161, 'Abono manual vía ABONO_EDICION. Ref Venta: #161', '2026-04-01 06:53:53', 1),
(100, 1, 162, 'abono', 0.00, 180.00, 180.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:54:26', 1),
(101, 1, 162, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 162, 'Abono manual vía ABONO_EDICION. Ref Venta: #162', '2026-04-01 06:54:43', 1),
(102, 1, 162, 'abono', 140.00, 0.00, 140.00, 'pago_manual', 162, 'Abono manual vía ABONO_EDICION. Ref Venta: #162', '2026-04-01 06:55:08', 1),
(103, 1, 163, 'abono', 0.00, 23.00, 23.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 22:58:33', 1),
(104, 1, 163, 'abono', 3.00, 0.00, 3.00, 'pago_manual', 163, 'Abono manual vía ABONO_EDICION. Ref Venta: #163', '2026-04-01 07:01:18', 1),
(105, 1, 164, 'abono', 0.00, 63.00, 63.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 23:01:54', 1),
(106, 1, 164, 'abono', 60.00, 0.00, 60.00, 'pago_manual', 164, 'Abono manual vía ABONO_EDICION. Ref Venta: #164', '2026-04-01 07:02:13', 1),
(107, 1, 164, 'abono', 60.00, 0.00, 60.00, 'pago_manual', 164, 'Abono manual vía CARGO_EDICION. Ref Venta: #164', '2026-04-01 07:02:46', 1),
(108, 1, 164, 'abono', 3.00, 0.00, 3.00, 'pago_manual', 164, 'Abono manual vía ABONO_EDICION. Ref Venta: #164', '2026-04-01 07:07:20', 1),
(109, 1, 164, 'abono', 63.00, 0.00, 63.00, 'pago_manual', 164, 'Abono manual vía AJUSTE. Ref Venta: #164', '2026-04-01 07:15:34', 1),
(110, 1, 165, 'abono', 0.00, 26.00, 26.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 23:35:59', 1),
(111, 1, 165, 'abono', 6.00, 0.00, 6.00, 'pago_manual', 165, 'Abono manual vía ABONO_EDICION. Ref Venta: #165', '2026-04-01 07:36:14', 1),
(112, 1, 166, 'abono', 0.00, 26.00, 26.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 23:36:52', 1),
(113, 1, 166, 'abono', 6.00, 0.00, 6.00, 'pago_manual', 166, 'Abono manual vía ABONO_EDICION. Ref Venta: #166', '2026-04-01 07:37:23', 1),
(114, 1, 167, 'abono', 0.00, 43.00, 43.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 23:38:32', 1),
(115, 1, 168, 'abono', 0.00, 23.00, 23.00, 'venta', NULL, 'Venta liquidada', '2026-03-31 23:39:05', 1),
(116, 1, 168, 'abono', 23.00, 0.00, 23.00, 'pago_manual', 168, 'Abono manual vía AJUSTE. Ref Venta: #168', '2026-04-01 22:40:34', 1),
(117, 1, 167, 'abono', 43.00, 0.00, 43.00, 'pago_manual', 167, 'Abono manual vía AJUSTE. Ref Venta: #167', '2026-04-01 22:41:46', 1),
(118, 1, 169, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-01 14:42:28', 1),
(119, 1, 166, 'abono', 26.00, 0.00, 26.00, 'pago_manual', 166, 'Abono manual vía AJUSTE. Ref Venta: #166', '2026-04-01 23:00:35', 1),
(120, 1, 170, 'cargo', 60.00, 60.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-01 15:01:17', 1),
(121, 1, 171, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-01 15:20:45', 1),
(122, 1, 171, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 171, 'Abono manual vía CANCELACION_SIN_PAGO. Ref Venta: #171', '2026-04-01 23:20:56', 1),
(123, 1, 171, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 171, 'Abono manual vía CANCELACION_SIN_PAGO. Ref Venta: #171', '2026-04-01 23:21:01', 1),
(124, 1, 171, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 171, 'Abono manual vía CANCELACION_SIN_PAGO. Ref Venta: #171', '2026-04-01 23:22:05', 1),
(125, 1, 172, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-01 15:29:02', 1),
(126, 1, 172, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 172, 'Abono manual vía Efectivo. Ref Venta: #172', '2026-04-01 15:29:33', 1),
(127, 1, 172, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 172, 'Abono manual vía CANCEL_CARGO_VENTA. Ref Venta: #172', '2026-04-01 23:29:45', 1),
(128, 1, 172, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 172, 'Abono manual vía RESTITUIR_PAGO_A_FAVOR. Ref Venta: #172', '2026-04-01 23:29:45', 1),
(129, 1, 173, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-01 15:39:09', 1),
(130, 1, 173, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 173, 'Abono manual vía Efectivo. Ref Venta: #173', '2026-04-01 15:39:40', 1),
(131, 1, 174, 'abono', 0.00, 40.00, 40.00, 'venta', NULL, 'Venta liquidada', '2026-04-01 15:40:23', 1),
(132, 1, 175, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-01 15:47:26', 1),
(133, 1, 175, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 175, 'Abono manual vía Efectivo. Ref Venta: #175', '2026-04-01 15:48:04', 1),
(134, 1, 175, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 175, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #175', '2026-04-01 23:49:15', 1),
(135, 1, 175, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 175, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #175', '2026-04-01 23:49:15', 1),
(136, 1, 176, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-01 15:53:35', 1),
(137, 1, 176, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 176, 'Abono manual vía Efectivo. Ref Venta: #176', '2026-04-01 15:53:52', 1),
(138, 1, 176, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 176, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #176', '2026-04-01 23:54:19', 1),
(139, 1, 176, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 176, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #176', '2026-04-01 23:54:19', 1),
(140, 1, 178, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-04-01 16:30:06', 1),
(141, 1, 33, 'abono', 4320.00, 0.00, 4320.00, 'pago_manual', 33, 'Abono manual vía Efectivo. Ref Venta: #33', '2026-04-06 18:40:10', 1),
(142, 1, 150, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 150, 'Abono manual vía Efectivo. Ref Venta: #150', '2026-04-06 18:40:42', 1),
(143, 1, 156, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 156, 'Abono manual vía Efectivo. Ref Venta: #156', '2026-04-06 18:40:54', 1),
(144, 1, 179, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-06 18:44:06', 1),
(145, 1, 179, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 179, 'Abono manual vía Efectivo. Ref Venta: #179', '2026-04-06 18:44:20', 1),
(146, 10, 42, 'abono', 806.00, 0.00, 806.00, 'pago_manual', 42, 'Abono manual vía Efectivo. Ref Venta: #42', '2026-04-06 20:49:34', 1),
(147, 10, 42, 'abono', 1.00, 0.00, 1.00, 'pago_manual', 42, 'Abono manual vía Efectivo. Ref Venta: #42', '2026-04-07 06:57:05', 1),
(148, 10, 42, 'abono', 1.00, 0.00, 1.00, 'pago_manual', 42, 'Abono manual vía Efectivo. Ref Venta: #42', '2026-04-07 07:13:06', 1),
(149, 10, 42, 'abono', 206.00, 0.00, 206.00, 'pago_manual', 42, 'Abono manual vía Saldo a Favor. Ref Venta: #42', '2026-04-07 23:06:03', 1),
(150, 10, 42, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 42, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #42', '2026-04-07 23:20:09', 1),
(151, 10, 42, 'abono', 80.00, 0.00, 80.00, 'pago_manual', 42, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #42', '2026-04-07 23:22:39', 1),
(152, 10, 42, 'abono', 100.00, 0.00, 100.00, 'pago_manual', 42, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #42', '2026-04-07 23:25:24', 1),
(153, 10, 180, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-04-07 15:26:43', 1),
(154, 10, 181, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 15:27:17', 1),
(155, 10, 182, 'cargo', 10.00, 20.00, 10.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 15:28:06', 1),
(156, 10, 182, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 182, 'Abono manual vía Efectivo. Ref Venta: #182', '2026-04-07 15:29:49', 1),
(157, 10, 182, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 182, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #182', '2026-04-07 23:30:09', 1),
(158, 10, 181, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 181, 'Abono manual vía Efectivo. Ref Venta: #181', '2026-04-07 15:37:06', 1),
(159, 10, 181, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 181, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #181', '2026-04-07 23:37:15', 1),
(160, 1, 183, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-04-07 15:37:32', 1),
(161, 1, 183, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 183, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #183', '2026-04-07 23:37:46', 1),
(162, 1, 184, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 15:38:21', 1),
(163, 1, 184, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 184, 'Abono manual vía Efectivo. Ref Venta: #184', '2026-04-07 15:38:56', 1),
(164, 1, 184, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 184, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #184', '2026-04-07 23:39:11', 1),
(165, 1, 185, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 15:42:08', 1),
(166, 1, 186, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 15:48:32', 1),
(167, 1, 187, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 15:57:59', 1),
(168, 1, 187, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 187, 'Abono manual vía DEUDA_GENERADA_VENTA. Ref Venta: #187', '2026-04-07 23:57:59', 1),
(169, 1, 188, 'abono', 0.00, 20.00, 20.00, 'venta', NULL, 'Venta liquidada', '2026-04-07 15:58:49', 1),
(170, 1, 188, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 188, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #188', '2026-04-07 23:58:49', 1),
(171, 1, 189, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 15:59:42', 1),
(172, 1, 189, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 189, 'Abono manual vía DEUDA_GENERADA_VENTA. Ref Venta: #189', '2026-04-07 23:59:42', 1),
(173, 1, 190, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:29:44', 1),
(174, 1, 191, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:31:33', 1),
(175, 1, 191, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 191, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #191', '2026-04-08 00:31:33', 1),
(176, 1, 192, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:32:01', 1),
(177, 1, 192, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 192, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #192', '2026-04-08 00:32:01', 1),
(178, 1, 193, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:34:00', 1),
(179, 1, 194, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:35:07', 1),
(180, 1, 194, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 194, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #194', '2026-04-08 00:35:07', 1),
(181, 1, 195, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:35:28', 1),
(182, 1, 195, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 195, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #195', '2026-04-08 00:35:28', 1),
(183, 1, 195, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 195, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #195', '2026-04-08 00:36:55', 1),
(184, 1, 193, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 193, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #193', '2026-04-08 00:37:02', 1),
(185, 1, 194, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 194, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #194', '2026-04-08 00:37:10', 1),
(186, 1, 192, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 192, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #192', '2026-04-08 00:37:16', 1),
(187, 1, 191, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 191, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #191', '2026-04-08 00:37:22', 1),
(188, 1, 190, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 190, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #190', '2026-04-08 00:37:28', 1),
(189, 1, 189, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 189, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #189', '2026-04-08 00:37:33', 1),
(190, 1, 187, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 187, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #187', '2026-04-08 00:37:40', 1),
(191, 1, 186, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 186, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #186', '2026-04-08 00:37:46', 1),
(192, 1, 185, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 185, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #185', '2026-04-08 00:37:52', 1),
(193, 1, 196, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:38:24', 1),
(194, 1, 196, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 196, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #196', '2026-04-08 00:38:24', 1),
(195, 1, 197, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:43:25', 1),
(196, 1, 197, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 197, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #197', '2026-04-08 00:43:25', 1),
(197, 1, 196, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 196, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #196', '2026-04-08 00:43:44', 1),
(198, 1, 197, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 197, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #197', '2026-04-08 00:43:50', 1),
(199, 1, 198, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 16:55:13', 1),
(200, 1, 198, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 198, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #198', '2026-04-08 00:55:13', 1),
(201, 1, 198, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 198, 'Abono manual vía Efectivo. Ref Venta: #198', '2026-04-07 16:55:49', 1),
(202, 1, 199, 'cargo', 20.00, 20.00, 0.00, 'venta', NULL, 'Venta con saldo pendiente', '2026-04-07 17:07:25', 1),
(203, 1, 199, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 199, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #199', '2026-04-08 01:07:25', 1),
(204, 1, 199, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 199, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #199', '2026-04-08 01:10:38', 1),
(205, 1, 200, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 200, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #200', '2026-04-08 01:11:09', 1),
(206, 1, 200, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 200, 'Abono manual vía Efectivo. Ref Venta: #200', '2026-04-07 17:14:58', 1),
(207, 10, 42, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 42, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #42', '2026-04-08 01:22:01', 1),
(208, 10, 42, 'abono', 40.00, 0.00, 40.00, 'pago_manual', 42, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #42', '2026-04-08 01:27:48', 1),
(209, 10, 42, 'abono', 100.00, 0.00, 100.00, 'pago_manual', 42, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #42', '2026-04-08 01:28:18', 1),
(210, 10, 42, 'abono', 100.00, 0.00, 100.00, 'pago_manual', 42, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #42', '2026-04-08 01:33:50', 1),
(211, 10, 42, 'abono', 3486.00, 0.00, 3486.00, 'pago_manual', 42, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #42', '2026-04-08 02:00:39', 1),
(212, 10, 42, 'abono', 1320.00, 0.00, 1320.00, 'pago_manual', 42, 'Abono manual vía LIMPIEZA_DEUDA_CANCELACION. Ref Venta: #42', '2026-04-08 02:00:39', 1),
(213, 1, 201, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 201, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #201', '2026-04-08 03:58:06', 1),
(214, 1, 201, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 201, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #201', '2026-04-08 04:14:48', 1),
(215, 1, 202, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 202, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #202', '2026-04-08 04:16:48', 1),
(216, 1, 202, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 202, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #202', '2026-04-08 04:17:02', 1),
(217, 13, 204, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 204, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #204', '2026-04-08 04:20:02', 1),
(218, 11, 206, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 206, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #206', '2026-04-08 04:23:41', 2),
(219, 11, 206, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 206, 'Abono manual vía ABONO_Efectivo. Ref Venta: #206', '2026-04-08 04:23:57', 2),
(220, 11, 206, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 206, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #206', '2026-04-08 04:24:23', 2),
(221, 11, 207, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 207, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #207', '2026-04-08 04:24:55', 2),
(222, 11, 207, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 207, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #207', '2026-04-08 04:25:15', 2),
(223, 11, 208, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 208, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #208', '2026-04-08 04:25:53', 2),
(224, 11, 208, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 208, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #208', '2026-04-08 04:26:08', 2),
(225, 1, 212, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 212, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #212', '2026-04-08 06:29:08', 3),
(226, 1, 212, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 212, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #212', '2026-04-08 06:30:27', 3),
(227, 1, 215, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 215, 'Abono manual vía ABONO_EDICION. Ref Venta: #215', '2026-04-08 06:50:50', 3),
(228, 1, 218, 'abono', 100.00, 0.00, 100.00, 'pago_manual', 218, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #218', '2026-04-08 07:17:12', 3),
(229, 1, 228, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 228, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #228', '2026-04-09 05:01:26', 1),
(230, 1, 229, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 229, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #229', '2026-04-09 06:05:18', 1),
(231, 1, 230, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 230, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #230', '2026-04-09 06:15:43', 1),
(232, 1, 231, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 231, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #231', '2026-04-09 06:17:20', 1),
(233, 1, 232, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 232, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #232', '2026-04-09 06:18:22', 1),
(234, 1, 233, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 233, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #233', '2026-04-09 06:25:19', 1),
(235, 1, 234, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 234, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #234', '2026-04-09 06:31:05', 1),
(236, 1, 235, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 235, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #235', '2026-04-09 06:32:07', 1),
(237, 1, 236, 'abono', 10.00, 0.00, 10.00, 'pago_manual', 236, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #236', '2026-04-09 07:04:10', 1),
(238, 1, 237, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 237, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #237', '2026-04-09 07:04:58', 1),
(239, 1, 237, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 237, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #237', '2026-04-09 07:05:19', 1),
(240, 1, 238, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 238, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #238', '2026-04-09 07:11:17', 1),
(241, 1, 238, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 238, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #238', '2026-04-09 07:11:29', 1),
(242, 1, 239, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 239, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #239', '2026-04-09 07:13:58', 1),
(243, 1, 239, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 239, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #239', '2026-04-09 07:14:14', 1),
(244, 1, 240, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 240, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #240', '2026-04-09 07:15:26', 1),
(245, 10, 241, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 241, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #241', '2026-04-09 07:16:51', 1),
(246, 10, 241, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 241, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #241', '2026-04-09 07:17:09', 1),
(247, 1, 242, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 242, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #242', '2026-04-09 07:17:59', 1),
(248, 1, 242, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 242, 'Abono manual vía ABONO_Efectivo. Ref Venta: #242', '2026-04-09 07:18:12', 1),
(249, 1, 245, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 245, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #245', '2026-04-11 01:47:44', 1),
(250, 1, 246, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 246, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #246', '2026-04-11 03:37:17', 1),
(251, 1, 246, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 246, 'Abono manual vía ABONO_Efectivo. Ref Venta: #246', '2026-04-11 03:37:51', 1),
(252, 1, 240, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 240, 'Abono manual vía ABONO_Efectivo. Ref Venta: #240', '2026-04-11 04:14:04', 1),
(253, 1, 248, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 248, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #248', '2026-04-11 04:18:39', 1),
(254, 1, 249, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 249, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #249', '2026-04-11 04:19:14', 1),
(255, 1, 249, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 249, 'Abono manual vía ABONO_Efectivo. Ref Venta: #249', '2026-04-12 02:49:51', 1),
(256, 1, 253, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 253, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #253', '2026-04-12 02:54:29', 1),
(257, 1, 255, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 255, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #255', '2026-04-13 22:36:34', 1),
(258, 1, 256, 'abono', 216.00, 0.00, 216.00, 'pago_manual', 256, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #256', '2026-04-13 22:37:04', 1),
(259, 1, 256, 'abono', 216.00, 0.00, 216.00, 'pago_manual', 256, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #256', '2026-04-13 23:01:45', 1),
(260, 1, 255, 'abono', 200.00, 0.00, 200.00, 'pago_manual', 255, 'Abono manual vía ABONO_Transferencia. Ref Venta: #255', '2026-04-13 23:02:01', 1),
(261, 1, 259, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 259, 'Abono manual vía CARGO_DEUDA_VENTA. Ref Venta: #259', '2026-04-15 12:50:18', 1),
(262, 1, 253, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 253, 'Abono manual vía ABONO_Efectivo. Ref Venta: #253', '2026-04-15 12:50:32', 1),
(263, 1, 259, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 259, 'Abono manual vía ABONO_Efectivo. Ref Venta: #259', '2026-04-15 12:51:19', 1),
(264, 1, 260, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 260, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #260', '2026-04-15 13:23:21', 1),
(265, 13, 204, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 204, 'Abono manual vía ABONO_Efectivo. Ref Venta: #204', '2026-04-17 07:01:49', 1),
(266, 1, 264, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 264, 'Abono manual vía DEVOLUCION_PAGO_CANCELACION. Ref Venta: #264', '2026-04-22 02:34:23', 3),
(267, 1, 265, 'abono', 20.00, 0.00, 20.00, 'pago_manual', 265, 'Abono manual vía USO_SALDO_A_FAVOR. Ref Venta: #265', '2026-04-22 02:35:10', 3);

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
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia') NOT NULL DEFAULT 'Efectivo',
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

INSERT INTO `compras` (`id`, `folio`, `proveedor`, `fecha_compra`, `almacen_id`, `total`, `metodo_pago`, `documento_url`, `observaciones`, `usuario_registra_id`, `estado`, `tiene_faltantes`, `fecha_registro`) VALUES
(1, '1', 'Hierros 123', '2026-03-13', 1, 2000.00, 'Tarjeta', 'uploads/compras/compra_F_49_1773444959.pdf', NULL, 1, 'cancelada', 0, '2026-03-13 23:35:59'),
(2, '2', 'Proveedor Materias primas 123', '2026-03-14', 2, 4000.00, 'Efectivo', 'uploads/compras/compra_F_52_1773499774.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 14:49:34'),
(3, '3', 'Proveedor Materias primas 123', '2026-03-14', 2, 4000.00, 'Efectivo', 'uploads/compras/compra_F_53_1773499845.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 14:50:45'),
(4, '4', 'Trituradora Maira1234', '2026-03-14', 2, 2000.00, 'Efectivo', 'uploads/compras/compra_4_1773502863.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 15:41:03'),
(5, '5', 'Cementos Fortaleza', '2026-03-14', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_5_1773513982.pdf', NULL, 1, 'confirmada', 0, '2026-03-14 18:46:22'),
(6, '6', 'Cementos Fortaleza', '2026-03-17', 1, 4000.00, 'Efectivo', 'uploads/compras/compra_6_1773757787.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 14:29:47'),
(7, '7', 'Materiales Centro', '2026-03-17', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_7_1773761828.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 15:37:08'),
(8, '8', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_8_1773762148.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 15:42:28'),
(9, '9', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_9_1773764305.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:18:25'),
(10, '10', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_10_1773764463.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:21:03'),
(11, '11', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_11_1773765181.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:33:01'),
(12, '12', 'Cementos Fortaleza', '2026-03-17', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_12_1773765285.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:34:45'),
(13, '13', 'Materiales Centro', '2026-03-17', 1, 1.00, 'Efectivo', 'uploads/compras/compra_13_1773765648.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:40:48'),
(14, '14', 'Materiales Centro', '2026-03-17', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_14_1773765701.pdf', NULL, 1, 'cancelada', 0, '2026-03-17 16:41:41'),
(15, '15', 'Cementos Fortaleza', '2026-03-17', 2, 2000.00, 'Efectivo', 'uploads/compras/compra_15_1773768485.pdf', NULL, 2, 'cancelada', 0, '2026-03-17 17:28:05'),
(16, '16', 'Cementos Fortaleza', '2026-03-17', 2, 2000.00, 'Efectivo', 'uploads/compras/compra_16_1773768556.pdf', NULL, 2, 'cancelada', 0, '2026-03-17 17:29:16'),
(17, '17', 'Cementos Fortaleza', '2026-03-17', 2, 200.00, 'Efectivo', 'uploads/compras/compra_17_1773773906.pdf', NULL, 2, 'confirmada', 0, '2026-03-17 18:58:26'),
(18, '18', 'Cementos Fortaleza', '2026-03-17', 2, 2000.00, 'Efectivo', 'uploads/compras/compra_18_1773774572.pdf', NULL, 2, 'confirmada', 0, '2026-03-17 19:09:32'),
(19, '19', 'Cementos Fortaleza', '2026-03-17', 2, 20.00, 'Efectivo', 'uploads/compras/compra_19_1773778747.pdf', NULL, 2, 'confirmada', 0, '2026-03-17 20:19:07'),
(20, '20', 'Cementos Fortaleza', '2026-03-18', 1, 1000.00, 'Efectivo', 'uploads/compras/compra_20_1773864884.pdf', NULL, 1, 'confirmada', 0, '2026-03-18 20:14:44'),
(21, '21', 'Cementos Fortaleza', '2026-03-18', 1, 1000.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:18:21'),
(22, '22', 'Cementos Fortaleza', '2026-03-18', 2, 1000.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:24:27'),
(23, '23', 'Cementos Fortaleza', '2026-03-18', 1, 10.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:56:02'),
(25, '24', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 22:56:55'),
(26, '25', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:01:13'),
(32, '26', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:11:01'),
(33, '27', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:11:39'),
(34, '28', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:15:14'),
(35, '29', 'Cementos Fortaleza', '2026-03-18', 1, 1.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-03-18 23:17:01'),
(36, '30', 'Cementos Fortaleza', '2026-03-18', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_30_1773884590.pdf', NULL, 1, 'confirmada', 0, '2026-03-19 01:43:10'),
(37, '31', 'Cementos Fortaleza', '2026-03-20', 1, 100.00, 'Efectivo', 'uploads/compras/compra_31_1774061230.pdf', NULL, 1, 'confirmada', 0, '2026-03-21 02:47:10'),
(38, '32', 'TECNOCENTRO', '2026-04-14', 2, 1500.00, 'Efectivo', 'uploads/compras/compra_32_1776232797.pdf', NULL, 1, 'confirmada', 0, '2026-04-15 05:59:57'),
(39, '33', 'Cementos Fortaleza', '2026-04-15', 1, 1500.00, 'Efectivo', 'uploads/compras/compra_33_1776233949.pdf', NULL, 1, 'confirmada', 0, '2026-04-15 06:19:09'),
(40, '34', 'Cementos Fortaleza', '2026-04-15', 1, 2000.00, 'Transferencia', 'uploads/compras/compra_34_1776275680.pdf', NULL, 1, 'confirmada', 0, '2026-04-15 17:54:40'),
(41, '35', 'Cementos Fortaleza', '2026-04-16', 1, 2.00, 'Transferencia', NULL, NULL, 1, 'confirmada', 0, '2026-04-16 20:58:09'),
(42, '36', 'Cementos Fortaleza', '2026-04-16', 1, 1000.00, 'Efectivo', 'uploads/compras/compra_36_1776376703.pdf', NULL, 1, 'confirmada', 0, '2026-04-16 21:58:23'),
(43, '37', 'Cementos Fortaleza', '2026-04-16', 1, 1000.00, 'Transferencia', 'uploads/compras/compra_37_1776377046.pdf', NULL, 1, 'confirmada', 0, '2026-04-16 22:04:06'),
(44, '38', 'Cementos Fortaleza', '2026-04-16', 1, 100.00, 'Tarjeta', 'uploads/compras/compra_38_1776377107.pdf', NULL, 1, 'confirmada', 0, '2026-04-16 22:05:07'),
(51, '39', '4', '2026-04-22', 1, 200.00, 'Efectivo', 'uploads/compras/compra_39_1776870243.pdf', NULL, 1, 'cancelada', 0, '2026-04-22 15:04:03'),
(52, '40', '4', '2026-04-22', 1, 0.00, 'Efectivo', 'uploads/compras/compra_40_1776873848.pdf', NULL, 1, 'cancelada', 0, '2026-04-22 16:04:08'),
(53, '41', '4', '2026-04-22', 1, 200.00, 'Efectivo', 'uploads/compras/compra_41_1776875204.pdf', NULL, 1, 'cancelada', 0, '2026-04-22 16:26:44'),
(55, '42', '4', '2026-04-22', 1, 2000.00, 'Efectivo', 'uploads/compras/compra_42_1776878161.pdf', NULL, 1, 'cancelada', 0, '2026-04-22 17:16:01'),
(57, '43', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_43_1776878529.pdf', NULL, 1, 'cancelada', 0, '2026-04-22 17:22:09'),
(58, '44', '4', '2026-04-22', 1, 200.00, 'Efectivo', 'uploads/compras/compra_44_1776878889.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 17:28:09'),
(59, '45', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_45_1776878985.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 17:29:45'),
(60, '46', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_46_1776879078.jpg', NULL, 1, 'confirmada', 0, '2026-04-22 17:31:18'),
(61, '47', '4', '2026-04-22', 1, 200.00, 'Efectivo', 'uploads/compras/compra_47_1776879433.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 17:37:13'),
(62, '48', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_48_1776879540.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 17:39:00'),
(63, '49', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_49_1776879622.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 17:40:22'),
(64, '50', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_50_1776879779.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 17:42:59'),
(65, '51', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_51_1776883383.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 18:43:03'),
(66, '52', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_52_1776883470.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 18:44:30'),
(67, '53', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_53_1776883884.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 18:51:24'),
(68, '54', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_54_1776884588.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 19:03:08'),
(71, '55', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_55_1776885103.pdf', NULL, 1, 'cancelada', 0, '2026-04-22 19:11:43'),
(72, '56', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_56_1776885309.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 19:15:09'),
(73, '57', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_57_1776885712.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 19:21:52'),
(74, '58', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_58_1776886062.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 19:27:42'),
(75, '59', '4', '2026-04-22', 1, 100.00, 'Efectivo', 'uploads/compras/compra_59_1776886282.pdf', NULL, 1, 'confirmada', 0, '2026-04-22 19:31:22'),
(76, '60', '4', '2026-04-23', 1, 100.00, 'Efectivo', 'uploads/compras/compra_60_1776961132.pdf', NULL, 1, 'confirmada', 0, '2026-04-23 16:18:52'),
(78, '61', '4', '2026-04-23', 1, 50.00, 'Efectivo', 'uploads/compras/compra_61_1776961222.pdf', NULL, 1, 'confirmada', 0, '2026-04-23 16:20:22'),
(79, '62', '2', '2026-04-23', 1, 100.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 17:03:45'),
(80, '63', '4', '2026-04-23', 1, 100.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 1, '2026-04-23 17:09:23'),
(81, '64', '4', '2026-04-23', 1, 10.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 17:33:49'),
(82, '65', '', '2026-04-23', 1, 60.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 19:14:21'),
(83, '66', '4', '2026-04-23', 1, 10.00, 'Efectivo', 'uploads/compras/compra_66_1776971795.pdf', NULL, 1, 'confirmada', 0, '2026-04-23 19:16:35'),
(84, '67', '4', '2026-04-23', 1, 1.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 19:18:56'),
(85, '68', '4', '2026-04-23', 1, 120.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 20:02:46'),
(86, '69', '4', '2026-04-23', 1, 10.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 20:06:07'),
(87, '70', '4', '2026-04-23', 1, 50.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 20:15:16'),
(88, '71', '4', '2026-04-23', 1, 120.01, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 20:17:43'),
(89, '72', '4', '2026-04-23', 1, 100.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 20:25:48'),
(94, '73', '4', '2026-04-23', 1, 100.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 21:48:17'),
(96, '74', '4', '2026-04-23', 1, 100.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 21:56:41'),
(97, '75', '4', '2026-04-23', 1, 200.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 22:02:15'),
(98, '76', '4', '2026-04-23', 1, 100.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 22:08:27'),
(99, '77', '4', '2026-04-23', 1, 50.00, 'Efectivo', 'uploads/compras/compra_77_1776982597.pdf', NULL, 1, 'confirmada', 0, '2026-04-23 22:16:37'),
(100, '78', '4', '2026-04-23', 1, 50.00, 'Efectivo', NULL, NULL, 1, 'confirmada', 0, '2026-04-23 22:25:50');

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
(5, 1, 2, 3, 25.0000, 1, '0', '2026-03-13 19:11:03'),
(6, 1, 7, 2, 20.0000, 3, '0', '2026-04-07 16:36:03'),
(7, 1, 23, 4, 1.0000, 3, '0', '2026-04-07 16:42:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `confirmacion_reparto_viaje`
--

CREATE TABLE `confirmacion_reparto_viaje` (
  `id` int(11) NOT NULL,
  `id_movimiento` int(11) NOT NULL,
  `id_venta` int(11) NOT NULL,
  `trabajador_id` int(11) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL,
  `fotografia_entrega` varchar(255) DEFAULT NULL,
  `fotografia_nota` varchar(255) DEFAULT NULL,
  `estatus` enum('Entregado','Parcial','Rechazado') DEFAULT 'Entregado',
  `comentario` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `confirmacion_reparto_viaje`
--

INSERT INTO `confirmacion_reparto_viaje` (`id`, `id_movimiento`, `id_venta`, `trabajador_id`, `vehiculo_id`, `fecha`, `hora`, `fotografia_entrega`, `fotografia_nota`, `estatus`, `comentario`, `created_at`) VALUES
(1, 118, 178, 1, 2, '2026-04-01', '10:01:03', 'uploads/evidencias/2026/04/02/EVI_118_1775087313.png', 'uploads/evidencias/2026/04/02/NOT_118_37cba45e.gif', 'Entregado', 'la primera', '2026-04-01 23:48:33'),
(5, 120, 0, 0, 0, '2026-04-06', '10:32:31', 'uploads/evidencias/2026/04/06/MAT_120_a9e96e3a.jpg', 'uploads/evidencias/2026/04/06/NOT_120_04b4c289.png', 'Entregado', 'La primera', '2026-04-06 16:32:31'),
(6, 119, 0, 0, 0, '2026-04-06', '11:03:45', 'uploads/evidencias/2026/04/06/MAT_119_9a3efd61.jpg', 'uploads/evidencias/2026/04/06/NOT_119_52b52fb4.png', 'Entregado', 'Sin observaciones', '2026-04-06 17:03:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `corte_de_caja`
--

CREATE TABLE `corte_de_caja` (
  `id` int(11) NOT NULL,
  `fecha_corte` date NOT NULL,
  `hora_cierre` time NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `venta_bruta` decimal(12,2) DEFAULT 0.00,
  `efectivo_real` decimal(12,2) DEFAULT 0.00,
  `transferencia` decimal(12,2) DEFAULT 0.00,
  `tarjeta` decimal(12,2) DEFAULT 0.00,
  `abono_efectivo` decimal(10,2) DEFAULT 0.00,
  `abono_tarjeta` decimal(10,2) DEFAULT 0.00,
  `abono_transferencia` decimal(10,2) DEFAULT 0.00,
  `abonos_totales` decimal(10,2) DEFAULT 0.00,
  `saldo_favor_usado` decimal(12,2) DEFAULT 0.00,
  `cobrado_total` decimal(12,2) DEFAULT 0.00,
  `gastos_totales` decimal(10,2) DEFAULT 0.00,
  `compras_totales` decimal(10,2) DEFAULT 0.00,
  `gran_total_ingresos` decimal(10,2) DEFAULT 0.00,
  `deuda_pendiente` decimal(12,2) DEFAULT 0.00,
  `usuario_id` int(11) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `corte_de_caja`
--

INSERT INTO `corte_de_caja` (`id`, `fecha_corte`, `hora_cierre`, `almacen_id`, `venta_bruta`, `efectivo_real`, `transferencia`, `tarjeta`, `abono_efectivo`, `abono_tarjeta`, `abono_transferencia`, `abonos_totales`, `saldo_favor_usado`, `cobrado_total`, `gastos_totales`, `compras_totales`, `gran_total_ingresos`, `deuda_pendiente`, `usuario_id`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, '2026-04-09', '12:18:18', 1, 20.00, 20.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 20.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, '2026-04-09 18:18:18', NULL),
(2, '2026-04-09', '12:18:18', 2, 200.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 200.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, '2026-04-09 18:18:18', NULL),
(3, '2026-04-09', '12:18:18', 3, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, '2026-04-09 18:18:18', NULL),
(4, '2026-04-09', '12:18:18', 4, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 1, NULL, '2026-04-09 18:18:18', NULL),
(5, '2026-04-10', '17:48:25', 1, 0.00, 60.00, 0.00, 0.00, 20.00, 0.00, 0.00, 20.00, 40.00, 100.00, 0.00, 0.00, 60.00, 20.00, 1, '', '2026-04-10 23:48:25', NULL),
(8, '2026-04-11', '10:20:58', 1, 20.00, 20.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 20.00, 0.00, 0.00, 20.00, 0.00, 1, '', '2026-04-11 16:05:02', '2026-04-11 10:20:58'),
(10, '2026-04-13', '14:39:14', 1, 5240.00, 5040.00, 200.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 5240.00, 0.00, 0.00, 5240.00, 0.00, 1, '', '2026-04-13 15:52:43', '2026-04-13 14:39:14'),
(22, '2026-04-14', '08:55:17', 1, 20.00, 4900.00, 200.00, 0.00, 20.00, 0.00, 0.00, 20.00, 20.00, 40.00, 200.00, 0.00, 5080.00, 0.00, 1, '', '2026-04-14 22:24:51', '2026-04-15 08:55:17'),
(36, '2026-04-15', '17:11:56', 1, 40.00, 2000.00, 0.00, 0.00, 20.00, 0.00, 0.00, 20.00, 20.00, 60.00, 0.00, 3500.00, 2000.00, 0.00, 1, '', '2026-04-15 14:31:31', '2026-04-15 17:11:56'),
(56, '2026-04-20', '17:55:15', 1, 20.00, 2132.50, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 20.00, 200.00, 0.00, 2132.50, 0.00, 1, '', '2026-04-20 23:55:15', NULL),
(57, '2026-04-21', '12:37:39', 1, 20.00, 2132.50, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 20.00, 200.00, 0.00, 2132.50, 0.00, 3, 'Corte de caja cerrado temprano', '2026-04-21 18:37:39', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_bancarias`
--

CREATE TABLE `cuentas_bancarias` (
  `id_cuenta` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `nombre_cuenta` varchar(100) NOT NULL,
  `tipo_cuenta` enum('Efectivo','Banco','Caja Fuerte') NOT NULL,
  `estatus` tinyint(4) DEFAULT 1,
  `saldo` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas_bancarias`
--

INSERT INTO `cuentas_bancarias` (`id_cuenta`, `id_almacen`, `nombre_cuenta`, `tipo_cuenta`, `estatus`, `saldo`) VALUES
(1, 1, 'Caja General Sucursal 1', 'Efectivo', 1, 0),
(2, 2, 'Caja General Sucursal 2', 'Efectivo', 1, 0),
(3, 3, 'Caja General Sucursal 3', 'Efectivo', 1, 0),
(4, 4, 'Caja General Sucursal 4', 'Efectivo', 1, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cuentas_por_pagar`
--

CREATE TABLE `cuentas_por_pagar` (
  `id` int(11) NOT NULL,
  `id_almacen` int(11) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `beneficiario` varchar(150) NOT NULL,
  `id_referencia_origen` int(11) DEFAULT NULL,
  `monto_total` decimal(12,2) NOT NULL,
  `monto_pagado` decimal(12,2) DEFAULT 0.00,
  `tipo_deuda` varchar(250) DEFAULT NULL,
  `estado` enum('pendiente','parcial','pagado','cancelado') DEFAULT 'pendiente',
  `fecha_vencimiento` date DEFAULT NULL,
  `notas` text DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cuentas_por_pagar`
--

INSERT INTO `cuentas_por_pagar` (`id`, `id_almacen`, `id_proveedor`, `beneficiario`, `id_referencia_origen`, `monto_total`, `monto_pagado`, `tipo_deuda`, `estado`, `fecha_vencimiento`, `notas`, `fecha_registro`) VALUES
(1, 1, NULL, 'Cementos Fortaleza', 44, 10.00, 100.00, '0', 'pagado', NULL, 'Ajuste generado por exceso en compra #44', '2026-04-20 19:25:10'),
(2, 1, NULL, 'Cementos Fortaleza', 43, 100.00, 100.00, 'excedente_material', 'pagado', NULL, 'Ajuste generado por exceso en compra #43', '2026-04-20 19:34:56'),
(4, 1, 4, 'Cementos Fortaleza', 42, 5000.00, 5000.00, 'excedente_material', 'pagado', NULL, 'Ajuste generado por exceso en compra #42', '2026-04-21 15:46:00'),
(5, 1, 4, 'Cementos Fortaleza', 41, 200.00, 200.00, 'excedente_material', 'pagado', NULL, 'Ajuste generado por exceso en compra #41', '2026-04-21 18:47:46'),
(7, 1, 4, 'Proveedor ID: 4', 65, 50.00, 50.00, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 51', '2026-04-22 18:43:03'),
(8, 1, 4, 'Proveedor ID: 4', 76, 50.00, 50.00, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 60', '2026-04-23 16:18:52'),
(9, 1, 4, 'Proveedor ID: 4', 87, 25.00, 25.00, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 70', '2026-04-23 20:15:16'),
(10, 1, 4, 'Proveedor ID: 4', 88, 56.67, 56.67, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 71', '2026-04-23 20:17:43'),
(11, 1, 4, 'Proveedor ID: 4', 89, 50.00, 50.00, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 72', '2026-04-23 20:25:48'),
(12, 1, 4, 'Proveedor ID: 4', 96, 50.00, 50.00, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 74', '2026-04-23 21:56:41'),
(13, 1, 4, 'Proveedor ID: 4', 97, 75.00, 75.00, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 75', '2026-04-23 22:02:15'),
(14, 1, 4, 'Proveedor ID: 4', 98, 50.00, 50.00, 'excedente_compra', 'pagado', NULL, 'Deuda generada por material excedente en Compra Folio: 76', '2026-04-23 22:08:27');

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
  `cantidad_excedente` decimal(10,2) DEFAULT 0.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `estado_entrega` enum('completo','incompleto','ajustado','excedente') DEFAULT 'completo',
  `subtotal` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_compra`
--

INSERT INTO `detalle_compra` (`id`, `compra_id`, `producto_id`, `cantidad`, `unidad_compra`, `factor_conversion`, `cantidad_faltante`, `cantidad_excedente`, `precio_unitario`, `estado_entrega`, `subtotal`) VALUES
(44, 1, 4, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(45, 2, 4, 0.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 4000.00),
(46, 3, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 4000.00),
(47, 4, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 2000.00),
(48, 5, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 2000.00),
(49, 6, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 200.00, 'completo', 4000.00),
(50, 7, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(51, 8, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(52, 9, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(53, 10, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(54, 11, 4, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(55, 12, 4, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(56, 13, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 0.00, 'completo', 1.00),
(57, 14, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(58, 15, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(59, 16, 21, 0.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(60, 17, 19, 0.00, 'PZA', 1.00, 0.00, 0.00, 1.33, 'completo', 200.00),
(61, 18, 19, 0.00, 'PZA', 1.00, 0.00, 0.00, 13.33, 'completo', 2000.00),
(62, 19, 22, 0.00, 'PZA', 1.00, 0.00, 0.00, 6.67, 'completo', 20.00),
(63, 20, 4, 10.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 1000.00),
(64, 21, 4, 10.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 1000.00),
(65, 22, 19, 150.00, 'PZA', 1.00, 0.00, 0.00, 6.67, 'completo', 1000.00),
(66, 23, 4, 1.00, 'PZA', 1.00, 0.00, 0.00, 10.00, 'completo', 10.00),
(67, 25, 4, 1.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 1.00),
(68, 26, 4, 1.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 1.00),
(69, 32, 4, 1.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 1.00),
(70, 33, 4, 1.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 1.00),
(71, 34, 4, 1.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 1.00),
(72, 35, 4, 1.00, 'PZA', 1.00, 0.00, 0.00, 1.00, 'completo', 1.00),
(73, 36, 21, 1000.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2000.00),
(74, 37, 21, 1.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 100.00),
(75, 38, 19, 0.00, 'PZA', 1.00, 0.00, 0.00, 10.00, 'completo', 1500.00),
(76, 39, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 75.00, 'completo', 1500.00),
(77, 40, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 2000.00),
(78, 41, 21, 1.00, 'PZA', 1.00, 0.00, 0.00, 2.00, 'completo', 2.00),
(79, 42, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 1000.00),
(80, 43, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 1000.00),
(81, 44, 21, 1000.00, 'PZA', 1.00, 0.00, 0.00, 0.10, 'completo', 100.00),
(84, 51, 1, 0.00, 'PZA', 1.00, 0.00, 2.00, 9.09, 'excedente', 200.00),
(85, 52, 1, 0.00, 'PZA', 1.00, 0.00, 2.00, 0.00, 'excedente', 0.00),
(86, 53, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 10.00, 'completo', 200.00),
(87, 55, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 2000.00),
(88, 57, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(89, 58, 8, 0.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 200.00),
(90, 59, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(91, 60, 8, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(92, 61, 8, 0.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'completo', 200.00),
(93, 62, 8, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(94, 63, 8, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(95, 64, 8, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(96, 65, 7, 0.00, 'PZA', 1.00, 0.00, 2.00, 25.00, 'excedente', 100.00),
(97, 66, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(98, 67, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(99, 68, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(102, 71, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(103, 72, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(104, 73, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(105, 74, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(106, 75, 7, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 100.00),
(107, 76, 1, 2.00, 'PZA', 1.00, 0.00, 2.00, 50.00, 'excedente', 100.00),
(109, 78, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 50.00),
(110, 79, 6, 0.00, 'PZA', 1.00, 0.00, 0.00, 100.00, 'incompleto', 100.00),
(111, 80, 1, 2.00, 'PZA', 1.00, 1.00, 0.00, 100.00, 'incompleto', 100.00),
(112, 81, 5, 1.00, 'PZA', 1.00, 0.00, 0.00, 10.00, 'completo', 10.00),
(113, 82, 1, 3.00, 'PZA', 1.00, 0.00, 2.00, 0.00, 'excedente', 50.00),
(114, 82, 6, 2.00, 'PZA', 1.00, 0.00, 1.00, 0.00, 'excedente', 10.00),
(115, 83, 5, 1.00, 'PZA', 1.00, 0.00, 0.00, 0.00, 'completo', 10.00),
(116, 84, 4, 2.00, 'PZA', 1.00, 0.00, 1.00, 0.00, 'excedente', 1.00),
(117, 85, 1, 4.00, 'PZA', 1.00, 0.00, 2.00, 0.00, 'excedente', 100.00),
(118, 85, 4, 4.00, 'PZA', 1.00, 0.00, 2.00, 0.00, 'excedente', 20.00),
(119, 86, 1, 2.00, 'PZA', 1.00, 0.00, 1.00, 0.00, 'excedente', 10.00),
(120, 87, 1, 2.00, 'PZA', 1.00, 0.00, 1.00, 0.00, 'excedente', 50.00),
(121, 88, 1, 4.00, 'PZA', 1.00, 0.00, 2.00, 0.00, 'excedente', 100.00),
(122, 88, 8, 3.00, 'PZA', 1.00, 0.00, 1.00, 0.00, 'excedente', 20.01),
(123, 89, 1, 3.00, 'PZA', 1.00, 0.00, 1.00, 50.00, 'excedente', 100.00),
(124, 94, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 0.00, 'completo', 100.00),
(125, 94, 1, 0.00, 'PZA', 1.00, 0.00, 0.00, 0.00, 'completo', 100.00),
(126, 96, 1, 2.00, 'PZA', 1.00, 0.00, 1.00, 50.00, 'excedente', 100.00),
(127, 97, 1, 2.00, 'PZA', 1.00, 0.00, 1.00, 50.00, 'excedente', 100.00),
(128, 97, 2, 2.00, 'PZA', 1.00, 0.00, 1.00, 50.00, 'excedente', 100.00),
(129, 98, 2, 2.00, 'PZA', 1.00, 0.00, 1.00, 50.00, 'excedente', 100.00),
(130, 99, 1, 1.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 50.00),
(131, 100, 1, 1.00, 'PZA', 1.00, 0.00, 0.00, 50.00, 'completo', 50.00);

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
(115, 120, 99, 1.00),
(116, 121, 100, 1.00),
(117, 122, 101, 1.00),
(118, 123, 102, 1.00),
(119, 124, 103, 20.00),
(120, 125, 104, 1.00),
(121, 126, 105, 1000.00),
(122, 127, 106, 1.00),
(123, 128, 107, 1.00),
(124, 129, 108, 1.00),
(125, 130, 109, 1.00),
(126, 131, 110, 1.00),
(127, 132, 111, 1.00),
(128, 133, 112, 20.00),
(129, 133, 113, 17.00),
(130, 133, 114, 4.00),
(131, 134, 115, 1.00),
(132, 135, 116, 1.00),
(133, 136, 117, 1.00),
(134, 137, 118, 1.00),
(135, 138, 119, 1.00),
(136, 139, 120, 1.00),
(137, 140, 121, 1.00),
(138, 141, 122, 1.00),
(139, 142, 123, 1.00),
(140, 143, 124, 1.00),
(141, 144, 125, 1.00),
(142, 145, 126, 1.00),
(143, 146, 127, 1.00),
(144, 146, 128, 1.00),
(145, 147, 129, 1.00),
(146, 148, 130, 1.00),
(147, 149, 131, 1.00),
(148, 150, 132, 1.00),
(149, 151, 133, 1.00),
(150, 152, 134, 1.00),
(151, 153, 135, 1.00),
(152, 154, 136, 1.00),
(153, 155, 137, 1.00),
(154, 156, 138, 1.00),
(155, 157, 139, 1.00),
(156, 158, 140, 1.00),
(157, 158, 141, 1.00),
(158, 159, 142, 1.00),
(159, 159, 143, 1.00),
(160, 160, 144, 1.00),
(161, 160, 145, 1.00),
(162, 161, 146, 1.00),
(163, 161, 147, 1.00),
(164, 162, 148, 1.00),
(165, 162, 149, 1.00),
(166, 163, 150, 1.00),
(167, 164, 151, 1.00),
(168, 167, 89, 1.00),
(169, 170, 153, 1.00),
(170, 170, 154, 1.00),
(171, 171, 155, 1.00),
(172, 172, 156, 1.00),
(173, 172, 157, 1.00),
(174, 173, 158, 1.00),
(175, 174, 159, 1.00),
(176, 175, 160, 1.00),
(177, 175, 161, 1.00),
(178, 176, 162, 1.00),
(179, 177, 163, 1.00),
(180, 177, 164, 1.00),
(181, 178, 165, 1.00),
(182, 178, 166, 1.00),
(183, 179, 167, 1.00),
(184, 179, 168, 1.00),
(185, 180, 169, 1.00),
(186, 180, 170, 1.00),
(187, 181, 171, 1.00),
(188, 181, 172, 1.00),
(189, 182, 173, 1.00),
(190, 182, 174, 1.00),
(191, 183, 175, 1.00),
(192, 184, 176, 1.00),
(193, 184, 177, 1.00),
(194, 185, 178, 1.00),
(195, 185, 179, 1.00),
(196, 186, 180, 1.00),
(197, 186, 181, 1.00),
(198, 187, 182, 1.00),
(199, 187, 183, 1.00),
(200, 188, 184, 1.00),
(201, 188, 185, 1.00),
(202, 189, 186, 1.00),
(203, 190, 187, 1.00),
(204, 191, 188, 1.00),
(205, 192, 189, 1.00),
(206, 193, 190, 2.00),
(207, 194, 191, 1.00),
(208, 195, 192, 1.00),
(209, 196, 193, 1.00),
(210, 197, 194, 1.00),
(211, 198, 195, 1.00),
(212, 199, 196, 1.00),
(213, 200, 197, 1.00),
(214, 201, 198, 1.00),
(215, 202, 199, 1.00),
(216, 203, 200, 1.00),
(217, 204, 201, 1.00),
(218, 205, 202, 1.00),
(219, 206, 203, 1.00),
(220, 207, 204, 1.00),
(221, 208, 205, 1.00),
(222, 209, 206, 1.00),
(223, 210, 207, 1.00),
(224, 211, 208, 1.00),
(225, 212, 209, 1.00),
(226, 213, 210, 1.00),
(227, 214, 211, 1.00),
(228, 215, 212, 1.00),
(229, 216, 213, 1.00),
(230, 217, 214, 2.00),
(231, 217, 215, 1.00),
(232, 218, 218, 2.00),
(233, 219, 219, 2.00),
(234, 220, 220, 2.00),
(235, 221, 221, 3.00),
(236, 222, 223, 1.00),
(237, 223, 224, 1.00),
(238, 224, 225, 1.00),
(239, 225, 226, 1.00),
(240, 226, 227, 1.00),
(241, 227, 228, 3.00),
(242, 228, 229, 1.00),
(243, 229, 230, 1.00),
(244, 230, 231, 1.00),
(245, 231, 232, 1.00),
(246, 232, 233, 1.00),
(247, 233, 234, 3.00),
(248, 234, 235, 2.00),
(249, 235, 236, 1.00),
(250, 236, 237, 1.00),
(251, 237, 238, 1.00),
(252, 238, 239, 2.00),
(253, 239, 240, 1.00),
(254, 240, 241, 1.00),
(255, 241, 242, 1.00),
(256, 242, 243, 1.00),
(257, 243, 244, 1.00),
(258, 244, 245, 1.00),
(259, 245, 247, 5.00),
(260, 246, 248, 9.00),
(261, 247, 249, 1.00),
(263, 248, 251, 3.00),
(264, 248, 252, 1.00),
(265, 249, 254, 1.00),
(267, 250, 256, 1.00),
(269, 251, 258, 2.00),
(270, 251, 259, 1.00),
(271, 252, 260, 1.00),
(272, 252, 261, 1.00),
(273, 253, 262, 1.00),
(274, 254, 263, 3.00),
(275, 255, 264, 1.00),
(276, 256, 265, 1.00),
(277, 257, 266, 1.00),
(278, 258, 267, 2.00),
(279, 259, 268, 1.00),
(280, 260, 269, 1.00),
(281, 261, 270, 1.00),
(282, 262, 271, 1.00),
(283, 263, 272, 1.00),
(284, 264, 273, 1.00),
(285, 265, 274, 1.00),
(286, 266, 275, 1.00),
(287, 267, 276, 1.00),
(288, 268, 277, 1.00),
(289, 269, 278, 1.00),
(290, 270, 279, 1.00),
(291, 271, 280, 1.00),
(292, 272, 281, 1.00),
(293, 273, 282, 1.00),
(294, 274, 283, 1.00),
(295, 275, 284, 1.00),
(296, 276, 285, 1.00),
(297, 277, 286, 1.00),
(298, 278, 287, 1.00),
(299, 279, 288, 1.00),
(300, 280, 289, 1.00),
(301, 281, 290, 1.00),
(302, 282, 291, 1.00),
(303, 283, 292, 1.00),
(304, 284, 293, 1.00),
(305, 285, 294, 1.00),
(306, 286, 295, 1.00),
(307, 287, 296, 1.00),
(308, 288, 297, 1.00),
(309, 289, 298, 1.00),
(310, 290, 299, 1.00),
(311, 291, 300, 1.00),
(312, 292, 301, 1.00),
(313, 293, 302, 1.00),
(314, 294, 303, 1.00),
(315, 294, 304, 1.00),
(316, 295, 305, 1.00),
(317, 296, 306, 1.00),
(318, 297, 307, 1.00),
(319, 298, 308, 1.00),
(320, 298, 309, 1.00),
(321, 299, 310, 1.00),
(323, 300, 312, 1.00),
(332, 305, 313, 2.00),
(333, 305, 314, 1.00),
(334, 306, 317, 1.00),
(335, 307, 318, 1.00),
(336, 308, 319, 1.00),
(337, 309, 320, 1.00),
(338, 310, 321, 1.00),
(339, 311, 322, 1.00),
(340, 312, 323, 1.00),
(341, 313, 324, 1.00),
(342, 314, 325, 1.00),
(343, 315, 326, 1.00),
(344, 316, 327, 1.00),
(345, 317, 328, 1.00),
(346, 318, 329, 1.00),
(347, 319, 330, 1.00),
(348, 320, 331, 1.00),
(349, 321, 332, 1.00),
(350, 322, 333, 1.00),
(351, 323, 334, 1.00),
(352, 324, 335, 1.00),
(353, 325, 336, 1.00),
(354, 326, 337, 1.00),
(355, 327, 338, 1.00),
(356, 328, 339, 1.00),
(357, 329, 340, 1.00),
(358, 330, 341, 1.00),
(359, 331, 342, 1.00),
(360, 332, 343, 1.00),
(361, 333, 344, 1.00),
(362, 334, 345, 1.00),
(363, 335, 346, 1.00),
(364, 336, 347, 1.00),
(365, 337, 348, 1.00),
(366, 338, 349, 1.00),
(367, 339, 350, 1.00),
(368, 340, 351, 1.00),
(369, 341, 352, 1.00),
(370, 342, 353, 1.00),
(371, 343, 355, 1.00),
(372, 344, 356, 1.00),
(373, 345, 357, 1.00),
(374, 346, 358, 1.00),
(375, 347, 359, 1.00),
(376, 348, 360, 1.00),
(377, 349, 361, 1.00),
(378, 350, 362, 1.00),
(379, 351, 363, 1.00),
(380, 352, 364, 1.00);

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
(22, 39, 'Compra e baterias para la lampara', 2.00, 200.00, 400.00),
(23, 40, 'pago de gasolina', 1.00, 200.00, 200.00),
(24, 41, 'pago de gasolina', 1.00, 200.00, 200.00),
(25, 42, 'pago de gasolina', 1.00, 200.00, 200.00),
(26, 43, 'pago de gasolina', 1.00, 150.00, 150.00),
(27, 44, 'Compra de gas', 1.00, 100.00, 100.00),
(28, 47, 'Pago de multa', 1.00, 200.00, 200.00),
(29, 48, 'Compra de material para papeleria', 1.00, 100.00, 100.00),
(30, 51, 'Préstamo a Javier por motivo de Adelanto de nomina', 1.00, 100.00, 100.00),
(31, 53, 'Préstamo a Javier por motivo de Adelanto de nomina', 1.00, 200.00, 200.00),
(32, 54, 'Préstamo a Javier por motivo de Adelanto de nomina', 1.00, 1.00, 1.00),
(33, 55, 'Préstamo a Juan por motivo de Adelanto de quincena', 1.00, 1.00, 1.00),
(34, 56, 'Préstamo a Juan por motivo de Adelanto de quincena', 1.00, 1.00, 1.00),
(35, 57, 'Préstamo a Juan por motivo de Adelnato de nomina', 1.00, 1.00, 1.00),
(36, 58, 'Préstamo a Juan por motivo de Adelnato de nomina', 1.00, 1.00, 1.00),
(37, 59, 'Préstamo a Juan por motivo de Adelanto nomina', 1.00, 1.00, 1.00),
(38, 60, 'Préstamo a Javier por motivo de Adelanto nomina', 1.00, 1.00, 1.00),
(39, 61, 'Préstamo a Javier por motivo de 1', 1.00, 1.00, 1.00),
(40, 62, 'Préstamo a Javier por motivo de 1', 1.00, 1.00, 1.00),
(41, 63, 'Préstamo a Javier por motivo de Adelanto de nomina', 1.00, 1.00, 1.00),
(42, 64, 'Préstamo a Javier por motivo de Adelanto de quincena', 1.00, 200.00, 200.00),
(43, 65, 'Préstamo a Javier por motivo de Adelanto de nomina', 1.00, 1.00, 1.00),
(44, 66, 'Préstamo a Javier por motivo de Adelanto de nomina', 1.00, 1.00, 1.00),
(45, 67, 'PRUEBA GASTO PRESTAMO', 1.00, 100.00, 100.00),
(46, 70, 'pago de gasolina', 1.00, 100.00, 100.00),
(47, 71, 'pago de gasolina', 1.00, 100.00, 100.00),
(48, 72, 'Préstamo a Javier por motivo de Adelanto de nomina', 1.00, 100.00, 100.00),
(49, 73, 'Préstamo a Juan por motivo de Adelanto de quincena', 1.00, 100.00, 100.00),
(50, 74, 'Cambio de balatas de torton', 1.00, 200.00, 200.00);

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

--
-- Volcado de datos para la tabla `detalle_pedido_vendedor`
--

INSERT INTO `detalle_pedido_vendedor` (`id`, `pedido_id`, `producto_id`, `cantidad`, `notas_producto`) VALUES
(2, 5, 21, 1, ''),
(3, 6, 4, 1, ''),
(4, 7, 4, 1000, ' se entrega el 19'),
(5, 7, 1, 20, 'se entrega el 20'),
(6, 8, 21, 1, ''),
(7, 9, 21, 1, ''),
(8, 10, 4, 1, 'se entrega mañana'),
(9, 11, 4, 1000, ''),
(10, 12, 23, 1, ''),
(11, 13, 21, 1, ''),
(12, 14, 23, 1, '');

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
(10, 10, 21, 1.00),
(11, 11, 21, 1000.00),
(12, 12, 21, 1000.00),
(13, 13, 21, 1000.00),
(14, 14, 21, 1.00);

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
(89, 20, 21, 1001.00, 1.00, 20.00, 'minorista', 20020.00, 'pendiente'),
(90, 21, 4, 2899.00, 2899.00, 3.00, 'minorista', 8697.00, 'entregado'),
(91, 22, 4, 899.00, 899.00, 3.00, 'minorista', 2697.00, 'entregado'),
(92, 23, 4, 1000.00, 1000.00, 3.00, 'minorista', 3000.00, 'entregado'),
(93, 24, 19, 8.00, 7.00, 200.00, 'minorista', 1600.00, 'parcial'),
(94, 25, 19, 250.00, 250.00, 200.00, 'minorista', 50000.00, 'entregado'),
(95, 26, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(96, 27, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(97, 28, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(98, 29, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(99, 29, 7, 1.00, 1.00, 0.00, 'minorista', 0.00, 'entregado'),
(100, 30, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(101, 31, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(102, 32, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(103, 33, 1, 20.00, 20.00, 216.00, 'minorista', 4320.00, 'entregado'),
(104, 34, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(105, 35, 4, 1000.00, 1000.00, 3.00, 'minorista', 3000.00, 'entregado'),
(106, 36, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(107, 37, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(108, 38, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(109, 39, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(110, 40, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(111, 41, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(112, 42, 1, 20.00, 20.00, 207.00, 'mayorista', 4140.00, 'entregado'),
(113, 42, 21, 17.00, 17.00, 18.00, 'mayorista', 306.00, 'entregado'),
(114, 42, 17, 4.00, 4.00, 90.00, 'mayorista', 360.00, 'entregado'),
(115, 43, 2, 1.00, 1.00, 120.00, 'minorista', 120.00, 'entregado'),
(116, 44, 8, 1.00, 1.00, 0.00, 'minorista', 0.00, 'entregado'),
(117, 45, 2, 1.00, 1.00, 120.00, 'minorista', 120.00, 'pendiente'),
(118, 46, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(119, 47, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(120, 48, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(121, 49, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(122, 50, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(123, 51, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(124, 52, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(125, 53, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(126, 54, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(127, 55, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(128, 55, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(129, 56, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(130, 57, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(131, 58, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(132, 59, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(133, 60, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(134, 61, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(135, 62, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(136, 63, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(137, 64, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(138, 65, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(139, 66, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(140, 67, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(141, 67, 4, 1.00, 1.00, 3.00, 'minorista', 0.00, 'entregado'),
(142, 68, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(143, 68, 4, 1.00, 1.00, 3.00, 'minorista', 0.00, 'entregado'),
(144, 69, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(145, 69, 4, 1.00, 1.00, 3.00, 'minorista', 0.00, 'entregado'),
(146, 70, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(147, 70, 4, 1.00, 1.00, 3.00, 'minorista', 0.00, 'entregado'),
(148, 71, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(149, 71, 4, 1.00, 1.00, 3.00, 'minorista', 0.00, 'entregado'),
(150, 72, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(151, 73, 19, 1.00, 1.00, 200.00, 'minorista', 200.00, 'pendiente'),
(152, 74, 19, 1.00, -1.00, 200.00, 'minorista', 200.00, 'pendiente'),
(153, 75, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'pendiente'),
(154, 75, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'pendiente'),
(155, 76, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(156, 77, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(157, 77, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(158, 78, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(159, 79, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(160, 80, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(161, 80, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(162, 81, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(163, 82, 3, 1.00, 1.00, 4.80, 'minorista', 4.80, 'entregado'),
(164, 82, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(165, 83, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(166, 83, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(167, 84, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(168, 84, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(169, 85, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(170, 85, 4, 1.00, 1.00, 3.00, 'minorista', 0.00, 'entregado'),
(171, 86, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(172, 86, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(173, 87, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(174, 87, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(175, 88, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(176, 89, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(177, 89, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(178, 90, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(179, 90, 3, 1.00, 1.00, 4.80, 'minorista', 4.80, 'entregado'),
(180, 91, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(181, 91, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(182, 92, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(183, 92, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(184, 93, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(185, 93, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(186, 94, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(187, 95, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(188, 96, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(189, 97, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(190, 98, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(191, 99, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(192, 102, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(193, 103, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(194, 104, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(195, 105, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(196, 106, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(197, 107, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(198, 108, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(199, 109, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(200, 110, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(201, 111, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(202, 112, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(203, 113, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(204, 114, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(205, 115, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(206, 116, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(207, 117, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(208, 118, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(209, 119, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(210, 120, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(211, 121, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(212, 122, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(213, 123, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(214, 124, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(215, 124, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(216, 125, 21, 1.00, 0.00, 20.00, 'minorista', 20.00, 'pendiente'),
(217, 125, 4, 0.00, 0.00, 3.00, 'minorista', 0.00, 'entregado'),
(218, 126, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(219, 127, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(220, 128, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(221, 129, 21, 3.00, 3.00, 20.00, 'minorista', 60.00, 'entregado'),
(222, 130, 21, 7.00, 0.00, 20.00, 'minorista', 140.00, 'pendiente'),
(223, 131, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(224, 132, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(225, 133, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(226, 140, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(227, 141, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(228, 142, 21, 3.00, 3.00, 20.00, 'minorista', 60.00, 'entregado'),
(229, 143, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(230, 144, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(231, 145, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(232, 146, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(233, 147, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(234, 148, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(235, 149, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(236, 150, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(237, 151, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(238, 152, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(239, 153, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(240, 154, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(241, 155, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(242, 156, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(243, 157, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(244, 158, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(245, 159, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(246, 160, 20, 1.00, 0.00, 100.00, 'minorista', 100.00, 'pendiente'),
(247, 161, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(248, 162, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(249, 163, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(251, 164, 21, 3.00, 3.00, 20.00, 'minorista', 60.00, 'entregado'),
(252, 164, 4, 0.00, 0.00, 3.00, 'minorista', 0.00, 'entregado'),
(254, 165, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(256, 166, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(258, 167, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(259, 167, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(260, 168, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(261, 168, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'entregado'),
(262, 169, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(263, 170, 21, 3.00, 3.00, 20.00, 'minorista', 60.00, 'entregado'),
(264, 171, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(265, 172, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(266, 173, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(267, 174, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'entregado'),
(268, 175, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(269, 176, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(270, 177, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(271, 178, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(272, 179, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(273, 180, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(274, 181, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(275, 182, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(276, 183, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(277, 184, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(278, 185, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(279, 186, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(280, 187, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(281, 188, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(282, 189, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(283, 190, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(284, 191, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(285, 192, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(286, 193, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(287, 194, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(288, 195, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(289, 196, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(290, 197, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(291, 198, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(292, 199, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(293, 200, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(294, 201, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(295, 202, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(296, 203, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(297, 204, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(298, 205, 21, 1.00, 1.00, 200.00, 'minorista', 0.00, 'entregado'),
(299, 206, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(300, 207, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(301, 208, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(302, 209, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(303, 210, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(304, 210, 20, 1.00, 1.00, 100.00, 'minorista', 100.00, 'entregado'),
(305, 211, 21, 1.00, 1.00, 200.00, 'minorista', 0.00, 'entregado'),
(306, 212, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(307, 213, 21, 1.00, 1.00, 20.00, 'minorista', 0.00, 'entregado'),
(308, 214, 23, 1.00, 1.00, 100.00, 'minorista', 100.00, 'entregado'),
(309, 214, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(310, 215, 23, 1.00, 1.00, 100.00, 'minorista', 100.00, 'entregado'),
(312, 216, 23, 7.00, 1.00, 100.00, 'minorista', 700.00, 'pendiente'),
(313, 217, 21, 2.00, 2.00, 20.00, 'minorista', 40.00, 'pendiente'),
(314, 217, 4, 1.00, 1.00, 3.00, 'minorista', 3.00, 'pendiente'),
(315, 217, 19, 1.00, 0.00, 200.00, 'minorista', 200.00, 'pendiente'),
(316, 217, 20, 1.00, 0.00, 100.00, 'minorista', 100.00, 'pendiente'),
(317, 218, 23, 1.00, 1.00, 100.00, 'minorista', 100.00, 'entregado'),
(318, 219, 23, 1.00, 1.00, 100.00, 'minorista', 0.00, 'entregado'),
(319, 220, 23, 1.00, 1.00, 100.00, 'minorista', 0.00, 'entregado'),
(320, 221, 23, 1.00, 1.00, 100.00, 'minorista', 0.00, 'entregado'),
(321, 222, 23, 1.00, 1.00, 100.00, 'minorista', 0.00, 'entregado'),
(322, 223, 23, 1.00, 1.00, 100.00, 'minorista', 100.00, 'entregado'),
(323, 224, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(324, 225, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(325, 226, 23, 1.00, 1.00, 100.00, 'minorista', 100.00, 'entregado'),
(326, 227, 23, 1.00, 1.00, 100.00, 'minorista', 100.00, 'entregado'),
(327, 228, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(328, 229, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(329, 230, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(330, 231, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(331, 232, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(332, 233, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(333, 234, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(334, 235, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(335, 236, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(336, 237, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(337, 238, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(338, 239, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(339, 240, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(340, 241, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(341, 242, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(342, 243, 21, 1.00, 1.00, 200.00, 'minorista', 200.00, 'entregado'),
(343, 244, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(344, 245, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(345, 246, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(346, 247, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(347, 248, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(348, 249, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(349, 250, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(350, 251, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(351, 252, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(352, 253, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(353, 254, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(354, 255, 19, 1.00, 0.00, 200.00, 'minorista', 200.00, 'pendiente'),
(355, 256, 1, 1.00, 1.00, 216.00, 'minorista', 216.00, 'entregado'),
(356, 257, 7, 1.00, 1.00, 0.00, 'minorista', 0.00, 'entregado'),
(357, 258, 22, 1.00, 1.00, 10.00, 'minorista', 10.00, 'entregado'),
(358, 259, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(359, 260, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(360, 261, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(361, 262, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(362, 263, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(363, 264, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado'),
(364, 265, 21, 1.00, 1.00, 20.00, 'minorista', 20.00, 'entregado');

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
(120, 29, 1, '2026-03-21 11:35:18', 'Entrega inicial generada en venta. Folio: V-260321113518'),
(121, 30, 1, '2026-03-21 21:44:47', 'Entrega inicial generada en venta. Folio: V-260321214447'),
(122, 31, 1, '2026-03-23 09:03:39', 'Entrega inicial generada en venta. Folio: V-260323090339'),
(123, 32, 1, '2026-03-23 19:52:21', 'Entrega inicial generada en venta. Folio: V-260323195221'),
(124, 33, 1, '2026-03-24 17:37:39', 'Entrega inicial generada en venta. Folio: V-260324173739'),
(125, 34, 1, '2026-03-25 12:00:40', 'Entrega inicial generada en venta. Folio: V-34'),
(126, 35, 1, '2026-03-26 10:13:57', 'Entrega inicial generada en venta. Folio: V-35'),
(127, 36, 1, '2026-03-26 10:15:14', 'Entrega inicial generada en venta. Folio: V-36'),
(128, 37, 1, '2026-03-26 10:18:11', 'Entrega inicial generada en venta. Folio: V-37'),
(129, 38, 1, '2026-03-26 10:23:02', 'Entrega inicial generada en venta. Folio: V-38'),
(130, 39, 1, '2026-03-26 10:23:55', 'Entrega inicial generada en venta. Folio: V-39'),
(131, 40, 1, '2026-03-26 11:06:59', 'Entrega inicial generada en venta. Folio: V-40'),
(132, 41, 1, '2026-03-26 12:43:04', 'Entrega inicial generada en venta. Folio: V-41'),
(133, 42, 3, '2026-03-26 13:24:43', 'Entrega inicial generada en venta. Folio: V-42'),
(134, 43, 3, '2026-03-26 13:31:59', 'Entrega inicial generada en venta. Folio: V-43'),
(135, 44, 3, '2026-03-26 13:33:54', 'Entrega inicial generada en venta. Folio: V-44'),
(136, 45, 3, '2026-03-26 13:37:59', NULL),
(137, 46, 1, '2026-03-26 13:43:09', 'Entrega inicial generada en venta. Folio: V-46'),
(138, 47, 1, '2026-03-26 13:43:38', 'Entrega inicial generada en venta. Folio: V-47'),
(139, 48, 1, '2026-03-26 16:24:31', 'Entrega inicial - Caja Rápida. Folio: V-48'),
(140, 49, 1, '2026-03-26 16:27:37', 'Entrega inicial generada en venta. Folio: V-49'),
(141, 50, 1, '2026-03-26 16:42:27', 'Entrega inicial - Caja Rápida. Folio: V-50'),
(142, 51, 1, '2026-03-26 16:44:44', 'Entrega inicial - Caja Rápida. Folio: V-51'),
(143, 52, 1, '2026-03-26 16:46:35', 'Entrega inicial - Caja Rápida. Folio: V-52'),
(144, 53, 1, '2026-03-26 16:52:00', 'Entrega inicial - Caja Rápida. Folio: V-53'),
(145, 54, 1, '2026-03-26 17:01:11', 'Entrega inicial - Caja Rápida. Folio: V-54'),
(146, 55, 1, '2026-03-26 17:17:13', 'Entrega inicial generada en venta. Folio: V-55'),
(147, 56, 1, '2026-03-26 17:23:17', 'Entrega inicial - Caja Rápida. Folio: V-56'),
(148, 57, 1, '2026-03-26 17:23:58', 'Entrega inicial - Caja Rápida. Folio: V-57'),
(149, 58, 1, '2026-03-26 17:25:55', 'Entrega inicial - Caja Rápida. Folio: V-58'),
(150, 59, 1, '2026-03-26 17:27:31', 'Entrega inicial - Caja Rápida. Folio: V-59'),
(151, 60, 1, '2026-03-26 17:55:47', 'Entrega inicial - Caja Rápida. Folio: V-60'),
(152, 61, 1, '2026-03-27 09:22:00', 'Entrega inicial - Caja Rápida. Folio: V-61'),
(153, 62, 1, '2026-03-27 09:28:20', 'Entrega inicial - Caja Rápida. Folio: V-62'),
(154, 63, 1, '2026-03-27 09:30:28', 'Entrega inicial - Caja Rápida. Folio: V-63'),
(155, 64, 1, '2026-03-27 09:33:19', 'Entrega inicial - Caja Rápida. Folio: V-64'),
(156, 65, 1, '2026-03-27 09:42:27', 'Entrega inicial - Caja Rápida. Folio: V-65'),
(157, 66, 1, '2026-03-27 09:43:53', 'Entrega inicial - Caja Rápida. Folio: V-66'),
(158, 67, 1, '2026-03-27 10:55:57', 'Entrega inicial - Caja Rápida. Folio: V-67'),
(159, 68, 1, '2026-03-27 10:58:08', 'Entrega inicial - Caja Rápida. Folio: V-68'),
(160, 69, 1, '2026-03-27 11:00:46', 'Entrega inicial - Caja Rápida. Folio: V-69'),
(161, 70, 1, '2026-03-27 11:02:27', 'Entrega inicial - Caja Rápida. Folio: V-70'),
(162, 71, 1, '2026-03-27 11:05:18', 'Entrega inicial - Caja Rápida. Folio: V-71'),
(163, 72, 1, '2026-03-27 11:37:01', 'Entrega inicial - Caja Rápida. Folio: V-72'),
(164, 73, 1, '2026-03-27 12:23:15', NULL),
(167, 20, 1, '2026-03-27 12:48:41', NULL),
(170, 75, 1, '2026-03-27 14:07:20', NULL),
(171, 76, 1, '2026-03-28 12:03:10', 'Entrega inicial generada en venta. Folio: V-76'),
(172, 77, 1, '2026-03-28 12:03:47', 'Entrega inicial generada en venta. Folio: V-77'),
(173, 78, 1, '2026-03-28 12:26:46', 'Entrega inicial generada en venta. Folio: V-78'),
(174, 79, 1, '2026-03-28 12:29:15', 'Entrega inicial generada en venta. Folio: V-79'),
(175, 80, 1, '2026-03-28 12:44:15', 'Entrega inicial generada en venta. Folio: V-80'),
(176, 81, 1, '2026-03-28 12:45:12', 'Entrega inicial generada en venta. Folio: V-81'),
(177, 82, 1, '2026-03-28 12:46:08', 'Entrega inicial generada en venta. Folio: V-82'),
(178, 83, 1, '2026-03-28 12:47:58', 'Entrega inicial generada en venta. Folio: V-83'),
(179, 84, 1, '2026-03-28 12:59:49', 'Entrega inicial generada en venta. Folio: V-84'),
(180, 85, 1, '2026-03-28 13:00:46', 'Entrega inicial - Caja Rápida. Folio: V-85'),
(181, 86, 1, '2026-03-28 13:08:03', 'Entrega inicial generada en venta. Folio: V-86'),
(182, 87, 1, '2026-03-28 13:12:47', 'Entrega inicial generada en venta. Folio: V-87'),
(183, 88, 1, '2026-03-28 13:13:30', 'Entrega inicial generada en venta. Folio: V-88'),
(184, 89, 1, '2026-03-28 13:28:54', 'Entrega inicial generada en venta. Folio: V-89'),
(185, 90, 1, '2026-03-28 13:41:55', 'Entrega inicial generada en venta. Folio: V-90'),
(186, 91, 1, '2026-03-30 08:30:09', 'Entrega inicial generada en venta. Folio: V-91'),
(187, 92, 1, '2026-03-30 08:31:20', 'Entrega inicial generada en venta. Folio: V-92'),
(188, 93, 1, '2026-03-30 08:50:58', 'Entrega inicial generada en venta. Folio: V-93'),
(189, 94, 1, '2026-03-30 14:30:36', 'Entrega inicial generada en venta. Folio: V-94'),
(190, 95, 3, '2026-03-30 14:35:46', 'Entrega inicial. Folio: V-95'),
(191, 96, 2, '2026-03-30 15:23:46', 'Entrega inicial. Folio: V-96'),
(192, 97, 3, '2026-03-30 15:25:23', 'Entrega inicial. Folio: V-97'),
(193, 98, 3, '2026-03-30 15:29:05', 'Entrega inicial. Folio: V-98'),
(194, 99, 3, '2026-03-30 15:31:48', 'Entrega inicial. Folio: V-99'),
(195, 102, 3, '2026-03-30 15:37:58', 'Entrega inicial. Folio: V-100'),
(196, 103, 3, '2026-03-30 17:20:48', 'Entrega inicial. Folio: V-103'),
(197, 104, 3, '2026-03-30 17:36:53', 'Entrega inicial. Folio: V-104'),
(198, 105, 3, '2026-03-30 17:37:26', 'Entrega inicial. Folio: V-105'),
(199, 106, 3, '2026-03-30 17:38:06', 'Entrega inicial. Folio: V-106'),
(200, 107, 3, '2026-03-30 17:45:09', 'Entrega inicial. Folio: V-107'),
(201, 108, 3, '2026-03-30 17:51:21', 'Entrega inicial. Folio: V-108'),
(202, 109, 3, '2026-03-30 17:57:26', 'Entrega inicial. Folio: V-109'),
(203, 110, 3, '2026-03-30 18:00:36', 'Entrega inicial. Folio: V-110'),
(204, 111, 3, '2026-03-31 08:23:57', 'Entrega inicial. Folio: V-111'),
(205, 112, 3, '2026-03-31 08:27:23', 'Entrega inicial. Folio: V-112'),
(206, 113, 3, '2026-03-31 08:29:40', 'Entrega inicial. Folio: V-113'),
(207, 114, 3, '2026-03-31 08:46:19', 'Entrega inicial. Folio: V-114'),
(208, 115, 3, '2026-03-31 09:04:16', 'Entrega inicial. Folio: V-115'),
(209, 116, 3, '2026-03-31 09:06:46', 'Entrega inicial. Folio: V-116'),
(210, 117, 3, '2026-03-31 09:16:23', 'Entrega inicial. Folio: V-117'),
(211, 118, 3, '2026-03-31 09:27:12', 'Entrega inicial. Folio: V-118'),
(212, 119, 3, '2026-03-31 09:55:39', 'Entrega inicial. Folio: V-119'),
(213, 120, 3, '2026-03-31 10:02:10', 'Entrega inicial. Folio: V-120'),
(214, 121, 3, '2026-03-31 10:22:03', 'Entrega inicial. Folio: V-121'),
(215, 122, 3, '2026-03-31 10:28:06', 'Entrega inicial. Folio: V-122'),
(216, 123, 3, '2026-03-31 10:28:45', 'Entrega inicial. Folio: V-123'),
(217, 124, 3, '2026-03-31 10:52:32', 'Entrega inicial. Folio: V-124'),
(218, 126, 3, '2026-03-31 10:55:53', 'Entrega inicial. Folio: V-126'),
(219, 127, 3, '2026-03-31 10:56:48', 'Entrega inicial. Folio: V-127'),
(220, 128, 3, '2026-03-31 10:59:11', 'Entrega inicial. Folio: V-128'),
(221, 129, 3, '2026-03-31 11:10:41', 'Entrega inicial. Folio: V-129'),
(222, 131, 1, '2026-03-31 12:45:59', 'Entrega inicial. Folio: V-131'),
(223, 132, 1, '2026-03-31 12:54:12', 'Entrega inicial. Folio: V-132'),
(224, 133, 1, '2026-03-31 12:54:33', 'Entrega inicial. Folio: V-133'),
(225, 140, 1, '2026-03-31 13:28:17', 'Entrega inicial. Folio: V-134'),
(226, 141, 1, '2026-03-31 13:30:33', 'Entrega inicial. Folio: V-141'),
(227, 142, 1, '2026-03-31 13:30:57', 'Entrega inicial. Folio: V-142'),
(228, 143, 1, '2026-03-31 14:28:41', 'Entrega inicial. Folio: V-143'),
(229, 144, 1, '2026-03-31 15:26:53', 'Entrega inicial. Folio: V-144'),
(230, 145, 1, '2026-03-31 15:29:29', 'Entrega inicial. Folio: V-145'),
(231, 146, 1, '2026-03-31 15:34:29', 'Entrega inicial. Folio: V-146'),
(232, 147, 1, '2026-03-31 15:35:40', 'Entrega inicial. Folio: V-147'),
(233, 148, 1, '2026-03-31 15:44:55', 'Entrega inicial. Folio: V-148'),
(234, 149, 1, '2026-03-31 15:46:09', 'Entrega inicial. Folio: V-149'),
(235, 150, 1, '2026-03-31 15:50:37', 'Entrega inicial. Folio: V-150'),
(236, 151, 1, '2026-03-31 15:55:55', 'Entrega inicial. Folio: V-151'),
(237, 152, 1, '2026-03-31 16:01:25', 'Entrega inicial. Folio: V-152'),
(238, 153, 1, '2026-03-31 16:02:17', 'Entrega inicial. Folio: V-153'),
(239, 154, 1, '2026-03-31 16:04:15', 'Entrega inicial. Folio: V-154'),
(240, 155, 1, '2026-03-31 16:05:35', 'Entrega inicial. Folio: V-155'),
(241, 156, 1, '2026-03-31 16:06:13', 'Entrega inicial. Folio: V-156'),
(242, 157, 1, '2026-03-31 16:17:04', 'Entrega inicial. Folio: V-157'),
(243, 158, 1, '2026-03-31 16:19:03', 'Entrega inicial. Folio: V-158'),
(244, 159, 1, '2026-03-31 16:21:15', 'Entrega inicial. Folio: V-159'),
(245, 161, 1, '2026-03-31 16:53:37', 'Entrega inicial. Folio: V-161'),
(246, 162, 1, '2026-03-31 16:54:26', 'Entrega inicial. Folio: V-162'),
(247, 163, 1, '2026-03-31 16:58:33', 'Entrega inicial. Folio: V-163'),
(248, 164, 1, '2026-03-31 17:01:54', 'Entrega inicial. Folio: V-164'),
(249, 165, 1, '2026-03-31 17:35:59', 'Entrega inicial. Folio: V-165'),
(250, 166, 1, '2026-03-31 17:36:52', 'Entrega inicial. Folio: V-166'),
(251, 167, 1, '2026-03-31 17:38:32', 'Entrega inicial. Folio: V-167'),
(252, 168, 1, '2026-03-31 17:39:05', 'Entrega inicial. Folio: V-168'),
(253, 169, 1, '2026-04-01 08:42:28', 'Entrega inicial. Folio: V-169'),
(254, 170, 1, '2026-04-01 09:01:17', 'Entrega inicial. Folio: V-170'),
(255, 171, 1, '2026-04-01 09:20:45', 'Entrega inicial. Folio: V-171'),
(256, 172, 1, '2026-04-01 09:29:02', 'Entrega inicial. Folio: V-172'),
(257, 173, 1, '2026-04-01 09:39:09', 'Entrega inicial. Folio: V-173'),
(258, 174, 1, '2026-04-01 09:40:23', 'Entrega inicial. Folio: V-174'),
(259, 175, 1, '2026-04-01 09:47:26', 'Entrega inicial. Folio: V-175'),
(260, 176, 1, '2026-04-01 09:53:35', 'Entrega inicial. Folio: V-176'),
(261, 177, 1, '2026-04-01 10:28:45', 'Entrega inicial - Caja Rápida. Folio: V-177'),
(262, 178, 1, '2026-04-01 10:30:06', 'Entrega inicial. Folio: V-178'),
(263, 179, 1, '2026-04-06 12:44:06', 'Entrega inicial. Folio: V-179'),
(264, 180, 1, '2026-04-07 09:26:43', 'Entrega inicial. Folio: V-180'),
(265, 181, 1, '2026-04-07 09:27:17', 'Entrega inicial. Folio: V-181'),
(266, 182, 1, '2026-04-07 09:28:06', 'Entrega inicial. Folio: V-182'),
(267, 183, 1, '2026-04-07 09:37:32', 'Entrega inicial. Folio: V-183'),
(268, 184, 1, '2026-04-07 09:38:21', 'Entrega inicial. Folio: V-184'),
(269, 185, 1, '2026-04-07 09:42:08', 'Entrega inicial. Folio: V-185'),
(270, 186, 1, '2026-04-07 09:48:32', 'Entrega inicial. Folio: V-186'),
(271, 187, 1, '2026-04-07 09:57:59', 'Entrega inicial. Folio: V-187'),
(272, 188, 1, '2026-04-07 09:58:49', 'Entrega inicial. Folio: V-188'),
(273, 189, 1, '2026-04-07 09:59:42', 'Entrega inicial. Folio: V-189'),
(274, 190, 1, '2026-04-07 10:29:44', 'Entrega inicial. Folio: V-190'),
(275, 191, 1, '2026-04-07 10:31:33', 'Entrega inicial. Folio: V-191'),
(276, 192, 1, '2026-04-07 10:32:01', 'Entrega inicial. Folio: V-192'),
(277, 193, 1, '2026-04-07 10:34:00', 'Entrega inicial. Folio: V-193'),
(278, 194, 1, '2026-04-07 10:35:07', 'Entrega inicial. Folio: V-194'),
(279, 195, 1, '2026-04-07 10:35:28', 'Entrega inicial. Folio: V-195'),
(280, 196, 1, '2026-04-07 10:38:24', 'Entrega inicial. Folio: V-196'),
(281, 197, 1, '2026-04-07 10:43:25', 'Entrega inicial. Folio: V-197'),
(282, 198, 1, '2026-04-07 10:55:13', 'Entrega inicial. Folio: V-198'),
(283, 199, 1, '2026-04-07 11:07:25', 'Entrega inicial. Folio: V-199'),
(284, 200, 1, '2026-04-07 11:11:09', 'Entrega inicial. Folio: V-200'),
(285, 201, 1, '2026-04-07 13:58:06', 'Entrega inicial. Folio: V-201'),
(286, 202, 1, '2026-04-07 14:16:48', 'Entrega inicial. Folio: V-202'),
(287, 203, 1, '2026-04-07 14:18:39', 'Entrega inicial - Caja Rápida. Folio: V-203'),
(288, 204, 1, '2026-04-07 14:20:02', 'Entrega inicial. Folio: V-204'),
(289, 205, 2, '2026-04-07 14:23:14', 'Entrega inicial - Caja Rápida. Folio: V-205'),
(290, 206, 2, '2026-04-07 14:23:41', 'Entrega inicial. Folio: V-206'),
(291, 207, 2, '2026-04-07 14:24:55', 'Entrega inicial. Folio: V-207'),
(292, 208, 2, '2026-04-07 14:25:53', 'Entrega inicial. Folio: V-208'),
(293, 209, 2, '2026-04-07 14:47:06', 'Entrega inicial. Folio: V-209'),
(294, 210, 2, '2026-04-07 14:48:08', 'Entrega inicial. Folio: V-210'),
(295, 211, 2, '2026-04-07 14:48:26', 'Entrega inicial - Caja Rápida. Folio: V-211'),
(296, 212, 3, '2026-04-07 16:29:08', 'Entrega inicial. Folio: V-212'),
(297, 213, 3, '2026-04-07 16:32:25', 'Entrega inicial - Caja Rápida. Folio: V-213'),
(298, 214, 3, '2026-04-07 16:46:34', 'Entrega inicial. Folio: V-214'),
(299, 215, 3, '2026-04-07 16:47:52', 'Entrega inicial. Folio: V-215'),
(300, 216, 3, '2026-04-07 17:02:45', NULL),
(305, 217, 3, '2026-04-07 17:06:13', NULL),
(306, 218, 3, '2026-04-07 17:17:12', 'Entrega inicial. Folio: V-218'),
(307, 219, 1, '2026-04-07 17:50:59', 'Entrega inicial - Caja Rápida. Folio: V-219'),
(308, 220, 1, '2026-04-07 17:54:52', 'Entrega inicial - Caja Rápida. Folio: V-220'),
(309, 221, 1, '2026-04-07 18:01:12', 'Entrega inicial - Caja Rápida. Folio: V-221'),
(310, 222, 1, '2026-04-08 08:56:43', 'Entrega inicial - Caja Rápida. Folio: V-222'),
(311, 223, 1, '2026-04-08 09:11:46', 'Entrega inicial - Caja Rápida. Folio: V-223'),
(312, 224, 2, '2026-04-08 09:27:08', 'Entrega inicial. Folio: V-224'),
(313, 225, 2, '2026-04-08 09:28:47', 'Entrega inicial. Folio: V-225'),
(314, 226, 1, '2026-04-08 12:04:58', 'Entrega inicial - Caja Rápida. Folio: V-226'),
(315, 227, 1, '2026-04-08 15:00:01', 'Entrega inicial. Folio: V-227'),
(316, 228, 1, '2026-04-08 15:01:26', 'Entrega inicial. Folio: V-228'),
(317, 229, 1, '2026-04-08 16:05:18', 'Entrega inicial. Folio: V-229'),
(318, 230, 1, '2026-04-08 16:15:43', 'Entrega inicial. Folio: V-230'),
(319, 231, 1, '2026-04-08 16:17:20', 'Entrega inicial. Folio: V-231'),
(320, 232, 1, '2026-04-08 16:18:22', 'Entrega inicial. Folio: V-232'),
(321, 233, 1, '2026-04-08 16:25:19', 'Entrega inicial. Folio: V-233'),
(322, 234, 1, '2026-04-08 16:31:05', 'Entrega inicial. Folio: V-234'),
(323, 235, 1, '2026-04-08 16:32:07', 'Entrega inicial. Folio: V-235'),
(324, 236, 1, '2026-04-08 17:04:10', 'Entrega inicial. Folio: V-236'),
(325, 237, 1, '2026-04-08 17:04:58', 'Entrega inicial. Folio: V-237'),
(326, 238, 1, '2026-04-08 17:11:17', 'Entrega inicial. Folio: V-238'),
(327, 239, 1, '2026-04-08 17:13:58', 'Entrega inicial. Folio: V-239'),
(328, 240, 1, '2026-04-08 17:15:26', 'Entrega inicial. Folio: V-240'),
(329, 241, 1, '2026-04-08 17:16:51', 'Entrega inicial. Folio: V-241'),
(330, 242, 1, '2026-04-08 17:17:59', 'Entrega inicial. Folio: V-242'),
(331, 243, 2, '2026-04-09 11:36:56', 'Entrega inicial. Folio: V-243'),
(332, 244, 1, '2026-04-09 11:38:03', 'Entrega inicial. Folio: V-244'),
(333, 245, 1, '2026-04-10 11:47:44', 'Entrega inicial. Folio: V-245'),
(334, 246, 1, '2026-04-10 13:37:17', 'Entrega inicial. Folio: V-246'),
(335, 247, 1, '2026-04-10 14:17:12', 'Entrega inicial. Folio: V-247'),
(336, 248, 1, '2026-04-10 14:18:39', 'Entrega inicial. Folio: V-248'),
(337, 249, 1, '2026-04-10 14:19:14', 'Entrega inicial. Folio: V-249'),
(338, 250, 1, '2026-04-11 09:50:18', 'Entrega inicial. Folio: V-250'),
(339, 251, 1, '2026-04-11 12:48:54', 'Entrega inicial. Folio: V-251'),
(340, 252, 1, '2026-04-11 12:52:17', 'Entrega inicial. Folio: V-252'),
(341, 253, 1, '2026-04-11 12:54:29', 'Entrega inicial. Folio: V-253'),
(342, 254, 1, '2026-04-13 08:36:08', 'Entrega inicial. Folio: V-254'),
(343, 256, 1, '2026-04-13 08:37:04', 'Entrega inicial. Folio: V-256'),
(344, 257, 1, '2026-04-13 08:37:19', 'Entrega inicial. Folio: V-257'),
(345, 258, 1, '2026-04-13 08:38:20', 'Entrega inicial. Folio: V-258'),
(346, 259, 1, '2026-04-14 22:50:18', 'Entrega inicial. Folio: V-259'),
(347, 260, 1, '2026-04-14 23:23:21', 'Entrega inicial. Folio: V-260'),
(348, 261, 1, '2026-04-15 09:55:42', 'Entrega inicial. Folio: V-261'),
(349, 262, 1, '2026-04-15 10:08:06', 'Entrega inicial. Folio: V-262'),
(350, 263, 1, '2026-04-20 17:53:18', 'Entrega inicial. Folio: V-263'),
(351, 264, 3, '2026-04-21 12:33:18', 'Entrega inicial. Folio: V-264'),
(352, 265, 3, '2026-04-21 12:35:10', 'Entrega inicial. Folio: V-265');

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

--
-- Volcado de datos para la tabla `faltantes_ingreso`
--

INSERT INTO `faltantes_ingreso` (`id`, `compra_id`, `producto_id`, `cantidad_pendiente`, `fecha_registro`) VALUES
(22, 80, 1, 1.00, '2026-04-23 17:09:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos`
--

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL,
  `folio` varchar(50) NOT NULL,
  `fecha_gasto` date NOT NULL,
  `almacen_id` int(11) NOT NULL COMMENT 'Almacén al que se carga el gasto',
  `categoria_id` int(11) DEFAULT NULL,
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

INSERT INTO `gastos` (`id`, `folio`, `fecha_gasto`, `almacen_id`, `categoria_id`, `usuario_registra_id`, `beneficiario`, `metodo_pago`, `total`, `documento_url`, `observaciones`, `estado`, `fecha_registro`) VALUES
(35, '1', '2026-03-14', 2, 1, 1, 'Purificadora Juanita', 'Efectivo', 10.00, 'GASTO_1773513216_69b5aa00431aa.pdf', 'Se habia acabo el agua', 'pagado', '2026-03-14 18:33:36'),
(36, '2', '2026-03-14', 1, 1, 1, 'Gastos de alimentacion para viajes', 'Efectivo', 400.00, 'GASTO_1773513888_69b5aca0833b5.pdf', 'Se compro comoda apara vaiaje a Puebla', 'pagado', '2026-03-14 18:44:48'),
(37, '3', '2026-03-17', 1, 1, 1, 'Gasolina', 'Efectivo', 200.00, NULL, 'compra de gasolina\n*** CANCELADO por casa el 2026-03-17 18:23 ***\nRAZÓN: me equivoque de concepto', 'cancelado', '2026-03-17 17:13:28'),
(38, '4', '2026-03-17', 1, 1, 1, 'Llanta para camión', 'Efectivo', 2000.00, 'GASTO_1773768248_69b98e38871c2.pdf', '\n*** CANCELADO por casa el 2026-03-17 18:24 ***\nRAZÓN: me equivoque llanta', 'cancelado', '2026-03-17 17:24:08'),
(39, '5', '2026-03-17', 2, 1, 1, 'Papleria el caminito de la escuela', 'Efectivo', 400.00, 'GASTO_1773768606_69b98f9ec89f1.pdf', '\n*** CANCELADO por juan el 2026-03-17 18:30 ***\nRAZÓN: eran de otra medida', 'cancelado', '2026-03-17 17:30:06'),
(40, '6', '2026-03-24', 1, 1, 1, 'Gasolina', 'Efectivo', 200.00, 'GASTO_1774381072_69c2e810c59bf.png', '', 'pagado', '2026-03-24 19:37:52'),
(41, '7', '2026-03-24', 1, NULL, 1, 'Gasolina', 'Efectivo', 200.00, 'GASTO_1774383108_69c2f0043c2c0.pdf', '', 'pagado', '2026-03-24 20:11:48'),
(42, '8', '2026-03-24', 1, NULL, 1, 'Gasolina', 'Efectivo', 200.00, NULL, '\n*** CANCELADO por Administrador General el 2026-03-25 21:42 ***\nRAZÓN: mle pude mal la cantidad', 'cancelado', '2026-03-24 20:14:15'),
(43, '9', '2026-03-24', 1, 3, 1, 'Gasolina', 'Efectivo', 150.00, NULL, '', 'pagado', '2026-03-24 20:57:05'),
(44, '10', '2026-04-14', 1, 3, 1, 'GAS REGIO', 'Efectivo', 100.00, 'GASTO_1776123326_69dd7dbebf9a4.png', '\n*** CANCELADO por casa el 2026-04-14 19:39 ***\nRAZÓN: ya no se requirio', 'cancelado', '2026-04-13 23:35:26'),
(47, '11', '2026-04-14', 1, 7, 1, 'CDMX', 'Efectivo', 200.00, 'GASTO_1776188881_69de7dd174cdc.pdf', '', 'pagado', '2026-04-14 17:48:01'),
(48, '12', '2026-04-14', 2, 3, 1, 'Papeleria Juanita', 'Efectivo', 100.00, 'GASTO_1776188936_69de7e0849fa4.pdf', '', 'pagado', '2026-04-14 17:48:56'),
(51, 'PREST-1776455093', '2026-04-17', 1, 8, 1, 'Javier', '0', 100.00, '', 'Préstamo a Javier por motivo de Adelanto de nomina\n*** CANCELADO por Administrador General el 2026-04-17 21:46 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 19:44:53'),
(53, 'PREST-1776455123', '2026-04-17', 1, 8, 1, 'Javier', '0', 200.00, '', 'Préstamo a Javier por motivo de Adelanto de nomina\n*** CANCELADO por Administrador General el 2026-04-17 21:46 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 19:45:23'),
(54, '13', '2026-04-17', 1, 8, 1, 'Javier', '0', 1.00, '', 'Préstamo a Javier por motivo de Adelanto de nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:47 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:24:01'),
(55, '14', '2026-04-17', 1, 8, 1, 'Juan', '0', 1.00, '', 'Préstamo a Juan por motivo de Adelanto de quincena\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:30:10'),
(56, '15', '2026-04-17', 1, 8, 1, 'Juan', '0', 1.00, '', 'Préstamo a Juan por motivo de Adelanto de quincena\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:30:10'),
(57, '16', '2026-04-17', 1, 8, 1, 'Juan', '0', 1.00, '', 'Préstamo a Juan por motivo de Adelnato de nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:33:55'),
(58, '17', '2026-04-17', 1, 8, 1, 'Juan', '0', 1.00, '', 'Préstamo a Juan por motivo de Adelnato de nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:33:55'),
(59, '18', '2026-04-17', 1, 8, 1, 'Juan', '0', 1.00, '', 'Préstamo a Juan por motivo de Adelanto nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:36:40'),
(60, '19', '2026-04-17', 1, 8, 1, 'Javier', '0', 1.00, '', 'Préstamo a Javier por motivo de Adelanto nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:39:32'),
(61, '20', '2026-04-17', 1, 8, 1, 'Javier', '0', 1.00, '', 'Préstamo a Javier por motivo de 1\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:41:04'),
(62, '21', '2026-04-17', 1, 8, 1, 'Javier', '0', 1.00, '', 'Préstamo a Javier por motivo de 1\n*** CANCELADO por Administrador General el 2026-04-17 23:48 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:41:04'),
(63, '22', '2026-04-17', 1, 8, 1, 'Javier', '0', 1.00, '', 'Préstamo a Javier por motivo de Adelanto de nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:49 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:42:56'),
(64, '23', '2026-04-17', 1, 8, 1, 'Javier', '0', 200.00, '', 'Préstamo a Javier por motivo de Adelanto de quincena\n*** CANCELADO por Administrador General el 2026-04-17 23:49 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:51:17'),
(65, '24', '2026-04-17', 1, 8, 1, 'Javier', '0', 1.00, '', 'Préstamo a Javier por motivo de Adelanto de nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:49 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:52:40'),
(66, '25', '2026-04-17', 1, 8, 1, 'Javier', '0', 1.00, '', 'Préstamo a Javier por motivo de Adelanto de nomina\n*** CANCELADO por Administrador General el 2026-04-17 23:49 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 20:53:25'),
(67, '26', '2026-04-17', 1, 8, 1, 'PRUEBA', '0', 100.00, '', 'PRUEBA GASTO PRESTAMO\n*** CANCELADO por Administrador General el 2026-04-17 23:49 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 21:40:48'),
(70, '27', '2026-04-17', 1, 3, 1, 'Gasolina', '0', 100.00, 'GASTO_1776462223_69e2a98ff2592.pdf', '\n*** CANCELADO por Administrador General el 2026-04-17 23:49 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 21:43:43'),
(71, '28', '2026-04-17', 2, 3, 1, 'Gasolina', 'Efectivo', 100.00, 'GASTO_1776462449_69e2aa718854a.pdf', '\n*** CANCELADO por Administrador General el 2026-04-17 23:49 ***\nRAZÓN: dato mal guardado', 'cancelado', '2026-04-17 21:47:29'),
(72, '29', '2026-04-17', 1, 8, 1, 'Javier', 'efectivo', 100.00, '', 'Préstamo a Javier por motivo de Adelanto de nomina', 'pagado', '2026-04-17 21:53:57'),
(73, '30', '2026-04-18', 1, 8, 1, 'Juan', 'efectivo', 100.00, '', 'Préstamo a Juan por motivo de Adelanto de quincena', 'pagado', '2026-04-18 17:41:35'),
(74, '31', '2026-04-20', 1, 5, 1, 'Taller mecanico el guero', 'Efectivo', 200.00, 'GASTO_1776698665_69e64529ba523.pdf', 'Llevio Javier', 'pagado', '2026-04-20 15:24:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gastos_categorias`
--

CREATE TABLE `gastos_categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `gastos_categorias`
--

INSERT INTO `gastos_categorias` (`id`, `nombre`, `descripcion`, `activo`) VALUES
(1, 'Viáticos', 'Gastos de viaje, alimentación y hospedaje del personal', 1),
(2, 'Papelería', 'Artículos de oficina y consumibles', 1),
(3, 'Gastos en General', 'Gastos operativos menores no clasificados', 1),
(4, 'Hospitalizaciones', 'Gastos médicos y de salud del personal', 1),
(5, 'Mantenimiento', 'Reparaciones de unidades o local', 1),
(6, 'Servicios', 'Pago de luz, agua, internet, etc.', 1),
(7, 'Gastos Legales', '', 1),
(8, 'Préstamo a Trabajador', '', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_capital`
--

CREATE TABLE `historial_capital` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) NOT NULL,
  `almacen_origen_id` int(11) NOT NULL,
  `almacen_destino_id` int(11) DEFAULT NULL,
  `caja_fuerte_destino_id` int(11) DEFAULT NULL,
  `banco_destino_id` int(11) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia') NOT NULL DEFAULT 'Efectivo',
  `usuario_registro_id` int(11) NOT NULL,
  `usuario_autoriza_id` int(11) DEFAULT NULL,
  `concepto` text DEFAULT NULL,
  `referencia_folio` varchar(50) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp(),
  `monto_efectivo` decimal(10,2) DEFAULT 0.00,
  `monto_tarjeta` decimal(10,2) DEFAULT 0.00,
  `monto_transferencia` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_capital`
--

INSERT INTO `historial_capital` (`id`, `categoria_id`, `almacen_origen_id`, `almacen_destino_id`, `caja_fuerte_destino_id`, `banco_destino_id`, `monto`, `metodo_pago`, `usuario_registro_id`, `usuario_autoriza_id`, `concepto`, `referencia_folio`, `fecha_movimiento`, `monto_efectivo`, `monto_tarjeta`, `monto_transferencia`) VALUES
(13, 1, 1, NULL, NULL, NULL, 220.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-13)', NULL, '2026-04-14 00:00:01', 20.00, 0.00, 200.00),
(15, 1, 1, NULL, NULL, NULL, 5240.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-13)', NULL, '2026-04-14 00:00:01', 5040.00, 0.00, 200.00),
(16, 1, 1, NULL, NULL, NULL, 5240.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-13)', NULL, '2026-04-13 00:00:01', 5040.00, 0.00, 200.00),
(17, 1, 1, NULL, NULL, NULL, 5240.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 5040.00, 0.00, 200.00),
(18, 1, 1, NULL, NULL, NULL, 5240.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 5040.00, 0.00, 200.00),
(19, 1, 1, NULL, NULL, NULL, 5240.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 5040.00, 0.00, 200.00),
(20, 1, 1, NULL, NULL, NULL, 0.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 0.00, 0.00, 0.00),
(21, 1, 1, NULL, NULL, NULL, 0.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 0.00, 0.00, 0.00),
(22, 1, 1, NULL, NULL, NULL, 0.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 0.00, 0.00, 0.00),
(23, 1, 1, NULL, NULL, NULL, 0.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 0.00, 0.00, 0.00),
(24, 1, 1, NULL, NULL, NULL, 0.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 0.00, 0.00, 0.00),
(25, 1, 1, NULL, NULL, NULL, 5040.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 4840.00, 0.00, 200.00),
(26, 1, 1, NULL, NULL, NULL, 5040.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 4840.00, 0.00, 200.00),
(27, 1, 1, NULL, NULL, NULL, 5040.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 4840.00, 0.00, 200.00),
(28, 1, 1, NULL, NULL, NULL, 5040.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 4840.00, 0.00, 200.00),
(29, 1, 1, NULL, NULL, NULL, 5040.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 4840.00, 0.00, 200.00),
(30, 1, 1, NULL, NULL, NULL, 5040.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 4840.00, 0.00, 200.00),
(31, 1, 1, NULL, NULL, NULL, 5040.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 4840.00, 0.00, 200.00),
(32, 1, 1, NULL, NULL, NULL, 3540.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 4840.00, -1500.00, 200.00),
(33, 1, 1, NULL, NULL, NULL, 5080.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 4880.00, 0.00, 200.00),
(34, 1, 1, NULL, NULL, NULL, 5080.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 4880.00, 0.00, 200.00),
(35, 1, 1, NULL, NULL, NULL, 3580.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 4880.00, -1500.00, 200.00),
(36, 1, 1, NULL, NULL, NULL, 3600.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 4900.00, -1500.00, 200.00),
(37, 1, 1, NULL, NULL, NULL, 3620.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 4920.00, -1500.00, 200.00),
(38, 1, 1, NULL, NULL, NULL, 3620.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 4920.00, -1500.00, 200.00),
(39, 1, 1, NULL, NULL, NULL, 3620.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3420.00, 0.00, 200.00),
(40, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(41, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(42, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(43, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(44, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(45, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(46, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(47, 1, 1, NULL, NULL, NULL, 3640.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 3440.00, 0.00, 200.00),
(51, 1, 1, NULL, NULL, NULL, 5440.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-14)', NULL, '2026-04-15 00:00:01', 3440.00, 0.00, 2000.00),
(55, 1, 1, NULL, NULL, NULL, 2000.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 2000.00, 0.00, 0.00),
(56, 1, 1, NULL, NULL, NULL, 1900.00, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-15)', NULL, '2026-04-16 00:00:01', 1900.00, 0.00, 0.00),
(57, 1, 1, NULL, NULL, NULL, 1800.00, 'Efectivo', 1, NULL, 'Saldo inicial modificado por)', NULL, '2026-04-16 00:00:01', 1800.00, 0.00, 0.00),
(58, 1, 1, NULL, NULL, NULL, 1700.00, 'Efectivo', 1, NULL, 'Saldo inicial modificado porpago de nomina)', NULL, '2026-04-16 00:00:01', 1700.00, 0.00, 0.00),
(73, 4, 1, NULL, 1, NULL, 2700.00, 'Efectivo', 1, NULL, 'Saldo inicial modificado por: inyeccion de capital', NULL, '2026-04-16 00:00:01', 2700.00, 0.00, 0.00),
(74, 5, 1, NULL, 1, NULL, 2400.00, 'Efectivo', 1, NULL, 'Movimiento de salida: Salida de efectivo a caja fuerte', NULL, '2026-04-16 00:00:01', 2400.00, 0.00, 0.00),
(75, 5, 1, NULL, 1, NULL, 1400.00, 'Efectivo', 1, NULL, 'Movimiento de salida: Movimiento de efectivo a caja fuerte', NULL, '2026-04-16 00:00:01', 1400.00, 0.00, 0.00),
(76, 10, 1, NULL, 1, NULL, 2000.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Ingreso desde caja fuerte', NULL, '2026-04-16 00:00:01', 2000.00, 0.00, 0.00),
(77, 10, 1, NULL, 1, NULL, 1800.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Ingeso desde caja fuerte', NULL, '2026-04-16 00:00:01', 1800.00, 0.00, 0.00),
(78, 10, 1, NULL, 1, NULL, 1800.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: entrada desde caja fuerte', NULL, '2026-04-16 00:00:01', 1800.00, 0.00, 0.00),
(79, 10, 1, NULL, 1, NULL, 2100.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Ingeso desde caja fuerte', NULL, '2026-04-16 00:00:01', 2100.00, 0.00, 0.00),
(80, 10, 1, NULL, 1, NULL, 2200.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Ingreso desde caja fuerte', NULL, '2026-04-16 00:00:01', 2200.00, 0.00, 0.00),
(81, 2, 1, 2, 1, NULL, 2100.00, 'Efectivo', 1, NULL, 'Movimiento de traspaso: Traspaso entre almacenes', NULL, '2026-04-16 00:00:01', 2100.00, 0.00, 0.00),
(82, 2, 1, 2, 1, NULL, 2000.00, 'Efectivo', 1, NULL, 'Movimiento de traspaso: ', NULL, '2026-04-16 00:00:01', 2000.00, 0.00, 0.00),
(83, 2, 2, NULL, NULL, NULL, 100.00, 'Efectivo', 1, NULL, 'Entrada por traspaso desde Almacén ID: 1', NULL, '2026-04-16 00:00:01', 100.00, 0.00, 0.00),
(84, 6, 1, NULL, 1, NULL, 1900.00, 'Efectivo', 1, NULL, 'Movimiento de salida: Pago de nomina : 2026-04-16 11:35:25', NULL, '2026-04-16 00:00:01', 1900.00, 0.00, 0.00),
(86, 13, 1, 1, NULL, NULL, 1901.00, 'Efectivo', 1, NULL, 'Movimiento de Entrada: Abono a préstamo ID: 22 : 2026-04-17 17:41:22', NULL, '2026-04-18 00:00:01', 1901.00, 0.00, 0.00),
(87, 13, 1, 1, NULL, NULL, 1902.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 21 : 2026-04-18 08:49:15', NULL, '2026-04-18 08:49:15', 1902.00, 0.00, 0.00),
(88, 13, 1, 1, NULL, NULL, 1903.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 20 : 2026-04-18 09:15:50', NULL, '2026-04-18 09:15:50', 1903.00, 0.00, 0.00),
(89, 13, 1, 1, NULL, NULL, 1904.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 18 : 2026-04-18 09:22:49', NULL, '2026-04-18 09:22:49', 1904.00, 0.00, 0.00),
(90, 13, 1, 1, NULL, NULL, 2004.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 19 : 2026-04-18 09:24:09', NULL, '2026-04-18 09:24:09', 2004.00, 0.00, 0.00),
(91, 13, 1, 1, NULL, NULL, 2005.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 16 : 2026-04-18 09:37:34', NULL, '2026-04-18 09:37:34', 2005.00, 0.00, 0.00),
(92, 13, 1, 1, NULL, NULL, 2006.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 17 : 2026-04-18 09:42:03', NULL, '2026-04-18 09:42:03', 2006.00, 0.00, 0.00),
(93, 13, 1, 1, NULL, NULL, 2007.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 17 : 2026-04-18 09:42:03', NULL, '2026-04-18 09:42:03', 2007.00, 0.00, 0.00),
(94, 13, 1, 1, NULL, NULL, 2008.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 15 : 2026-04-18 09:51:05', NULL, '2026-04-18 09:51:05', 2008.00, 0.00, 0.00),
(95, 13, 1, 1, NULL, NULL, 2009.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 14 : 2026-04-18 09:59:43', NULL, '2026-04-18 09:59:43', 2009.00, 0.00, 0.00),
(96, 13, 1, 1, NULL, NULL, 2010.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 14 : 2026-04-18 09:59:43', NULL, '2026-04-18 09:59:43', 2010.00, 0.00, 0.00),
(97, 13, 1, 1, NULL, NULL, 2011.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 12 : 2026-04-18 10:00:31', NULL, '2026-04-18 10:00:31', 2011.00, 0.00, 0.00),
(98, 13, 1, 1, NULL, NULL, 2012.00, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 13 : 2026-04-18 10:01:50', NULL, '2026-04-18 10:01:50', 2012.00, 0.00, 0.00),
(99, 13, 1, 1, NULL, NULL, 2012.50, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 11 : 2026-04-18 10:03:51', NULL, '2026-04-18 10:03:51', 2012.50, 0.00, 0.00),
(100, 10, 1, NULL, 1, NULL, 2112.50, 'Efectivo', 1, NULL, 'Movimiento de entrada: Traspáso dse efectivo : 2026-04-18 11:39:04', NULL, '2026-04-18 11:39:04', 2112.50, 0.00, 0.00),
(101, 10, 1, NULL, 1, NULL, 2212.50, 'Efectivo', 1, NULL, 'Movimiento de entrada: Traspaso de efectivo : 2026-04-18 11:40:01', NULL, '2026-04-18 11:40:01', 2212.50, 0.00, 0.00),
(102, 13, 1, 1, NULL, NULL, 2312.50, 'Efectivo', 1, NULL, 'Movimiento de entrada: Abono a préstamo ID: 26Monto100 : 2026-04-20 09:15:43', NULL, '2026-04-20 09:15:43', 2312.50, 0.00, 0.00),
(103, 1, 1, NULL, NULL, NULL, 2132.50, 'Efectivo', 1, NULL, 'Saldo inicial automático (Corte: 2026-04-20)', NULL, '2026-04-21 00:00:01', 2132.50, 0.00, 0.00),
(104, 1, 1, NULL, NULL, NULL, 2132.50, 'Efectivo', 3, NULL, 'Saldo inicial automático (Corte: 2026-04-21)', NULL, '2026-04-22 00:00:01', 2132.50, 0.00, 0.00),
(105, 2, 1, 3, 1, NULL, 2032.50, 'Efectivo', 1, NULL, 'Movimiento de traspaso: Flujo de efectivo para cambio Monto :100 Fecha: 2026-04-21 12:55:48', NULL, '2026-04-21 12:55:48', 2032.50, 0.00, 0.00),
(106, 2, 3, NULL, NULL, NULL, 100.00, 'Efectivo', 1, NULL, 'Entrada por traspaso desde Almacén ID: 1 Monto 100 | 2026-04-21 12:55:48', NULL, '2026-04-21 12:55:48', 100.00, 0.00, 0.00),
(107, 5, 2, NULL, 2, NULL, 90.00, 'Efectivo', 1, NULL, 'Movimiento de salida: recolección de efectivo Monto :10 Fecha: 2026-04-21 12:59:14', NULL, '2026-04-21 12:59:14', 90.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_pagos`
--

CREATE TABLE `historial_pagos` (
  `id` int(11) NOT NULL,
  `venta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `saldo_favor` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_pagos`
--

INSERT INTO `historial_pagos` (`id`, `venta_id`, `usuario_id`, `monto`, `saldo_favor`, `metodo_pago`, `referencia`, `fecha`) VALUES
(61, 1, 3, 400.00, 0.00, 'Efectivo', NULL, '2026-03-12 16:23:33'),
(62, 2, 3, 400.00, 0.00, 'Efectivo', NULL, '2026-03-12 16:24:39'),
(63, 3, 3, 600.00, 0.00, 'Efectivo', NULL, '2026-03-12 16:25:08'),
(64, 4, 3, 400.00, 0.00, 'Efectivo', NULL, '2026-03-12 16:32:55'),
(65, 5, 1, 400.00, 0.00, 'Efectivo', NULL, '2026-03-12 16:35:04'),
(66, 6, 1, 400.00, 0.00, 'Efectivo', NULL, '2026-03-12 16:39:36'),
(67, 7, 3, 40.00, 0.00, 'Efectivo', NULL, '2026-03-12 17:41:05'),
(68, 8, 3, 400.00, 0.00, 'Efectivo', NULL, '2026-03-12 17:41:27'),
(69, 9, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-03-12 17:41:57'),
(70, 10, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-12 22:04:21'),
(71, 11, 3, 40.00, 0.00, 'Efectivo', NULL, '2026-03-13 12:05:21'),
(72, 12, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-13 16:30:14'),
(73, 13, 1, 4.80, 0.00, 'Efectivo', NULL, '2026-03-13 16:33:04'),
(74, 14, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-13 17:34:43'),
(75, 15, 3, 3.00, 0.00, 'Efectivo', NULL, '2026-03-13 17:36:48'),
(76, 16, 2, 200400.00, 0.00, 'Efectivo', NULL, '2026-03-14 08:45:31'),
(77, 17, 1, 1000.00, 0.00, 'Efectivo', NULL, '2026-03-14 09:13:13'),
(78, 18, 1, 20000.00, 0.00, 'Efectivo', NULL, '2026-03-14 09:14:56'),
(79, 19, 1, 3.00, 0.00, 'Efectivo', NULL, '2026-03-14 09:16:39'),
(80, 20, 3, 20020.00, 0.00, 'Efectivo', NULL, '2026-03-14 15:15:46'),
(81, 21, 3, 8697.00, 0.00, 'Efectivo', NULL, '2026-03-17 10:50:27'),
(82, 22, 1, 2697.00, 0.00, 'Efectivo', NULL, '2026-03-17 10:53:20'),
(83, 23, 2, 3000.00, 0.00, 'Efectivo', NULL, '2026-03-17 11:42:54'),
(84, 24, 2, 1600.00, 0.00, 'Efectivo', NULL, '2026-03-17 12:55:28'),
(85, 25, 2, 50000.00, 0.00, 'Efectivo', NULL, '2026-03-17 13:09:48'),
(86, 26, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-19 16:01:55'),
(87, 27, 1, 200.00, 0.00, 'Efectivo', NULL, '2026-03-20 17:51:22'),
(88, 28, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-21 11:34:25'),
(89, 29, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-21 11:35:18'),
(90, 30, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-21 21:44:47'),
(91, 31, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-23 09:03:39'),
(92, 32, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-23 19:52:21'),
(93, 34, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-25 12:00:40'),
(94, 35, 1, 3000.00, 0.00, 'Efectivo', NULL, '2026-03-26 10:13:57'),
(95, 36, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 10:15:14'),
(96, 37, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 10:18:11'),
(97, 38, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 10:23:02'),
(98, 39, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 10:23:55'),
(99, 40, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 11:06:59'),
(100, 41, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 12:43:04'),
(101, 43, 3, 120.00, 0.00, 'Efectivo', NULL, '2026-03-26 13:31:59'),
(102, 45, 3, 120.00, 0.00, 'Efectivo', NULL, '2026-03-26 13:36:43'),
(103, 46, 1, 200.00, 0.00, 'Efectivo', NULL, '2026-03-26 13:43:09'),
(104, 47, 1, 3.00, 0.00, 'Efectivo', NULL, '2026-03-26 13:43:38'),
(105, 42, 3, 1000.00, 0.00, 'Efectivo', NULL, '2026-03-26 13:53:30'),
(106, 48, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 16:24:31'),
(107, 49, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 16:27:37'),
(108, 50, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 16:42:27'),
(109, 51, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 16:44:44'),
(110, 52, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 16:46:35'),
(111, 53, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 16:52:00'),
(112, 54, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 17:01:11'),
(113, 55, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-26 17:17:13'),
(114, 56, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 17:23:17'),
(115, 57, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 17:23:58'),
(116, 58, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 17:25:55'),
(117, 59, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 17:27:31'),
(118, 60, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-26 17:55:47'),
(119, 61, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-27 09:22:00'),
(120, 62, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-27 09:28:20'),
(121, 63, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-27 09:30:28'),
(122, 64, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-27 09:33:19'),
(123, 65, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-27 09:42:27'),
(124, 66, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-27 09:43:53'),
(125, 67, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-27 10:55:57'),
(126, 68, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-27 10:58:08'),
(127, 69, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-27 11:00:46'),
(128, 70, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-27 11:02:27'),
(129, 71, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-27 11:05:18'),
(130, 72, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-27 11:37:01'),
(131, 42, 1, 1000.00, 0.00, 'Efectivo', NULL, '2026-03-20 11:55:00'),
(132, 73, 1, 200.00, 0.00, 'Efectivo', NULL, '2026-03-27 12:22:58'),
(133, 74, 1, 200.00, 0.00, 'Efectivo', NULL, '2026-03-27 12:47:55'),
(134, 75, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-27 14:02:01'),
(135, 76, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:03:10'),
(136, 77, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:03:47'),
(137, 78, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:26:46'),
(138, 79, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:29:15'),
(139, 80, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:44:15'),
(140, 81, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:45:12'),
(141, 82, 1, 7.80, 0.00, 'Efectivo', NULL, '2026-03-28 12:46:08'),
(142, 83, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:47:58'),
(143, 84, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 12:59:49'),
(144, 85, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 13:00:46'),
(145, 86, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 13:08:03'),
(146, 87, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 13:12:47'),
(147, 88, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-28 13:13:30'),
(148, 89, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-28 13:28:54'),
(149, 90, 1, 24.80, 0.00, 'Efectivo', NULL, '2026-03-28 13:41:55'),
(150, 91, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-30 08:30:09'),
(151, 92, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-30 08:31:20'),
(152, 93, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-30 08:50:58'),
(153, 94, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 14:30:36'),
(154, 95, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 14:35:46'),
(155, 96, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-03-30 15:23:46'),
(156, 97, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 15:25:23'),
(157, 98, 3, 40.00, 0.00, 'Efectivo', NULL, '2026-03-30 15:29:05'),
(158, 99, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 15:31:48'),
(159, 102, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 15:37:58'),
(160, 103, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 17:23:40'),
(161, 104, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 17:36:53'),
(162, 105, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 17:37:37'),
(163, 106, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 17:38:19'),
(164, 107, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 17:45:23'),
(165, 108, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 17:51:32'),
(166, 109, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 17:57:37'),
(167, 110, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-30 18:00:54'),
(168, 111, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 08:24:09'),
(169, 112, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 08:27:40'),
(170, 113, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 08:29:51'),
(171, 114, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 08:46:37'),
(172, 115, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 09:04:27'),
(173, 116, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 09:06:56'),
(174, 117, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 09:16:23'),
(175, 118, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 09:27:12'),
(176, 119, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 09:55:39'),
(177, 120, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:02:10'),
(178, 121, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:22:03'),
(179, 122, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:28:06'),
(180, 123, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:28:45'),
(181, 124, 3, 43.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:52:32'),
(182, 125, 3, 43.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:54:34'),
(183, 126, 3, 40.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:55:53'),
(184, 127, 3, 40.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:56:48'),
(185, 128, 3, 40.00, 0.00, 'Efectivo', NULL, '2026-03-31 10:59:11'),
(186, 129, 3, 60.00, 0.00, 'Efectivo', NULL, '2026-03-31 11:10:41'),
(187, 130, 1, 60.00, 0.00, 'Efectivo', NULL, '2026-03-31 11:23:15'),
(188, 131, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 12:45:59'),
(189, 132, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 12:54:12'),
(190, 133, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 12:54:33'),
(191, 140, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 13:28:17'),
(192, 141, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 13:30:33'),
(193, 143, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 14:28:41'),
(194, 144, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:26:53'),
(195, 145, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:29:29'),
(196, 146, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:34:29'),
(197, 147, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:35:40'),
(198, 144, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:41:23'),
(199, 145, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:41:33'),
(200, 130, 1, 80.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:41:48'),
(201, 148, 1, 60.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:45:10'),
(202, 149, 1, 40.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:49:32'),
(203, 151, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 15:56:11'),
(204, 152, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:01:25'),
(205, 153, 1, 40.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:02:17'),
(206, 154, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:04:26'),
(207, 154, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:04:31'),
(208, 154, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:04:51'),
(209, 155, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:05:46'),
(210, 157, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:17:04'),
(211, 158, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:19:03'),
(212, 159, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:21:15'),
(213, 160, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:21:33'),
(214, 161, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:53:37'),
(215, 162, 1, 180.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:54:26'),
(216, 163, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-31 16:58:33'),
(217, 164, 1, 63.00, 0.00, 'Efectivo', NULL, '2026-03-31 17:01:54'),
(218, 165, 1, 26.00, 0.00, 'Efectivo', NULL, '2026-03-31 17:35:59'),
(219, 166, 1, 26.00, 0.00, 'Efectivo', NULL, '2026-03-31 17:36:52'),
(220, 167, 1, 43.00, 0.00, 'Efectivo', NULL, '2026-03-31 17:38:32'),
(221, 168, 1, 23.00, 0.00, 'Efectivo', NULL, '2026-03-31 17:39:05'),
(222, 172, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-04-01 09:29:33'),
(223, 173, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-04-01 09:39:40'),
(224, 174, 1, 40.00, 0.00, 'Efectivo', NULL, '2026-04-01 09:40:23'),
(225, 175, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-04-01 09:48:04'),
(226, 176, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-04-01 09:53:52'),
(227, 177, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-01 10:28:45'),
(228, 178, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-01 10:30:06'),
(229, 33, 1, 4320.00, 0.00, 'Efectivo', NULL, '2026-04-06 12:40:10'),
(230, 150, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-06 12:40:42'),
(231, 156, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-06 12:40:54'),
(232, 179, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-06 12:44:20'),
(234, 42, 1, 806.00, 0.00, 'Efectivo', NULL, '2026-04-06 14:49:34'),
(235, 42, 1, 1.00, 0.00, 'Efectivo', NULL, '2026-04-07 00:36:16'),
(236, 42, 1, 1.00, 0.00, 'Efectivo', NULL, '2026-04-07 00:37:42'),
(239, 42, 1, 1.00, 0.00, 'Efectivo', NULL, '2026-04-07 00:57:05'),
(240, 42, 1, 1.00, 0.00, 'Efectivo', NULL, '2026-04-07 01:13:06'),
(241, 42, 1, 206.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 17:06:03'),
(242, 42, 1, 10.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 17:20:09'),
(243, 42, 1, 80.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 17:22:39'),
(244, 42, 1, 100.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 17:25:24'),
(245, 180, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 09:26:43'),
(246, 182, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-04-07 09:28:06'),
(247, 182, 1, 10.00, 0.00, 'Efectivo', NULL, '2026-04-07 09:29:49'),
(248, 181, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 09:37:06'),
(249, 183, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 09:37:32'),
(250, 184, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 09:38:56'),
(251, 188, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 09:58:49'),
(252, 198, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 10:55:49'),
(253, 200, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 11:14:58'),
(254, 42, 1, 40.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 19:22:01'),
(255, 42, 1, 40.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 19:27:48'),
(256, 42, 1, 100.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 19:28:18'),
(257, 42, 1, 100.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 19:33:50'),
(258, 201, 1, 20.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 22:14:48'),
(259, 202, 1, 20.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 22:17:02'),
(260, 203, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 14:18:39'),
(261, 205, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-04-07 14:23:14'),
(262, 206, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-04-07 22:23:57'),
(263, 207, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-04-07 14:24:55'),
(264, 208, 2, 200.00, 0.00, 'Saldo a Favor', NULL, '2026-04-07 22:26:08'),
(265, 209, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-04-07 14:47:06'),
(266, 210, 2, 300.00, 0.00, 'Efectivo', NULL, '2026-04-07 14:48:08'),
(267, 211, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-04-07 14:48:26'),
(268, 212, 3, 20.00, 0.00, 'Saldo a Favor', NULL, '2026-04-08 00:30:27'),
(269, 213, 3, 20.00, 0.00, 'Efectivo', NULL, '2026-04-07 16:32:25'),
(270, 214, 3, 120.00, 0.00, 'Efectivo', NULL, '2026-04-07 16:46:34'),
(271, 215, 3, 120.00, 0.00, 'Efectivo', NULL, '2026-04-07 16:47:52'),
(272, 216, 3, 700.00, 0.00, 'Efectivo', NULL, '2026-04-07 17:02:16'),
(273, 217, 3, 343.00, 0.00, 'Efectivo', NULL, '2026-04-07 17:04:27'),
(274, 218, 3, 100.00, 0.00, 'Efectivo', NULL, '2026-04-07 17:17:12'),
(275, 219, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-04-07 17:50:59'),
(276, 220, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-04-07 17:54:52'),
(277, 221, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-04-07 18:01:12'),
(278, 222, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-04-08 08:56:43'),
(279, 223, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-04-08 09:11:46'),
(280, 224, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-04-08 09:27:08'),
(281, 225, 2, 200.00, 0.00, 'Efectivo', NULL, '2026-04-08 09:28:47'),
(282, 226, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-04-08 12:04:58'),
(283, 227, 1, 100.00, 0.00, 'Efectivo', NULL, '2026-04-08 15:00:01'),
(284, 228, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-08 15:01:26'),
(285, 229, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-08 16:05:18'),
(286, 230, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-08 16:15:43'),
(287, 231, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-08 16:17:20'),
(288, 232, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-08 16:18:22'),
(289, 233, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-08 16:25:19'),
(290, 234, 1, 20.00, 0.00, 'Saldo a Favor', NULL, '2026-04-08 16:31:05'),
(291, 235, 1, 20.00, 0.00, 'Efectivo', NULL, '2026-04-08 16:32:07'),
(292, 236, 1, 20.00, 10.00, 'Efectivo', '', '2026-04-08 17:04:10'),
(293, 237, 1, 20.00, 20.00, 'Saldo a Favor', NULL, '2026-04-09 01:05:19'),
(294, 238, 1, 20.00, 20.00, 'Saldo a Favor', '', '2026-04-09 01:11:29'),
(295, 239, 1, 20.00, 20.00, 'Saldo a Favor', '', '2026-04-09 01:14:14'),
(296, 241, 1, 20.00, 20.00, 'Saldo a Favor', '', '2026-04-09 01:17:09'),
(297, 242, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-09 01:18:12'),
(298, 243, 2, 200.00, 0.00, 'Efectivo', '', '2026-04-09 11:36:56'),
(299, 244, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-09 11:38:03'),
(300, 245, 1, 20.00, 20.00, 'Saldo a Favor', '', '2026-04-10 11:47:44'),
(301, 246, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-10 21:37:51'),
(302, 240, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-10 22:14:04'),
(303, 247, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-10 14:17:12'),
(304, 248, 1, 20.00, 20.00, 'Saldo a Favor', '', '2026-04-10 14:18:39'),
(305, 250, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-11 09:50:18'),
(306, 251, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-11 12:48:54'),
(307, 249, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-11 20:49:51'),
(308, 252, 1, 20.00, 0.00, 'Transferencia', '', '2026-04-11 12:52:17'),
(309, 254, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-13 08:36:08'),
(310, 258, 1, 10.00, 0.00, 'Efectivo', '', '2026-04-13 08:38:20'),
(311, 256, 1, 216.00, 216.00, 'Saldo a Favor', '', '2026-04-13 17:01:45'),
(312, 255, 1, 200.00, 0.00, 'Transferencia', '', '2026-04-13 17:02:01'),
(313, 253, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-15 06:50:32'),
(314, 259, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-14 06:51:19'),
(315, 260, 1, 20.00, 20.00, 'Saldo a Favor', '', '2026-04-15 23:23:21'),
(316, 261, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-15 09:55:42'),
(317, 262, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-15 10:08:06'),
(318, 204, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-17 01:01:49'),
(319, 263, 1, 20.00, 0.00, 'Efectivo', '', '2026-04-20 17:53:18'),
(320, 264, 3, 20.00, 0.00, 'Efectivo', '', '2026-04-21 12:33:18'),
(321, 265, 3, 20.00, 20.00, 'Saldo a Favor', '', '2026-04-21 12:35:10');

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
(32, 1, 1, 137.00, 10.00, 500.00),
(33, 2, 1, 31.00, 10.00, 500.00),
(34, 3, 1, 10.00, 10.00, 500.00),
(35, 4, 1, 10.00, 10.00, 500.00),
(36, 1, 2, 9.00, 10.00, 500.00),
(37, 2, 2, 11.00, 10.00, 500.00),
(38, 3, 2, 10.00, 10.00, 500.00),
(39, 4, 2, 10.00, 10.00, 500.00),
(40, 1, 3, 172.00, 10.00, 500.00),
(41, 2, 3, 10.00, 10.00, 500.00),
(42, 3, 3, 10.00, 10.00, 500.00),
(43, 4, 3, 10.00, 10.00, 500.00),
(44, 1, 4, 1012.00, 10.00, 500.00),
(45, 2, 4, 2009.00, 10.00, 500.00),
(46, 3, 4, 9.00, 10.00, 500.00),
(47, 4, 4, 10.00, 10.00, 500.00),
(48, 1, 5, 11.00, 10.00, 500.00),
(49, 2, 5, 10.00, 10.00, 500.00),
(50, 3, 5, 10.00, 10.00, 500.00),
(51, 4, 5, 10.00, 10.00, 500.00),
(63, 1, 6, 13.00, 0.00, 0.00),
(64, 2, 6, 10.00, 1.00, 0.00),
(65, 3, 6, 10.00, 0.00, 0.00),
(66, 4, 6, 10.00, 0.00, 0.00),
(67, 1, 7, 27.00, 0.00, 0.00),
(68, 2, 7, 10.00, 0.00, 0.00),
(69, 1, 8, 24.00, 0.00, 0.00),
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
(140, 2, 20, 4.00, 0.00, 0.00),
(141, 3, 20, 10.00, 0.00, 0.00),
(142, 4, 20, 10.00, 0.00, 0.00),
(153, 2, 19, 350.00, 0.00, 0.00),
(163, 1, 21, 1857.00, 0.00, 0.00),
(164, 2, 21, 3378.00, 0.00, 0.00),
(165, 3, 21, 245.00, 0.00, 0.00),
(166, 4, 21, 246.00, 0.00, 0.00),
(186, 2, 22, 2.00, 0.00, 0.00),
(201, 1, 23, 887.00, 1.00, 0.00),
(202, 2, 24, 1000.00, 0.00, 0.00),
(203, 2, 23, 101.00, 0.00, 0.00);

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
(32, 177, 74, 1.00, 100.00, '2026-03-21 02:47:10'),
(33, 184, 75, 150.00, 10.00, '2026-04-15 05:59:57'),
(34, 185, 76, 20.00, 75.00, '2026-04-15 06:19:09'),
(35, 186, 77, 20.00, 100.00, '2026-04-15 17:54:40'),
(36, 187, 78, 1.00, 2.00, '2026-04-16 20:58:09'),
(37, 188, 79, 20.00, 50.00, '2026-04-16 21:58:23'),
(38, 189, 80, 20.00, 50.00, '2026-04-16 22:04:06'),
(39, 190, 81, 1000.00, 0.10, '2026-04-16 22:05:07'),
(45, 199, 89, 2.00, 100.00, '2026-04-22 17:28:09'),
(46, 200, 90, 2.00, 50.00, '2026-04-22 17:29:45'),
(47, 201, 91, 2.00, 50.00, '2026-04-22 17:31:18'),
(48, 202, 92, 2.00, 100.00, '2026-04-22 17:37:13'),
(49, 203, 93, 2.00, 50.00, '2026-04-22 17:39:00'),
(50, 204, 94, 2.00, 50.00, '2026-04-22 17:40:22'),
(51, 205, 95, 2.00, 50.00, '2026-04-22 17:42:59'),
(52, 206, 96, 4.00, 25.00, '2026-04-22 18:43:03'),
(53, 207, 97, 2.00, 50.00, '2026-04-22 18:44:30'),
(54, 208, 98, 2.00, 50.00, '2026-04-22 18:51:24'),
(55, 209, 99, 2.00, 50.00, '2026-04-22 19:03:08'),
(57, 211, 103, 2.00, 50.00, '2026-04-22 19:15:09'),
(58, 212, 104, 2.00, 50.00, '2026-04-22 19:21:52'),
(59, 213, 105, 2.00, 50.00, '2026-04-22 19:27:42'),
(60, 214, 106, 2.00, 50.00, '2026-04-22 19:31:22'),
(61, 215, 107, 4.00, 25.00, '2026-04-23 16:18:52'),
(62, 216, 109, 1.00, 50.00, '2026-04-23 16:20:22'),
(63, 217, 110, 1.00, 100.00, '2026-04-23 17:03:45'),
(64, 218, 111, 1.00, 100.00, '2026-04-23 17:09:23'),
(65, 219, 112, 1.00, 10.00, '2026-04-23 17:33:49'),
(66, 220, 113, 3.00, 0.00, '2026-04-23 19:14:21'),
(67, 221, 114, 2.00, 0.00, '2026-04-23 19:14:21'),
(68, 222, 115, 1.00, 0.00, '2026-04-23 19:16:35'),
(69, 223, 116, 2.00, 0.00, '2026-04-23 19:18:56'),
(70, 224, 117, 4.00, 0.00, '2026-04-23 20:02:46'),
(71, 225, 118, 4.00, 0.00, '2026-04-23 20:02:46'),
(72, 226, 119, 2.00, 0.00, '2026-04-23 20:06:07'),
(73, 227, 120, 2.00, 0.00, '2026-04-23 20:15:16'),
(74, 228, 121, 4.00, 0.00, '2026-04-23 20:17:43'),
(75, 229, 122, 3.00, 0.00, '2026-04-23 20:17:43'),
(76, 230, 123, 3.00, 0.00, '2026-04-23 20:25:48'),
(77, 231, 125, 1.00, 0.00, '2026-04-23 21:48:17'),
(78, 232, 126, 3.00, 0.00, '2026-04-23 21:56:41'),
(79, 233, 127, 3.00, 0.00, '2026-04-23 22:02:15'),
(80, 234, 128, 3.00, 0.00, '2026-04-23 22:02:15'),
(81, 235, 129, 3.00, 0.00, '2026-04-23 22:08:27'),
(82, 236, 130, 1.00, 0.00, '2026-04-23 22:16:37'),
(83, 237, 131, 1.00, 50.00, '2026-04-23 22:25:50');

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
(53, 115, 108, 86, 2.00, 10.00, 100.00, '2026-03-21 18:01:59'),
(54, 120, 109, 87, 68.00, 5.50, 20.00, '2026-03-21 22:16:33'),
(55, 145, 109, 87, 100.00, 10.00, 20.00, '2026-03-21 22:16:33'),
(56, 176, 109, 87, 10.00, 2.00, 20.00, '2026-03-21 22:16:33'),
(57, 176, 121, 100, 1.00, 2.00, 20.00, '2026-03-22 03:47:40'),
(58, 176, 122, 101, 1.00, 2.00, 20.00, '2026-03-23 15:03:52'),
(59, 176, 123, 102, 1.00, 2.00, 20.00, '2026-03-24 01:53:02'),
(60, 176, 125, 104, 1.00, 2.00, 20.00, '2026-03-25 19:37:11'),
(61, 81, 124, 103, 4.00, 4.00, 216.00, '2026-03-26 15:57:39'),
(62, 150, 124, 103, 16.00, 100.00, 216.00, '2026-03-26 15:57:39'),
(63, 147, 126, 105, 1000.00, 1.00, 3.00, '2026-03-26 16:14:17'),
(64, 176, 127, 106, 1.00, 2.00, 20.00, '2026-03-26 16:15:26'),
(65, 176, 128, 107, 1.00, 2.00, 20.00, '2026-03-26 16:18:20'),
(66, 176, 129, 108, 1.00, 2.00, 20.00, '2026-03-26 16:23:11'),
(67, 176, 130, 109, 1.00, 2.00, 20.00, '2026-03-26 16:24:06'),
(68, 176, 131, 110, 1.00, 2.00, 20.00, '2026-03-26 17:07:20'),
(69, 176, 132, 111, 1.00, 2.00, 20.00, '2026-03-26 18:43:42'),
(71, 106, 135, 116, 1.00, 10.00, 0.00, '2026-03-26 19:34:16'),
(72, 122, 137, 118, 1.00, 5.50, 200.00, '2026-03-26 19:43:26'),
(73, 94, 138, 119, 1.00, 1200.00, 3.00, '2026-03-26 19:43:47'),
(74, 176, 150, 132, 1.00, 2.00, 20.00, '2026-03-26 23:27:31'),
(75, 176, 151, 133, 1.00, 2.00, 20.00, '2026-03-26 23:55:47'),
(76, 176, 152, 134, 1.00, 2.00, 20.00, '2026-03-27 15:22:00'),
(77, 176, 153, 135, 1.00, 2.00, 20.00, '2026-03-27 15:28:20'),
(78, 176, 154, 136, 1.00, 2.00, 20.00, '2026-03-27 15:30:28'),
(79, 176, 155, 137, 1.00, 2.00, 20.00, '2026-03-27 15:33:19'),
(80, 176, 156, 138, 1.00, 2.00, 20.00, '2026-03-27 15:42:27'),
(81, 176, 157, 139, 1.00, 2.00, 20.00, '2026-03-27 15:43:53'),
(82, 176, 162, 148, 1.00, 2.00, 20.00, '2026-03-27 17:05:18'),
(83, 166, 162, 149, 1.00, 100.00, 3.00, '2026-03-27 17:05:18'),
(84, 176, 163, 150, 1.00, 2.00, 20.00, '2026-03-27 17:37:01'),
(85, 176, 170, 153, 1.00, 2.00, 20.00, '2026-03-27 22:13:22'),
(86, 166, 170, 154, 1.00, 100.00, 3.00, '2026-03-27 22:13:22'),
(87, 176, 161, 146, 1.00, 2.00, 20.00, '2026-03-28 14:54:06'),
(88, 166, 161, 147, 1.00, 100.00, 3.00, '2026-03-28 14:54:06'),
(89, 176, 160, 144, 1.00, 2.00, 20.00, '2026-03-28 15:05:37'),
(90, 166, 160, 145, 1.00, 100.00, 3.00, '2026-03-28 15:05:37'),
(91, 166, 159, 143, 1.00, 100.00, 3.00, '2026-03-28 15:16:08'),
(92, 176, 159, 142, 1.00, 2.00, 20.00, '2026-03-28 15:17:17'),
(97, 176, 171, 155, 1.00, 2.00, 20.00, '2026-03-28 18:26:34'),
(100, 176, 176, 162, 1.00, 2.00, 20.00, '2026-03-28 18:45:27'),
(101, 176, 180, 169, 1.00, 2.00, 20.00, '2026-03-28 19:00:46'),
(102, 166, 180, 170, 1.00, 100.00, 3.00, '2026-03-28 19:00:46'),
(103, 176, 183, 175, 1.00, 2.00, 20.00, '2026-03-28 19:13:42'),
(115, 176, 186, 180, 1.00, 2.00, 20.00, '2026-03-30 16:38:35'),
(116, 166, 186, 181, 1.00, 100.00, 3.00, '2026-03-30 16:38:35'),
(119, 176, 188, 184, 1.00, 2.00, 20.00, '2026-03-30 16:41:11'),
(120, 166, 188, 185, 1.00, 100.00, 3.00, '2026-03-30 16:41:11'),
(121, 176, 187, 182, 1.00, 2.00, 20.00, '2026-03-30 18:46:33'),
(122, 167, 187, 183, 1.00, 100.00, 3.00, '2026-03-30 18:46:33'),
(123, 176, 261, 270, 1.00, 2.00, 20.00, '2026-04-01 16:28:45'),
(124, 176, 262, 271, 1.00, 2.00, 20.00, '2026-04-01 22:39:48'),
(126, 176, 247, 249, 1.00, 2.00, 20.00, '2026-04-01 23:59:07'),
(127, 176, 246, 248, 9.00, 2.00, 20.00, '2026-04-01 23:59:16'),
(129, 176, 194, 191, 1.00, 2.00, 20.00, '2026-04-02 18:36:59'),
(130, 176, 193, 190, 2.00, 2.00, 20.00, '2026-04-02 18:37:08'),
(131, 176, 284, 293, 1.00, 2.00, 20.00, '2026-04-07 18:45:25'),
(132, 176, 185, 178, 1.00, 2.00, 20.00, '2026-04-07 19:00:14'),
(133, 89, 185, 179, 1.00, 85.00, 4.80, '2026-04-07 19:00:14'),
(134, 176, 184, 176, 1.00, 2.00, 20.00, '2026-04-07 19:03:02'),
(135, 167, 184, 177, 1.00, 100.00, 3.00, '2026-04-07 19:03:37'),
(136, 176, 287, 296, 1.00, 2.00, 20.00, '2026-04-07 20:18:39'),
(137, 148, 289, 298, 1.00, 1.00, 200.00, '2026-04-07 20:23:14'),
(138, 148, 292, 301, 1.00, 1.00, 200.00, '2026-04-07 20:33:55'),
(139, 148, 293, 302, 1.00, 1.00, 200.00, '2026-04-07 20:47:16'),
(140, 148, 295, 305, 1.00, 1.00, 200.00, '2026-04-07 20:48:26'),
(141, 176, 297, 307, 1.00, 2.00, 20.00, '2026-04-07 22:32:25'),
(142, 181, 298, 308, 1.00, 100.00, 100.00, '2026-04-07 22:47:19'),
(143, 176, 298, 309, 1.00, 2.00, 20.00, '2026-04-07 22:47:19'),
(146, 181, 300, 312, 1.00, 100.00, 100.00, '2026-04-07 23:03:25'),
(147, 181, 307, 318, 1.00, 100.00, 100.00, '2026-04-07 23:50:59'),
(148, 181, 308, 319, 1.00, 100.00, 100.00, '2026-04-07 23:54:52'),
(149, 181, 309, 320, 1.00, 100.00, 100.00, '2026-04-08 00:01:12'),
(150, 181, 310, 321, 1.00, 100.00, 100.00, '2026-04-08 14:56:43'),
(151, 181, 311, 322, 1.00, 100.00, 100.00, '2026-04-08 15:11:46'),
(152, 148, 294, 303, 1.00, 1.00, 200.00, '2026-04-08 15:26:51'),
(153, 116, 294, 304, 1.00, 10.00, 100.00, '2026-04-08 15:26:56'),
(154, 148, 312, 323, 1.00, 1.00, 200.00, '2026-04-08 15:27:19'),
(155, 148, 313, 324, 1.00, 1.00, 200.00, '2026-04-08 15:29:02'),
(156, 181, 314, 325, 1.00, 100.00, 100.00, '2026-04-08 18:04:58'),
(157, 176, 350, 362, 1.00, 2.00, 20.00, '2026-04-20 23:53:39'),
(158, 181, 306, 317, 1.00, 100.00, 100.00, '2026-04-21 19:21:55');

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
(81, 1, 1, 'LOTE-INI-32', 6.00, 0.00, 4.00, '2026-03-12 23:46:54', 'agotado'),
(82, 1, 2, 'LOTE-INI-33', 12.00, 11.00, 450.00, '2026-03-12 23:46:54', 'activo'),
(83, 1, 3, 'LOTE-INI-34', 10.00, 10.00, 450.00, '2026-03-12 23:46:54', 'activo'),
(84, 1, 4, 'LOTE-INI-35', 10.00, 10.00, 450.00, '2026-03-12 23:46:54', 'activo'),
(85, 2, 1, 'LOTE-INI-36', 9.00, 5.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(86, 2, 2, 'LOTE-INI-37', 11.00, 11.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(87, 2, 3, 'LOTE-INI-38', 10.00, 10.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(88, 2, 4, 'LOTE-INI-39', 10.00, 10.00, 12.50, '2026-03-12 23:46:54', 'activo'),
(89, 3, 1, 'LOTE-INI-40', 10.00, 123.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(90, 3, 2, 'LOTE-INI-41', 10.00, 10.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(91, 3, 3, 'LOTE-INI-42', 10.00, 10.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(92, 3, 4, 'LOTE-INI-43', 10.00, 10.00, 85.00, '2026-03-12 23:46:54', 'activo'),
(93, 4, 2, 'LOTE-INI-45', 7.00, 0.00, 1200.00, '2026-03-12 23:46:54', 'agotado'),
(94, 4, 3, 'LOTE-INI-46', 10.00, 9.00, 1200.00, '2026-03-12 23:46:54', 'activo'),
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
(106, 8, 1, 'LOTE-INI-69', 10.00, 9.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(107, 8, 2, 'LOTE-INI-70', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(108, 9, 1, 'LOTE-INI-71', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(109, 9, 2, 'LOTE-INI-72', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(110, 10, 1, 'LOTE-INI-91', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(111, 10, 2, 'LOTE-INI-92', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(112, 17, 1, 'LOTE-INI-112', 10.00, 4.00, 250.00, '2026-03-12 23:46:54', 'activo'),
(113, 17, 2, 'LOTE-INI-113', 10.00, 6.00, 250.00, '2026-03-12 23:46:54', 'activo'),
(114, 18, 1, 'LOTE-INI-115', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(115, 20, 1, 'LOTE-INI-139', 0.00, 0.00, 10.00, '2026-03-12 23:46:54', 'agotado'),
(116, 20, 2, 'LOTE-INI-140', 5.00, 4.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(117, 20, 3, 'LOTE-INI-141', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(118, 20, 4, 'LOTE-INI-142', 10.00, 10.00, 10.00, '2026-03-12 23:46:54', 'activo'),
(119, 19, 2, 'LOTE-INI-153', 7.00, 0.00, 10.00, '2026-03-12 23:46:54', 'agotado'),
(120, 21, 1, 'LOTE-INI-163', 233.00, 0.00, 5.50, '2026-03-12 23:46:54', 'agotado'),
(121, 21, 2, 'LOTE-INI-164', 248.00, 0.00, 5.50, '2026-03-12 23:46:54', 'agotado'),
(122, 21, 3, 'LOTE-INI-165', 246.00, 245.00, 5.50, '2026-03-12 23:46:54', 'activo'),
(123, 21, 4, 'LOTE-INI-166', 247.00, 246.00, 5.50, '2026-03-12 23:46:54', 'activo'),
(144, 4, 1, 'LOTE-1-4-1', 1000.00, 0.00, 2.00, '2026-03-13 23:35:59', 'agotado'),
(145, 21, 1, 'TR-260314-BCA9', 100.00, 0.00, 10.00, '2026-03-13 23:39:59', 'agotado'),
(146, 21, 2, 'TR-260314-A1EE', 40.00, 0.00, 25.00, '2026-03-13 23:55:29', 'agotado'),
(147, 4, 2, 'LOTE-2-4-2', 4000.00, 2007.00, 1.00, '2026-03-14 14:49:34', 'activo'),
(148, 21, 2, 'LOTE-3-21-2', 4000.00, 3279.00, 1.00, '2026-03-14 14:50:45', 'activo'),
(149, 1, 2, 'LOTE-4-1-2', 20.00, 20.00, 100.00, '2026-03-14 15:41:03', 'activo'),
(150, 1, 1, 'LOTE-5-1-1', 20.00, 3.00, 100.00, '2026-03-14 18:46:22', 'activo'),
(162, 21, 2, 'L-TR-64-183813', 100.00, 100.00, 5.50, '2026-03-17 17:38:13', 'activo'),
(163, 19, 2, 'LOTE-17-19-2', 150.00, 0.00, 1.33, '2026-03-17 18:58:26', 'agotado'),
(164, 19, 2, 'LOTE-18-19-2', 150.00, 50.00, 13.33, '2026-03-17 19:09:32', 'activo'),
(165, 22, 2, 'LOTE-19-22-2', 3.00, 3.00, 6.67, '2026-03-17 20:19:07', 'activo'),
(166, 4, 1, 'LOTE-20-4-1', 10.00, 0.00, 100.00, '2026-03-18 20:14:44', 'agotado'),
(167, 4, 1, 'LOTE-21-4-1', 10.00, 1008.00, 100.00, '2026-03-18 22:18:21', 'activo'),
(168, 19, 2, 'LOTE-22-19-2', 150.00, 150.00, 6.67, '2026-03-18 22:24:27', 'activo'),
(169, 4, 1, 'LOTE-23-4-1', 1.00, 1.00, 10.00, '2026-03-18 22:56:02', 'activo'),
(170, 4, 1, 'LOTE-25-4-1', 1.00, 1.00, 1.00, '2026-03-18 22:56:55', 'activo'),
(171, 4, 1, 'LOTE-26-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:01:13', 'activo'),
(172, 4, 1, 'LOTE-32-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:11:01', 'activo'),
(173, 4, 1, 'LOTE-33-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:11:39', 'activo'),
(174, 4, 1, 'LOTE-34-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:15:14', 'activo'),
(175, 4, 1, 'LOTE-35-4-1', 1.00, 1.00, 1.00, '2026-03-18 23:17:01', 'activo'),
(176, 21, 1, 'LOTE-36-21-1', 1000.00, 935.00, 2.00, '2026-03-19 01:43:10', 'activo'),
(177, 21, 1, 'LOTE-37-21-1', 1.00, 1.00, 100.00, '2026-03-21 02:47:10', 'activo'),
(178, 4, 2, 'L-TR-130-163810', 2.00, 2.00, 100.00, '2026-03-21 15:38:10', 'activo'),
(179, 3, 1, 'TR-260326-BCE4', 50.00, 50.00, 0.08, '2026-03-25 23:40:23', 'activo'),
(180, 21, 2, 'L-TR-139-175509', 1.00, 1.00, 2.00, '2026-04-06 15:55:09', 'activo'),
(181, 23, 1, 'L-AL-M1-004129', 1000.00, 889.00, 100.00, '2026-04-07 22:41:29', 'activo'),
(182, 24, 2, 'L-ARENA-173209', 1000.00, 1000.00, 1200.00, '2026-04-08 15:32:09', 'activo'),
(183, 23, 2, 'L-TR-460-205017', 1.00, 1.00, 100.00, '2026-04-09 18:50:17', 'activo'),
(184, 19, 2, 'LOTE-38-19-2', 150.00, 150.00, 10.00, '2026-04-15 05:59:57', 'activo'),
(185, 1, 1, 'LOTE-39-1-1', 20.00, 20.00, 75.00, '2026-04-15 06:19:09', 'activo'),
(186, 1, 1, 'LOTE-40-1-1', 20.00, 20.00, 100.00, '2026-04-15 17:54:40', 'activo'),
(187, 21, 1, 'LOTE-41-21-1', 1.00, 1.00, 2.00, '2026-04-16 20:58:09', 'activo'),
(188, 1, 1, 'LOTE-42-1-1', 20.00, 20.00, 50.00, '2026-04-16 21:58:23', 'activo'),
(189, 1, 1, 'LOTE-43-1-1', 20.00, 20.00, 50.00, '2026-04-16 22:04:06', 'activo'),
(190, 21, 1, 'LOTE-44-21-1', 1000.00, 1000.00, 0.10, '2026-04-16 22:05:07', 'activo'),
(191, 23, 2, 'L-TR-488-213901', 100.00, 100.00, 100.00, '2026-04-18 19:39:01', 'activo'),
(199, 8, 1, 'LOTE-58-8-1', 2.00, 2.00, 100.00, '2026-04-22 17:28:09', 'activo'),
(200, 1, 1, 'LOTE-59-1-1', 2.00, 2.00, 50.00, '2026-04-22 17:29:45', 'activo'),
(201, 8, 1, 'LOTE-60-8-1', 2.00, 2.00, 50.00, '2026-04-22 17:31:18', 'activo'),
(202, 8, 1, 'LOTE-61-8-1', 2.00, 2.00, 100.00, '2026-04-22 17:37:13', 'activo'),
(203, 8, 1, 'LOTE-62-8-1', 2.00, 2.00, 50.00, '2026-04-22 17:39:00', 'activo'),
(204, 8, 1, 'LOTE-63-8-1', 2.00, 2.00, 50.00, '2026-04-22 17:40:22', 'activo'),
(205, 8, 1, 'LOTE-64-8-1', 2.00, 2.00, 50.00, '2026-04-22 17:42:59', 'activo'),
(206, 7, 1, 'LOTE-65-7-1', 4.00, 4.00, 25.00, '2026-04-22 18:43:03', 'activo'),
(207, 7, 1, 'LOTE-66-7-1', 2.00, 2.00, 50.00, '2026-04-22 18:44:30', 'activo'),
(208, 7, 1, 'LOTE-67-7-1', 2.00, 2.00, 50.00, '2026-04-22 18:51:24', 'activo'),
(209, 7, 1, 'LOTE-68-7-1', 2.00, 2.00, 50.00, '2026-04-22 19:03:08', 'activo'),
(211, 7, 1, 'LOTE-72-7-1', 2.00, 2.00, 50.00, '2026-04-22 19:15:09', 'activo'),
(212, 7, 1, 'LOTE-73-7-1', 2.00, 2.00, 50.00, '2026-04-22 19:21:52', 'activo'),
(213, 7, 1, 'LOTE-74-7-1', 2.00, 2.00, 50.00, '2026-04-22 19:27:42', 'activo'),
(214, 7, 1, 'LOTE-75-7-1', 2.00, 2.00, 50.00, '2026-04-22 19:31:22', 'activo'),
(215, 1, 1, 'LOTE-76-1-1', 4.00, 4.00, 25.00, '2026-04-23 16:18:52', 'activo'),
(216, 1, 1, 'LOTE-78-1-1', 1.00, 1.00, 50.00, '2026-04-23 16:20:22', 'activo'),
(217, 6, 1, 'LOTE-79-6-1', 1.00, 1.00, 100.00, '2026-04-23 17:03:45', 'activo'),
(218, 1, 1, 'LOTE-80-1-1', 1.00, 1.00, 100.00, '2026-04-23 17:09:23', 'activo'),
(219, 5, 1, 'LOTE-81-5-1', 1.00, 1.00, 10.00, '2026-04-23 17:33:49', 'activo'),
(220, 1, 1, 'LOTE-82-1-1', 3.00, 3.00, 0.00, '2026-04-23 19:14:21', 'activo'),
(221, 6, 1, 'LOTE-82-6-1', 2.00, 2.00, 0.00, '2026-04-23 19:14:21', 'activo'),
(222, 5, 1, 'LOTE-83-5-1', 1.00, 1.00, 0.00, '2026-04-23 19:16:35', 'activo'),
(223, 4, 1, 'LOTE-84-4-1', 2.00, 2.00, 0.00, '2026-04-23 19:18:56', 'activo'),
(224, 1, 1, 'LOTE-85-1-1', 4.00, 4.00, 0.00, '2026-04-23 20:02:46', 'activo'),
(225, 4, 1, 'LOTE-85-4-1', 4.00, 4.00, 0.00, '2026-04-23 20:02:46', 'activo'),
(226, 1, 1, 'LOTE-86-1-1', 2.00, 2.00, 0.00, '2026-04-23 20:06:07', 'activo'),
(227, 1, 1, 'LOTE-87-1-1', 2.00, 2.00, 0.00, '2026-04-23 20:15:16', 'activo'),
(228, 1, 1, 'LOTE-88-1-1', 4.00, 4.00, 0.00, '2026-04-23 20:17:43', 'activo'),
(229, 8, 1, 'LOTE-88-8-1', 3.00, 3.00, 0.00, '2026-04-23 20:17:43', 'activo'),
(230, 1, 1, 'LOTE-89-1-1', 3.00, 3.00, 0.00, '2026-04-23 20:25:48', 'activo'),
(231, 1, 1, 'LOTE-94-1-1', 1.00, 1.00, 0.00, '2026-04-23 21:48:17', 'activo'),
(232, 1, 1, 'LOTE-96-1-1', 3.00, 3.00, 0.00, '2026-04-23 21:56:41', 'activo'),
(233, 1, 1, 'LOTE-97-1-1', 3.00, 3.00, 0.00, '2026-04-23 22:02:15', 'activo'),
(234, 2, 1, 'LOTE-97-2-1', 3.00, 3.00, 0.00, '2026-04-23 22:02:15', 'activo'),
(235, 2, 1, 'LOTE-98-2-1', 3.00, 3.00, 0.00, '2026-04-23 22:08:27', 'activo'),
(236, 1, 1, 'LOTE-99-1-1', 1.00, 1.00, 0.00, '2026-04-23 22:16:37', 'activo'),
(237, 1, 1, 'LOTE-100-1-1', 1.00, 1.00, 50.00, '2026-04-23 22:25:50', 'activo');

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
(8, 63, 2, 1, 82, 1.00, 'daño', 'Administrador General', 'un carro roso el cargamento y se rompio un bulto', '2026-03-14 01:14:20'),
(9, 148, 1, 21, 176, 1.00, 'daño', 'Administrador General', 'se hizo mal', '2026-03-26 17:56:38'),
(10, 150, 1, 2, 85, 1.00, 'daño', 'Administrador General', 'prueba en presentacion', '2026-03-26 18:58:04');

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
(13, 'Entregas', 'entregas', 'bi-truck', 1, 1),
(14, 'Clientes Estatus', 'clientesEstatus', 'bi-person-badge', 1, 1),
(15, 'configuracion de acceso', 'Configuracion', 'bi bi-gear-fill', 1, 1),
(16, 'Transmutaciones', 'transmutaciones', 'bi-arrow-repeat', 1, 1),
(17, 'solicitudes de Compra', 'solicitudesCompra', 'bi-cart-check-fill', 1, 1),
(18, 'Trabajadores', 'trabajadores', 'bi-people-fill', 1, 1),
(19, 'Vehiculos', 'vehiculos', 'bi-truck-front-fill', 1, 1),
(20, 'Repartos', 'repartos', 'bi-truck-flatbed', 1, 1),
(22, 'Historial de ventas', 'ventashistorial', 'bi-receipt', 1, 1),
(23, 'Pedidos Vendedor', 'pedidosVendedor', 'bi-person-badge-fill', 1, 1),
(24, 'Caja Rapida', 'cajaRapida', '', 1, 1),
(25, 'Mis Repartos', 'misRepartos', 'bi-map-fill', 1, 1),
(26, 'tesoreria', 'tesoreria', '', 0, 1),
(27, 'finanzas admin', 'finanzas_admin', '', 0, 1),
(28, 'Prestamos', 'prestamos', '', 0, 1);

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
(133, 7, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 29, 'Salida por venta folio: V-260321113518. Entregado real: 1 de 1', '2026-03-21 17:35:18'),
(134, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 30, 'Salida por venta folio: V-260321214447. Entregado real: 1 de 1', '2026-03-22 03:44:47'),
(135, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 31, 'Salida por venta folio: V-260323090339. Entregado real: 1 de 1', '2026-03-23 15:03:39'),
(136, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 32, 'Salida por venta folio: V-260323195221. Entregado real: 1 de 1', '2026-03-24 01:52:21'),
(137, 1, 'salida', 20.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 33, 'Salida por venta folio: V-260324173739. Entregado real: 20 de 20', '2026-03-24 23:37:39'),
(138, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 34, 'Salida por venta folio: V-34. Entregado real: 1 de 1', '2026-03-25 18:00:40'),
(139, 21, 'traspaso', 1.00, 1, 2, 1, 1, 1, 1, NULL, NULL, '', '2026-04-06 15:55:09'),
(140, 1, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Salida Transmutación #23', '2026-03-25 23:40:23'),
(141, 3, 'entrada', 50.00, NULL, 1, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'Entrada Transmutación #23', '2026-03-25 23:40:23'),
(142, 4, 'salida', 1000.00, 2, NULL, 1, NULL, NULL, NULL, NULL, 35, 'Salida por venta folio: V-35. Entregado real: 1000 de 1000', '2026-03-26 16:13:57'),
(143, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 36, 'Salida por venta folio: V-36. Entregado real: 1 de 1', '2026-03-26 16:15:14'),
(144, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 37, 'Salida por venta folio: V-37. Entregado real: 1 de 1', '2026-03-26 16:18:11'),
(145, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 38, 'Salida por venta folio: V-38. Entregado real: 1 de 1', '2026-03-26 16:23:02'),
(146, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 39, 'Salida por venta folio: V-39. Entregado real: 1 de 1', '2026-03-26 16:23:55'),
(147, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 40, 'Salida por venta folio: V-40. Entregado real: 1 de 1', '2026-03-26 17:06:59'),
(148, 21, 'ajuste', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'se hizo mal', '2026-03-26 17:56:38'),
(149, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 41, 'Salida por venta folio: V-41. Entregado real: 1 de 1', '2026-03-26 18:43:04'),
(150, 2, 'ajuste', 1.00, 1, NULL, 1, NULL, NULL, NULL, 'Administrador General', NULL, 'prueba en presentacion', '2026-03-26 18:58:04'),
(151, 1, 'salida', 20.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 42, 'Salida por venta folio: V-42. Entregado real: 20 de 20', '2026-03-26 19:24:43'),
(152, 21, 'salida', 17.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 42, 'Salida por venta folio: V-42. Entregado real: 17 de 17', '2026-03-26 19:24:43'),
(153, 17, 'salida', 4.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 42, 'Salida por venta folio: V-42. Entregado real: 4 de 4', '2026-03-26 19:24:43'),
(154, 2, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 43, 'Salida por venta folio: V-43. Entregado real: 1 de 1', '2026-03-26 19:31:59'),
(155, 8, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 44, 'Salida por venta folio: V-44. Entregado real: 1 de 1', '2026-03-26 19:33:54'),
(156, 2, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 45, 'Salida por entrega parcial. Folio Venta: V-45', '2026-03-26 19:37:59'),
(157, 21, 'salida', 1.00, 3, NULL, 1, NULL, NULL, NULL, NULL, 46, 'Salida por venta folio: V-46. Entregado real: 1 de 1', '2026-03-26 19:43:09'),
(158, 4, 'salida', 1.00, 3, NULL, 1, NULL, NULL, NULL, NULL, 47, 'Salida por venta folio: V-47. Entregado real: 1 de 1', '2026-03-26 19:43:38'),
(159, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 48, 'Salida por venta rápida folio: V-48. Entregado: 1 de 1', '2026-03-26 22:24:31'),
(160, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 48, 'REINGRESO POR CANCELACIÓN - Folio: V-48. Motivo: mal pago', '2026-03-26 22:25:32'),
(161, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 49, 'Salida por venta folio: V-49. Entregado real: 1 de 1', '2026-03-26 22:27:37'),
(162, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 50, 'Salida por venta rápida folio: V-50. Entregado: 1 de 1', '2026-03-26 22:42:27'),
(163, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 51, 'Salida por venta rápida folio: V-51. Entregado: 1 de 1', '2026-03-26 22:44:44'),
(164, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 52, 'Salida por venta rápida folio: V-52. Entregado: 1 de 1', '2026-03-26 22:46:35'),
(165, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 53, 'Salida por venta rápida folio: V-53. Entregado: 1 de 1', '2026-03-26 22:52:00'),
(166, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 54, 'Salida por venta rápida folio: V-54. Entregado: 1 de 1', '2026-03-26 23:01:11'),
(167, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 55, 'Salida por venta folio: V-55. Entregado real: 1 de 1', '2026-03-26 23:17:13'),
(168, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 55, 'Salida por venta folio: V-55. Entregado real: 1 de 1', '2026-03-26 23:17:13'),
(169, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 56, 'Salida por venta rápida folio: V-56. Entregado: 1 de 1', '2026-03-26 23:23:17'),
(170, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 57, 'Salida por venta rápida folio: V-57. Entregado: 1 de 1', '2026-03-26 23:23:58'),
(171, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 58, 'Salida por venta rápida folio: V-58. Entregado: 1 de 1', '2026-03-26 23:25:55'),
(172, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 59, 'Salida por venta rápida folio: V-59. Entregado: 1 de 1', '2026-03-26 23:27:31'),
(173, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 60, 'Salida por venta rápida folio: V-60. Entregado: 1 de 1', '2026-03-26 23:55:47'),
(174, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 61, 'Salida por venta rápida folio: V-61. Entregado: 1 de 1', '2026-03-27 15:22:00'),
(175, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 62, 'Salida por venta rápida folio: V-62. Entregado: 1 de 1', '2026-03-27 15:28:20'),
(176, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 63, 'Salida por venta rápida folio: V-63. Entregado: 1 de 1', '2026-03-27 15:30:28'),
(177, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 64, 'Salida por venta rápida folio: V-64. Entregado: 1 de 1', '2026-03-27 15:33:19'),
(178, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 65, 'Salida por venta rápida folio: V-65. Entregado: 1 de 1', '2026-03-27 15:42:27'),
(179, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 66, 'Salida por venta rápida folio: V-66. Entregado: 1 de 1', '2026-03-27 15:43:53'),
(180, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 67, 'Salida por venta rápida folio: V-67. Entregado: 1 de 1', '2026-03-27 16:55:57'),
(181, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 67, 'Salida por venta rápida folio: V-67. Entregado: 1 de 1', '2026-03-27 16:55:57'),
(182, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 68, 'Salida por venta rápida folio: V-68. Entregado: 1 de 1', '2026-03-27 16:58:08'),
(183, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 68, 'Salida por venta rápida folio: V-68. Entregado: 1 de 1', '2026-03-27 16:58:08'),
(184, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 69, 'Salida por venta rápida folio: V-69. Entregado: 1 de 1', '2026-03-27 17:00:46'),
(185, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 69, 'Salida por venta rápida folio: V-69. Entregado: 1 de 1', '2026-03-27 17:00:46'),
(186, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 70, 'Salida por venta rápida folio: V-70. Entregado: 1 de 1', '2026-03-27 17:02:27'),
(187, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 70, 'Salida por venta rápida folio: V-70. Entregado: 1 de 1', '2026-03-27 17:02:27'),
(188, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 71, 'Salida por venta rápida folio: V-71. Entregado: 1 de 1', '2026-03-27 17:05:18'),
(189, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 71, 'Salida por venta rápida folio: V-71. Entregado: 1 de 1', '2026-03-27 17:05:18'),
(190, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 72, 'Salida por venta rápida folio: V-72. Entregado: 1 de 1', '2026-03-27 17:37:01'),
(191, 19, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 73, 'Salida por entrega parcial. Folio Venta: V-73', '2026-03-27 18:23:15'),
(192, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 20, 'Salida por entrega parcial. Folio Venta: V-260314151546', '2026-03-27 18:48:41'),
(193, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 75, 'Salida por entrega parcial. Folio Venta: V-75', '2026-03-27 20:07:20'),
(194, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 75, 'Salida por entrega parcial. Folio Venta: V-75', '2026-03-27 20:07:20'),
(195, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 76, 'Salida por venta folio: V-76. Entregado real: 1 de 1', '2026-03-28 18:03:10'),
(196, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 77, 'Salida por venta folio: V-77. Entregado real: 1 de 1', '2026-03-28 18:03:47'),
(197, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 77, 'Salida por venta folio: V-77. Entregado real: 1 de 1', '2026-03-28 18:03:47'),
(198, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 78, 'Salida por venta folio: V-78. Entregado real: 1 de 1', '2026-03-28 18:26:46'),
(199, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 78, 'REINGRESO POR CANCELACIÓN - Folio: V-78. Motivo: ya no quiso el material', '2026-03-28 18:28:51'),
(200, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 79, 'Salida por venta folio: V-79. Entregado real: 1 de 1', '2026-03-28 18:29:15'),
(201, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 79, 'REINGRESO POR CANCELACIÓN - Folio: V-79. Motivo: ya no quiso el material', '2026-03-28 18:29:52'),
(202, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 77, 'REINGRESO POR CANCELACIÓN - Folio: V-77. Motivo: ya no se ocupa', '2026-03-28 18:43:53'),
(203, 4, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 77, 'REINGRESO POR CANCELACIÓN - Folio: V-77. Motivo: ya no se ocupa', '2026-03-28 18:43:54'),
(204, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 80, 'Salida por venta folio: V-80. Entregado real: 1 de 1', '2026-03-28 18:44:15'),
(205, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 80, 'Salida por venta folio: V-80. Entregado real: 1 de 1', '2026-03-28 18:44:15'),
(206, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 81, 'Salida por venta folio: V-81. Entregado real: 1 de 1', '2026-03-28 18:45:12'),
(207, 3, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 82, 'Salida por venta folio: V-82. Entregado real: 1 de 1', '2026-03-28 18:46:08'),
(208, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 82, 'Salida por venta folio: V-82. Entregado real: 1 de 1', '2026-03-28 18:46:08'),
(209, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 83, 'Salida por venta folio: V-83. Entregado real: 1 de 1', '2026-03-28 18:47:58'),
(210, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 83, 'Salida por venta folio: V-83. Entregado real: 1 de 1', '2026-03-28 18:47:58'),
(211, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 84, 'Salida por venta folio: V-84. Entregado real: 1 de 1', '2026-03-28 18:59:49'),
(212, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 84, 'Salida por venta folio: V-84. Entregado real: 1 de 1', '2026-03-28 18:59:49'),
(213, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 85, 'Salida por venta rápida folio: V-85. Entregado: 1 de 1', '2026-03-28 19:00:46'),
(214, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 85, 'Salida por venta rápida folio: V-85. Entregado: 1 de 1', '2026-03-28 19:00:46'),
(215, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 86, 'Salida por venta folio: V-86. Entregado real: 1 de 1', '2026-03-28 19:08:03'),
(216, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 86, 'Salida por venta folio: V-86. Entregado real: 1 de 1', '2026-03-28 19:08:03'),
(217, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 87, 'Salida por venta folio: V-87. Entregado real: 1 de 1', '2026-03-28 19:12:47'),
(218, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 87, 'Salida por venta folio: V-87. Entregado real: 1 de 1', '2026-03-28 19:12:47'),
(219, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 88, 'Salida por venta folio: V-88. Entregado real: 1 de 1', '2026-03-28 19:13:30'),
(220, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 89, 'Salida por venta folio: V-89. Entregado real: 1 de 1', '2026-03-28 19:28:54'),
(221, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 89, 'Salida por venta folio: V-89. Entregado real: 1 de 1', '2026-03-28 19:28:54'),
(222, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 90, 'Salida por venta folio: V-90. Entregado real: 1 de 1', '2026-03-28 19:41:55'),
(223, 3, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 90, 'Salida por venta folio: V-90. Entregado real: 1 de 1', '2026-03-28 19:41:55'),
(224, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 91, 'Salida por venta folio: V-91. Entregado real: 1 de 1', '2026-03-30 14:30:09'),
(225, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 91, 'Salida por venta folio: V-91. Entregado real: 1 de 1', '2026-03-30 14:30:09'),
(226, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 92, 'Salida por venta folio: V-92. Entregado real: 1 de 1', '2026-03-30 14:31:20'),
(227, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 92, 'Salida por venta folio: V-92. Entregado real: 1 de 1', '2026-03-30 14:31:20'),
(228, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 93, 'Salida por venta folio: V-93. Entregado real: 1 de 1', '2026-03-30 14:50:58'),
(229, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 93, 'Salida por venta folio: V-93. Entregado real: 1 de 1', '2026-03-30 14:50:58'),
(230, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 94, 'Salida por venta folio: V-94. Entregado real: 1 de 1', '2026-03-30 20:30:36'),
(231, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 94, 'REINGRESO POR CANCELACIÓN - Folio: V-94. Motivo: se cancela la venta', '2026-03-30 20:32:56'),
(232, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 95, 'Salida Venta: V-95. Entregado: 1 / 1', '2026-03-30 20:35:46'),
(233, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 96, 'Salida Venta: V-96. Entregado: 1 / 1', '2026-03-30 21:23:46'),
(234, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 97, 'Salida Venta: V-97. Entregado: 1 / 1', '2026-03-30 21:25:23'),
(235, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 98, 'Salida Venta: V-98. Entregado: 2 / 2', '2026-03-30 21:29:05'),
(236, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 99, 'Salida Venta: V-99. Entregado: 1 / 1', '2026-03-30 21:31:48'),
(237, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 102, 'Salida Venta: V-100. Entregado: 1 / 1', '2026-03-30 21:37:58'),
(238, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 103, 'Salida Venta: V-103. Entregado: 1 / 1', '2026-03-30 23:20:48'),
(239, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 104, 'Salida Venta: V-104. Entregado: 1 / 1', '2026-03-30 23:36:53'),
(240, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 105, 'Salida Venta: V-105. Entregado: 1 / 1', '2026-03-30 23:37:26'),
(241, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 106, 'Salida Venta: V-106. Entregado: 1 / 1', '2026-03-30 23:38:06'),
(242, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 107, 'Salida Venta: V-107. Entregado: 1 / 1', '2026-03-30 23:45:09'),
(243, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 108, 'Salida Venta: V-108. Entregado: 1 / 1', '2026-03-30 23:51:21'),
(244, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 109, 'Salida Venta: V-109. Entregado: 1 / 1', '2026-03-30 23:57:26'),
(245, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 110, 'Salida Venta: V-110. Entregado: 1 / 1', '2026-03-31 00:00:36'),
(246, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 111, 'Salida Venta: V-111. Entregado: 1 / 1', '2026-03-31 14:23:57'),
(247, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 112, 'Salida Venta: V-112. Entregado: 1 / 1', '2026-03-31 14:27:23'),
(248, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 113, 'Salida Venta: V-113. Entregado: 1 / 1', '2026-03-31 14:29:40'),
(249, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 114, 'Salida Venta: V-114. Entregado: 1 / 1', '2026-03-31 14:46:19'),
(250, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 115, 'Salida Venta: V-115. Entregado: 1 / 1', '2026-03-31 15:04:16'),
(251, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 116, 'Salida Venta: V-116. Entregado: 1 / 1', '2026-03-31 15:06:46'),
(252, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 116, 'REINGRESO POR CANCELACIÓN - Folio: V-116. Motivo: ya no quiso el amterial', '2026-03-31 15:08:41'),
(253, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 117, 'Salida Venta: V-117. Entregado: 1 / 1', '2026-03-31 15:16:23'),
(254, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 117, 'REINGRESO POR CANCELACIÓN - Folio: V-117. Motivo: ya no quiso el producto', '2026-03-31 15:16:39'),
(255, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 118, 'Salida Venta: V-118. Entregado: 1 / 1', '2026-03-31 15:27:12'),
(256, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 118, 'REINGRESO POR CANCELACIÓN - Folio: V-118. Motivo: ya no quiso el material', '2026-03-31 15:27:25'),
(257, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 119, 'Salida Venta: V-119. Entregado: 1 / 1', '2026-03-31 15:55:39'),
(258, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 119, 'REINGRESO POR CANCELACIÓN - Folio: V-119. Motivo: ya no quiso el material', '2026-03-31 15:55:51'),
(259, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 120, 'Salida Venta: V-120. Entregado: 1 / 1', '2026-03-31 16:02:10'),
(260, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 120, 'REINGRESO POR CANCELACIÓN - Folio: V-120. Motivo: ya no quiso el material', '2026-03-31 16:02:20'),
(261, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 121, 'Salida Venta: V-121. Entregado: 1 / 1', '2026-03-31 16:22:03'),
(262, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 121, 'REINGRESO POR CANCELACIÓN - Folio: V-121. Motivo: ya no quiso el material', '2026-03-31 16:24:36'),
(263, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 122, 'Salida Venta: V-122. Entregado: 1 / 1', '2026-03-31 16:28:06'),
(264, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 122, 'REINGRESO POR CANCELACIÓN - Folio: V-122. Motivo: ya no quiso el material', '2026-03-31 16:28:24'),
(265, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 123, 'Salida Venta: V-123. Entregado: 1 / 1', '2026-03-31 16:28:45'),
(266, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 123, 'REINGRESO POR CANCELACIÓN - Folio: V-123. Motivo: ', '2026-03-31 16:28:57'),
(267, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 124, 'Salida Venta: V-124. Entregado: 2 / 2', '2026-03-31 16:52:32'),
(268, 4, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 124, 'Salida Venta: V-124. Entregado: 1 / 1', '2026-03-31 16:52:32'),
(269, 21, 'entrada', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 124, 'REINGRESO POR CANCELACIÓN - Folio: V-124. Motivo: ', '2026-03-31 16:54:13'),
(270, 4, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 124, 'REINGRESO POR CANCELACIÓN - Folio: V-124. Motivo: ', '2026-03-31 16:54:13'),
(271, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 126, 'Salida Venta: V-126. Entregado: 2 / 2', '2026-03-31 16:55:53'),
(272, 21, 'entrada', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 126, 'REINGRESO POR CANCELACIÓN - Folio: V-126. Motivo: insuficuencia de stock', '2026-03-31 16:56:13'),
(273, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 127, 'Salida Venta: V-127. Entregado: 2 / 2', '2026-03-31 16:56:48'),
(274, 21, 'entrada', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 127, 'REINGRESO POR CANCELACIÓN - Folio: V-127. Motivo: ya no quiso el material', '2026-03-31 16:57:03'),
(275, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 128, 'Salida Venta: V-128. Entregado: 2 / 2', '2026-03-31 16:59:11'),
(276, 21, 'entrada', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 128, 'REINGRESO POR CANCELACIÓN - Folio: V-128. Motivo: ya no quiso el material', '2026-03-31 16:59:27'),
(277, 21, 'salida', 3.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 129, 'Salida Venta: V-129. Entregado: 3 / 3', '2026-03-31 17:10:41'),
(278, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 131, 'Salida Venta: V-131. Entregado: 1 / 1', '2026-03-31 18:45:59'),
(279, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 132, 'Salida Venta: V-132. Entregado: 1 / 1', '2026-03-31 18:54:12'),
(280, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 133, 'Salida Venta: V-133. Entregado: 1 / 1', '2026-03-31 18:54:33'),
(281, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 140, 'Salida Venta: V-134. Entregado: 1 / 1', '2026-03-31 19:28:17'),
(282, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 141, 'Salida Venta: V-141. Entregado: 1 / 1', '2026-03-31 19:30:33'),
(283, 21, 'salida', 3.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 142, 'Salida Venta: V-142. Entregado: 3 / 3', '2026-03-31 19:30:57'),
(284, 21, 'entrada', 3.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 142, 'REINGRESO POR CANCELACIÓN - Folio: V-142. Motivo: ya no quiso el material', '2026-03-31 19:31:44'),
(285, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 143, 'Salida Venta: V-143. Entregado: 1 / 1', '2026-03-31 20:28:41'),
(286, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 144, 'Salida Venta: V-144. Entregado: 1 / 1', '2026-03-31 21:26:53'),
(287, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 145, 'Salida Venta: V-145. Entregado: 1 / 1', '2026-03-31 21:29:29'),
(288, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 146, 'Salida Venta: V-146. Entregado: 1 / 1', '2026-03-31 21:34:29'),
(289, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 147, 'Salida Venta: V-147. Entregado: 1 / 1', '2026-03-31 21:35:40'),
(290, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 147, 'REINGRESO POR CANCELACIÓN - Folio: V-147. Motivo: ya no quiso el material', '2026-03-31 21:41:08'),
(291, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 146, 'REINGRESO POR CANCELACIÓN - Folio: V-146. Motivo: ya no quiso el material', '2026-03-31 21:41:16'),
(292, 21, 'salida', 3.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 148, 'Salida Venta: V-148. Entregado: 3 / 3', '2026-03-31 21:44:55'),
(293, 21, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 149, 'Salida Venta: V-149. Entregado: 2 / 2', '2026-03-31 21:46:09'),
(294, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 150, 'Salida Venta: V-150. Entregado: 1 / 1', '2026-03-31 21:50:37'),
(295, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 151, 'Salida Venta: V-151. Entregado: 1 / 1', '2026-03-31 21:55:55'),
(296, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 152, 'Salida Venta: V-152. Entregado: 1 / 1', '2026-03-31 22:01:25'),
(297, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 152, 'REINGRESO POR CANCELACIÓN - Folio: V-152. Motivo: ya no quiso el material', '2026-03-31 22:01:36'),
(298, 21, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 153, 'Salida Venta: V-153. Entregado: 2 / 2', '2026-03-31 22:02:17'),
(299, 21, 'entrada', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 153, 'REINGRESO POR CANCELACIÓN - Folio: V-153. Motivo: ya no quiso el material', '2026-03-31 22:02:27'),
(300, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 154, 'Salida Venta: V-154. Entregado: 1 / 1', '2026-03-31 22:04:15'),
(301, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 155, 'Salida Venta: V-155. Entregado: 1 / 1', '2026-03-31 22:05:35'),
(302, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 156, 'Salida Venta: V-156. Entregado: 1 / 1', '2026-03-31 22:06:13'),
(303, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 157, 'Salida Venta: V-157. Entregado: 1 / 1', '2026-03-31 22:17:04'),
(304, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 158, 'Salida Venta: V-158. Entregado: 1 / 1', '2026-03-31 22:19:03'),
(305, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 159, 'Salida Venta: V-159. Entregado: 1 / 1', '2026-03-31 22:21:15'),
(306, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 159, 'REINGRESO POR CANCELACIÓN - Folio: V-159. Motivo: ya no quiso el material', '2026-03-31 22:36:56'),
(307, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 149, 'AJUSTE EDICIÓN: Devolución de 1 unidades (Venta 149)', '2026-03-31 22:51:43'),
(308, 21, 'entrada', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 148, 'AJUSTE EDICIÓN: Devolución de 2 unidades (Venta 148)', '2026-03-31 22:53:03'),
(309, 21, 'salida', 5.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 161, 'Salida Venta: V-161. Entregado: 5 / 5', '2026-03-31 22:53:37'),
(310, 21, 'entrada', 4.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 161, 'AJUSTE EDICIÓN: Devolución de 4 unidades (Venta 161)', '2026-03-31 22:53:53'),
(311, 21, 'salida', 9.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 162, 'Salida Venta: V-162. Entregado: 9 / 9', '2026-03-31 22:54:26'),
(312, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 162, 'AJUSTE EDICIÓN: Devolución de 1 unidades (Venta 162)', '2026-03-31 22:54:43'),
(313, 21, 'entrada', 7.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 162, 'AJUSTE EDICIÓN: Devolución de 7 unidades (Venta 162)', '2026-03-31 22:55:08'),
(314, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 163, 'Salida Venta: V-163. Entregado: 1 / 1', '2026-03-31 22:58:33'),
(315, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 163, 'Salida Venta: V-163. Entregado: 1 / 1', '2026-03-31 22:58:33'),
(316, 21, 'salida', 3.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 164, 'Salida Venta: V-164. Entregado: 3 / 3', '2026-03-31 23:01:54'),
(317, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 164, 'Salida Venta: V-164. Entregado: 1 / 1', '2026-03-31 23:01:54'),
(318, 4, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 164, 'AJUSTE EDICIÓN: Devolución de 1 unidades (Venta 164)', '2026-03-31 23:07:20'),
(319, 21, 'entrada', 3.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 164, 'REINGRESO POR CANCELACIÓN - Folio: V-164. Motivo: ya no quiso el material', '2026-03-31 23:15:34'),
(320, 4, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 163, 'AJUSTE EDICIÓN: Devolución de 1 unidades (Venta 163)', '2026-03-31 23:29:58'),
(321, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 165, 'Salida Venta: V-165. Entregado: 1 / 1', '2026-03-31 23:35:59'),
(322, 4, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 165, 'Salida Venta: V-165. Entregado: 2 / 2', '2026-03-31 23:35:59'),
(323, 4, 'entrada', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 165, 'ELIMINACIÓN POR AJUSTE A CERO - Venta ID: 165', '2026-03-31 23:36:14'),
(324, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 166, 'Salida Venta: V-166. Entregado: 1 / 1', '2026-03-31 23:36:52'),
(325, 4, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 166, 'Salida Venta: V-166. Entregado: 2 / 2', '2026-03-31 23:36:52'),
(326, 4, 'entrada', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 166, 'ELIMINACIÓN POR AJUSTE A CERO - Venta ID: 166', '2026-03-31 23:37:23'),
(327, 21, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 167, 'Salida Venta: V-167. Entregado: 2 / 2', '2026-03-31 23:38:32'),
(328, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 167, 'Salida Venta: V-167. Entregado: 1 / 1', '2026-03-31 23:38:32'),
(329, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 168, 'Salida Venta: V-168. Entregado: 1 / 1', '2026-03-31 23:39:05'),
(330, 4, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 168, 'Salida Venta: V-168. Entregado: 1 / 1', '2026-03-31 23:39:05'),
(331, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 168, 'REINGRESO POR CANCELACIÓN - Folio: V-168. Motivo: ya no quiso el material', '2026-04-01 14:40:34'),
(332, 4, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 168, 'REINGRESO POR CANCELACIÓN - Folio: V-168. Motivo: ya no quiso el material', '2026-04-01 14:40:34'),
(333, 21, 'entrada', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 167, 'REINGRESO POR CANCELACIÓN - Folio: V-167. Motivo: ya no quiso el material', '2026-04-01 14:41:46'),
(334, 4, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 167, 'REINGRESO POR CANCELACIÓN - Folio: V-167. Motivo: ya no quiso el material', '2026-04-01 14:41:46'),
(335, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 169, 'Salida Venta: V-169. Entregado: 1 / 1', '2026-04-01 14:42:28'),
(336, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 169, 'REINGRESO POR CANCELACIÓN - Folio: V-169. Motivo: ya no quiso el material', '2026-04-01 14:43:00'),
(337, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 166, 'REINGRESO POR CANCELACIÓN - Folio: V-166. Motivo: ya no quiso el material', '2026-04-01 15:00:35'),
(338, 21, 'salida', 3.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 170, 'Salida Venta: V-170. Entregado: 3 / 3', '2026-04-01 15:01:17'),
(339, 21, 'entrada', 3.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 170, 'REINGRESO POR CANCELACIÓN - Folio: V-170. Motivo: ya no quiso el material', '2026-04-01 15:01:33'),
(340, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 171, 'Salida Venta: V-171. Entregado: 1 / 1', '2026-04-01 15:20:45'),
(341, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 171, 'REINGRESO POR CANCELACIÓN - Folio: V-171. Motivo: ya no quiso el material', '2026-04-01 15:22:05'),
(342, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 172, 'Salida Venta: V-172. Entregado: 1 / 1', '2026-04-01 15:29:02'),
(343, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 172, 'REINGRESO POR CANCELACIÓN - Folio: V-172. Motivo: ya no quiso el material', '2026-04-01 15:29:45'),
(344, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 173, 'Salida Venta: V-173. Entregado: 1 / 1', '2026-04-01 15:39:09'),
(345, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 173, 'REINGRESO POR CANCELACIÓN - Folio: V-173. Motivo: ya no quiso el material', '2026-04-01 15:39:55'),
(346, 21, 'salida', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 174, 'Salida Venta: V-174. Entregado: 2 / 2', '2026-04-01 15:40:23'),
(347, 21, 'entrada', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 174, 'REINGRESO POR CANCELACIÓN - Folio: V-174. Motivo: ya no quiso el material', '2026-04-01 15:40:46'),
(348, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 165, 'REINGRESO POR CANCELACIÓN - Folio: V-165. Motivo: ya no quiso el material', '2026-04-01 15:41:11'),
(349, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 175, 'Salida Venta: V-175. Entregado: 1 / 1', '2026-04-01 15:47:26'),
(350, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 175, 'REINGRESO POR CANCELACIÓN - Folio: V-175. Motivo: ya no quiso el material', '2026-04-01 15:49:15'),
(351, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 176, 'Salida Venta: V-176. Entregado: 1 / 1', '2026-04-01 15:53:35'),
(352, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 176, 'REINGRESO POR CANCELACIÓN - Folio: V-176. Motivo: ya no quiso el material', '2026-04-01 15:54:19'),
(353, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 177, 'Salida por venta rápida folio: V-177. Entregado: 1 de 1', '2026-04-01 16:28:45'),
(354, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 178, 'Salida Venta: V-178. Entregado: 1 / 1', '2026-04-01 16:30:06'),
(355, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 179, 'Salida Venta: V-179. Entregado: 1 / 1', '2026-04-06 18:44:06'),
(356, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 180, 'Salida Venta: V-180. Entregado: 1 / 1', '2026-04-07 15:26:43'),
(357, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 181, 'Salida Venta: V-181. Entregado: 1 / 1', '2026-04-07 15:27:17'),
(358, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 182, 'Salida Venta: V-182. Entregado: 1 / 1', '2026-04-07 15:28:06'),
(359, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 182, 'REINGRESO POR CANCELACIÓN - Folio: V-182. Motivo: ya no quiso el material', '2026-04-07 15:30:09'),
(360, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 181, 'REINGRESO POR CANCELACIÓN - Folio: V-181. Motivo: ya no quiso el material', '2026-04-07 15:37:15'),
(361, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 183, 'Salida Venta: V-183. Entregado: 1 / 1', '2026-04-07 15:37:32'),
(362, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 183, 'REINGRESO POR CANCELACIÓN - Folio: V-183. Motivo: ya no quiso el material', '2026-04-07 15:37:46'),
(363, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 184, 'Salida Venta: V-184. Entregado: 1 / 1', '2026-04-07 15:38:21'),
(364, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 184, 'REINGRESO POR CANCELACIÓN - Folio: V-184. Motivo: ya no quiso el material', '2026-04-07 15:39:11'),
(365, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 185, 'Salida Venta: V-185. Entregado: 1 / 1', '2026-04-07 15:42:08'),
(366, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 186, 'Salida Venta: V-186. Entregado: 1 / 1', '2026-04-07 15:48:32'),
(367, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 187, 'Salida Venta: V-187. Entregado: 1 / 1', '2026-04-07 15:57:59'),
(368, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 188, 'Salida Venta: V-188. Entregado: 1 / 1', '2026-04-07 15:58:49'),
(369, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 189, 'Salida Venta: V-189. Entregado: 1 / 1', '2026-04-07 15:59:42'),
(370, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 190, 'Salida Venta: V-190. Entregado: 1 / 1', '2026-04-07 16:29:44'),
(371, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 191, 'Salida Venta: V-191. Entregado: 1 / 1', '2026-04-07 16:31:33'),
(372, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 192, 'Salida Venta: V-192. Entregado: 1 / 1', '2026-04-07 16:32:01'),
(373, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 193, 'Salida Venta: V-193. Entregado: 1 / 1', '2026-04-07 16:34:00'),
(374, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 194, 'Salida Venta: V-194. Entregado: 1 / 1', '2026-04-07 16:35:07'),
(375, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 195, 'Salida Venta: V-195. Entregado: 1 / 1', '2026-04-07 16:35:28'),
(376, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 195, 'REINGRESO POR CANCELACIÓN - Folio: V-195. Motivo: ya no quiso el material', '2026-04-07 16:36:56'),
(377, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 193, 'REINGRESO POR CANCELACIÓN - Folio: V-193. Motivo: ya no quiso el material', '2026-04-07 16:37:02'),
(378, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 194, 'REINGRESO POR CANCELACIÓN - Folio: V-194. Motivo: ya no quiso el material', '2026-04-07 16:37:10'),
(379, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 192, 'REINGRESO POR CANCELACIÓN - Folio: V-192. Motivo: ya no quiso el material', '2026-04-07 16:37:16'),
(380, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 191, 'REINGRESO POR CANCELACIÓN - Folio: V-191. Motivo: ya no quiso el material', '2026-04-07 16:37:22'),
(381, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 190, 'REINGRESO POR CANCELACIÓN - Folio: V-190. Motivo: ya no quiso el material', '2026-04-07 16:37:28'),
(382, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 189, 'REINGRESO POR CANCELACIÓN - Folio: V-189. Motivo: ya no quiso el material', '2026-04-07 16:37:33'),
(383, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 187, 'REINGRESO POR CANCELACIÓN - Folio: V-187. Motivo: ya no quiso el material', '2026-04-07 16:37:40');
INSERT INTO `movimientos` (`id`, `producto_id`, `tipo`, `cantidad`, `almacen_origen_id`, `almacen_destino_id`, `usuario_registra_id`, `usuario_autoriza_id`, `usuario_envia_id`, `usuario_recibe_id`, `responsable_movimiento`, `referencia_id`, `observaciones`, `fecha`) VALUES
(384, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 186, 'REINGRESO POR CANCELACIÓN - Folio: V-186. Motivo: ya no quiso el material', '2026-04-07 16:37:46'),
(385, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 185, 'REINGRESO POR CANCELACIÓN - Folio: V-185. Motivo: ya no quiso el material', '2026-04-07 16:37:52'),
(386, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 196, 'Salida Venta: V-196. Entregado: 1 / 1', '2026-04-07 16:38:24'),
(387, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 197, 'Salida Venta: V-197. Entregado: 1 / 1', '2026-04-07 16:43:25'),
(388, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 196, 'REINGRESO POR CANCELACIÓN - Folio: V-196. Motivo: ya no quiso el material', '2026-04-07 16:43:44'),
(389, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 197, 'REINGRESO POR CANCELACIÓN - Folio: V-197. Motivo: ya no quiso el material', '2026-04-07 16:43:50'),
(390, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 198, 'Salida Venta: V-198. Entregado: 1 / 1', '2026-04-07 16:55:13'),
(391, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 199, 'Salida Venta: V-199. Entregado: 1 / 1', '2026-04-07 17:07:25'),
(392, 21, 'entrada', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 199, 'REINGRESO POR CANCELACIÓN - Folio: V-199. Motivo: ya no quiso el material', '2026-04-07 17:10:38'),
(393, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 200, 'Salida Venta: V-200. Entregado: 1 / 1', '2026-04-07 17:11:09'),
(394, 1, 'entrada', 20.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 42, 'REINGRESO POR CANCELACIÓN - Folio: V-42. Motivo: ya no quiso el material', '2026-04-07 18:00:39'),
(395, 21, 'entrada', 17.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 42, 'REINGRESO POR CANCELACIÓN - Folio: V-42. Motivo: ya no quiso el material', '2026-04-07 18:00:39'),
(396, 17, 'entrada', 4.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 42, 'REINGRESO POR CANCELACIÓN - Folio: V-42. Motivo: ya no quiso el material', '2026-04-07 18:00:39'),
(397, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 201, 'Salida Venta: V-201. Entregado: 1 / 1', '2026-04-07 19:58:06'),
(398, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 202, 'Salida Venta: V-202. Entregado: 1 / 1', '2026-04-07 20:16:48'),
(399, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 203, 'Salida por venta rápida folio: V-203. Entregado: 1 de 1', '2026-04-07 20:18:39'),
(400, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 204, 'Salida Venta: V-204. Entregado: 1 / 1', '2026-04-07 20:20:02'),
(401, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 205, 'Salida por venta rápida folio: V-205. Entregado: 1 de 1', '2026-04-07 20:23:14'),
(402, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 206, 'Salida Venta: V-206. Entregado: 1 / 1', '2026-04-07 20:23:41'),
(403, 21, 'entrada', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 206, 'REINGRESO POR CANCELACIÓN - Folio: V-206. Motivo: ya no quiso el material', '2026-04-07 20:24:23'),
(404, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 207, 'Salida Venta: V-207. Entregado: 1 / 1', '2026-04-07 20:24:55'),
(405, 21, 'entrada', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 207, 'REINGRESO POR CANCELACIÓN - Folio: V-207. Motivo: ya no quiso el material', '2026-04-07 20:25:15'),
(406, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 208, 'Salida Venta: V-208. Entregado: 1 / 1', '2026-04-07 20:25:53'),
(407, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 209, 'Salida Venta: V-209. Entregado: 1 / 1', '2026-04-07 20:47:06'),
(408, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 210, 'Salida Venta: V-210. Entregado: 1 / 1', '2026-04-07 20:48:08'),
(409, 20, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 210, 'Salida Venta: V-210. Entregado: 1 / 1', '2026-04-07 20:48:08'),
(410, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 211, 'Salida por venta rápida folio: V-211. Entregado: 1 de 1', '2026-04-07 20:48:26'),
(411, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 212, 'Salida Venta: V-212. Entregado: 1 / 1', '2026-04-07 22:29:08'),
(412, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 213, 'Salida por venta rápida folio: V-213. Entregado: 1 de 1', '2026-04-07 22:32:25'),
(413, 1, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, 'casa', NULL, 'Salida Transmutación #24', '2026-04-07 22:35:02'),
(414, 3, 'entrada', 40.00, NULL, 1, 3, NULL, NULL, NULL, 'casa', NULL, 'Entrada Transmutación #24', '2026-04-07 22:35:02'),
(415, 23, 'entrada', 1000.00, NULL, 1, 3, NULL, NULL, NULL, NULL, NULL, 'Carga inicial mediante nuevo producto (Lote: L-AL-M1-004129)', '2026-04-07 22:41:29'),
(416, 23, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, 'casa', NULL, 'Salida Transmutación #25', '2026-04-07 22:43:05'),
(417, 4, 'entrada', 1000.00, NULL, 1, 3, NULL, NULL, NULL, 'casa', NULL, 'Entrada Transmutación #25', '2026-04-07 22:43:05'),
(418, 23, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 214, 'Salida Venta: V-214. Entregado: 1 / 1', '2026-04-07 22:46:34'),
(419, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 214, 'Salida Venta: V-214. Entregado: 1 / 1', '2026-04-07 22:46:34'),
(420, 23, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 215, 'Salida Venta: V-215. Entregado: 1 / 1', '2026-04-07 22:47:52'),
(421, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 215, 'Salida Venta: V-215. Entregado: 1 / 1', '2026-04-07 22:47:52'),
(422, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 215, 'ELIMINACIÓN POR AJUSTE A CERO - Venta ID: 215', '2026-04-07 22:50:49'),
(423, 23, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 216, 'Salida por entrega parcial. Folio Venta: V-216', '2026-04-07 23:02:45'),
(432, 21, 'salida', 2.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 217, 'Salida por entrega parcial. Folio Venta: V-217', '2026-04-07 23:06:13'),
(433, 4, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 217, 'Salida por entrega parcial. Folio Venta: V-217', '2026-04-07 23:06:13'),
(434, 23, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 218, 'Salida Venta: V-218. Entregado: 1 / 1', '2026-04-07 23:17:12'),
(435, 23, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 219, 'Salida por venta rápida folio: V-219. Entregado: 1 de 1', '2026-04-07 23:50:59'),
(436, 23, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 220, 'Salida por venta rápida folio: V-220. Entregado: 1 de 1', '2026-04-07 23:54:52'),
(437, 23, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 221, 'Salida por venta rápida folio: V-221. Entregado: 1 de 1', '2026-04-08 00:01:12'),
(438, 23, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 222, 'Salida por venta rápida folio: V-222. Entregado: 1 de 1', '2026-04-08 14:56:43'),
(439, 23, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 223, 'Salida por venta rápida folio: V-223. Entregado: 1 de 1', '2026-04-08 15:11:46'),
(440, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 224, 'Salida Venta: V-224. Entregado: 1 / 1', '2026-04-08 15:27:08'),
(441, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 225, 'Salida Venta: V-225. Entregado: 1 / 1', '2026-04-08 15:28:47'),
(442, 24, 'entrada', 1000.00, NULL, 2, 2, NULL, NULL, NULL, NULL, NULL, 'Carga inicial mediante nuevo producto (Lote: L-ARENA-173209)', '2026-04-08 15:32:09'),
(443, 23, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 226, 'Salida por venta rápida folio: V-226. Entregado: 1 de 1', '2026-04-08 18:04:58'),
(444, 23, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 227, 'Salida Venta: V-227. Entregado: 1 / 1', '2026-04-08 21:00:01'),
(445, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 228, 'Salida Venta: V-228. Entregado: 1 / 1', '2026-04-08 21:01:26'),
(446, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 229, 'Salida Venta: V-229. Entregado: 1 / 1', '2026-04-08 22:05:18'),
(447, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 230, 'Salida Venta: V-230. Entregado: 1 / 1', '2026-04-08 22:15:43'),
(448, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 231, 'Salida Venta: V-231. Entregado: 1 / 1', '2026-04-08 22:17:20'),
(449, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 232, 'Salida Venta: V-232. Entregado: 1 / 1', '2026-04-08 22:18:22'),
(450, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 233, 'Salida Venta: V-233. Entregado: 1 / 1', '2026-04-08 22:25:19'),
(451, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 234, 'Salida Venta: V-234. Entregado: 1 / 1', '2026-04-08 22:31:05'),
(452, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 235, 'Salida Venta: V-235. Entregado: 1 / 1', '2026-04-08 22:32:07'),
(453, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 236, 'Salida Venta: V-236. Entregado: 1 / 1', '2026-04-08 23:04:10'),
(454, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 237, 'Salida Venta: V-237. Entregado: 1 / 1', '2026-04-08 23:04:58'),
(455, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 238, 'Salida Venta: V-238. Entregado: 1 / 1', '2026-04-08 23:11:17'),
(456, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 239, 'Salida Venta: V-239. Entregado: 1 / 1', '2026-04-08 23:13:58'),
(457, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 240, 'Salida Venta: V-240. Entregado: 1 / 1', '2026-04-08 23:15:26'),
(458, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 241, 'Salida Venta: V-241. Entregado: 1 / 1', '2026-04-08 23:16:51'),
(459, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 242, 'Salida Venta: V-242. Entregado: 1 / 1', '2026-04-08 23:17:59'),
(460, 23, 'traspaso', 1.00, 1, 2, 1, 1, 1, 1, NULL, NULL, 'hola', '2026-04-09 18:50:17'),
(461, 21, 'salida', 1.00, 2, NULL, 2, NULL, NULL, NULL, NULL, 243, 'Salida Venta: V-243. Entregado: 1 / 1', '2026-04-09 17:36:56'),
(462, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 244, 'Salida Venta: V-244. Entregado: 1 / 1', '2026-04-09 17:38:03'),
(463, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 245, 'Salida Venta: V-245. Entregado: 1 / 1', '2026-04-10 17:47:44'),
(464, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 246, 'Salida Venta: V-246. Entregado: 1 / 1', '2026-04-10 19:37:17'),
(465, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 247, 'Salida Venta: V-247. Entregado: 1 / 1', '2026-04-10 20:17:12'),
(466, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 248, 'Salida Venta: V-248. Entregado: 1 / 1', '2026-04-10 20:18:39'),
(467, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 249, 'Salida Venta: V-249. Entregado: 1 / 1', '2026-04-10 20:19:14'),
(468, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 250, 'Salida Venta: V-250. Entregado: 1 / 1', '2026-04-11 15:50:18'),
(469, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 251, 'Salida Venta: V-251. Entregado: 1 / 1', '2026-04-11 18:48:54'),
(470, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 252, 'Salida Venta: V-252. Entregado: 1 / 1', '2026-04-11 18:52:17'),
(471, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 253, 'Salida Venta: V-253. Entregado: 1 / 1', '2026-04-11 18:54:29'),
(472, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 254, 'Salida Venta: V-254. Entregado: 1 / 1', '2026-04-13 14:36:08'),
(473, 1, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 256, 'Salida Venta: V-256. Entregado: 1 / 1', '2026-04-13 14:37:04'),
(474, 7, 'salida', 1.00, 2, NULL, 1, NULL, NULL, NULL, NULL, 257, 'Salida Venta: V-257. Entregado: 1 / 1', '2026-04-13 14:37:19'),
(475, 22, 'salida', 1.00, 2, NULL, 1, NULL, NULL, NULL, NULL, 258, 'Salida Venta: V-258. Entregado: 1 / 1', '2026-04-13 14:38:20'),
(476, 7, 'entrada', 1.00, 2, NULL, 1, NULL, NULL, NULL, NULL, 257, 'REINGRESO POR CANCELACIÓN - Folio: V-257. Motivo: ', '2026-04-13 14:38:32'),
(477, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 259, 'Salida Venta: V-259. Entregado: 1 / 1', '2026-04-15 04:50:18'),
(478, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 260, 'Salida Venta: V-260. Entregado: 1 / 1', '2026-04-15 05:23:21'),
(479, 19, 'entrada', 150.00, NULL, 2, 1, NULL, NULL, NULL, NULL, 38, 'Compra Folio: 32 (Lote: LOTE-38-19-2)', '2026-04-15 05:59:57'),
(480, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 39, 'Compra Folio: 33 (Lote: LOTE-39-1-1)', '2026-04-15 06:19:09'),
(481, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 261, 'Salida Venta: V-261. Entregado: 1 / 1', '2026-04-15 15:55:42'),
(482, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 262, 'Salida Venta: V-262. Entregado: 1 / 1', '2026-04-15 16:08:06'),
(483, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 40, 'Compra Folio: 34 (Lote: LOTE-40-1-1)', '2026-04-15 17:54:40'),
(484, 21, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 41, 'Compra Folio: 35 (Lote: LOTE-41-21-1)', '2026-04-16 20:58:09'),
(485, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 42, 'Compra Folio: 36 (Lote: LOTE-42-1-1)', '2026-04-16 21:58:23'),
(486, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 43, 'Compra Folio: 37 (Lote: LOTE-43-1-1)', '2026-04-16 22:04:06'),
(487, 21, 'entrada', 1000.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 44, 'Compra Folio: 38 (Lote: LOTE-44-21-1)', '2026-04-16 22:05:07'),
(488, 23, 'traspaso', 100.00, 1, 2, 1, 1, 1, 1, NULL, NULL, 'Traspaso por solicitud del administrador', '2026-04-18 19:39:01'),
(489, 21, 'salida', 1.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 263, 'Salida Venta: V-263. Entregado: 1 / 1', '2026-04-20 23:53:18'),
(490, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 264, 'Salida Venta: V-264. Entregado: 1 / 1', '2026-04-21 18:33:18'),
(491, 21, 'entrada', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 264, 'REINGRESO POR CANCELACIÓN - Folio: V-264. Motivo: YA NO QUISO EL MATERIAL', '2026-04-21 18:34:23'),
(492, 21, 'salida', 1.00, 1, NULL, 3, NULL, NULL, NULL, NULL, 265, 'Salida Venta: V-265. Entregado: 1 / 1', '2026-04-21 18:35:10'),
(493, 1, 'entrada', 22.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 51, 'Compra Folio: 39 (Lote: LOTE-51-1-1)', '2026-04-22 15:04:03'),
(494, 1, 'entrada', 22.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 52, 'Compra Folio: 40 (Lote: LOTE-52-1-1)', '2026-04-22 16:04:08'),
(495, 1, 'ajuste', 22.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 52, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 40', '2026-04-22 16:05:58'),
(496, 1, 'ajuste', 22.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 51, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 39', '2026-04-22 16:06:03'),
(497, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 53, 'Compra Folio: 41 (Lote: LOTE-53-1-1)', '2026-04-22 16:26:44'),
(498, 1, 'entrada', 20.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 55, 'Compra Folio: 42 (Lote: LOTE-55-1-1)', '2026-04-22 17:16:01'),
(499, 1, 'ajuste', 20.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 53, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 41', '2026-04-22 17:18:02'),
(500, 1, 'ajuste', 20.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 55, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 42', '2026-04-22 17:18:05'),
(501, 1, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 57, 'Compra Folio: 43 (Lote: LOTE-57-1-1)', '2026-04-22 17:22:09'),
(502, 1, 'ajuste', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 57, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 43', '2026-04-22 17:27:12'),
(503, 8, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 58, 'Compra Folio: 44 (Lote: LOTE-58-8-1)', '2026-04-22 17:28:09'),
(504, 1, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 59, 'Compra Folio: 45 (Lote: LOTE-59-1-1)', '2026-04-22 17:29:45'),
(505, 8, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 60, 'Compra Folio: 46 (Lote: LOTE-60-8-1)', '2026-04-22 17:31:18'),
(506, 8, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 61, 'Compra Folio: 47 (Lote: LOTE-61-8-1)', '2026-04-22 17:37:13'),
(507, 8, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 62, 'Compra Folio: 48 (Lote: LOTE-62-8-1)', '2026-04-22 17:39:00'),
(508, 8, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 63, 'Compra Folio: 49 (Lote: LOTE-63-8-1)', '2026-04-22 17:40:22'),
(509, 8, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 64, 'Compra Folio: 50 (Lote: LOTE-64-8-1)', '2026-04-22 17:42:59'),
(510, 7, 'entrada', 4.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 65, 'Compra Folio: 51 (Lote: LOTE-65-7-1)', '2026-04-22 18:43:03'),
(511, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 66, 'Compra Folio: 52 (Lote: LOTE-66-7-1)', '2026-04-22 18:44:30'),
(512, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 67, 'Compra Folio: 53 (Lote: LOTE-67-7-1)', '2026-04-22 18:51:24'),
(513, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 68, 'Compra Folio: 54 (Lote: LOTE-68-7-1)', '2026-04-22 19:03:08'),
(514, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 71, 'Compra Folio: 55 (Lote: LOTE-71-7-1)', '2026-04-22 19:11:43'),
(515, 7, 'ajuste', 2.00, 1, NULL, 1, NULL, NULL, NULL, NULL, 71, 'REVERSIÓN POR CANCELACIÓN - COMPRA: 55', '2026-04-22 19:14:12'),
(516, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 72, 'Compra Folio: 56 (Lote: LOTE-72-7-1)', '2026-04-22 19:15:09'),
(517, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 73, 'Compra Folio: 57 (Lote: LOTE-73-7-1)', '2026-04-22 19:21:52'),
(518, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 74, 'Compra Folio: 58 (Lote: LOTE-74-7-1)', '2026-04-22 19:27:42'),
(519, 7, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 75, 'Compra Folio: 59 (Lote: LOTE-75-7-1)', '2026-04-22 19:31:22'),
(520, 1, 'entrada', 4.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 76, 'Compra Folio: 60 (Lote: LOTE-76-1-1)', '2026-04-23 16:18:52'),
(521, 1, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 78, 'Compra Folio: 61 (Lote: LOTE-78-1-1)', '2026-04-23 16:20:22'),
(522, 6, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 79, 'Compra Folio: 62 (Lote: LOTE-79-6-1)', '2026-04-23 17:03:45'),
(523, 6, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 79, 'Entrada Faltante (Compra: 62)', '2026-04-23 17:07:09'),
(524, 1, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 80, 'Compra Folio: 63 (Lote: LOTE-80-1-1)', '2026-04-23 17:09:23'),
(525, 5, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 81, 'Compra Folio: 64 (Lote: LOTE-81-5-1)', '2026-04-23 17:33:49'),
(526, 1, 'entrada', 3.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 82, 'Compra Folio: 65 (Lote: LOTE-82-1-1)', '2026-04-23 19:14:21'),
(527, 6, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 82, 'Compra Folio: 65 (Lote: LOTE-82-6-1)', '2026-04-23 19:14:21'),
(528, 5, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 83, 'Compra Folio: 66 (Lote: LOTE-83-5-1)', '2026-04-23 19:16:35'),
(529, 4, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 84, 'Compra Folio: 67 (Lote: LOTE-84-4-1)', '2026-04-23 19:18:56'),
(530, 1, 'entrada', 4.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 85, 'Compra Folio: 68 (Lote: LOTE-85-1-1)', '2026-04-23 20:02:46'),
(531, 4, 'entrada', 4.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 85, 'Compra Folio: 68 (Lote: LOTE-85-4-1)', '2026-04-23 20:02:46'),
(532, 1, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 86, 'Compra Folio: 69 (Lote: LOTE-86-1-1)', '2026-04-23 20:06:07'),
(533, 1, 'entrada', 2.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 87, 'Compra Folio: 70 (Lote: LOTE-87-1-1)', '2026-04-23 20:15:16'),
(534, 1, 'entrada', 4.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 88, 'Compra Folio: 71 (Lote: LOTE-88-1-1)', '2026-04-23 20:17:43'),
(535, 8, 'entrada', 3.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 88, 'Compra Folio: 71 (Lote: LOTE-88-8-1)', '2026-04-23 20:17:43'),
(536, 1, 'entrada', 3.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 89, 'Compra Folio: 72 (Lote: LOTE-89-1-1)', '2026-04-23 20:25:48'),
(537, 1, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 94, 'Compra Folio: 73 (Lote: LOTE-94-1-1)', '2026-04-23 21:48:17'),
(538, 1, 'entrada', 3.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 96, 'Compra Folio: 74 (Lote: LOTE-96-1-1)', '2026-04-23 21:56:41'),
(539, 1, 'entrada', 3.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 97, 'Compra Folio: 75 (Lote: LOTE-97-1-1)', '2026-04-23 22:02:15'),
(540, 2, 'entrada', 3.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 97, 'Compra Folio: 75 (Lote: LOTE-97-2-1)', '2026-04-23 22:02:15'),
(541, 2, 'entrada', 3.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 98, 'Compra Folio: 76 (Lote: LOTE-98-2-1)', '2026-04-23 22:08:27'),
(542, 1, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 99, 'Compra Folio: 77 (Lote: LOTE-99-1-1)', '2026-04-23 22:16:37'),
(543, 1, 'entrada', 1.00, NULL, 1, 1, NULL, NULL, NULL, NULL, 100, 'Compra Folio: 78 (Lote: LOTE-100-1-1)', '2026-04-23 22:25:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_cuentas_por_pagar`
--

CREATE TABLE `pagos_cuentas_por_pagar` (
  `id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL,
  `compra_id` int(11) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `metodo_pago` varchar(50) DEFAULT 'Efectivo',
  `referencia_pago` varchar(100) DEFAULT NULL,
  `fecha_pago` datetime DEFAULT current_timestamp(),
  `usuario_id` int(11) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pagos_cuentas_por_pagar`
--

INSERT INTO `pagos_cuentas_por_pagar` (`id`, `almacen_id`, `proveedor_id`, `compra_id`, `monto`, `metodo_pago`, `referencia_pago`, `fecha_pago`, `usuario_id`, `observaciones`) VALUES
(1, 1, 4, 65, 50.00, 'Efectivo', 'PAGO-EXCEDENTE-65', '2026-04-22 12:43:03', 1, 'Pago de deuda generada por material excedente en Compra Folio: 51'),
(2, 1, 4, 76, 50.00, 'Efectivo', 'PAGO-REF-1776961222', '2026-04-23 10:20:22', 1, 'Pago de deuda (Compra #76) por $50.00'),
(3, 1, 4, 87, 25.00, 'Efectivo', 'PAGO-REF-1776982597', '2026-04-23 16:16:37', 1, 'Pago de deuda (Compra #87) por $25.00'),
(4, 1, 4, 88, 56.67, 'Efectivo', 'PAGO-REF-1776982597', '2026-04-23 16:16:37', 1, 'Pago de deuda (Compra #88) por $56.67'),
(5, 1, 4, 89, 50.00, 'Efectivo', 'PAGO-REF-1776982597', '2026-04-23 16:16:37', 1, 'Pago de deuda (Compra #89) por $50.00'),
(6, 1, 4, 96, 50.00, 'Efectivo', 'PAGO-REF-1776982597', '2026-04-23 16:16:37', 1, 'Pago de deuda (Compra #96) por $50.00'),
(7, 1, 4, 97, 75.00, 'Efectivo', 'PAGO-REF-1776982597', '2026-04-23 16:16:37', 1, 'Pago de deuda (Compra #97) por $75.00'),
(8, 1, 4, 98, 50.00, 'Efectivo', 'PAGO-REF-1776982597', '2026-04-23 16:16:37', 1, 'Pago de deuda (Compra #98) por $50.00');

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

--
-- Volcado de datos para la tabla `pedidos_vendedores`
--

INSERT INTO `pedidos_vendedores` (`id`, `folio`, `vendedor_id`, `cliente_id`, `almacen_id`, `fecha_solicitud`, `prioridad`, `estatus`, `observaciones`) VALUES
(4, 'PED-00001', 1, 1, 1, '2026-03-25 15:45:22', 'Media', 1, ''),
(5, 'PED-00005', 1, 2, 1, '2026-03-25 15:59:27', 'Media', 1, ''),
(6, 'PED-00006', 1, 1, 1, '2026-03-25 16:05:00', 'Media', 1, ''),
(7, 'PED-00007', 1, 2, 1, '2026-03-25 16:43:30', 'Media', 1, 'se entrega mañana'),
(8, 'PED-00008', 1, 1, 1, '2026-03-25 17:42:53', 'Media', 1, ''),
(9, 'PED-00009', 1, 1, 1, '2026-03-25 17:44:43', 'Media', 1, ''),
(10, 'PED-00010', 1, 1, 1, '2026-03-25 17:48:55', 'Media', 1, ''),
(11, 'PED-00011', 4, 10, 1, '2026-03-25 18:28:58', 'Media', 1, ''),
(12, 'PED-00012', 1, 11, 1, '2026-04-16 20:45:13', 'Media', 0, ''),
(13, 'PED-00013', 1, 1, 2, '2026-04-16 20:46:59', 'Media', 0, ''),
(14, 'PED-00014', 1, 13, 1, '2026-04-20 14:58:05', 'Media', 1, '');

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
(2759, 1, 'almacenes'),
(2754, 1, 'cajaRapida'),
(2760, 1, 'clientes'),
(2757, 1, 'clientesEstatus'),
(2767, 1, 'compras'),
(2756, 1, 'Configuracion'),
(2764, 1, 'corteCaja'),
(2765, 1, 'entregas'),
(2768, 1, 'finanzas'),
(2745, 1, 'finanzas_admin'),
(2766, 1, 'inicio'),
(2763, 1, 'Mermas'),
(2755, 1, 'misRepartos'),
(2761, 1, 'movimientos'),
(2753, 1, 'pedidosVendedor'),
(2744, 1, 'prestamos'),
(2769, 1, 'proveedores'),
(2751, 1, 'repartos'),
(2748, 1, 'solicitudesCompra'),
(2746, 1, 'tesoreria'),
(2749, 1, 'trabajadores'),
(2747, 1, 'transmutaciones'),
(2762, 1, 'usuarios'),
(2750, 1, 'vehiculos'),
(2758, 1, 'ventas'),
(2752, 1, 'ventashistorial'),
(2797, 2, 'almacenes'),
(2795, 2, 'cajaRapida'),
(2798, 2, 'clientes'),
(2803, 2, 'compras'),
(2801, 2, 'entregas'),
(2802, 2, 'inicio'),
(2800, 2, 'Mermas'),
(2799, 2, 'movimientos'),
(2804, 2, 'proveedores'),
(2793, 2, 'repartos'),
(2791, 2, 'trabajadores'),
(2790, 2, 'transmutaciones'),
(2792, 2, 'vehiculos'),
(2796, 2, 'ventas'),
(2794, 2, 'ventashistorial'),
(2810, 3, 'almacenes'),
(2811, 3, 'clientes'),
(2816, 3, 'compras'),
(2814, 3, 'entregas'),
(2815, 3, 'inicio'),
(2813, 3, 'Mermas'),
(2812, 3, 'movimientos'),
(2817, 3, 'proveedores'),
(2807, 3, 'repartos'),
(2805, 3, 'trabajadores'),
(2806, 3, 'vehiculos'),
(2809, 3, 'ventas'),
(2808, 3, 'ventashistorial'),
(2780, 5, 'almacenes'),
(2778, 5, 'cajaRapida'),
(2781, 5, 'clientes'),
(2788, 5, 'compras'),
(2785, 5, 'corteCaja'),
(2786, 5, 'entregas'),
(2770, 5, 'finanzas_admin'),
(2787, 5, 'inicio'),
(2784, 5, 'Mermas'),
(2782, 5, 'movimientos'),
(2789, 5, 'proveedores'),
(2776, 5, 'repartos'),
(2773, 5, 'solicitudesCompra'),
(2771, 5, 'tesoreria'),
(2774, 5, 'trabajadores'),
(2772, 5, 'transmutaciones'),
(2783, 5, 'usuarios'),
(2775, 5, 'vehiculos'),
(2779, 5, 'ventas'),
(2777, 5, 'ventashistorial'),
(2819, 6, 'inicio'),
(2818, 6, 'misRepartos');

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
(143, 7, 1, 100.00, 90.00, 80.00),
(144, 7, 2, 0.00, 0.00, 0.00),
(145, 8, 1, 50.00, 40.00, 30.00),
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
(195, 22, 4, 10.00, 9.00, 8.00),
(196, 23, 1, 100.00, 90.00, 80.00),
(197, 24, 2, 10.00, 9.00, 8.00),
(198, 23, 2, 100.00, 90.00, 80.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id` int(11) NOT NULL,
  `trabajador_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `monto_total` decimal(12,2) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` enum('activo','pendiente','pagado','cancelado') NOT NULL DEFAULT 'pendiente',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id`, `trabajador_id`, `almacen_id`, `monto_total`, `descripcion`, `estado`, `fecha_registro`) VALUES
(1, 6, 1, 100.00, 'Adelanto de nomina', 'activo', '2026-04-17 19:33:58'),
(2, 6, 1, 100.00, 'Adelanto de quincena', 'activo', '2026-04-17 19:37:02'),
(3, 6, 1, 100.00, 'Adelanto de nomina', 'activo', '2026-04-17 19:44:53'),
(4, 6, 1, 100.00, 'Adelanto de nomina', 'activo', '2026-04-17 19:44:53'),
(5, 6, 1, 200.00, 'Adelanto de nomina', 'activo', '2026-04-17 19:45:23'),
(6, 6, 1, 100.00, 'Adelanto de nomina', 'activo', '2026-04-17 20:16:06'),
(7, 6, 1, 100.00, 'Adelanto de nomina', 'activo', '2026-04-17 20:17:30'),
(8, 6, 1, 1.00, 'Adelanto de nomina', 'activo', '2026-04-17 20:23:06'),
(9, 6, 1, 1.00, 'Adelanto de nomina', 'activo', '2026-04-17 20:24:01'),
(10, 1, 1, 1.00, 'Adelanto de quincena', 'activo', '2026-04-17 20:30:10'),
(11, 1, 1, 1.00, 'Adelanto de quincena', 'pagado', '2026-04-17 20:30:10'),
(12, 1, 1, 1.00, 'Adelnato de nomina', 'pagado', '2026-04-17 20:33:55'),
(13, 1, 1, 1.00, 'Adelnato de nomina', 'pagado', '2026-04-17 20:33:55'),
(14, 1, 1, 1.00, 'Adelanto nomina', 'pagado', '2026-04-17 20:36:40'),
(15, 6, 1, 1.00, 'Adelanto nomina', 'pagado', '2026-04-17 20:39:32'),
(16, 6, 1, 1.00, '1', 'pagado', '2026-04-17 20:41:04'),
(17, 6, 1, 1.00, '1', 'pagado', '2026-04-17 20:41:04'),
(18, 6, 1, 1.00, 'Adelanto de nomina', 'pagado', '2026-04-17 20:42:56'),
(19, 6, 1, 200.00, 'Adelanto de quincena', 'pagado', '2026-04-17 20:51:17'),
(20, 6, 1, 1.00, 'Adelanto de nomina', 'pagado', '2026-04-17 20:52:40'),
(21, 6, 1, 1.00, 'Adelanto de nomina', 'pagado', '2026-04-17 20:53:25'),
(22, 6, 1, 1.00, '1', 'pagado', '2026-04-17 21:40:48'),
(23, 6, 1, 1.00, '1', 'pagado', '2026-04-17 21:41:44'),
(24, 6, 1, 1.00, '1', 'pagado', '2026-04-17 21:41:48'),
(25, 6, 1, 100.00, 'Adelanto de nomina', 'activo', '2026-04-17 21:53:57'),
(26, 1, 1, 100.00, 'Adelanto de quincena', 'pagado', '2026-04-18 17:41:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos_abonos`
--

CREATE TABLE `prestamos_abonos` (
  `id` int(11) NOT NULL,
  `prestamo_id` int(11) NOT NULL,
  `monto_abono` decimal(12,2) NOT NULL,
  `numero_pago` int(11) NOT NULL,
  `metodo_pago` enum('efectivo','transferencia','descuento_nomina') DEFAULT 'efectivo',
  `usuario_registro_id` int(11) NOT NULL,
  `fecha_abono` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos_abonos`
--

INSERT INTO `prestamos_abonos` (`id`, `prestamo_id`, `monto_abono`, `numero_pago`, `metodo_pago`, `usuario_registro_id`, `fecha_abono`, `observaciones`) VALUES
(1, 25, 100.00, 1, 'efectivo', 1, '2026-04-17 22:27:16', ''),
(2, 24, 1.00, 1, 'efectivo', 1, '2026-04-17 22:29:02', ''),
(3, 19, 100.00, 1, 'efectivo', 1, '2026-04-17 22:29:12', ''),
(4, 23, 1.00, 1, 'efectivo', 1, '2026-04-17 23:36:10', ''),
(5, 22, 1.00, 1, 'efectivo', 1, '2026-04-17 23:41:22', ''),
(6, 21, 1.00, 1, 'efectivo', 1, '2026-04-18 14:49:15', 'Pago de prestamo'),
(7, 20, 1.00, 1, 'efectivo', 1, '2026-04-18 15:15:50', 'Pago de prestamo'),
(8, 18, 1.00, 1, 'efectivo', 1, '2026-04-18 15:22:49', 'Pago de prestamo'),
(9, 19, 100.00, 2, 'efectivo', 1, '2026-04-18 15:24:09', 'Pago de prestamo'),
(10, 16, 1.00, 1, 'efectivo', 1, '2026-04-18 15:37:34', 'Pago de préstamo'),
(11, 17, 1.00, 1, 'efectivo', 1, '2026-04-18 15:42:03', 'Pago de nomina'),
(12, 17, 1.00, 2, 'efectivo', 1, '2026-04-18 15:42:03', 'Pago de nomina'),
(13, 15, 1.00, 1, 'efectivo', 1, '2026-04-18 15:51:05', 'Pago de prestamo'),
(14, 14, 1.00, 1, 'efectivo', 1, '2026-04-18 15:59:43', 'Pago de prestamo'),
(15, 14, 1.00, 2, 'efectivo', 1, '2026-04-18 15:59:43', 'Pago de prestamo'),
(16, 12, 1.00, 1, 'efectivo', 1, '2026-04-18 16:00:31', 'Pago  prestamo'),
(17, 13, 1.00, 1, 'efectivo', 1, '2026-04-18 16:01:50', 'Pago de prestamo'),
(18, 11, 0.50, 1, 'efectivo', 1, '2026-04-18 16:03:51', 'Pago de préstamo'),
(19, 11, 0.50, 2, 'efectivo', 1, '2026-04-18 16:08:48', 'Pago prestamo'),
(20, 26, 100.00, 1, 'efectivo', 1, '2026-04-20 15:15:43', 'Pago de prestamo');

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
(7, 'MC01', 'Mortero', 'Mortero', 'Bulto', 'Tonelada', 20.00, '2121232', '', 98.00, 16.00, 1, '2026-03-04 15:39:32', 2),
(8, 'Yeso-25', 'Yeso 45 kg', 'Yeso blanco', 'Bulto', 'Tonelada', 40.00, '32312313', '', 68.00, 8.00, 1, '2026-03-04 17:24:48', 1),
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
(21, 'Ani-40', 'anillo 40x40', 'Anillo de 40x40', 'Kg', 'Tonelada', 1000.00, '1234', 'h123', 20000.00, 16.00, 1, '2026-03-12 15:05:50', 3),
(22, 'SOL-01', 'Solvente para pintura', '', 'Ltr', 'Galon', 3.00, '0', '', 0.00, 16.00, 1, '2026-03-17 20:18:31', 7),
(23, 'AL-M1', 'Alambron 1/4', 'Alambron 1/4', 'Kilo', 'Tonelada', 1000.00, '0', '', 100.00, 16.00, 1, '2026-04-07 22:41:29', 2),
(24, 'ARENA', 'Arena', 'Arena blanca', 'Kilo', 'Tonelada', 1000.00, '0', 'H87', 1200.00, 16.00, 1, '2026-04-08 15:32:09', 1);

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
(32, 68, 1, '2026-03-21 18:01:59', 1),
(33, 69, 1, '2026-03-21 22:16:33', 1),
(34, 134, 1, '2026-03-22 03:47:40', 1),
(35, 135, 1, '2026-03-23 15:03:52', 1),
(36, 136, 1, '2026-03-24 01:53:02', 1),
(37, 138, 1, '2026-03-25 19:37:11', 1),
(38, 137, 1, '2026-03-26 15:57:39', 1),
(39, 142, 1, '2026-03-26 16:14:17', 1),
(40, 143, 1, '2026-03-26 16:15:26', 1),
(41, 144, 1, '2026-03-26 16:18:20', 1),
(42, 145, 1, '2026-03-26 16:23:11', 1),
(43, 146, 1, '2026-03-26 16:24:06', 1),
(44, 147, 1, '2026-03-26 17:07:20', 1),
(45, 149, 1, '2026-03-26 18:43:42', 1),
(46, 152, 3, '2026-03-26 19:26:57', 3),
(47, 155, 3, '2026-03-26 19:34:16', 3),
(48, 157, 1, '2026-03-26 19:43:26', 1),
(49, 158, 1, '2026-03-26 19:43:47', 1),
(50, 172, 1, '2026-03-26 23:27:31', 1),
(51, 173, 1, '2026-03-26 23:55:47', 1),
(52, 174, 1, '2026-03-27 15:22:00', 1),
(53, 175, 1, '2026-03-27 15:28:20', 1),
(54, 176, 1, '2026-03-27 15:30:28', 1),
(55, 177, 1, '2026-03-27 15:33:19', 1),
(56, 178, 1, '2026-03-27 15:42:27', 1),
(57, 179, 1, '2026-03-27 15:43:53', 1),
(58, 188, 1, '2026-03-27 17:05:18', 1),
(59, 189, 1, '2026-03-27 17:05:18', 1),
(60, 190, 1, '2026-03-27 17:37:01', 1),
(61, 193, 1, '2026-03-27 22:13:22', 1),
(62, 194, 1, '2026-03-27 22:13:22', 1),
(63, 186, 1, '2026-03-28 14:54:06', 1),
(64, 187, 1, '2026-03-28 14:54:06', 1),
(65, 184, 1, '2026-03-28 15:05:37', 1),
(66, 185, 1, '2026-03-28 15:05:37', 1),
(67, 183, 1, '2026-03-28 15:16:08', 1),
(68, 182, 1, '2026-03-28 15:17:17', 1),
(73, 195, 1, '2026-03-28 18:26:34', 1),
(76, 206, 1, '2026-03-28 18:45:27', 1),
(77, 213, 1, '2026-03-28 19:00:46', 1),
(78, 214, 1, '2026-03-28 19:00:46', 1),
(79, 219, 1, '2026-03-28 19:13:42', 1),
(91, 224, 1, '2026-03-30 16:38:35', 1),
(92, 225, 1, '2026-03-30 16:38:35', 1),
(95, 228, 1, '2026-03-30 16:41:11', 1),
(96, 229, 1, '2026-03-30 16:41:11', 1),
(97, 226, 1, '2026-03-30 18:46:33', 1),
(98, 227, 1, '2026-03-30 18:46:33', 1),
(99, 353, 1, '2026-04-01 16:28:45', 1),
(100, 354, 1, '2026-04-01 22:39:48', 1),
(101, 314, 1, '2026-04-01 23:59:07', 1),
(102, 311, 1, '2026-04-01 23:59:16', 1),
(103, 236, 1, '2026-04-02 18:36:59', 1),
(104, 235, 1, '2026-04-02 18:37:08', 1),
(105, 393, 1, '2026-04-07 18:45:25', 1),
(106, 222, 1, '2026-04-07 19:00:14', 1),
(107, 223, 1, '2026-04-07 19:00:14', 1),
(108, 220, 1, '2026-04-07 19:03:02', 1),
(109, 221, 1, '2026-04-07 19:03:37', 1),
(110, 399, 1, '2026-04-07 20:18:39', 1),
(111, 401, 2, '2026-04-07 20:23:14', 2),
(112, 406, 2, '2026-04-07 20:33:55', 2),
(113, 407, 2, '2026-04-07 20:47:16', 2),
(114, 410, 2, '2026-04-07 20:48:26', 2),
(115, 412, 3, '2026-04-07 22:32:25', 3),
(116, 418, 3, '2026-04-07 22:47:19', 3),
(117, 419, 3, '2026-04-07 22:47:19', 3),
(118, 420, 3, '2026-04-07 22:48:21', 3),
(120, 423, 3, '2026-04-07 23:03:25', 3),
(121, 435, 1, '2026-04-07 23:50:59', 1),
(122, 436, 1, '2026-04-07 23:54:52', 1),
(123, 437, 1, '2026-04-08 00:01:12', 1),
(124, 438, 1, '2026-04-08 14:56:43', 1),
(125, 439, 1, '2026-04-08 15:11:46', 1),
(126, 408, 2, '2026-04-08 15:26:51', 2),
(127, 409, 2, '2026-04-08 15:26:56', 2),
(128, 440, 2, '2026-04-08 15:27:19', 2),
(129, 441, 2, '2026-04-08 15:29:02', 2),
(130, 443, 1, '2026-04-08 18:04:58', 1),
(131, 489, 1, '2026-04-20 23:53:39', 1),
(132, 434, 1, '2026-04-21 19:21:55', 1);

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
(3, 'supervisor'),
(6, 'Trabajador'),
(5, 'Vendedor');

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
(10, 1, 1, 4, '2026-03-21 02:46:28', 'recibido', NULL),
(11, 1, 1, 4, '2026-03-26 15:02:09', 'pendiente', NULL),
(12, 1, 1, 4, '2026-03-26 15:02:57', 'pendiente', NULL),
(13, 1, 1, 4, '2026-03-26 15:04:56', 'recibido', NULL),
(14, 1, 1, 4, '2026-04-16 20:48:00', 'recibido', NULL);

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

--
-- Volcado de datos para la tabla `solicitudes_pedidos`
--

INSERT INTO `solicitudes_pedidos` (`id`, `vendedor_id`, `cliente_id`, `fecha_solicitud`, `estado`, `observaciones`) VALUES
(2, 1, 1, '2026-03-25 16:05:00', 'pendiente', ''),
(3, 1, 2, '2026-03-25 16:43:30', 'pendiente', 'se entrega mañana'),
(4, 1, 1, '2026-03-25 17:42:53', 'pendiente', ''),
(5, 1, 1, '2026-03-25 17:44:43', 'pendiente', ''),
(6, 1, 1, '2026-03-25 17:48:55', 'pendiente', ''),
(7, 4, 10, '2026-03-25 18:28:58', 'pendiente', ''),
(8, 1, 11, '2026-04-16 20:45:13', 'pendiente', ''),
(9, 1, 1, '2026-04-16 20:46:59', 'pendiente', ''),
(10, 1, 13, '2026-04-20 14:58:05', 'pendiente', '');

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
(4, 'Arnulfo', '1234567890', 'cargador', 'activo', 4, '2026-03-21 17:10:09'),
(5, 'Cornelio', '1234567890', 'cargador', 'activo', 3, '2026-03-26 15:56:47'),
(6, 'Javier', '123456789', 'cargador', 'activo', 1, '2026-03-26 15:57:16');

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
(22, 1, 1, '2026-03-14 01:12:21', 'se rompio el bulto se recupero'),
(23, 1, 1, '2026-03-25 23:40:23', ''),
(24, 3, 1, '2026-04-07 22:35:02', ''),
(25, 3, 1, '2026-04-07 22:43:05', '');

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
(10, 22, 62, 'entrada', 3, 89, 25.00, 85.00, 85.00),
(11, 23, 140, 'salida', 1, 81, 1.00, 4.00, 4.00),
(12, 23, 141, 'entrada', 3, 179, 50.00, 0.08, 0.08),
(13, 24, 413, 'salida', 1, 150, 1.00, 100.00, 100.00),
(14, 24, 414, 'entrada', 3, 89, 40.00, 85.00, 85.00),
(15, 25, 416, 'salida', 23, 181, 1.00, 100.00, 100.00),
(16, 25, 417, 'entrada', 4, 167, 1000.00, 100.00, 100.00);

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
(13, 'RUT-260321-02-36', 2, 27, 'cerrado', '2026-03-21 18:52:14'),
(14, 'RUT-260321-02-36', 2, 30, 'cerrado', '2026-03-22 03:50:34'),
(17, 'RUT-260324-02-25', 2, 34, 'cerrado', '2026-03-24 14:20:28'),
(19, 'RUT-260326-02-52', 2, 39, 'cerrado', '2026-03-26 16:06:17'),
(20, 'RUT-260326-02-52', 2, 40, 'cerrado', '2026-03-26 16:06:50'),
(21, 'RUT-260326-03-13', 3, 41, 'cerrado', '2026-03-26 16:14:26'),
(22, 'RUT-260326-02-52', 2, 42, 'cerrado', '2026-03-26 16:15:40'),
(23, 'RUT-260326-02-52', 2, 43, 'cerrado', '2026-03-26 16:18:31'),
(24, 'RUT-260326-02-52', 2, 44, 'cerrado', '2026-03-26 16:23:20'),
(25, 'RUT-260326-02-52', 2, 45, 'cerrado', '2026-03-26 16:24:14'),
(29, 'RUT-260326-1000-76', 1000, 50, 'cerrado', '2026-03-26 19:44:51'),
(30, 'RUT-260327-02-72', 2, 54, 'cerrado', '2026-03-27 23:42:54'),
(31, 'RUT-260327-02-72', 2, 55, 'cerrado', '2026-03-28 14:54:06'),
(32, 'RUT-260327-02-72', 2, 56, 'cerrado', '2026-03-28 14:54:06'),
(33, 'RUT-260327-02-72', 2, 61, 'cerrado', '2026-03-28 16:34:31'),
(34, 'RUT-260327-02-72', 2, 62, 'cerrado', '2026-03-28 16:34:31'),
(35, 'RUT-260328-02-51', 2, 64, 'cerrado', '2026-03-28 17:59:48'),
(36, 'RUT-260328-02-51', 2, 65, 'cerrado', '2026-03-28 17:59:48'),
(37, 'RUT-260328-02-72', 2, 66, 'cerrado', '2026-03-28 18:04:15'),
(38, 'RUT-260328-02-72', 2, 67, 'cerrado', '2026-03-28 18:04:15'),
(41, 'RUT-260328-02-98', 2, 73, 'cerrado', '2026-03-28 18:46:28'),
(42, 'RUT-260328-02-98', 2, 74, 'cerrado', '2026-03-28 18:46:28'),
(43, 'RUT-260328-02-45', 2, 75, 'cerrado', '2026-03-28 19:00:16'),
(44, 'RUT-260328-02-45', 2, 76, 'cerrado', '2026-03-28 19:00:16'),
(45, 'RUT-260328-02-45', 2, 79, 'cerrado', '2026-03-28 19:08:24'),
(46, 'RUT-260328-02-45', 2, 80, 'cerrado', '2026-03-28 19:08:24'),
(82, 'RUT-260330-02-36', 2, 116, 'cerrado', '2026-03-30 16:41:11'),
(83, 'RUT-260330-02-36', 2, 117, 'cerrado', '2026-03-30 16:41:11'),
(91, 'RUT-260401-02-15', 2, 126, 'cerrado', '2026-04-01 22:40:00'),
(92, 'RUT-260401-02-15', 2, 127, 'cerrado', '2026-04-01 23:59:45'),
(99, 'RUT-260407-03-89', 3, 143, 'cerrado', '2026-04-07 20:34:09'),
(102, 'RUT-260407-03-46', 3, 151, 'cerrado', '2026-04-07 22:52:02'),
(104, 'RUT-260407-02-78', 2, 153, 'cerrado', '2026-04-07 22:53:35'),
(105, 'RUT-260407-02-78', 2, 154, 'cerrado', '2026-04-07 22:55:05'),
(106, 'RUT-260408-03-69', 3, 160, 'abierto', '2026-04-08 15:27:28'),
(107, 'RUT-260408-03-69', 3, 161, 'abierto', '2026-04-08 15:27:41'),
(108, 'RUT-260408-01-72', 1, 162, 'abierto', '2026-04-08 15:28:22'),
(109, 'RUT-260408-01-72', 1, 163, 'abierto', '2026-04-08 15:29:12'),
(110, 'RUT-260420-02-42', 2, 165, 'abierto', '2026-04-20 23:53:49');

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
(27, 2, 1, 68, '2026-03-21', NULL, '2026-03-21 21:51:49', NULL, NULL, 'completado', NULL),
(28, 999, 1, 69, '2026-03-21', NULL, '2026-03-21 16:17:00', NULL, NULL, 'completado', NULL),
(29, 999, 1, 134, '2026-03-21', NULL, '2026-03-21 21:49:32', NULL, NULL, 'completado', NULL),
(30, 2, 1, 133, '2026-03-21', NULL, '2026-03-21 21:51:49', NULL, NULL, 'completado', NULL),
(31, 999, 1, 135, '2026-03-23', NULL, '2026-03-23 13:30:57', NULL, NULL, 'completado', NULL),
(34, 2, 1, 136, '2026-03-24', NULL, '2026-03-26 09:58:15', NULL, NULL, 'completado', NULL),
(39, 2, 6, 138, '2026-03-26', NULL, '2026-03-26 10:24:31', NULL, NULL, 'completado', NULL),
(40, 2, 6, 137, '2026-03-26', NULL, '2026-03-26 10:24:31', NULL, NULL, 'completado', NULL),
(41, 3, 2, 142, '2026-03-26', NULL, '2026-03-26 10:24:37', NULL, NULL, 'completado', NULL),
(42, 2, 1, 143, '2026-03-26', NULL, '2026-03-26 10:24:31', NULL, NULL, 'completado', NULL),
(43, 2, 6, 144, '2026-03-26', NULL, '2026-03-26 10:24:31', NULL, NULL, 'completado', NULL),
(44, 2, 6, 145, '2026-03-26', NULL, '2026-03-26 10:24:31', NULL, NULL, 'completado', NULL),
(45, 2, 6, 146, '2026-03-26', NULL, '2026-03-26 10:24:31', NULL, NULL, 'completado', NULL),
(48, 999, 3, 152, '2026-03-26', NULL, '2026-03-26 13:27:40', NULL, NULL, 'completado', NULL),
(50, 1000, 5, 157, '2026-03-26', NULL, '2026-03-28 11:57:30', NULL, NULL, 'completado', NULL),
(51, 999, 1, 188, '2026-03-27', NULL, '2026-03-27 11:05:18', NULL, NULL, 'completado', NULL),
(52, 999, 1, 189, '2026-03-27', NULL, '2026-03-27 11:05:18', NULL, NULL, 'completado', NULL),
(53, 999, 1, 190, '2026-03-27', NULL, '2026-03-27 11:37:01', NULL, NULL, 'completado', NULL),
(54, 2, 6, 179, '2026-03-27', NULL, '2026-03-28 11:57:23', NULL, NULL, 'completado', NULL),
(55, 2, 6, 186, '2026-03-28', NULL, '2026-03-28 11:57:23', NULL, NULL, 'completado', NULL),
(56, 2, 6, 187, '2026-03-28', NULL, '2026-03-28 11:57:23', NULL, NULL, 'completado', NULL),
(57, 999, 1, 184, '2026-03-28', NULL, '2026-03-28 09:05:37', NULL, NULL, 'completado', NULL),
(58, 999, 1, 185, '2026-03-28', NULL, '2026-03-28 09:05:37', NULL, NULL, 'completado', NULL),
(59, 999, 1, 194, '2026-03-28', NULL, '2026-03-28 09:15:08', NULL, NULL, 'completado', NULL),
(60, 999, 1, 193, '2026-03-28', NULL, '2026-03-28 09:16:50', NULL, NULL, 'completado', NULL),
(61, 2, 6, 182, '2026-03-28', NULL, '2026-03-28 11:57:23', NULL, NULL, 'completado', NULL),
(62, 2, 6, 183, '2026-03-28', NULL, '2026-03-28 11:57:23', NULL, NULL, 'completado', NULL),
(63, 999, 1, 178, '2026-03-28', NULL, '2026-03-28 10:56:19', NULL, NULL, 'completado', NULL),
(64, 2, 6, 180, '2026-03-28', NULL, '2026-03-28 12:00:01', NULL, NULL, 'completado', NULL),
(65, 2, 6, 181, '2026-03-28', NULL, '2026-03-28 12:00:01', NULL, NULL, 'completado', NULL),
(66, 2, 6, 196, '2026-03-28', NULL, '2026-03-28 12:04:27', NULL, NULL, 'completado', NULL),
(67, 2, 6, 197, '2026-03-28', NULL, '2026-03-28 12:04:27', NULL, NULL, 'completado', NULL),
(70, 999, 1, 204, '2026-03-28', NULL, '2026-03-28 12:44:32', NULL, NULL, 'completado', NULL),
(71, 999, 1, 205, '2026-03-28', NULL, '2026-03-28 12:44:32', NULL, NULL, 'completado', NULL),
(72, 999, 1, 206, '2026-03-28', NULL, '2026-03-28 12:45:36', NULL, NULL, 'completado', NULL),
(73, 2, 6, 207, '2026-03-28', NULL, '2026-03-28 12:46:41', NULL, NULL, 'completado', NULL),
(74, 2, 6, 208, '2026-03-28', NULL, '2026-03-28 12:46:41', NULL, NULL, 'completado', NULL),
(75, 2, 6, 211, '2026-03-28', NULL, '2026-03-28 13:09:00', NULL, NULL, 'completado', NULL),
(76, 2, 6, 212, '2026-03-28', NULL, '2026-03-28 13:09:00', NULL, NULL, 'completado', NULL),
(77, 999, 1, 213, '2026-03-28', NULL, '2026-03-28 13:00:46', NULL, NULL, 'completado', NULL),
(78, 999, 1, 214, '2026-03-28', NULL, '2026-03-28 13:00:46', NULL, NULL, 'completado', NULL),
(79, 2, 6, 215, '2026-03-28', NULL, '2026-03-28 13:09:00', NULL, NULL, 'completado', NULL),
(80, 2, 6, 216, '2026-03-28', NULL, '2026-03-28 13:09:00', NULL, NULL, 'completado', NULL),
(116, 2, 6, 228, '2026-03-30', NULL, '2026-03-30 11:28:44', NULL, NULL, 'completado', NULL),
(117, 2, 6, 229, '2026-03-30', NULL, '2026-03-30 11:28:44', NULL, NULL, 'completado', NULL),
(125, 999, 1, 353, '2026-04-01', NULL, '2026-04-01 10:28:45', NULL, NULL, 'completado', NULL),
(126, 2, 6, 354, '2026-04-01', NULL, '2026-04-02 12:07:31', NULL, NULL, 'completado', NULL),
(127, 2, 6, 314, '2026-04-01', NULL, '2026-04-02 12:07:31', NULL, NULL, 'completado', NULL),
(132, 999, 1, 393, '2026-04-07', NULL, '2026-04-07 12:45:45', NULL, NULL, 'completado', NULL),
(133, 999, 1, 224, '2026-04-07', NULL, '2026-04-07 12:49:56', NULL, NULL, 'completado', NULL),
(134, 999, 1, 225, '2026-04-07', NULL, '2026-04-07 12:49:56', NULL, NULL, 'completado', NULL),
(135, 999, 1, 222, '2026-04-07', NULL, '2026-04-07 13:00:14', NULL, NULL, 'completado', NULL),
(136, 999, 1, 223, '2026-04-07', NULL, '2026-04-07 13:00:14', NULL, NULL, 'completado', NULL),
(139, 999, 1, 220, '2026-04-07', NULL, '2026-04-07 13:15:40', NULL, NULL, 'completado', NULL),
(140, 999, 1, 221, '2026-04-07', NULL, '2026-04-07 13:15:40', NULL, NULL, 'completado', NULL),
(141, 999, 1, 399, '2026-04-07', NULL, '2026-04-07 14:18:39', NULL, NULL, 'completado', NULL),
(142, 999, 2, 401, '2026-04-07', NULL, '2026-04-07 14:23:14', NULL, NULL, 'completado', NULL),
(143, 3, 3, 406, '2026-04-07', NULL, '2026-04-07 14:46:32', NULL, NULL, 'completado', NULL),
(145, 999, 2, 410, '2026-04-07', NULL, '2026-04-07 14:48:26', NULL, NULL, 'completado', NULL),
(146, 999, 3, 412, '2026-04-07', NULL, '2026-04-07 16:32:25', NULL, NULL, 'completado', NULL),
(147, 999, 3, 418, '2026-04-07', NULL, '2026-04-07 16:47:19', NULL, NULL, 'completado', NULL),
(148, 999, 3, 419, '2026-04-07', NULL, '2026-04-07 16:47:19', NULL, NULL, 'completado', NULL),
(149, 999, 3, 420, '2026-04-07', NULL, '2026-04-07 16:48:31', NULL, NULL, 'completado', NULL),
(151, 3, 3, 407, '2026-04-07', NULL, '2026-04-08 09:25:11', NULL, NULL, 'completado', NULL),
(153, 2, 6, 311, '2026-04-07', NULL, '2026-04-07 16:55:49', NULL, NULL, 'completado', NULL),
(154, 2, 6, 235, '2026-04-07', NULL, '2026-04-07 16:55:49', NULL, NULL, 'completado', NULL),
(155, 999, 1, 435, '2026-04-07', NULL, '2026-04-07 17:50:59', NULL, NULL, 'completado', NULL),
(156, 999, 1, 436, '2026-04-07', NULL, '2026-04-07 17:54:52', NULL, NULL, 'completado', NULL),
(157, 999, 1, 437, '2026-04-07', NULL, '2026-04-07 18:01:12', NULL, NULL, 'completado', NULL),
(158, 999, 1, 438, '2026-04-08', NULL, '2026-04-08 08:56:43', NULL, NULL, 'completado', NULL),
(159, 999, 1, 439, '2026-04-08', NULL, '2026-04-08 09:11:46', NULL, NULL, 'completado', NULL),
(160, 3, 2, 440, '2026-04-08', NULL, NULL, NULL, NULL, 'en_transito', NULL),
(161, 3, 2, 408, '2026-04-08', NULL, NULL, NULL, NULL, 'en_transito', NULL),
(162, 1, 3, 409, '2026-04-08', NULL, NULL, NULL, NULL, 'en_transito', NULL),
(163, 1, 3, 441, '2026-04-08', NULL, NULL, NULL, NULL, 'en_transito', NULL),
(164, 999, 1, 443, '2026-04-08', NULL, '2026-04-08 12:04:58', NULL, NULL, 'completado', NULL),
(165, 2, 6, 489, '2026-04-20', NULL, NULL, NULL, NULL, 'en_transito', NULL);

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
(22, 27, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(23, 28, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(24, 29, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(25, 30, 1, 'la cabaña romatica 1 ', NULL, NULL, NULL, NULL, 'pendiente'),
(26, 31, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(29, 34, 1, 'la primera 2', NULL, NULL, NULL, NULL, 'pendiente'),
(31, 39, 1, 'La cabaña aromática 1', NULL, NULL, NULL, NULL, 'pendiente'),
(32, 40, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(33, 41, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(34, 42, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(35, 43, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(36, 44, 1, 'sol 114', NULL, NULL, NULL, NULL, 'pendiente'),
(37, 45, 1, 'la cima 1', NULL, NULL, NULL, NULL, 'pendiente'),
(40, 48, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(42, 50, 1, 'la cabaña aromatica 2', NULL, NULL, NULL, NULL, 'pendiente'),
(43, 51, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(44, 52, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(45, 53, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(46, 54, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(47, 55, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(48, 56, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(49, 57, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio (Despacho Masivo)', NULL, NULL, NULL, NULL, 'visitado'),
(50, 58, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio (Despacho Masivo)', NULL, NULL, NULL, NULL, 'visitado'),
(51, 59, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(52, 60, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(53, 61, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(54, 62, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(55, 63, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(56, 64, 1, 'la cabañita', NULL, NULL, NULL, NULL, 'pendiente'),
(57, 65, 1, 'la cabañita', NULL, NULL, NULL, NULL, 'pendiente'),
(58, 66, 1, 'la cabañota', NULL, NULL, NULL, NULL, 'pendiente'),
(59, 67, 1, 'la cabañota', NULL, NULL, NULL, NULL, 'pendiente'),
(62, 70, 1, 'ENTREGA EN PATIO: Asignación Logística (Faltantes)', NULL, NULL, NULL, NULL, 'visitado'),
(63, 71, 1, 'ENTREGA EN PATIO: Asignación Logística (Faltantes)', NULL, NULL, NULL, NULL, 'visitado'),
(64, 72, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(65, 73, 1, '1', NULL, NULL, NULL, NULL, 'pendiente'),
(66, 74, 1, '1', NULL, NULL, NULL, NULL, 'pendiente'),
(67, 75, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(68, 76, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(69, 77, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(70, 78, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(71, 79, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(72, 80, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(108, 116, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(109, 117, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(117, 125, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(118, 126, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, '2026-04-02 10:01:03', 'visitado'),
(119, 127, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, '2026-04-06 11:03:45', 'visitado'),
(124, 132, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(125, 133, 1, 'ENTREGA EN PATIO: Asignación Logística (Faltantes)', NULL, NULL, NULL, NULL, 'visitado'),
(126, 134, 1, 'ENTREGA EN PATIO: Asignación Logística (Faltantes)', NULL, NULL, NULL, NULL, 'visitado'),
(127, 135, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio (Despacho Masivo)', NULL, NULL, NULL, NULL, 'visitado'),
(128, 136, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio (Despacho Masivo)', NULL, NULL, NULL, NULL, 'visitado'),
(131, 139, 1, 'ENTREGA EN PATIO: Asignación Logística (Faltantes)', NULL, NULL, NULL, NULL, 'visitado'),
(132, 140, 1, 'ENTREGA EN PATIO: Asignación Logística (Faltantes)', NULL, NULL, NULL, NULL, 'visitado'),
(133, 141, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(134, 142, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(135, 143, 1, 'cementos fortaleza centro', NULL, NULL, NULL, NULL, 'pendiente'),
(137, 145, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(138, 146, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(139, 147, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio (Despacho Masivo)', NULL, NULL, NULL, NULL, 'visitado'),
(140, 148, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio (Despacho Masivo)', NULL, NULL, NULL, NULL, 'visitado'),
(141, 149, 1, 'ENTREGA EN PATIO: Entrega Directa en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(143, 151, 1, 'La cabaña del abuelo 01', NULL, NULL, NULL, NULL, 'pendiente'),
(145, 153, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(146, 154, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(147, 155, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(148, 156, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(149, 157, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(150, 158, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(151, 159, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(152, 160, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(153, 161, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(154, 162, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(155, 163, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente'),
(156, 164, 1, 'PATIO: Entrega en Patio', NULL, NULL, NULL, NULL, 'visitado'),
(157, 165, 1, 'VENTAS DE MOSTRADOR', NULL, NULL, NULL, NULL, 'pendiente');

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
(8, 10, 1, 'Ayudante'),
(9, 29, 1, 'Ayudante'),
(12, 39, 1, 'Ayudante'),
(13, 40, 1, 'Ayudante'),
(15, 51, 6, 'Ayudante'),
(16, 52, 6, 'Ayudante'),
(17, 53, 6, 'Ayudante'),
(18, 77, 6, 'Ayudante'),
(19, 78, 6, 'Ayudante'),
(20, 79, 1, 'Ayudante'),
(21, 80, 1, 'Ayudante'),
(26, 125, 6, 'Ayudante'),
(27, 141, 6, 'Ayudante'),
(28, 142, 2, 'Ayudante'),
(29, 145, 2, 'Ayudante'),
(30, 145, 3, 'Ayudante'),
(31, 146, 6, 'Ayudante'),
(32, 146, 1, 'Ayudante'),
(33, 155, 6, 'Ayudante'),
(34, 156, 6, 'Ayudante'),
(35, 157, 6, 'Ayudante'),
(36, 158, 6, 'Ayudante'),
(37, 159, 6, 'Ayudante'),
(38, 164, 6, 'Ayudante'),
(39, 164, 1, 'Ayudante');

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
(1, 'Kenwork', '1234567', '123456789', 2016, 1200.00, 'en_ruta', 1, 2),
(2, 'Freightliner Cascadia', '12345678', '123456789', 2021, 9000.00, 'en_ruta', 1, 1),
(3, 'jeap', '123456789', '123456789', 2022, 1.00, 'en_ruta', 1, 2),
(999, 'MOSTRADOR / PATIO', 'CLIENTE', 'ENTREGA-DIRECTA', 2026, 0.00, 'disponible', 1, NULL),
(1000, 'chevi', '123456', '123456789', 2010, 1500.00, 'disponible', 1, 3);

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
(2, 'juan', 'juan', '$2y$10$7wQp8D.mUvvpuRviPO6G0.8chLmuwjP.ckbkCTaFEjEMBjEgDLHVW', 5, 2, 1, '2026-02-28 14:38:54'),
(3, 'casa', 'casa', '$2y$10$GSEt/ZVPPLDwQrPY4Ams8eTS1z27IFxtsFkH9kgVtUPTftcrnKhsC', 5, 1, 1, '2026-03-02 22:31:17'),
(4, 'JavierTrabajador', 'JavierTrabajador', '$2y$10$LKFr4og.uGpUlL7PRedxHe5gAuIctxgMAbG/qkVc4Ons9XPh5eR6a', 6, 1, 1, '2026-03-25 18:27:21'),
(5, 'vero', 'Vero', '$2y$10$uYXSSRCAtl9qn8K1s0OBMudqtmKxDXgUt4DDCbBDkkaQhwjCd93Z2', 2, 3, 1, '2026-03-26 20:08:52'),
(6, 'andrea', 'Andrea', '$2y$10$JFwWXOQ4KxkivGA.gra6DOIDcVP7yZLfiaMFboAPe6rw/gaWaBTVi', 2, 4, 1, '2026-03-26 20:09:14');

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
(20, 'V-260314151546', 1, 1, 3, '2026-03-14 15:15:46', 20020.00, 0.00, 20020.00, 'pagado', 'parcial', 'activa', ''),
(21, 'V-260317105027', 1, 1, 3, '2026-03-17 10:50:27', 8697.00, 0.00, 8697.00, 'pagado', 'entregado', 'cancelada', ''),
(22, 'V-260317105320', 1, 1, 1, '2026-03-17 10:53:20', 2697.00, 0.00, 2697.00, 'pagado', 'entregado', 'activa', ''),
(23, 'V-260317114254', 2, 2, 2, '2026-03-17 11:42:54', 3000.00, 0.00, 3000.00, 'pagado', 'entregado', 'activa', ''),
(24, 'V-260317125528', 2, 2, 2, '2026-03-17 12:55:28', 1600.00, 0.00, 1600.00, 'pagado', 'parcial', 'activa', ''),
(25, 'V-260317130948', 2, 2, 2, '2026-03-17 13:09:48', 50000.00, 0.00, 50000.00, 'pagado', 'entregado', 'activa', ''),
(26, 'V-260319160155', 11, 1, 1, '2026-03-19 16:01:55', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(27, 'V-260320175122', 1, 4, 1, '2026-03-20 17:51:22', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(28, 'V-260321113425', 1, 1, 1, '2026-03-21 11:34:25', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(29, 'V-260321113518', 1, 1, 1, '2026-03-21 11:35:18', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(30, 'V-260321214447', 1, 1, 1, '2026-03-21 21:44:47', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(31, 'V-260323090339', 1, 1, 1, '2026-03-23 09:03:39', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(32, 'V-260323195221', 1, 1, 1, '2026-03-23 19:52:21', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(33, 'V-260324173739', 1, 1, 1, '2026-03-24 17:37:39', 4320.00, 0.00, 4320.00, 'pendiente', 'entregado', 'activa', ''),
(34, 'V-34', 1, 1, 1, '2026-03-25 12:00:40', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(35, 'V-35', 1, 2, 1, '2026-03-26 10:13:57', 3000.00, 0.00, 3000.00, 'pagado', 'entregado', 'activa', ''),
(36, 'V-36', 1, 1, 1, '2026-03-26 10:15:14', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(37, 'V-37', 1, 1, 1, '2026-03-26 10:18:11', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(38, 'V-38', 1, 1, 1, '2026-03-26 10:23:02', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(39, 'V-39', 1, 1, 1, '2026-03-26 10:23:55', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(40, 'V-40', 13, 1, 1, '2026-03-26 11:06:59', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(41, 'V-41', 13, 1, 1, '2026-03-26 12:43:04', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(42, 'V-42', 10, 1, 3, '2026-03-26 13:24:43', 4806.00, 0.00, 4806.00, 'pendiente', 'entregado', 'cancelada', ''),
(43, 'V-43', 1, 1, 3, '2026-03-26 13:31:59', 120.00, 0.00, 120.00, 'pagado', 'entregado', 'activa', ''),
(44, 'V-44', 1, 1, 3, '2026-03-26 13:33:54', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(45, 'V-45', 1, 1, 3, '2026-03-26 13:36:43', 120.00, 0.00, 120.00, 'pagado', 'entregado', 'activa', ''),
(46, 'V-46', 1, 3, 1, '2026-03-26 13:43:09', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(47, 'V-47', 1, 3, 1, '2026-03-26 13:43:38', 3.00, 0.00, 3.00, 'pagado', 'entregado', 'activa', ''),
(48, 'V-48', 1, 1, 1, '2026-03-26 16:24:31', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'cancelada', ''),
(49, 'V-49', 1, 1, 1, '2026-03-26 16:27:37', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(50, 'V-50', 1, 1, 1, '2026-03-26 16:42:27', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(51, 'V-51', 1, 1, 1, '2026-03-26 16:44:44', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(52, 'V-52', 1, 1, 1, '2026-03-26 16:46:35', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(53, 'V-53', 1, 1, 1, '2026-03-26 16:52:00', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(54, 'V-54', 1, 1, 1, '2026-03-26 17:01:11', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(55, 'V-55', 1, 1, 1, '2026-03-26 17:17:13', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(56, 'V-56', 1, 1, 1, '2026-03-26 17:23:17', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(57, 'V-57', 1, 1, 1, '2026-03-26 17:23:58', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(58, 'V-58', 1, 1, 1, '2026-03-26 17:25:55', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(59, 'V-59', 1, 1, 1, '2026-03-26 17:27:31', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(60, 'V-60', 1, 1, 1, '2026-03-26 17:55:47', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(61, 'V-61', 1, 1, 1, '2026-03-27 09:22:00', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(62, 'V-62', 1, 1, 1, '2026-03-27 09:28:20', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(63, 'V-63', 1, 1, 1, '2026-03-27 09:30:28', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(64, 'V-64', 1, 1, 1, '2026-03-27 09:33:19', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(65, 'V-65', 1, 1, 1, '2026-03-27 09:42:27', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(66, 'V-66', 1, 1, 1, '2026-03-27 09:43:53', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(67, 'V-67', 1, 1, 1, '2026-03-27 10:55:57', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(68, 'V-68', 1, 1, 1, '2026-03-27 10:58:08', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(69, 'V-69', 1, 1, 1, '2026-03-27 11:00:46', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(70, 'V-70', 1, 1, 1, '2026-03-27 11:02:27', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(71, 'V-71', 1, 1, 1, '2026-03-27 11:05:18', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(72, 'V-72', 1, 1, 1, '2026-03-27 11:37:01', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(73, 'V-73', 1, 1, 1, '2026-03-27 12:22:58', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(74, 'V-74', 1, 1, 1, '2026-03-27 12:47:55', 200.00, 0.00, 200.00, 'pagado', 'pendiente', 'cancelada', ''),
(75, 'V-75', 13, 1, 1, '2026-03-27 14:02:01', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(76, 'V-76', 1, 1, 1, '2026-03-28 12:03:10', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(77, 'V-77', 1, 1, 1, '2026-03-28 12:03:47', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'cancelada', ''),
(78, 'V-78', 1, 1, 1, '2026-03-28 12:26:46', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(79, 'V-79', 1, 1, 1, '2026-03-28 12:29:15', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(80, 'V-80', 1, 1, 1, '2026-03-28 12:44:15', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(81, 'V-81', 1, 1, 1, '2026-03-28 12:45:12', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(82, 'V-82', 1, 1, 1, '2026-03-28 12:46:08', 7.80, 0.00, 7.80, 'pagado', 'entregado', 'activa', ''),
(83, 'V-83', 1, 1, 1, '2026-03-28 12:47:58', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(84, 'V-84', 1, 1, 1, '2026-03-28 12:59:49', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(85, 'V-85', 1, 1, 1, '2026-03-28 13:00:46', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(86, 'V-86', 1, 1, 1, '2026-03-28 13:08:03', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(87, 'V-87', 1, 1, 1, '2026-03-28 13:12:47', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(88, 'V-88', 1, 1, 1, '2026-03-28 13:13:30', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(89, 'V-89', 1, 1, 1, '2026-03-28 13:28:54', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(90, 'V-90', 1, 1, 1, '2026-03-28 13:41:55', 24.80, 0.00, 24.80, 'pagado', 'entregado', 'activa', ''),
(91, 'V-91', 1, 1, 1, '2026-03-30 08:30:09', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(92, 'V-92', 1, 1, 1, '2026-03-30 08:31:20', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(93, 'V-93', 1, 1, 1, '2026-03-30 08:50:58', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'activa', ''),
(94, 'V-94', 13, 1, 1, '2026-03-30 14:30:36', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(95, 'V-95', 13, 1, 3, '2026-03-30 14:35:46', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(96, 'V-96', 2, 2, 2, '2026-03-30 15:23:46', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(97, 'V-97', 1, 1, 3, '2026-03-30 15:25:23', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(98, 'V-98', 1, 1, 3, '2026-03-30 15:29:05', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'activa', ''),
(99, 'V-99', 1, 1, 3, '2026-03-30 15:31:48', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(102, 'V-100', 1, 1, 3, '2026-03-30 15:37:58', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(103, 'V-103', 10, 1, 3, '2026-03-30 17:20:48', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(104, 'V-104', 1, 1, 3, '2026-03-30 17:36:53', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(105, 'V-105', 1, 1, 3, '2026-03-30 17:37:26', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(106, 'V-106', 1, 1, 3, '2026-03-30 17:38:06', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(107, 'V-107', 1, 1, 3, '2026-03-30 17:45:09', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(108, 'V-108', 1, 1, 3, '2026-03-30 17:51:21', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(109, 'V-109', 1, 1, 3, '2026-03-30 17:57:26', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(110, 'V-110', 1, 1, 3, '2026-03-30 18:00:36', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(111, 'V-111', 1, 1, 3, '2026-03-31 08:23:57', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(112, 'V-112', 1, 1, 3, '2026-03-31 08:27:23', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(113, 'V-113', 1, 1, 3, '2026-03-31 08:29:40', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(114, 'V-114', 1, 1, 3, '2026-03-31 08:46:19', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(115, 'V-115', 1, 1, 3, '2026-03-31 09:04:15', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(116, 'V-116', 1, 1, 3, '2026-03-31 09:06:46', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(117, 'V-117', 1, 1, 3, '2026-03-31 09:16:23', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(118, 'V-118', 13, 1, 3, '2026-03-31 09:27:12', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(119, 'V-119', 1, 1, 3, '2026-03-31 09:55:39', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(120, 'V-120', 1, 1, 3, '2026-03-31 10:02:10', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(121, 'V-121', 1, 1, 3, '2026-03-31 10:22:03', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(122, 'V-122', 1, 1, 3, '2026-03-31 10:28:06', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(123, 'V-123', 1, 1, 3, '2026-03-31 10:28:45', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(124, 'V-124', 1, 1, 3, '2026-03-31 10:52:32', 43.00, 0.00, 43.00, 'pagado', 'entregado', 'cancelada', ''),
(125, 'V-125', 1, 1, 3, '2026-03-31 10:54:34', 20.00, 0.00, 20.00, 'pagado', 'pendiente', 'activa', ''),
(126, 'V-126', 1, 1, 3, '2026-03-31 10:55:53', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'cancelada', ''),
(127, 'V-127', 1, 1, 3, '2026-03-31 10:56:48', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'cancelada', ''),
(128, 'V-128', 1, 1, 3, '2026-03-31 10:59:11', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'cancelada', ''),
(129, 'V-129', 1, 1, 3, '2026-03-31 11:10:41', 60.00, 0.00, 60.00, 'pagado', 'entregado', 'activa', ''),
(130, 'V-130', 1, 1, 1, '2026-03-31 11:23:15', 140.00, 0.00, 140.00, 'parcial', 'pendiente', 'activa', ''),
(131, 'V-131', 1, 1, 1, '2026-03-31 12:45:59', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(132, 'V-132', 1, 1, 1, '2026-03-31 12:54:12', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(133, 'V-133', 1, 1, 1, '2026-03-31 12:54:33', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(140, 'V-134', 1, 1, 1, '2026-03-31 13:28:17', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(141, 'V-141', 1, 1, 1, '2026-03-31 13:30:33', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(142, 'V-142', 1, 1, 1, '2026-03-31 13:30:57', 60.00, 0.00, 60.00, 'pendiente', 'entregado', 'cancelada', ''),
(143, 'V-143', 1, 1, 1, '2026-03-31 14:28:41', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(144, 'V-144', 1, 1, 1, '2026-03-31 15:26:53', 20.00, 0.00, 20.00, 'parcial', 'entregado', 'activa', ''),
(145, 'V-145', 1, 1, 1, '2026-03-31 15:29:29', 20.00, 0.00, 20.00, 'parcial', 'entregado', 'activa', ''),
(146, 'V-146', 1, 1, 1, '2026-03-31 15:34:29', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(147, 'V-147', 1, 1, 1, '2026-03-31 15:35:40', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(148, 'V-148', 1, 1, 1, '2026-03-31 15:44:55', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(149, 'V-149', 1, 1, 1, '2026-03-31 15:46:09', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(150, 'V-150', 1, 1, 1, '2026-03-31 15:50:37', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(151, 'V-151', 1, 1, 1, '2026-03-31 15:55:55', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(152, 'V-152', 1, 1, 1, '2026-03-31 16:01:25', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(153, 'V-153', 1, 1, 1, '2026-03-31 16:02:17', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'cancelada', ''),
(154, 'V-154', 1, 1, 1, '2026-03-31 16:04:15', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(155, 'V-155', 1, 1, 1, '2026-03-31 16:05:35', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(156, 'V-156', 1, 1, 1, '2026-03-31 16:06:13', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(157, 'V-157', 1, 1, 1, '2026-03-31 16:17:04', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(158, 'V-158', 1, 1, 1, '2026-03-31 16:19:03', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(159, 'V-159', 1, 1, 1, '2026-03-31 16:21:15', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(160, 'V-160', 1, 1, 1, '2026-03-31 16:21:33', 100.00, 0.00, 100.00, 'pagado', 'pendiente', 'activa', ''),
(161, 'V-161', 1, 1, 1, '2026-03-31 16:53:37', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(162, 'V-162', 1, 1, 1, '2026-03-31 16:54:26', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(163, 'V-163', 1, 1, 1, '2026-03-31 16:58:33', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(164, 'V-164', 1, 1, 1, '2026-03-31 17:01:54', 60.00, 0.00, 60.00, 'pagado', 'entregado', 'cancelada', ''),
(165, 'V-165', 1, 1, 1, '2026-03-31 17:35:59', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(166, 'V-166', 1, 1, 1, '2026-03-31 17:36:52', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(167, 'V-167', 1, 1, 1, '2026-03-31 17:38:32', 43.00, 0.00, 43.00, 'pagado', 'entregado', 'cancelada', ''),
(168, 'V-168', 1, 1, 1, '2026-03-31 17:39:05', 23.00, 0.00, 23.00, 'pagado', 'entregado', 'cancelada', ''),
(169, 'V-169', 1, 1, 1, '2026-04-01 08:42:28', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(170, 'V-170', 1, 1, 1, '2026-04-01 09:01:17', 60.00, 0.00, 60.00, 'pendiente', 'entregado', 'cancelada', ''),
(171, 'V-171', 1, 1, 1, '2026-04-01 09:20:45', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(172, 'V-172', 1, 1, 1, '2026-04-01 09:29:02', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(173, 'V-173', 1, 1, 1, '2026-04-01 09:39:09', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(174, 'V-174', 1, 1, 1, '2026-04-01 09:40:23', 40.00, 0.00, 40.00, 'pagado', 'entregado', 'cancelada', ''),
(175, 'V-175', 1, 1, 1, '2026-04-01 09:47:26', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(176, 'V-176', 1, 1, 1, '2026-04-01 09:53:35', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(177, 'V-177', 1, 1, 1, '2026-04-01 10:28:45', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(178, 'V-178', 1, 1, 1, '2026-04-01 10:30:06', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(179, 'V-179', 1, 1, 1, '2026-04-06 12:44:06', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(180, 'V-180', 10, 1, 1, '2026-04-07 09:26:43', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(181, 'V-181', 10, 1, 1, '2026-04-07 09:27:17', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(182, 'V-182', 10, 1, 1, '2026-04-07 09:28:06', 20.00, 0.00, 20.00, 'parcial', 'entregado', 'cancelada', ''),
(183, 'V-183', 1, 1, 1, '2026-04-07 09:37:32', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(184, 'V-184', 1, 1, 1, '2026-04-07 09:38:21', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(185, 'V-185', 1, 1, 1, '2026-04-07 09:42:08', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(186, 'V-186', 1, 1, 1, '2026-04-07 09:48:32', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(187, 'V-187', 1, 1, 1, '2026-04-07 09:57:59', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(188, 'V-188', 1, 1, 1, '2026-04-07 09:58:49', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(189, 'V-189', 1, 1, 1, '2026-04-07 09:59:42', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(190, 'V-190', 1, 1, 1, '2026-04-07 10:29:44', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(191, 'V-191', 1, 1, 1, '2026-04-07 10:31:33', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(192, 'V-192', 1, 1, 1, '2026-04-07 10:32:01', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(193, 'V-193', 1, 1, 1, '2026-04-07 10:34:00', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(194, 'V-194', 1, 1, 1, '2026-04-07 10:35:07', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(195, 'V-195', 1, 1, 1, '2026-04-07 10:35:28', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(196, 'V-196', 1, 1, 1, '2026-04-07 10:38:24', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(197, 'V-197', 1, 1, 1, '2026-04-07 10:43:25', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(198, 'V-198', 1, 1, 1, '2026-04-07 10:55:13', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(199, 'V-199', 1, 1, 1, '2026-04-07 11:07:25', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'cancelada', ''),
(200, 'V-200', 1, 1, 1, '2026-04-07 11:11:09', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(201, 'V-201', 1, 1, 1, '2026-04-07 13:58:06', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(202, 'V-202', 1, 1, 1, '2026-04-07 14:16:48', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(203, 'V-203', 1, 1, 1, '2026-04-07 14:18:39', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(204, 'V-204', 13, 1, 1, '2026-04-07 14:20:02', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(205, 'V-205', 2, 2, 2, '2026-04-07 14:23:14', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(206, 'V-206', 11, 2, 2, '2026-04-07 14:23:41', 200.00, 0.00, 200.00, 'pendiente', 'entregado', 'cancelada', ''),
(207, 'V-207', 11, 2, 2, '2026-04-07 14:24:55', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'cancelada', ''),
(208, 'V-208', 11, 2, 2, '2026-04-07 14:25:53', 200.00, 0.00, 200.00, 'pendiente', 'entregado', 'activa', ''),
(209, 'V-209', 2, 2, 2, '2026-04-07 14:47:06', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(210, 'V-210', 2, 2, 2, '2026-04-07 14:48:08', 300.00, 0.00, 300.00, 'pagado', 'entregado', 'activa', ''),
(211, 'V-211', 2, 2, 2, '2026-04-07 14:48:26', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(212, 'V-212', 1, 1, 3, '2026-04-07 16:29:08', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(213, 'V-213', 1, 1, 3, '2026-04-07 16:32:25', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(214, 'V-214', 1, 1, 3, '2026-04-07 16:46:34', 120.00, 0.00, 120.00, 'pagado', 'entregado', 'activa', ''),
(215, 'V-215', 1, 1, 3, '2026-04-07 16:47:52', 100.00, 0.00, 100.00, 'pagado', 'entregado', 'activa', ''),
(216, 'V-216', 1, 1, 3, '2026-04-07 17:02:16', 700.00, 0.00, 700.00, 'pagado', 'parcial', 'activa', ''),
(217, 'V-217', 1, 1, 3, '2026-04-07 17:04:27', 343.00, 0.00, 343.00, 'pagado', 'parcial', 'activa', ''),
(218, 'V-218', 1, 1, 3, '2026-04-07 17:17:12', 100.00, 0.00, 100.00, 'pagado', 'entregado', 'activa', ''),
(219, 'V-219', 1, 1, 1, '2026-04-07 17:50:59', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(220, 'V-220', 1, 1, 1, '2026-04-07 17:54:52', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(221, 'V-221', 1, 1, 1, '2026-04-07 18:01:12', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'activa', ''),
(222, 'V-222', 1, 1, 1, '2026-04-08 08:56:43', 100.00, 0.00, 100.00, 'pagado', 'entregado', 'activa', ''),
(223, 'V-223', 1, 1, 1, '2026-04-08 09:11:46', 100.00, 0.00, 100.00, 'pagado', 'entregado', 'activa', ''),
(224, 'V-224', 2, 2, 2, '2026-04-08 09:27:08', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(225, 'V-225', 2, 2, 2, '2026-04-08 09:28:47', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(226, 'V-226', 1, 1, 1, '2026-04-08 12:04:58', 100.00, 0.00, 100.00, 'pagado', 'entregado', 'activa', ''),
(227, 'V-227', 1, 1, 1, '2026-04-08 15:00:01', 100.00, 0.00, 100.00, 'pagado', 'entregado', 'activa', ''),
(228, 'V-228', 1, 1, 1, '2026-04-08 15:01:26', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(229, 'V-229', 1, 1, 1, '2026-04-08 16:05:18', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(230, 'V-230', 1, 1, 1, '2026-04-08 16:15:43', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(231, 'V-231', 1, 1, 1, '2026-04-08 16:17:20', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(232, 'V-232', 1, 1, 1, '2026-04-08 16:18:22', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(233, 'V-233', 1, 1, 1, '2026-04-08 16:25:19', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(234, 'V-234', 1, 1, 1, '2026-04-08 16:31:05', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(235, 'V-235', 1, 1, 1, '2026-04-08 16:32:07', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(236, 'V-236', 1, 1, 1, '2026-04-08 17:04:10', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(237, 'V-237', 1, 1, 1, '2026-04-08 17:04:58', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(238, 'V-238', 1, 1, 1, '2026-04-08 17:11:17', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(239, 'V-239', 1, 1, 1, '2026-04-08 17:13:58', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(240, 'V-240', 1, 1, 1, '2026-04-08 17:15:26', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(241, 'V-241', 10, 1, 1, '2026-04-08 17:16:51', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(242, 'V-242', 1, 1, 1, '2026-04-08 17:17:59', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(243, 'V-243', 2, 2, 2, '2026-04-09 11:36:56', 200.00, 0.00, 200.00, 'pagado', 'entregado', 'activa', ''),
(244, 'V-244', 1, 1, 1, '2026-04-09 11:38:03', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(245, 'V-245', 1, 1, 1, '2026-04-10 11:47:44', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(246, 'V-246', 1, 1, 1, '2026-04-10 13:37:17', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(247, 'V-247', 1, 1, 1, '2026-04-10 14:17:12', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(248, 'V-248', 1, 1, 1, '2026-04-10 14:18:39', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(249, 'V-249', 1, 1, 1, '2026-04-10 14:19:14', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(250, 'V-250', 1, 1, 1, '2026-04-11 09:50:18', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(251, 'V-251', 1, 1, 1, '2026-04-11 12:48:54', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(252, 'V-252', 1, 1, 1, '2026-04-11 12:52:17', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(253, 'V-253', 1, 1, 1, '2026-04-11 12:54:29', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(254, 'V-254', 1, 1, 1, '2026-04-13 08:36:08', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(255, 'V-255', 1, 1, 1, '2026-04-13 08:36:34', 200.00, 0.00, 200.00, 'pendiente', 'pendiente', 'activa', ''),
(256, 'V-256', 1, 1, 1, '2026-04-13 08:37:04', 216.00, 0.00, 216.00, 'pendiente', 'entregado', 'activa', ''),
(257, 'V-257', 1, 2, 1, '2026-04-13 08:37:19', 0.00, 0.00, 0.00, 'pagado', 'entregado', 'cancelada', ''),
(258, 'V-258', 1, 2, 1, '2026-04-13 08:38:20', 10.00, 0.00, 10.00, 'pagado', 'entregado', 'activa', ''),
(259, 'V-259', 1, 1, 1, '2026-04-14 22:50:18', 20.00, 0.00, 20.00, 'pendiente', 'entregado', 'activa', ''),
(260, 'V-260', 1, 1, 1, '2026-04-14 23:23:21', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(261, 'V-261', 1, 1, 1, '2026-04-15 09:55:42', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(262, 'V-262', 1, 1, 1, '2026-04-15 10:08:06', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(263, 'V-263', 1, 1, 1, '2026-04-20 17:53:18', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', ''),
(264, 'V-264', 1, 1, 3, '2026-04-21 12:33:18', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'cancelada', ''),
(265, 'V-265', 1, 1, 3, '2026-04-21 12:35:10', 20.00, 0.00, 20.00, 'pagado', 'entregado', 'activa', '');

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
-- Indices de la tabla `cajas_fuertes`
--
ALTER TABLE `cajas_fuertes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `capital_categorias`
--
ALTER TABLE `capital_categorias`
  ADD PRIMARY KEY (`id`);

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
-- Indices de la tabla `clientes_saldos`
--
ALTER TABLE `clientes_saldos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_cliente_unico` (`cliente_id`),
  ADD KEY `idx_ultima_venta` (`ultima_venta_id`);

--
-- Indices de la tabla `clientes_saldos_log`
--
ALTER TABLE `clientes_saldos_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_log_cliente` (`cliente_id`),
  ADD KEY `idx_venta_rastreo` (`venta_id`);

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
-- Indices de la tabla `confirmacion_reparto_viaje`
--
ALTER TABLE `confirmacion_reparto_viaje`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_movimiento_2` (`id_movimiento`),
  ADD UNIQUE KEY `id_movimiento_3` (`id_movimiento`),
  ADD UNIQUE KEY `id_movimiento_4` (`id_movimiento`),
  ADD UNIQUE KEY `id_movimiento_5` (`id_movimiento`),
  ADD KEY `id_movimiento` (`id_movimiento`),
  ADD KEY `id_venta` (`id_venta`),
  ADD KEY `trabajador_id` (`trabajador_id`);

--
-- Indices de la tabla `corte_de_caja`
--
ALTER TABLE `corte_de_caja`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fecha_corte` (`fecha_corte`,`almacen_id`),
  ADD UNIQUE KEY `unique_corte` (`fecha_corte`,`almacen_id`);

--
-- Indices de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  ADD PRIMARY KEY (`id_cuenta`),
  ADD KEY `id_almacen` (`id_almacen`);

--
-- Indices de la tabla `cuentas_por_pagar`
--
ALTER TABLE `cuentas_por_pagar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_almacen` (`id_almacen`);

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
  ADD KEY `usuario_registra_id` (`usuario_registra_id`),
  ADD KEY `fk_gasto_categoria` (`categoria_id`);

--
-- Indices de la tabla `gastos_categorias`
--
ALTER TABLE `gastos_categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `historial_capital`
--
ALTER TABLE `historial_capital`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cap_categoria` (`categoria_id`),
  ADD KEY `fk_cap_alm_destino` (`almacen_destino_id`),
  ADD KEY `fk_cap_user_reg` (`usuario_registro_id`),
  ADD KEY `fk_cap_user_aut` (`usuario_autoriza_id`),
  ADD KEY `idx_fecha_mov` (`fecha_movimiento`),
  ADD KEY `idx_alm_origen` (`almacen_origen_id`),
  ADD KEY `fk_cap_caja_fuerte` (`caja_fuerte_destino_id`),
  ADD KEY `fk_cap_banco` (`banco_destino_id`);

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
-- Indices de la tabla `pagos_cuentas_por_pagar`
--
ALTER TABLE `pagos_cuentas_por_pagar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_almacen` (`almacen_id`),
  ADD KEY `idx_proveedor` (`proveedor_id`),
  ADD KEY `idx_compra` (`compra_id`),
  ADD KEY `idx_fecha` (`fecha_pago`);

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
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_prestamo_trabajador` (`trabajador_id`);

--
-- Indices de la tabla `prestamos_abonos`
--
ALTER TABLE `prestamos_abonos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_abono_prestamo` (`prestamo_id`);

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
  ADD KEY `fk_tripulante_trabajador` (`usuario_id`);

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
-- AUTO_INCREMENT de la tabla `cajas_fuertes`
--
ALTER TABLE `cajas_fuertes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `capital_categorias`
--
ALTER TABLE `capital_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

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
-- AUTO_INCREMENT de la tabla `clientes_saldos`
--
ALTER TABLE `clientes_saldos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=289;

--
-- AUTO_INCREMENT de la tabla `clientes_saldos_log`
--
ALTER TABLE `clientes_saldos_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=268;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `config_transmutaciones`
--
ALTER TABLE `config_transmutaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `confirmacion_reparto_viaje`
--
ALTER TABLE `confirmacion_reparto_viaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `corte_de_caja`
--
ALTER TABLE `corte_de_caja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT de la tabla `cuentas_bancarias`
--
ALTER TABLE `cuentas_bancarias`
  MODIFY `id_cuenta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cuentas_por_pagar`
--
ALTER TABLE `cuentas_por_pagar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT de la tabla `detalle_entrega`
--
ALTER TABLE `detalle_entrega`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=381;

--
-- AUTO_INCREMENT de la tabla `detalle_gasto`
--
ALTER TABLE `detalle_gasto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido_vendedor`
--
ALTER TABLE `detalle_pedido_vendedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `detalle_solicitud_compra`
--
ALTER TABLE `detalle_solicitud_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `detalle_traspaso`
--
ALTER TABLE `detalle_traspaso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=365;

--
-- AUTO_INCREMENT de la tabla `entregas_venta`
--
ALTER TABLE `entregas_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=353;

--
-- AUTO_INCREMENT de la tabla `faltantes_ingreso`
--
ALTER TABLE `faltantes_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT de la tabla `gastos_categorias`
--
ALTER TABLE `gastos_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `historial_capital`
--
ALTER TABLE `historial_capital`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=322;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT de la tabla `lotes_ingresos_detalle`
--
ALTER TABLE `lotes_ingresos_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `lotes_movimientos_salida`
--
ALTER TABLE `lotes_movimientos_salida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT de la tabla `lotes_stock`
--
ALTER TABLE `lotes_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=238;

--
-- AUTO_INCREMENT de la tabla `mermas`
--
ALTER TABLE `mermas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=544;

--
-- AUTO_INCREMENT de la tabla `pagos_cuentas_por_pagar`
--
ALTER TABLE `pagos_cuentas_por_pagar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `pedidos_vendedores`
--
ALTER TABLE `pedidos_vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2820;

--
-- AUTO_INCREMENT de la tabla `precios_producto`
--
ALTER TABLE `precios_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=199;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `prestamos_abonos`
--
ALTER TABLE `prestamos_abonos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `registro_salida_lotes`
--
ALTER TABLE `registro_salida_lotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `solicitudes_compra`
--
ALTER TABLE `solicitudes_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `solicitudes_pedidos`
--
ALTER TABLE `solicitudes_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `transmutaciones`
--
ALTER TABLE `transmutaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `transmutacion_detalle`
--
ALTER TABLE `transmutacion_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `transporte_consolidacion`
--
ALTER TABLE `transporte_consolidacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT de la tabla `transporte_repartos_maestro`
--
ALTER TABLE `transporte_repartos_maestro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT de la tabla `transporte_rutas_puntos`
--
ALTER TABLE `transporte_rutas_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT de la tabla `transporte_tripulantes_detalle`
--
ALTER TABLE `transporte_tripulantes_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `transporte_vehiculos`
--
ALTER TABLE `transporte_vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

--
-- AUTO_INCREMENT de la tabla `traspasos`
--
ALTER TABLE `traspasos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=266;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `fk_cliente_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `clientes_saldos`
--
ALTER TABLE `clientes_saldos`
  ADD CONSTRAINT `fk_saldos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `cuentas_por_pagar`
--
ALTER TABLE `cuentas_por_pagar`
  ADD CONSTRAINT `cuentas_por_pagar_ibfk_1` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id`);

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
  ADD CONSTRAINT `fk_gasto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `gastos_categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gasto_usuario` FOREIGN KEY (`usuario_registra_id`) REFERENCES `usuarios` (`id`);

--
-- Filtros para la tabla `historial_capital`
--
ALTER TABLE `historial_capital`
  ADD CONSTRAINT `fk_cap_alm_destino` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_cap_alm_origen` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cap_banco` FOREIGN KEY (`banco_destino_id`) REFERENCES `cuentas_bancarias` (`id_cuenta`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cap_caja_fuerte` FOREIGN KEY (`caja_fuerte_destino_id`) REFERENCES `cajas_fuertes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cap_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `capital_categorias` (`id`),
  ADD CONSTRAINT `fk_cap_user_aut` FOREIGN KEY (`usuario_autoriza_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_cap_user_reg` FOREIGN KEY (`usuario_registro_id`) REFERENCES `usuarios` (`id`);

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
-- Filtros para la tabla `pagos_cuentas_por_pagar`
--
ALTER TABLE `pagos_cuentas_por_pagar`
  ADD CONSTRAINT `fk_pago_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pago_compra` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_pago_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`) ON DELETE CASCADE;

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
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `fk_prestamo_trabajador` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`);

--
-- Filtros para la tabla `prestamos_abonos`
--
ALTER TABLE `prestamos_abonos`
  ADD CONSTRAINT `fk_abono_prestamo` FOREIGN KEY (`prestamo_id`) REFERENCES `prestamos` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_reparto_encargado` FOREIGN KEY (`usuario_encargado_id`) REFERENCES `trabajadores` (`id`) ON UPDATE CASCADE,
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
  ADD CONSTRAINT `fk_trip_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_tripulante_trabajador` FOREIGN KEY (`usuario_id`) REFERENCES `trabajadores` (`id`) ON UPDATE CASCADE;

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