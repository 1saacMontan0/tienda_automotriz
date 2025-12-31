<?php

function generar_compras($conexion, $id_empresa) {
    // 1. Preparar la sentencia con los placeholders '?'
    $sentencia = "SELECT id_producto AS id, 'inventario' AS tipo, nombre, descripcion,
        compatible_desde, compatible_hasta, unidades, precio_compra, fecha, hora , marca, modelo,
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
    // Pasamos el ID dos veces (una para cada SELECT del UNION)
    $consulta->execute([$id_empresa, $id_empresa]);
    
    $compra = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if ($compra) {
        foreach ($compra as $fila) {
            echo '<tr>';
                echo '<td>
                    <div style="height:35px;border:1px solid #25d366;display:grid;place-items:center;border-radius:15px;padding:0 15px">'
                    .$fila['tipo'].
                    '</div>
                </td>';
                echo '<td>'.htmlspecialchars($fila['descripcion']).'</td>';
                echo '<td>'.$fila['nombre'].'</td>';
                echo '<td>'.$fila['marca'].'</td>';
                echo '<td>'.$fila['modelo'].'</td>';
                echo '<td>'.$fila['compatible_desde'].' - '.$fila['compatible_hasta'].'</td>';
                echo '<td>
                    <div style="height:35px;border:1px solid #25d366;display:grid;place-items:center;border-radius:15px;padding:0 15px">
                        '.$fila['unidades'].
                    '</div>
                </td>';
                echo '<td>$'.$fila['precio_compra'].'</td>';
                echo '<td>'.$fila['precio_venta'].'</td>';
                echo '<td>' . $fila['fecha'] ." ". $fila['hora'] . '</td>';
                
                // Botón Editar
                echo "<td style='width:120px'>
                    <form action='../../controllers/compras/tipo_actualizacion.php' method='post'>
                        <input type='hidden' name='tipo' value='".$fila['tipo']."'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id']) . "'
                            class='edit-btn'
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(98, 160, 234, 0.3);'>
                            ✏️ Editar
                        </button>
                    </form>
                </td>";
                
                // Botón Eliminar
                echo "<td style='width:120px'>
                    <form action='../../controllers/compras/tipo_borrar.php' method='post'>
                        <input type='hidden' name='tipo' value='".$fila['tipo']."'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id']) . "'
                            class='delete-btn'
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(192, 28, 40, 0.3);'>
                            🗑️ Eliminar
                        </button>
                    </form>
                </td>";
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='11' style='text-align:center;'>No se encontraron registros para esta empresa.</td></tr>";
    }

    $consulta = null;
}

function compra_individual($conexion, $id_compra) {
    $sentencia = "SELECT descripcion, cantidad, precio_compra FROM compras
        WHERE id_compra = ?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_compra]);
    return $compra = $consulta->fetchAll(PDO::FETCH_ASSOC);
}

function inventario_individual($conexion, $id_producto) {
    $sentencia = "SELECT nombre, descripcion, compatible_desde, compatible_hasta, unidades, precio_compra, marca, modelo, precio_venta FROM inventario
        WHERE id_producto = ?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_producto]);
    return $compra = $consulta->fetchAll(PDO::FETCH_ASSOC);
}
?>