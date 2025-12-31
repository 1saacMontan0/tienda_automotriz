
<?php

# funcion que elimina clientes.
function borrar_clientes($conexion, $id_cliente) {
    $sentencia = "DELETE FROM clientes WHERE id_cliente=?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_cliente]);
}

?>