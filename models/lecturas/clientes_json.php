
<?php

function obtener_datos_clientes_json($conexion, $id_empresa) {
    // 1. Preparar la sentencia
    $sentencia = "SELECT c.id_cliente, c.nombre, c.correo, c.telefono, c.direccion, c.nit, c.fecha, c.hora,
        IFNULL(SUM(v.total), 0) AS total
        FROM clientes c LEFT JOIN ventas v ON c.id_cliente = v.id_cliente WHERE c.id_empresa = ? GROUP BY c.id_cliente";
    
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $datos_formateados = [];

    // 2. Mapear datos (Encabezado como Clave => Valor)
    if ($usuarios) {
        foreach ($usuarios as $fila) {
            $datos_formateados[] = [
                'Nombre'    => $fila['nombre'],
                'Telefono'  => $fila['telefono'],
                'Correo'    => $fila['correo'],
                'Dirección' => $fila['direccion'],
                'IVA'       => '', 
                'NIT'       => $fila['nit'],
                'Total'     => $fila['total'],  
                'Registro'  => $fila['fecha'] . ' ' . $fila['hora']
            ];
        }
    }

    return $datos_formateados;
}
?>