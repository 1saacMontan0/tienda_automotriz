
<?php

function borrar_compra($conexion, $id_compra) {
    $sentencia = "DELETE FROM compras WHERE id_compra=?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_compra]);
}

function borrar_inventario($conexion, $id_producto) {
    $sentencia = "DELETE FROM inventario WHERE id_producto=?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_producto]);
}


?>