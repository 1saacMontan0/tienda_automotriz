
<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/lecturas/compras.php"); # mostrar tabla
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

if (isset($_POST['tipo'])) {
    if ($_POST['tipo'] === 'servicio') {
        $_SESSION['id_compra'] = $_POST['id'];
        header("Location:../../pages/gestion/actualizar_compras.php"); 
    }
    elseif ($_POST['tipo'] === 'inventario') {
        #header("Location:../../pages/gestion/actualizar_inventario.php");
        $_SESSION['id_inventario'] = $_POST['id'];
        header("Location:../../pages/gestion/actualizar_inventario.php"); 
    }
    else {
        header("Location:../../pages/gestion/compras.php");
    }
}
else {
    header("Location:../../pages/gestion/compras.php");
}
exit;

?>