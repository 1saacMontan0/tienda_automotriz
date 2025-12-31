
<?php

# Funcion que genera los registros para reportes de clientes.
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
    $html = "";
    if ($usuarios) {
        foreach ($usuarios as $fila) {
            $html .= '<tr>';
                $html .= '<td style="padding:0 10px">'.$fila['nombre'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['telefono'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['correo'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['direccion'].'</td>';
                $html .= '<td style="padding:0 10px"></td>';
                $html .= '<td style="padding:0 10px">'.$fila['nit'].'</td>';
                $html .='<td style="padding:0 10px">'.$fila['total'].'</td>';
                $html .= '<td style="padding:0 10px">'.$fila['fecha'].' '.$fila['hora'].'</td>';
            $html .= '</tr>';
            
        }
    } else {
        echo "<tr><td colspan='10' style='text-align:center;'>No se encontraron clientes para esta empresa.</td></tr>";
    }
    return $html;
    // Cerramos la conexión de la sentencia
    $consulta = null;
}

?>