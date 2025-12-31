
<?php

# Toma las credenciales ingresadas en el login y revisa si el usuario esta
# registrado en la base de datos, si el usuario existe, paso a evaluar  la
# contraseña.

# por  ultimo discrimino por rol al usuario y obtengo el id de la empresa a
# la que  el  usuario  pertenece,  para  luego filtrar los datos que de una
# empresa.

# archivos requeridos.
require('../../.config/.conexion.php'); # conexion a la base de datos
require('../../vendor/autoload.php'); # librerias composer y variables de entorno.
require('../../models/busquedas/usuarios.php'); # funcion para verificar usuarios.
$redirec = "../../index.php"; # donde se enviara al usuario si algo falla.

session_start();

# conexion a la base de datos.
$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);

# validaciones.
$usuario = $_POST['usuario'];
$secret = $_POST['contra'];

# verificar la existencia del usuario
verificar_usuarios($conexion, $usuario, $secret, $redirec);

# discriminar usuarios segun su rol.
switch($_SESSION['rol']) {
    case '1':
        header("Location: ../../pages/gestion/clientes.php");
        break;
    default :
        header("Location: $redireccion");
        break;
}
exit;

?>