
<?php
function inventario_json($conexion, $id_empresa) {
    // 1. Preparar la sentencia combinada
    $sentencia = "SELECT nombre,
                        compatible_desde, compatible_hasta, unidades, precio_compra, fecha, hora, marca, modelo,
                        precio_venta
                  FROM inventario
                  WHERE id_empresa = ?
                  ORDER BY fecha DESC, hora DESC";

    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    $items = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $datos_formateados = [];

    // 2. Mapear datos (Clave => Valor)
    if ($items) {
        foreach ($items as $fila) {
            $datos_formateados[] = [
                'Nombre'           => $fila['nombre'],
                'Marca'            => $fila['marca'],
                'Modelo'           => $fila['modelo'],
                'CompatibleDesde'  => $fila['compatible_desde'],
                'CompatibleHasta'  => $fila['compatible_hasta'],
                'Unidades'         => $fila['unidades'],
                'PrecioCompra'     => $fila['precio_compra'],
                'PrecioVenta'      => $fila['precio_venta'],
                'TotalVendido'     => 0,
                'Ganancia'         => 0,
                'Registro'         => $fila['fecha'] . ' ' . $fila['hora']
            ];
        }
    }

    return $datos_formateados;
}
?>