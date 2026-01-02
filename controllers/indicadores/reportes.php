
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

$redirec = "../../pages/gestion/indicadores.php";  

if (empty($_POST['reporte'])) {
    $_SESSION['errores'][] = 'No decidio el tipo de reporte que desea generar';
    header("Location: $redirec");
    exit;
}

switch ($_POST['reporte']) {
    case 'pdf':
        // --- 1. CÁLCULOS Y CONSULTAS ---

// Total Ventas
$sentencia = "SELECT SUM(total) AS total_ventas FROM ventas";
$consulta = $conexion->prepare($sentencia);
$consulta->execute();
$datosV = $consulta->fetch(PDO::FETCH_ASSOC);
$totalVentas = $datosV['total_ventas'] ?? 0;
$consulta->closeCursor();

// Total Compras
$sentencia = "SELECT SUM(precio_compra) AS total_compras FROM compras";
$consulta = $conexion->prepare($sentencia);
$consulta->execute();
$datosC = $consulta->fetch(PDO::FETCH_ASSOC);
$totalCompras = $datosC['total_compras'] ?? 0;
$consulta->closeCursor();

// Utilidad Neta
$utilidadNeta = $totalVentas - $totalCompras;

// Producto Más Vendido
$sentencia = "SELECT i.nombre AS nombre
    FROM ventas v
    JOIN inventario i ON v.id_producto = i.id_producto
    GROUP BY i.id_producto, i.nombre
    ORDER BY SUM(v.cantidad) DESC
    LIMIT 1";
$consulta = $conexion->prepare($sentencia);
$consulta->execute();
$datosP = $consulta->fetch(PDO::FETCH_ASSOC);
$productoMasVendido = $datosP['nombre'] ?? 'N/A';
$consulta->closeCursor();


// --- 2. CONFIGURACIÓN DE MPDF ---

$mpdf = new \Mpdf\Mpdf([
    'orientation' => 'L', 
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10
]);

// Obtener datos de empresa
$registro = optener_empresa($conexion, $_SESSION['id_empresa']);
if (!isset($registro) || empty($registro)) {
    header("Location: ../../pages/gestion/clientes.php");
    exit;
}
$empresa = $registro[0];
$fecha_generacion = date('d/m/Y, H:i:s');


// --- 3. ESTILOS CSS (Compatibles con mPDF) ---

$stylesheet = "
    body { font-family: sans-serif; }
    .titulo { text-align:center; color:#1a5fb4; font-size:22pt; font-weight:bold; margin-bottom:0; }
    .subtitulo { text-align:center; color:#252525; font-size:14pt; margin-top:5px; }
    .contenedor-empresa { background:#f6f5f4; padding: 15px; border-radius: 10px; margin: 20px 0; clear: both; }
    .contenedor-empresa ul { list-style-type:none; padding:0; }
    .footer { text-align:center; color:#666666; font-size:10pt; margin-top:20px; clear: both; }

    /* Cuadrícula usando Floats para mPDF */
    .fila { width: 100%; clear: both; margin-bottom: 20px; }
    .div-cuadricula {
        float: left;
        width: 46%; /* Casi la mitad */
        background:#f6f5f4;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        font-size: 12pt;
        font-weight: bold;
        border: 1px solid #ddd;
    }
    .dato-valor {
        display: block;
        font-size: 16pt;
        color: #1a5fb4;
        margin-top: 8px;
    }
    .espacio-derecha { margin-right: 2%; }
";


// --- 4. CONSTRUCCIÓN DEL HTML ---

$html = "<h1 class='titulo'>".htmlspecialchars($empresa['nombre'])."</h1>";
$html .= "<h2 class='titulo' style='font-size:18pt;'>Reporte de Inventario</h2>";
$html .= "<p class='subtitulo'>Sistema CRM + Inventario</p>";
$html .= "<p class='subtitulo' style='font-size:12pt;'>Generado: $fecha_generacion</p>";

$html .= '<div class="contenedor-empresa">';
$html .= '<h3>Información de la Empresa</h3><ul>';
foreach ($empresa as $campo => $valor) {
    $label = ucfirst(str_replace('_', ' ', $campo));
    $html .= "<li><strong>$label:</strong> $valor</li>";
}
$html .= '</ul></div>';

// Sección de Cuadros (Dashboard)
$html .= '<div class="fila">';
$html .= '  <div class="div-cuadricula espacio-derecha">Total Ventas<br><span class="dato-valor">$'.number_format($totalVentas, 2).'</span></div>';
$html .= '  <div class="div-cuadricula">Total Compras<br><span class="dato-valor">$'.number_format($totalCompras, 2).'</span></div>';
$html .= '</div>';

$html .= '<div class="fila">';
$html .= '  <div class="div-cuadricula espacio-derecha">Utilidad Neta<br><span class="dato-valor">$'.number_format($utilidadNeta, 2).'</span></div>';
$html .= '  <div class="div-cuadricula">Producto más vendido<br><span class="dato-valor" style="font-size:12pt;">'.htmlspecialchars($productoMasVendido).'</span></div>';
$html .= '</div>';

$html .= "<p class='footer'>&copy; ".date('Y')." ".htmlspecialchars($empresa['nombre'])." - Sistema CRM + Inventario</p>";


// --- 5. GENERACIÓN DEL PDF ---

$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

if (ob_get_contents()) ob_end_clean();
$mpdf->Output('Reporte_inventario.pdf', \Mpdf\Output\Destination::DOWNLOAD);
exit;
    default:
        $_SESSION['errores'][] = 'No decidio el tipo de reporte que desea generar';
        break;
}

header("Location: $redirec");
exit;

?>