
<?php

# esta pagina resive los datos del formulario de compras y registra 
# la compra

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
require ("../../models/inserciones/compras.php"); # registrar compras
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

if (isset($_POST['tipo'])) {
    // Se corrigió "iventario" por "inventario"
    if ($_POST['tipo'] === 'inventario') { 
        if ( isset($_POST['producto'], $_POST['descripcion'], $_POST['compatible_desde'],
            $_POST['compatible_hasta'], $_POST['cantidad'], $_POST['precio_compra'],
            $_POST['marca'], $_POST['modelo'], $_POST['precio_venta'])
        ) {
            registrar_inventario(
                $conexion, $_SESSION['id_empresa'], $_POST['producto'], $_POST['descripcion'],
                $_POST['compatible_desde'], $_POST['compatible_hasta'], $_POST['cantidad'],
                $_POST['precio_compra'], $_POST['marca'], $_POST['modelo'], $_POST['precio_venta']
            );
            header("Location: $redirec");
        }
        else {
            $_SESSION['errores'][] = 'Faltan datos';
            header("Location: $redirec");
            exit;
        }
    }
    elseif ($_POST['tipo'] === 'servicio') {
        if ( isset( $_POST['descripcion'], $_POST['cantidad'], $_POST['precio_compra']) ) {
            compra_general(
                $conexion, $_SESSION['id_empresa'], $_POST['descripcion'],
                $_POST['cantidad'], $_POST['precio_compra']
            );
            header("Location: $redirec");
        }
        else {
            $_SESSION['errores'][] = 'Faltan datos';
            header("Location: $redirec");
            exit;
        }
    }
    else {
        header("Location: $redirec");
    }
}
else {
    header("Location: $redirec");
}
exit;

?>