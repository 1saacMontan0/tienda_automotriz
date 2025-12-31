
<?php
function obtener_datos_inventario_servicios_json($conexion, $id_empresa) {
    // 1. Preparar la sentencia combinada
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
    $items = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $datos_formateados = [];

    // 2. Mapear datos (Clave => Valor)
    if ($items) {
        foreach ($items as $fila) {
            $datos_formateados[] = [
                'Tipo'             => $fila['tipo'],
                'Nombre'           => $fila['nombre'],
                'Descripción'      => $fila['descripcion'],
                'CompatibleDesde'  => $fila['compatible_desde'],
                'CompatibleHasta'  => $fila['compatible_hasta'],
                'Unidades'         => $fila['unidades'],
                'PrecioCompra'     => $fila['precio_compra'],
                'PrecioVenta'      => $fila['precio_venta'],
                'Marca'            => $fila['marca'],
                'Modelo'           => $fila['modelo'],
                'Registro'         => $fila['fecha'] . ' ' . $fila['hora']
            ];
        }
    }

    return $datos_formateados;
}
?>