SELECT
                ROW_NUMBER() OVER (ORDER BY en.id) AS num_registro,
                MAX(v.folio) AS folio_venta,
                en.id AS entrega_id,
                en.venta_id,
                en.usuario_id,
                en.fecha,

                MAX(tc.id) AS folio,
                IFNULL(MAX(tc.viaje_folio), 'Sin Viaje Asignado') AS viaje_folio,

                -- Evidencias e imágenes
                MAX(crv.id) AS id_evidencia,
                MAX(crv.estatus) AS estatus_evidencia,
                MAX(crv.comentario) AS comentario_evidencia,
                MAX(IF(crv.id IS NOT NULL, 1, 0)) AS ya_entregado,

                MAX(IF(crv.fotografia_entrega IS NOT NULL AND crv.fotografia_entrega != '', 
                    CONCAT( crv.fotografia_entrega), NULL)) AS foto_registrada,

                MAX(IF(crv.fotografia_nota IS NOT NULL AND crv.fotografia_nota != '', 
                    CONCAT( crv.fotografia_nota), NULL)) AS nota_registrada,

                MAX(trp.descripcion_punto) AS direccion_entrega,
                MAX(c.nombre_comercial) AS cliente,
                MAX(trm.vehiculo_id) AS vehiculo_id,

                GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ', ') AS productos,

                GROUP_CONCAT(
                    DISTINCT CONCAT(m.cantidad, ' ', p.unidad_medida)
                    ORDER BY p.nombre
                    SEPARATOR ', '
                ) AS cantidades_detalladas,

                SUM(m.cantidad) AS total_piezas_venta

            FROM entregas_venta en

            INNER JOIN movimientos m
                ON m.entrega_id = en.id

            INNER JOIN transporte_repartos_maestro trm
                ON trm.entrega_venta_id = m.id
             
            LEFT JOIN transporte_consolidacion tc
                ON tc.reparto_id = trm.id
            LEFT JOIN confirmacion_reparto_viaje crv
                ON crv.entrega_id = en.id

            LEFT JOIN transporte_rutas_puntos trp
                ON trp.reparto_id = trm.id

            LEFT JOIN productos p
                ON p.id = m.producto_id

            LEFT JOIN ventas v
                ON v.id = en.venta_id

            LEFT JOIN clientes c
                ON c.id = v.id_cliente

            WHERE tc.viaje_folio='RUT-260729-1012-90'

            GROUP BY
               
                trp.descripcion_punto 