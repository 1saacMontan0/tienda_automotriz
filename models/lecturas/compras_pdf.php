
<?php

# Funcion que genera los registros para reportes de clientes.
function generar_compras($conexion, $id_empresa) {
    // 1. Preparar la sentencia
    $sentencia = "SELECT id_producto AS id, 'inventario' AS tipo, nombre, descripcion,
            compatible_desde, compatible_hasta, unidades, precio_compra, fecha, hora, marca, modelo,
            precio_venta
        FROM inventario
        WHERE id_empresa = ?
        UNION ALL
        SELECT id_compra AS id, 'servicio' AS tipo, '-' AS nombre,
            descripcion, '-' AS compatible_desde, '-' AS compatible_hasta, cantidad AS unidades,
            precio_compra, fecha, hora, '-' AS marca, '-' AS modelo, '-' AS precio_venta
        FROM compras
        WHERE id_empresa = ?
        ORDER BY fecha DESC, hora DESC";

    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa, $id_empresa]);

    // 2. Capturar todos los registros
    $items = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // 3. Verificar y mostrar
    $html = "";
    if ($items) {
        foreach ($items as $fila) {
            $html .= '<tr>';
                $html .= '<td style="padding:0 10px">'.$fila['tipo'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['descripcion'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['nombre'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['marca'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['modelo'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['compatible_desde'].' - '.$fila['compatible_hasta'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['unidades'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['precio_compra'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['precio_venta'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['fecha'].' '.$fila['hora'].'</td>';
            $html .= '</tr>';
        }
    } else {
        $html .= "<tr><td colspan='11' style='text-align:center;'>No se encontraron registros de inventario o servicios para esta empresa.</td></tr>";
    }

    // Cerramos la conexión de la sentencia
    $consulta = null;

    return $html;
}


?>