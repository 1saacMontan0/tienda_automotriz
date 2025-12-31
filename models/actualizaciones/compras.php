<?php

function actualizar_compra($conexion, $id_compra, $descripcion, $cantidad, $precio_compra) {
    $sentencia = "UPDATE compras SET
        descripcion=?,
        cantidad=?,
        precio_compra=?
    WHERE id_compra=?";
    $consulta= $conexion->prepare($sentencia);
    $consulta->execute([$descripcion, $cantidad, $precio_compra, $id_compra]);
}

function actualizar_inventario(
        $conexion, $id_producto, $nombre, $descripcion, $desde, $hasta, $unidades, $p_compra, $marca, $modelo, $p_venta
    ) {
    $sentencia = "UPDATE inventario SET
        nombre=?,
        descripcion=?,
        compatible_desde=?,
        compatible_hasta=?,
        unidades=?,
        precio_compra=?,
        marca=?,
        modelo=?,
        precio_venta=?
    WHERE id_producto=?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$nombre, $descripcion, $desde, $hasta, $unidades, $p_compra, $marca, $modelo, $p_venta, $id_producto]);
}
?>