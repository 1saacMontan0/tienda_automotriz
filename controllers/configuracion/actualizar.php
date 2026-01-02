<?php

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/actualizaciones/empresa.php"); # mostrar tabla
require ("../../controllers/filtros/check_session.php"); # comprobar session.
$redirec = "../../pages/gestion/configuracion.php"; # donde se enviara al usuario si algo falla.

session_start();

# conexion a la base de datos
$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);

# verificar session
if (isset($_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'])) {
    check_session($conexion,
        $_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'], $redirec
    );
}
else {
    $_SESSION['errores'][] = 'usuario no autorizado';
    header("Location: $redirec");
    exit;
}

# Verificamos que vengan los datos mínimos necesarios (nombre y correo por ejemplo)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre'], $_POST['correo'])) {
    
    // Captura de datos del POST
    $id_empresa = $_SESSION['id_empresa']; // El ID se toma de la sesión por seguridad
    $nombre     = $_POST['nombre'];
    $telefono   = $_POST['telefono'];
    $correo     = $_POST['correo'];
    $direccion  = $_POST['direccion'] ?? null;
    $nit        = $_POST['nit'] ?? null;
    $nrc        = $_POST['nrc'] ?? null;
    $giro       = $_POST['giro'] ?? null;

    // Llamada a la función del modelo con los parámetros capturados
    $resultado = actualizar_empresa(
        $conexion,
        $id_empresa,
        $nombre,
        $telefono,
        $correo,
        $direccion,
        $nit,
        $nrc,
        $giro
    );

    if ($resultado) {
        $_SESSION['success'] = "Datos actualizados correctamente.";
    } else {
        $_SESSION['errores'][] = "Error al intentar actualizar los datos.";
    }

} else {
    $_SESSION['errores'][] = "Solicitud no válida.";
}

// Redirección final
header("Location: $redirec");
exit;

?>