-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 21-03-2026 a las 20:58:27
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compras`
--
ALTER TABLE `compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `config_transmutaciones`
--
ALTER TABLE `config_transmutaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_compra`
--
ALTER TABLE `detalle_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_entrega`
--
ALTER TABLE `detalle_entrega`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_gasto`
--
ALTER TABLE `detalle_gasto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_pedido_vendedor`
--
ALTER TABLE `detalle_pedido_vendedor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_solicitud_compra`
--
ALTER TABLE `detalle_solicitud_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_traspaso`
--
ALTER TABLE `detalle_traspaso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `detalle_venta`
--
ALTER TABLE `detalle_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `entregas_venta`
--
ALTER TABLE `entregas_venta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `faltantes_ingreso`
--
ALTER TABLE `faltantes_ingreso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `gastos`
--
ALTER TABLE `gastos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `historial_pagos`
--
ALTER TABLE `historial_pagos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes_ingresos_detalle`
--
ALTER TABLE `lotes_ingresos_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes_movimientos_salida`
--
ALTER TABLE `lotes_movimientos_salida`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `lotes_stock`
--
ALTER TABLE `lotes_stock`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `mermas`
--
ALTER TABLE `mermas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos_vendedores`
--
ALTER TABLE `pedidos_vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `permisos_roles`
--
ALTER TABLE `permisos_roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `precios_producto`
--
ALTER TABLE `precios_producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `registro_salida_lotes`
--
ALTER TABLE `registro_salida_lotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_compra`
--
ALTER TABLE `solicitudes_compra`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `solicitudes_pedidos`
--
ALTER TABLE `solicitudes_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transmutaciones`
--
ALTER TABLE `transmutaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transmutacion_detalle`
--
ALTER TABLE `transmutacion_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transporte_consolidacion`
--
ALTER TABLE `transporte_consolidacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transporte_repartos_maestro`
--
ALTER TABLE `transporte_repartos_maestro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transporte_rutas_puntos`
--
ALTER TABLE `transporte_rutas_puntos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transporte_tripulantes_detalle`
--
ALTER TABLE `transporte_tripulantes_detalle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `transporte_vehiculos`
--
ALTER TABLE `transporte_vehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `traspasos`
--
ALTER TABLE `traspasos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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