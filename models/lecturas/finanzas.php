<?php

function ingresos($conexion) {
    // 1. Se añade el FROM para especificar la tabla (ajustar nombre si es diferente)
    $sentencia = "SELECT id_ingreso AS id, descripcion, categoria, monto, fecha, hora 
              FROM ingresos 
              UNION ALL 
              SELECT id_venta AS id, 'Sin descripción' AS descripcion, 'ventas' AS categoria, total AS monto, fecha, hora 
              FROM ventas 
              ORDER BY fecha DESC, hora DESC";
    
    $consulta = $conexion->query($sentencia);
    $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if ($registros) {
        foreach ($registros as $fila) {
            echo '<tr>';
                // Agregado un espacio entre fecha y hora
                echo '<td>' . $fila['fecha'] . ' ' . $fila['hora'] . '</td>';
                echo '<td>' . htmlspecialchars($fila['descripcion']) . '</td>';
                echo '<td>' . htmlspecialchars($fila['categoria']) . '</td>';
                
                // Corregido: Se usa 'monto' ya que 'total' no existe en el SELECT
                echo '<td>$' . number_format($fila['monto'], 2) . '</td>';
                
                // Botón Editar
                echo "<td style='width:120px'>
                    <form action='../../pages/gestion/actualizar_ingresos.php' method='post'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id']) . "'
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(98, 160, 234, 0.3);'>
                            ✏️ Editar
                        </button>
                    </form>
                </td>";
                
                // Botón Eliminar
                echo "<td style='width:120px'>
                    <form action='../../controllers/finanzas/borrar_ingresos.php' method='post'>
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
        // Ajustado el colspan a 6, que es el número real de columnas en tu tabla actual
        echo "<tr><td colspan='6' style='text-align:center;'>No se encontraron registros de ingresos.</td></tr>";
    }

    $consulta = null;
}

function egresos($conexion) {
    // 1. Se añade el FROM para especificar la tabla (ajustar nombre si es diferente)
    $sentencia = "SELECT id_egreso AS id, descripcion, categoria, monto, fecha, hora 
              FROM egresos 
              UNION ALL 
              SELECT id_compra AS id, descripcion, 'compras' AS categoria, (cantidad * precio_compra) AS monto, fecha, hora 
              FROM compras 
              ORDER BY fecha DESC, hora DESC";

    
    
    $consulta = $conexion->query($sentencia);
    $registros = $consulta->fetchAll(PDO::FETCH_ASSOC);

    if ($registros) {
        foreach ($registros as $fila) {
            echo '<tr>';
                // Agregado un espacio entre fecha y hora
                echo '<td>' . $fila['fecha'] . ' ' . $fila['hora'] . '</td>';
                echo '<td>' . htmlspecialchars($fila['descripcion']) . '</td>';
                echo '<td>' . htmlspecialchars($fila['categoria']) . '</td>';
                
                // Corregido: Se usa 'monto' ya que 'total' no existe en el SELECT
                echo '<td>$' . number_format($fila['monto'], 2) . '</td>';
                
                // Botón Editar
                echo "<td style='width:120px'>
                    <form action='../../pages/gestion/actualizar_egresos.php' method='post'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id']) . "'
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(98, 160, 234, 0.3);'>
                            ✏️ Editar
                        </button>
                    </form>
                </td>";
                
                // Botón Eliminar
                echo "<td style='width:120px'>
                    <form action='../../controllers/finanzas/borrar_egresos.php' method='post'>
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
        // Ajustado el colspan a 6, que es el número real de columnas en tu tabla actual
        echo "<tr><td colspan='6' style='text-align:center;'>No se encontraron registros de egresos.</td></tr>";
    }

    $consulta = null;
}

?>