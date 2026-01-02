
<?php

# Aqui se generan reportes pdf, excel y json.

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/lecturas/empresa.php"); # informacion de empresas.
require ("../../models/lecturas/compras_pdf.php"); # tabla clientes
require ("../../models/lecturas/inventario_pdf.php"); # tabla clientes
require ("../../models/lecturas/inventario_excel.php"); # tabla clientes
require ("../../models/lecturas/inventario_json.php"); # tabla clientes
require ("../../controllers/filtros/check_session.php"); # comprobar session.

# para generar hoja de calculo.
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

$redirec = "../../pages/gestion/finanzas.php";  

if (empty($_POST['reporte'])) {
    $_SESSION['errores'][] = 'No decidio el tipo de reporte que desea generar';
    header("Location: $redirec");
    exit;
}

switch ($_POST['reporte']) {
    case 'pdf':
        echo "generando pdf";
        break;
    case 'excel':
        echo "generando excel";
        break;
    case 'json':
        echo "generando json";
        break;
    default:
        #$_SESSION['errores'][] = 'No decidio el tipo de reporte que desea generar';
        break;
}

#header("Location: $redirec");
#exit;

?>