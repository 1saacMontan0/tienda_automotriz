
<?php 

function optener_empresa($conexion, $id_empresa) {
    $sentencia = "SELECT nombre, telefono, correo, direccion, nit, nrc, giro FROM empresa WHERE id_empresa=?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    return $usuario = $consulta->fetchAll(PDO::FETCH_ASSOC);
}

?>