
<?php

# funcion para verificar si el usuario que se loggeo esta registrado.

function verificar_usuarios($conexion, $user, $contra, $redirec) {
    # sentencia SQL
    $sentencia = "SELECT id_empresa, usuario, contra, rol FROM usuarios WHERE usuario = ?";
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$user]); # parametros
    $usuario = $consulta->fetch(PDO::FETCH_ASSOC); # guarda retorno de la consulta

    # si el usuario existe
    if ($usuario) {
        if ($contra === $usuario['contra']) { # compruebo la contraseña
            $_SESSION['usuario'] = $usuario['usuario'];
            $_SESSION['rol'] = $usuario['rol'];
            $_SESSION['id_empresa'] = $usuario['id_empresa'];
        }
        else { # si la contraseña fuera incorrecta
            $_SESSION['errores'][] = 'La contraseña es incorrecta';
            header("Location: $redirec");
            exit;
        }
    }
    # si el usuario no existe
    else {
        $_SESSION['errores'][] = 'El usuario no existe.';
        header("Location: $redirec");
        exit;
    }
}


?>