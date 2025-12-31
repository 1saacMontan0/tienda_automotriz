
<?php


# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
require ("../../models/actualizaciones/clientes.php"); # genera la tabla de clientes
$redirec = "../../pages/gestion/clientes.php"; # donde se enviara al usuario si algo falla.

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

actualizar_cliente(
    $conexion, $_POST['id'], $_POST['nombre'], $_POST['telefono'],
    $_POST['correo'], $_POST['direccion'], $_POST['nit'], $redirec
);

header("Location: $redirec");
exit;

?>