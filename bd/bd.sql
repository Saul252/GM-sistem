-- ============================================================
-- Base de datos: sistema_almacenes
-- SQL limpio: CREATE TABLE con todos los parámetros inline
-- Generado: 2026-04-11
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";
SET NAMES utf8mb4;

-- ============================================================

CREATE TABLE `almacenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `hora_cierre_programada` time DEFAULT '18:00:00',
  `ubicacion` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `modulos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `identificador` varchar(50) NOT NULL,
  `icono` varchar(50) DEFAULT 'fas fa-box',
  `orden` int(11) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `identificador` (`identificador`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `gastos_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `capital_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `tipo_operacion` enum('entrada','salida','traspaso') NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estatus` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `almacen_id` int(11) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `rol_id` (`rol_id`),
  KEY `fk_usuario_almacen` (`almacen_id`),
  CONSTRAINT `fk_usuario_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `permisos_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) NOT NULL,
  `modulo` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rol_modulo` (`rol_id`,`modulo`),
  CONSTRAINT `fk_permisos_rol_db` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `rol` enum('administrador','vendedor','chofer','almacenista','cargador') NOT NULL DEFAULT 'vendedor',
  `estado` enum('activo','inactivo','vacaciones','en_ruta') NOT NULL DEFAULT 'activo',
  `almacen_id` int(11) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_trabajador_almacen` (`almacen_id`),
  CONSTRAINT `fk_trabajador_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `categoria_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `fk_categoria` (`categoria_id`),
  CONSTRAINT `fk_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_comercial` varchar(150) NOT NULL,
  `razon_social` varchar(200) DEFAULT NULL,
  `rfc` varchar(13) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `api_token` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_api_token` (`api_token`),
  UNIQUE KEY `rfc_almacen_unique` (`rfc`,`almacen_id`),
  KEY `idx_cliente_almacen` (`almacen_id`),
  CONSTRAINT `fk_cliente_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `clientes_saldos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `saldo_a_favor` decimal(12,2) DEFAULT 0.00 COMMENT 'Dinero real que el cliente tiene para usar',
  `saldo_en_contra` decimal(12,2) DEFAULT 0.00 COMMENT 'Deuda pendiente del cliente',
  `ultima_venta_id` int(11) DEFAULT NULL,
  `ultima_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_cliente_unico` (`cliente_id`),
  KEY `idx_ultima_venta` (`ultima_venta_id`),
  CONSTRAINT `fk_saldos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `clientes_saldos_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `usuario_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_log_cliente` (`cliente_id`),
  KEY `idx_venta_rastreo` (`venta_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Monto total de la compra vs lo que pagó en ese instante';

-- ============================================================

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `almacen_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `stock` decimal(10,2) DEFAULT 0.00,
  `stock_minimo` decimal(10,2) DEFAULT 0.00,
  `stock_maximo` decimal(10,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  UNIQUE KEY `almacen_id` (`almacen_id`,`producto_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `inventario_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `precios_producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `precio_minorista` decimal(10,2) NOT NULL,
  `precio_mayorista` decimal(10,2) NOT NULL,
  `precio_distribuidor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `producto_id` (`producto_id`,`almacen_id`),
  KEY `almacen_id` (`almacen_id`),
  CONSTRAINT `precios_producto_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `precios_producto_ibfk_2` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `ventas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `almacen_id` (`almacen_id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `id_cliente` (`id_cliente`),
  CONSTRAINT `ventas_ibfk_1` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `ventas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `ventas_ibfk_3` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `detalle_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `cantidad_entregada` decimal(10,2) NOT NULL DEFAULT 0.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `tipo_precio` enum('minorista','mayorista','distribuidor') NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `estado_entrega` enum('pendiente','parcial','entregado') DEFAULT 'pendiente',
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `detalle_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_venta_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `entregas_venta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `entregas_venta_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `entregas_venta_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `detalle_entrega` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entrega_id` int(11) NOT NULL,
  `detalle_venta_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entrega_id` (`entrega_id`),
  KEY `detalle_venta_id` (`detalle_venta_id`),
  CONSTRAINT `detalle_entrega_ibfk_1` FOREIGN KEY (`entrega_id`) REFERENCES `entregas_venta` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_entrega_ibfk_2` FOREIGN KEY (`detalle_venta_id`) REFERENCES `detalle_venta` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `historial_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venta_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `saldo_favor` decimal(10,2) DEFAULT 0.00,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `fecha` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `venta_id` (`venta_id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `historial_pagos_ibfk_1` FOREIGN KEY (`venta_id`) REFERENCES `ventas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `historial_pagos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `compras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `almacen_id` (`almacen_id`),
  KEY `usuario_registra_id` (`usuario_registra_id`),
  CONSTRAINT `compras_ibfk_1` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `compras_ibfk_2` FOREIGN KEY (`usuario_registra_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `detalle_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `unidad_compra` varchar(20) DEFAULT 'PZA',
  `factor_conversion` decimal(10,2) DEFAULT 1.00,
  `cantidad_faltante` decimal(10,2) DEFAULT 0.00,
  `precio_unitario` decimal(10,2) NOT NULL,
  `estado_entrega` enum('completo','incompleto','ajustado') DEFAULT 'completo',
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_id` (`compra_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `detalle_compra_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_compra_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `faltantes_ingreso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad_pendiente` decimal(10,2) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `compra_id` (`compra_id`),
  CONSTRAINT `faltantes_ingreso_ibfk_1` FOREIGN KEY (`compra_id`) REFERENCES `compras` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `lotes_stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `codigo_lote` varchar(50) NOT NULL COMMENT 'Código único para rastrear este grupo de productos',
  `cantidad_inicial` decimal(10,2) NOT NULL COMMENT 'Lo que entró originalmente',
  `cantidad_actual` decimal(10,2) NOT NULL COMMENT 'Lo que queda disponible para entregar',
  `precio_compra_unitario` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Costo de adquisición real de este lote',
  `fecha_ingreso` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_lote` enum('activo','agotado','bloqueado') DEFAULT 'activo',
  PRIMARY KEY (`id`),
  KEY `idx_lote_producto` (`producto_id`),
  KEY `idx_lote_almacen` (`almacen_id`),
  CONSTRAINT `fk_lotes_stock_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `fk_lotes_stock_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `lotes_ingresos_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lote_id` int(11) NOT NULL,
  `detalle_compra_id` int(11) NOT NULL,
  `cantidad_recibida` decimal(10,2) NOT NULL,
  `costo_aplicado` decimal(10,2) NOT NULL COMMENT 'Costo pactado en la compra para este lote',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_ingreso_lote` (`lote_id`),
  KEY `idx_ingreso_compra` (`detalle_compra_id`),
  CONSTRAINT `fk_ingreso_detalle_compra` FOREIGN KEY (`detalle_compra_id`) REFERENCES `detalle_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ingreso_lote_ref` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `lotes_movimientos_salida` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lote_id` int(11) NOT NULL,
  `entrega_venta_id` int(11) NOT NULL COMMENT 'Referencia a entregas_venta',
  `detalle_venta_id` int(11) NOT NULL COMMENT 'Referencia al producto vendido originalmente',
  `cantidad_salida` decimal(10,2) NOT NULL,
  `costo_compra_historico` decimal(10,2) NOT NULL COMMENT 'Lo que nos costó a nosotros el lote',
  `precio_venta_pactado` decimal(10,2) NOT NULL COMMENT 'A cuánto se le vendió al cliente',
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_salida_lote` (`lote_id`),
  KEY `idx_salida_entrega` (`entrega_venta_id`),
  KEY `idx_salida_detalle_venta` (`detalle_venta_id`),
  CONSTRAINT `fk_salida_detalle_venta` FOREIGN KEY (`detalle_venta_id`) REFERENCES `detalle_venta` (`id`),
  CONSTRAINT `fk_salida_entrega_venta` FOREIGN KEY (`entrega_venta_id`) REFERENCES `entregas_venta` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_salida_lote_ref` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `producto_id` (`producto_id`),
  KEY `almacen_origen_id` (`almacen_origen_id`),
  KEY `almacen_destino_id` (`almacen_destino_id`),
  KEY `usuario_registra_id` (`usuario_registra_id`),
  KEY `usuario_autoriza_id` (`usuario_autoriza_id`),
  KEY `usuario_envia_id` (`usuario_envia_id`),
  KEY `usuario_recibe_id` (`usuario_recibe_id`),
  CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `movimientos_ibfk_2` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `movimientos_ibfk_3` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `movimientos_ibfk_4` FOREIGN KEY (`usuario_registra_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `movimientos_ibfk_5` FOREIGN KEY (`usuario_autoriza_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `movimientos_ibfk_6` FOREIGN KEY (`usuario_envia_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `movimientos_ibfk_7` FOREIGN KEY (`usuario_recibe_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `mermas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movimiento_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `tipo_merma` enum('robo','daño','caducidad','otro') DEFAULT 'otro',
  `responsable_declaracion` varchar(150) DEFAULT NULL,
  `descripcion_suceso` text NOT NULL,
  `fecha_reporte` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_merma_movimiento` (`movimiento_id`),
  KEY `fk_merma_almacen` (`almacen_id`),
  KEY `fk_merma_producto` (`producto_id`),
  KEY `fk_merma_lote` (`lote_id`),
  CONSTRAINT `fk_merma_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`),
  CONSTRAINT `fk_merma_movimiento` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `registro_salida_lotes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `movimiento_id` int(11) NOT NULL,
  `usuario_patio_id` int(11) NOT NULL,
  `fecha_despacho` timestamp NOT NULL DEFAULT current_timestamp(),
  `usuario_despacho_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_movimiento_unico` (`movimiento_id`),
  KEY `fk_reg_salida_user` (`usuario_patio_id`),
  CONSTRAINT `fk_reg_salida_mov` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`),
  CONSTRAINT `fk_reg_salida_user` FOREIGN KEY (`usuario_patio_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `traspasos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `almacen_origen_id` int(11) NOT NULL,
  `almacen_destino_id` int(11) NOT NULL,
  `usuario_solicita_id` int(11) NOT NULL,
  `usuario_autoriza_id` int(11) DEFAULT NULL,
  `estado` enum('pendiente','aprobado','rechazado','cancelado') DEFAULT 'pendiente',
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_autorizacion` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `almacen_origen_id` (`almacen_origen_id`),
  KEY `almacen_destino_id` (`almacen_destino_id`),
  KEY `usuario_solicita_id` (`usuario_solicita_id`),
  KEY `usuario_autoriza_id` (`usuario_autoriza_id`),
  CONSTRAINT `traspasos_ibfk_1` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `traspasos_ibfk_2` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `traspasos_ibfk_3` FOREIGN KEY (`usuario_solicita_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `traspasos_ibfk_4` FOREIGN KEY (`usuario_autoriza_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `detalle_traspaso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `traspaso_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `traspaso_id` (`traspaso_id`),
  KEY `producto_id` (`producto_id`),
  CONSTRAINT `detalle_traspaso_ibfk_1` FOREIGN KEY (`traspaso_id`) REFERENCES `traspasos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `detalle_traspaso_ibfk_2` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `gastos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `almacen_id` (`almacen_id`),
  KEY `usuario_registra_id` (`usuario_registra_id`),
  KEY `fk_gasto_categoria` (`categoria_id`),
  CONSTRAINT `fk_gasto_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `fk_gasto_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `gastos_categorias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_gasto_usuario` FOREIGN KEY (`usuario_registra_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `detalle_gasto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gasto_id` int(11) NOT NULL,
  `descripcion` varchar(255) NOT NULL COMMENT '¿En qué se gastó?',
  `cantidad` decimal(10,2) NOT NULL DEFAULT 1.00,
  `precio_unitario` decimal(12,2) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `gasto_id` (`gasto_id`),
  CONSTRAINT `fk_detalle_gasto_cabecera` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `historial_capital` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `almacen_origen_id` int(11) NOT NULL,
  `almacen_destino_id` int(11) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `metodo_pago` enum('Efectivo','Tarjeta','Transferencia') NOT NULL DEFAULT 'Efectivo',
  `usuario_registro_id` int(11) NOT NULL,
  `usuario_autoriza_id` int(11) DEFAULT NULL,
  `concepto` text DEFAULT NULL,
  `referencia_folio` varchar(50) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_cap_categoria` (`categoria_id`),
  KEY `fk_cap_alm_destino` (`almacen_destino_id`),
  KEY `fk_cap_user_reg` (`usuario_registro_id`),
  KEY `fk_cap_user_aut` (`usuario_autoriza_id`),
  KEY `idx_fecha_mov` (`fecha_movimiento`),
  KEY `idx_alm_origen` (`almacen_origen_id`),
  CONSTRAINT `fk_cap_alm_destino` FOREIGN KEY (`almacen_destino_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cap_alm_origen` FOREIGN KEY (`almacen_origen_id`) REFERENCES `almacenes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_cap_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `capital_categorias` (`id`),
  CONSTRAINT `fk_cap_user_aut` FOREIGN KEY (`usuario_autoriza_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_cap_user_reg` FOREIGN KEY (`usuario_registro_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `cuentas_bancarias` (
  `id_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `id_almacen` int(11) NOT NULL,
  `nombre_cuenta` varchar(100) NOT NULL,
  `tipo_cuenta` enum('Efectivo','Banco','Caja Fuerte') NOT NULL,
  `estatus` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id_cuenta`),
  KEY `id_almacen` (`id_almacen`),
  CONSTRAINT `cuentas_bancarias_ibfk_1` FOREIGN KEY (`id_almacen`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================


CREATE TABLE `corte_de_caja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`),
  UNIQUE KEY `fecha_corte` (`fecha_corte`,`almacen_id`),
  CONSTRAINT `corte_de_caja_ibfk_1` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `config_transmutaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `almacen_id` int(11) NOT NULL,
  `producto_origen_id` int(11) NOT NULL,
  `producto_destino_id` int(11) NOT NULL,
  `rendimiento_teorico` decimal(10,4) NOT NULL COMMENT 'Ej: 1 bulto -> 50.00 kg',
  `usuario_id` int(11) NOT NULL,
  `notas` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_almacen_origen_destino` (`almacen_id`,`producto_origen_id`,`producto_destino_id`),
  KEY `fk_config_trans_origen` (`producto_origen_id`),
  KEY `fk_config_trans_destino` (`producto_destino_id`),
  CONSTRAINT `fk_config_trans_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `fk_config_trans_destino` FOREIGN KEY (`producto_destino_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_config_trans_origen` FOREIGN KEY (`producto_origen_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `transmutaciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_trans_usuario` (`usuario_id`),
  KEY `fk_trans_almacen` (`almacen_id`),
  CONSTRAINT `fk_trans_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `fk_trans_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `transmutacion_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transmutacion_id` int(11) NOT NULL,
  `movimiento_id` int(11) NOT NULL,
  `tipo` enum('salida','entrada') NOT NULL,
  `producto_id` int(11) NOT NULL,
  `lote_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo_unitario_historico` decimal(10,2) NOT NULL,
  `costo_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_det_trans_cabecera` (`transmutacion_id`),
  KEY `fk_det_trans_prod` (`producto_id`),
  KEY `fk_det_trans_lote` (`lote_id`),
  KEY `fk_det_trans_mov` (`movimiento_id`),
  CONSTRAINT `fk_det_trans_cabecera` FOREIGN KEY (`transmutacion_id`) REFERENCES `transmutaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_det_trans_lote` FOREIGN KEY (`lote_id`) REFERENCES `lotes_stock` (`id`),
  CONSTRAINT `fk_det_trans_mov` FOREIGN KEY (`movimiento_id`) REFERENCES `movimientos` (`id`),
  CONSTRAINT `fk_det_trans_prod` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `solicitudes_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `administrador_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `proveedor_id` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','ordenado','recibido','cancelado') DEFAULT 'pendiente',
  `compra_id_final` int(11) DEFAULT NULL COMMENT 'Relación con la compra real una vez ejecutada',
  PRIMARY KEY (`id`),
  KEY `fk_sol_comp_admin` (`administrador_id`),
  KEY `fk_sol_comp_compra` (`compra_id_final`),
  CONSTRAINT `fk_sol_comp_admin` FOREIGN KEY (`administrador_id`) REFERENCES `usuarios` (`id`),
  CONSTRAINT `fk_sol_comp_compra` FOREIGN KEY (`compra_id_final`) REFERENCES `compras` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `detalle_solicitud_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `solicitud_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_det_sol_cabecera` (`solicitud_id`),
  KEY `fk_det_sol_producto` (`producto_id`),
  CONSTRAINT `fk_det_sol_cabecera` FOREIGN KEY (`solicitud_id`) REFERENCES `solicitudes_compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_det_sol_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `solicitudes_pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vendedor_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` enum('pendiente','en_compra','listo_entrega','finalizado','cancelado') DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_sol_ped_vendedor` (`vendedor_id`),
  KEY `fk_sol_ped_cliente` (`cliente_id`),
  CONSTRAINT `fk_sol_ped_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`),
  CONSTRAINT `fk_sol_ped_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `pedidos_vendedores` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `folio` varchar(20) NOT NULL,
  `vendedor_id` int(11) NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `almacen_id` int(11) NOT NULL,
  `fecha_solicitud` timestamp NOT NULL DEFAULT current_timestamp(),
  `prioridad` enum('Baja','Media','Alta') DEFAULT 'Media',
  `estatus` tinyint(1) NOT NULL DEFAULT 1,
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `folio` (`folio`),
  KEY `fk_pedido_vendedor` (`vendedor_id`),
  KEY `fk_pedido_almacen` (`almacen_id`),
  CONSTRAINT `fk_pedido_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`),
  CONSTRAINT `fk_pedido_vendedor` FOREIGN KEY (`vendedor_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `detalle_pedido_vendedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pedido_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `notas_producto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_detalle_pedido` (`pedido_id`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos_vendedores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `transporte_vehiculos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL COMMENT 'Ej. Torton Internacional',
  `placas` varchar(20) NOT NULL,
  `serie_vin` varchar(50) DEFAULT NULL,
  `modelo_año` int(4) DEFAULT NULL,
  `capacidad_carga_kg` decimal(10,2) DEFAULT NULL,
  `estado_unidad` enum('disponible','en_ruta','mantenimiento','fuera_servicio') DEFAULT 'disponible',
  `activo` tinyint(1) DEFAULT 1,
  `almacen_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `placas` (`placas`),
  KEY `fk_vehiculo_almacen` (`almacen_id`),
  CONSTRAINT `fk_vehiculo_almacen` FOREIGN KEY (`almacen_id`) REFERENCES `almacenes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `transporte_repartos_maestro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vehiculo_id` int(11) NOT NULL,
  `usuario_encargado_id` int(11) NOT NULL COMMENT 'El chofer o responsable del reparto',
  `entrega_venta_id` int(11) DEFAULT NULL COMMENT 'Relación con la entrega (si aplica)',
  `fecha_programada` date NOT NULL,
  `hora_salida_real` datetime DEFAULT NULL,
  `hora_llegada_real` datetime DEFAULT NULL,
  `km_inicial` int(11) DEFAULT NULL,
  `km_final` int(11) DEFAULT NULL,
  `estado_reparto` enum('preparacion','en_transito','completado','cancelado') DEFAULT 'preparacion',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_reparto_vehiculo` (`vehiculo_id`),
  KEY `idx_reparto_encargado` (`usuario_encargado_id`),
  CONSTRAINT `fk_reparto_encargado` FOREIGN KEY (`usuario_encargado_id`) REFERENCES `trabajadores` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_reparto_vehiculo` FOREIGN KEY (`vehiculo_id`) REFERENCES `transporte_vehiculos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `transporte_rutas_puntos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reparto_id` int(11) NOT NULL,
  `orden_visita` int(11) NOT NULL DEFAULT 1,
  `descripcion_punto` varchar(255) NOT NULL COMMENT 'Ej. Bodega Central o Domicilio Cliente X',
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL,
  `llegada_estimada` datetime DEFAULT NULL,
  `llegada_real` datetime DEFAULT NULL,
  `estado_punto` enum('pendiente','visitado','omitido') DEFAULT 'pendiente',
  PRIMARY KEY (`id`),
  KEY `idx_ruta_punto_reparto` (`reparto_id`),
  CONSTRAINT `fk_ruta_punto_reparto` FOREIGN KEY (`reparto_id`) REFERENCES `transporte_repartos_maestro` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `transporte_tripulantes_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `reparto_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL COMMENT 'Ayudante o personal de apoyo',
  `rol_secundario` varchar(50) DEFAULT 'Ayudante' COMMENT 'Ej. Estibador, Copiloto',
  PRIMARY KEY (`id`),
  KEY `idx_trip_reparto` (`reparto_id`),
  KEY `fk_tripulante_trabajador` (`usuario_id`),
  CONSTRAINT `fk_trip_reparto_cab` FOREIGN KEY (`reparto_id`) REFERENCES `transporte_repartos_maestro` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tripulante_trabajador` FOREIGN KEY (`usuario_id`) REFERENCES `trabajadores` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `transporte_consolidacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `viaje_folio` varchar(50) NOT NULL,
  `vehiculo_id` int(11) NOT NULL,
  `reparto_id` int(11) NOT NULL,
  `estatus_consolidado` enum('abierto','cerrado') DEFAULT 'abierto',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_vehiculo_activo` (`vehiculo_id`,`estatus_consolidado`),
  CONSTRAINT `transporte_consolidacion_ibfk_1` FOREIGN KEY (`vehiculo_id`) REFERENCES `transporte_vehiculos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ============================================================

CREATE TABLE `confirmacion_reparto_viaje` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_movimiento` (`id_movimiento`),
  KEY `id_venta` (`id_venta`),
  KEY `trabajador_id` (`trabajador_id`),
  CONSTRAINT `confirmacion_ibfk_1` FOREIGN KEY (`id_movimiento`) REFERENCES `movimientos` (`id`),
  CONSTRAINT `confirmacion_ibfk_2` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id`),
  CONSTRAINT `confirmacion_ibfk_3` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;