
<?php

function generar_clientes($conexion, $id_empresa) {
    // 1. Preparar la sentencia
    $sentencia = "SELECT c.id_cliente, c.nombre, c.correo, c.telefono, c.direccion, c.nit, c.fecha, c.hora,
        IFNULL(SUM(v.total), 0) AS total
        FROM clientes c LEFT JOIN ventas v ON c.id_cliente = v.id_cliente WHERE c.id_empresa = ? GROUP BY c.id_cliente";
    
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    
    // 2. Capturar todos los registros
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    // 3. Verificar y mostrar
    if ($usuarios) {
        foreach ($usuarios as $fila) {
            echo '<tr>';
                // Imprimimos columna por columna manualmente
                echo '<td>';
                    echo htmlspecialchars($fila['nombre']);
                    echo '<div style="display:flex">';
                        // Botón WhatsApp
                        echo "<button 
                                type='button'
                                class='wha-btn'
                                style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                    transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(37, 211, 102, 0.3); margin: 0 15px 0 0'
                                onclick=\"window.open('https://wa.me/503" . htmlspecialchars($fila['telefono']) . "', '_blank')\">
                                💬 WhatsApp
                            </button>";
                        // Botón Correo
                        echo "<button 
                                type='button'
                                class='correo-btn'
                                style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                    transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(66, 133, 244, 0.3);'
                                onclick=\"window.location.href='mailto:" . htmlspecialchars($fila['correo']) . "'\">
                                ✉️ Correo
                            </button>";
                    echo '</div>';   
                echo '</td>';
                echo '<td>'.htmlspecialchars($fila['telefono']).'</td>';
                echo '<td>'.htmlspecialchars($fila['correo']).'</td>';
                echo '<td>'. htmlspecialchars($fila['direccion']).'</td>';
                echo '<td>
                    <div style="height:35px;border:1px solid #25d366;display:grid;place-items:center;border-radius:15px;padding:0 15px">
                    $'.htmlspecialchars($fila['total']).
                    '</div>
                </td>';
                echo '<td>' . $fila['fecha'] ." ". $fila['hora'] . '</td>';
                // Botón Editar
                echo "<td style='width:120px'>
                    <form action='actualizar_clientes.php' method='post'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id_cliente']) . " '
                            class=edit-btn
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(98, 160, 234, 0.3);'>
                            ✏️ Editar
                        </button>
                    </form>
                </td>";
                // Botón Eliminar
                echo "<td style='width:120px'>
                    <form action='../../controllers/clientes/borrar.php' method='post'>
                        <button type='submit' name='id' value='" . htmlspecialchars($fila['id_cliente']) . "'
                            class=delete-btn
                            style='border-radius: 8px; font-size: 0.85rem; cursor: pointer; border: none; width:115px; height:35px;
                                transition: all 0.3s ease; font-weight: 600; border: 2px solid rgba(192, 28, 40, 0.3);'>
                            🗑️ Eliminar
                        </button>
                    </form>
                </td>";
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='10' style='text-align:center;'>No se encontraron clientes para esta empresa.</td></tr>";
    }

    // Cerramos la conexión de la sentencia
    $consulta = null;
}

function cliente_individual($conexion, $id_cliente) {
    $sentencia = "SELECT nombre, correo, nit, telefono, direccion 
        FROM clientes WHERE id_cliente=?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_cliente]);
    return $usuario = $consulta->fetchAll(PDO::FETCH_ASSOC);
}

?>