
<?php

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/actualizaciones/compras.php"); # mostrar tabla
require ('../../utils/mensajes_back.php'); # mensajes de error
require ("../../controllers/filtros/check_session.php"); # comprobar session.
$redirec = "../../pages/gestion/compras.php"; # donde se enviara al usuario si algo falla.

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

if (isset($_POST['id_compra'], $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_compra'])) {
    actualizar_compra($conexion, $_POST['id_compra'], $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_compra']);
}
else {
    header("Location: $redirec");
    
}
header("Location: $redirec");
exit;

?>