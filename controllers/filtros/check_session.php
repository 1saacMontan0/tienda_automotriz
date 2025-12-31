
<?php

    # verifico que las sessiones que se inician cuan el login tiene
    # exito esten llenas y para   evitar   que   las sessiones sean
    # manipuladas reviso que los datos esten registrados en la base
    # de datos.

    function check_session($conexion, $user, $id_empresa, $rol, $redirec) {
        $sentencia = "SELECT id_empresa, usuario, rol
            FROM usuarios WHERE id_empresa=? AND usuario=? AND rol=?";
        $consulta = $conexion->prepare($sentencia);
        $consulta->execute([$id_empresa, $user, $rol]); # parametros
        $usuario = $consulta->fetch(PDO::FETCH_ASSOC); # guarda retorno de la consulta

        # Si el usuario no existe
        if ($usuario === false) {
            $_SESSION['errores'][] = 'usuario no autorizado';
            header("Location: $redirec");
            exit;
        }
    }

?>