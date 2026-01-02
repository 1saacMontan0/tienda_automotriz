<?php

function generar_ventas($conexion, $id_empresa) {
    // 1. Consulta actualizada incluyendo v.tipo_factura
    $sentencia = "SELECT 
            i.id_producto AS id, 
            'venta' AS tipo, 
            i.nombre AS producto_nombre, 
            i.descripcion,
            i.compatible_desde, 
            i.compatible_hasta, 
            v.cantidad AS unidades, 
            i.precio_compra, 
            v.fecha, 
            v.hora, 
            i.marca, 
            i.modelo,
            i.precio_venta,
            v.total AS total,
            v.tipo_factura,
            (v.total - (i.precio_compra * v.cantidad)) AS ganancia,
            c.nombre AS cliente_nombre
        FROM ventas v
        INNER JOIN inventario i ON v.id_producto = i.id_producto
        INNER JOIN clientes c ON v.id_cliente = c.id_cliente
        WHERE i.id_empresa = ?
        ORDER BY v.fecha DESC, v.hora DESC";

    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);

    $items = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // 2. Construcción del HTML
    $html = "";
    if ($items) {
        foreach ($items as $fila) {
            $html .= '<tr>';
                $html .= '<td style="padding:0 10px">'.$fila['cliente_nombre'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['producto_nombre'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['marca']." / ".$fila['modelo'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['compatible_desde'].' - '.$fila['compatible_hasta'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['unidades'].'</td>';
                $html .= '<td style="padding:0 10px">$'.number_format($fila['total'], 2).'</td>';
                $html .= '<td style="padding:0 10px; color: #25d366; font-weight: bold;">$'.number_format($fila['ganancia'], 2).'</td>';
                // Nueva celda para Tipo de Factura
                $html .= '<td style="padding:0 10px">'.$fila['tipo_factura'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['fecha'].' '.$fila['hora'].'</td>';
            $html .= '</tr>';
        }
    } else {
        // Se ajustó el colspan a 9 para cubrir la nueva columna
        $html .= "<tr><td colspan='9' style='text-align:center;'>No se encontraron registros de ventas para esta empresa.</td></tr>";
    }

    $consulta = null;
    return $html;
}

?>