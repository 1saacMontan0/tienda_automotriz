
<?php

# funcion que actualiza un registro de la tabla clientes.
function actualizar_cliente($conexion, $id, $nombre, $telefono, $correo, $direccion, $nit, $redirec) {
    // 1. Verificar duplicados en otros registros
    $check = $conexion->prepare("
        SELECT correo, nit, telefono 
        FROM clientes 
        WHERE (correo = ? OR nit = ? OR telefono = ?)
        AND id_cliente != ?
        LIMIT 1
    ");
    $check->execute([$correo, $nit, $telefono, $id]);
    $existe = $check->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
        if ($existe['correo'] === $correo) {
            $_SESSION['errores'][] = 'El correo ya está registrado en otro cliente';
        }
        if ($existe['nit'] === $nit) {
            $_SESSION['errores'][] = 'El NIT ya está registrado en otro cliente';
        }
        if ($existe['telefono'] === $telefono) {
            $_SESSION['errores'][] = 'El teléfono ya está registrado en otro cliente';
        }
        header("Location: $redirec");
        exit;
    }

    // 2. Actualizar si no hay duplicados
    $sentencia = "UPDATE clientes SET
            nombre=?,
            telefono=?,
            correo=?,
            direccion=?,
            nit=?
        WHERE id_cliente=?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$nombre, $telefono, $correo, $direccion, $nit, $id]);
}

?>