<?php

function inventario($conexion, $id_empresa) {
    // 1. Preparar la sentencia con los placeholders '?'
    $sentencia = "SELECT id_producto AS id, 'inventario' AS tipo, nombre, descripcion,
        compatible_desde, compatible_hasta, unidades, precio_compra, fecha, hora , marca, modelo,
        precio_venta
        FROM inventario
        WHERE id_empresa = ?
        ORDER BY fecha DESC, hora DESC";
    
    $consulta = $conexion->prepare($sentencia);
    // Pasamos el ID dos veces (una para cada SELECT del UNION)
    $consulta->execute([$id_empresa]);
    
    $compra = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if ($compra) {
        foreach ($compra as $fila) {
            echo '<tr>';
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
                echo '<td>0</td>';
                echo '<td>0</td>';
                echo '<td>' . $fila['fecha'] ." ". $fila['hora'] . '</td>';
                
                // Botón Editar
                echo "<td style='width:120px'>
                    <form action='../../pages/gestion/inventario/actualizar_inventario.php' method='post'>
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
                    <form action='../../controllers/inventario/borrar.php' method='post'>
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

function productos($conexion, $id_empresa) {
    // 1. Preparar la sentencia con los placeholders '?'
    $sentencia = "SELECT  nombre, compatible_desde, compatible_hasta, unidades, marca, modelo,
        precio_venta
        FROM inventario
        WHERE id_empresa = ? AND unidades >= 1
        ORDER BY fecha DESC, hora DESC";
    
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    return $usuario = $consulta->fetchAll(PDO::FETCH_ASSOC);
}

?>