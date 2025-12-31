
<?php

# Funcion que registra nuevos clientes. Verifica que el correo, telefono y nit no se dupliquen


function registrar_clientes($conexion, $id_empresa, $nombre, $correo, $nit, $telefono, $direccion, $redirec) {
    // 1. Verificar duplicados
    $check = $conexion->prepare("
        SELECT correo, nit, telefono 
        FROM clientes 
        WHERE correo = ? OR nit = ? OR telefono = ?
        LIMIT 1
    ");
    $check->execute([$correo, $nit, $telefono]);
    $existe = $check->fetch(PDO::FETCH_ASSOC);

    if ($existe) {
        if ($existe['correo'] === $correo) {
            $_SESSION['errores'][] = 'El correo ya está registrado';
        }
        if ($existe['nit'] === $nit) {
            $_SESSION['errores'][] = 'El NIT ya está registrado';
        }
        if ($existe['telefono'] === $telefono) {
            $_SESSION['errores'][] = 'El teléfono ya está registrado';
        }
        header("Location: $redirec");
        exit;
    }

    // 2. Insertar si no hay duplicados
    $sentencia = "INSERT INTO clientes 
        (id_empresa, nombre, correo, nit, telefono, fecha, hora, direccion)
        VALUES (?, ?, ?, ?, ?, CURDATE(), CURTIME(), ?)";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa, $nombre, $correo, $nit, $telefono, $direccion]);
}



?>