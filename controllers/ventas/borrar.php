
<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/eliminar/compras.php"); # mostrar tabla
require ('../../utils/mensajes_back.php'); # mensajes de error
require ("../../controllers/filtros/check_session.php"); # comprobar session.
$redirec = "../../index.php"; # donde se enviara al usuario si algo falla.

session_start();

# conexion a la base de datos
$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);
# verificar session
if (isset($_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'], $_POST['id'])) {
    check_session($conexion,
        $_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'], $redirec
    );
}
else {
    $_SESSION['errores'][] = 'usuario no autorizado';
    header("Location: $redirec");
    exit;
}

$redirec = "../../pages/gestion/ventas.php";

$id_venta = (int)$_POST['id'];
// Sentencia preparada para eliminar directamente
$sql_delete = "DELETE FROM ventas WHERE id_venta = ?";
$stmt = $conexion->prepare($sql_delete);
// Ejecución pasando el ID en el arreglo
if ($stmt->execute([$id_venta])) {
    header("Location: $redirec");
    exit;
}
header("Location: $redirec");
exit;



?>