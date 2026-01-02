<?php 

# muestra tabla de ventas:
function ventas($conexion, $id_empresa) {
    // Consulta con JOIN para relacionar las 3 tablas y calcular ganancia
    $sentencia = "SELECT 
        v.id_venta AS id,
        c.nombre AS cliente_nombre,
        i.nombre AS producto_nombre,
        i.marca,
        i.modelo,
        i.compatible_desde,
        i.compatible_hasta,
        v.cantidad,
        v.total,
        v.tipo_factura,
        v.fecha,
        v.hora,
        (v.total - (i.precio_compra * v.cantidad)) AS ganancia
        FROM ventas v
        INNER JOIN clientes c ON v.id_cliente = c.id_cliente
        INNER JOIN inventario i ON v.id_producto = i.id_producto
        WHERE c.id_empresa = ? 
        ORDER BY v.fecha DESC, v.hora DESC";
    
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    
    $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if ($registros) {
        foreach ($registros as $fila) {
            echo '<tr>';
                // 1. Cliente
                echo '<td>'.$fila['cliente_nombre'].'</td>';

                // 2. Producto (Solo)
                echo '<td><b>'.$fila['producto_nombre'].'</b></td>';

                // 3. Marca y Modelo (Juntos)
                echo '<td>'.$fila['marca'].' / '.$fila['modelo'].'</td>';

                // 4. Años compatibles
                echo '<td>'.$fila['compatible_desde'].' - '.$fila['compatible_hasta'].'</td>';

                // 5. Cantidad vendida (Estilo original)
                echo '<td>
                    <div style="height:35px;border:1px solid #25d366;display:grid;place-items:center;border-radius:15px;padding:0 15px">
                        '.$fila['cantidad'].'
                    </div>
                </td>';

                // 6. Total y 7. Ganancia
                echo '<td>$'.number_format($fila['total'], 2).'</td>';
                echo '<td style="color: #25d366; font-weight: bold;">$'.number_format($fila['ganancia'], 2).'</td>';

                // 8. Tipo de factura
                echo '<td>'.$fila['tipo_factura'].'</td>';

                // 9. Fecha y Hora
                echo '<td>' . $fila['fecha'] ."<br>". $fila['hora'] . '</td>';
                
                 echo "<td style='width:120px'>
                    <form action='../../pages/gestion/ventas/actualizar_venta.php' method='post'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id']) . "'
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(98, 160, 234, 0.3);'>
                            Factura
                        </button>
                    </form>
                </td>";

                // Botón Editar (Estilo Original)
                echo "<td style='width:120px'>
                    <form action='../../pages/gestion/actualizar_ventas.php' method='post'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id']) . "'
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(98, 160, 234, 0.3);'>
                            ✏️ Editar
                        </button>
                    </form>
                </td>";
                
                // Botón Eliminar (Estilo Original)
                echo "<td style='width:120px'>
                    <form action='../../controllers/ventas/borrar.php' method='post'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id']) . "'
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(192, 28, 40, 0.3);'>
                            🗑️ Eliminar
                        </button>
                    </form>
                </td>";
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='11' style='text-align:center;'>No se encontraron registros de ventas.</td></tr>";
    }

    $consulta = null;
}
?>