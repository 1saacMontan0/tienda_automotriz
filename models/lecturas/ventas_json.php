<?php
function ventas_json($conexion, $id_empresa) {
    // 1. Consulta con JOINS para obtener datos reales de ventas y clientes
    $sentencia = "SELECT 
            c.nombre AS cliente_nombre,
            i.nombre AS producto_nombre, 
            i.marca, 
            i.modelo, 
            i.compatible_desde, 
            i.compatible_hasta,
            v.cantidad AS unidades, 
            v.total AS total_vendido,
            (v.total - (i.precio_compra * v.cantidad)) AS ganancias,
            v.tipo_factura,
            v.fecha, 
            v.hora
        FROM ventas v
        INNER JOIN inventario i ON v.id_producto = i.id_producto
        INNER JOIN clientes c ON v.id_cliente = c.id_cliente
        WHERE i.id_empresa = ?
        ORDER BY v.fecha DESC, v.hora DESC";

    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    $items = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $datos_formateados = [];

    // 2. Mapear datos siguiendo tu requerimiento exacto
    if ($items) {
        foreach ($items as $fila) {
            $datos_formateados[] = [
                'Cliente'          => $fila['cliente_nombre'],
                'Producto'         => $fila['producto_nombre'],
                'Marca'            => $fila['marca'],
                'Modelo'           => $fila['modelo'],
                'CompatibleDesde'  => $fila['compatible_desde'],
                'CompatibleHasta'  => $fila['compatible_hasta'],
                'CantidadVendida'  => $fila['unidades'],
                'TotalVendido'     => $fila['total_vendido'],
                'Ganancia'         => $fila['ganancias'],
                'TipoFactura'      => $fila['tipo_factura'],
                'FechaHora'        => $fila['fecha'] . ' ' . $fila['hora']
            ];
        }
    }

    return $datos_formateados;
}
?>