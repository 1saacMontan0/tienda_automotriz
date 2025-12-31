
<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
require ("../../models/inserciones/clientes.php"); # genera la tabla de clientes
$redirec = "../../index.php"; # donde se enviara al usuario si algo falla.

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

$redirec = "../../pages/gestion/clientes.php";  

// En registrar.php
registrar_clientes(
    $conexion, $_SESSION['id_empresa'], $_POST['nombre'], $_POST['correo'],
    $_POST['nit'], $_POST['telefono'], $_POST['direccion'], $redirec
);

# devuelvo al login
header("Location: ../../pages/gestion/clientes.php");
exit;

?>