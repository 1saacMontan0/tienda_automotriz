
<?php

# Registra compras generales

function compra_general($conexion, $id_empresa, $descripcion, $cantidad, $precio) {
    $sentencia = "INSERT INTO compras (id_empresa, descripcion, cantidad, precio_compra, fecha, hora)
        VALUES (?,?,?,?, CURDATE(), CURTIME())";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa, $descripcion, $cantidad, $precio]);
}

// Eliminamos $direccion de la lista de parámetros
function registrar_inventario(
    $conexion, $id_empresa, $nombre, $descipcion, $compatible_desde, $compatible_hasta,
    $unidades, $precio_compra, $marca, $modelo, $precio_venta
) {
    $sentencia = "INSERT INTO inventario 
        (id_empresa, nombre, descripcion, compatible_desde, compatible_hasta, unidades, precio_compra,
            fecha, hora, marca, modelo, precio_venta)
        VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), CURTIME(), ?, ?, ?)";

    $consulta = $conexion->prepare($sentencia);
    
    // Ahora el execute tiene los 10 valores correspondientes a los 10 '?'
    $consulta->execute([
        $id_empresa, $nombre, $descipcion, $compatible_desde, $compatible_hasta, 
        $unidades, $precio_compra, $marca, $modelo, $precio_venta
    ]);
}



?>