<?php

# Genera la factura
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/lecturas/empresa.php"); # informacion de empresas.
require ("../../controllers/filtros/check_session.php"); # comprobar session.

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$redirec = "../../index.php"; 

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

$redirec = "../../pages/gestion/ventas.php";

$mpdf = new \Mpdf\Mpdf([
    'orientation' => 'L', 
    'margin_left' => 10,
    'margin_right' => 10,
    'margin_top' => 10,
    'margin_bottom' => 10
]);

// 1. Datos de la empresa
$registro = optener_empresa($conexion, $_SESSION['id_empresa']);
if (!isset($registro) || empty($registro)) {
    header("Location: ../../pages/gestion/clientes.php");
    exit;
}
$empresa = $registro[0];
$fecha_generacion = date('d/m/Y, H:i:s');

# 2. Datos de la venta con JOIN
$stmt = $conexion->prepare("
    SELECT v.*, c.nombre AS nombre_cliente, p.nombre AS nombre_producto, 
           p.marca, p.modelo, p.compatible_desde, p.compatible_hasta
    FROM ventas v
    INNER JOIN clientes c ON v.id_cliente = c.id_cliente
    INNER JOIN inventario p ON v.id_producto = p.id_producto
    WHERE v.id_venta = ?
");
$stmt->execute([$_POST['id']]);
$venta = $stmt->fetch(PDO::FETCH_ASSOC);

# Cálculos de IVA
$total = (float)$venta['total'];
$subtotal = $total / 1.13;
$iva = $total - $subtotal;

// 2. Estilos CSS actualizados para alineación
$stylesheet = "
    body { font-family: sans-serif; color: #333; }
    .titulo { text-align:center; color:#1a5fb4; font-size:22pt; font-weight:bold; margin-bottom:0; }
    .subtitulo { text-align:center; color:#252525; font-size:14pt; margin-top:5px; }
    .linea { border: none; border-top: 3px solid #1a5fb4; width: 100%; margin: 15px 0; }
    
    /* Estilos para la alineación Clave-Valor */
    .tabla-datos { width: 100%; border-collapse: collapse; margin: 20px 0; width:100%}
    .tabla-datos td { padding: 5px 0; border: none; font-size: 11pt; }
    .clave { width: 150px; font-weight: bold; color: #555; }
    .valor { text-align: left; }
    
    .contenedor-empresa { background:#f6f5f4; padding: 15px; border-radius: 10px; margin: 20px 0; border: 1px solid #ddd; }
    .footer { text-align:center; color:#666666; font-size:10pt; margin-top:20px; }
";

// 3. Construcción del HTML
$html = "<h2 class='titulo'>Numero: " . date("Ymd") . "-" . mt_rand(1000, 9999) . "</h2>";
$html .= '<h2 class="titulo">' . htmlspecialchars($venta['tipo_factura']) . '</h2>';
$html .= "<p class='subtitulo'>" . htmlspecialchars($empresa['nombre']) . "</p>";
$html .= "<p class='subtitulo'>Sistema CRM + Inventario</p>";
$html .= "<p class='subtitulo' style='font-size:12pt;'>Generado: $fecha_generacion</p>";
$html .= "<hr class='linea'>";

# --- BLOQUE DE DATOS ALINEADO (CLAVE - VALOR) ---
$html .= "<table class='tabla-datos'>";
$html .= "<tr><td class='clave'>Cliente:</td><td class='valor'>" . htmlspecialchars($venta['nombre_cliente']) . "</td></tr>";
$html .= "<tr><td class='clave'>Producto:</td><td class='valor'>" . htmlspecialchars($venta['nombre_producto']) . "</td></tr>";
$html .= "<tr><td class='clave'>Marca/Modelo:</td><td class='valor'>" . htmlspecialchars($venta['marca']) . " / " . htmlspecialchars($venta['modelo']) . "</td></tr>";
$html .= "<tr><td class='clave'>Año:</td><td class='valor'>" . $venta['compatible_desde'] . " - " . $venta['compatible_hasta'] . "</td></tr>";
$html .= "<tr><td class='clave'>Cantidad:</td><td class='valor'>" . $venta['cantidad'] . "</td></tr>";
$html .= "<tr><td class='clave'>Subtotal:</td><td class='valor'>$" . number_format($subtotal, 2) . "</td></tr>";
$html .= "<tr><td class='clave'>IVA (13%):</td><td class='valor'>$" . number_format($iva, 2) . "</td></tr>";
$html .= "<tr><td class='clave'>Total:</td><td class='valor'><strong>$" . number_format($total, 2) . "</strong></td></tr>";
$html .= "<tr><td class='clave'>Observaciones:</td><td class='valor'>" . (isset($venta['observaciones']) ? htmlspecialchars($venta['observaciones']) : 'N/A') . "</td></tr>";
$html .= "</table>";
# -----------------------------------------------

$html .= '<div class="contenedor-empresa">';
$html .= '<h3>Información de la Empresa</h3>';
$html .= '<ul>';
foreach ($empresa as $campo => $valor) {
    if ($campo == 'id_empresa') continue;
    $label = ucfirst(str_replace('_', ' ', $campo));
    $html .= "<li><strong>$label:</strong> " . htmlspecialchars($valor) . "</li>";
}
$html .= '</ul></div>';

$html .= "<p class='footer'>&copy; " . date('Y') . " " . htmlspecialchars($empresa['nombre']) . " - Sistema CRM + Inventario</p>";

// 4. Procesar y Descargar
$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);

if (ob_get_contents()) ob_end_clean();
$mpdf->Output('factura_' . $venta['id_venta'] . '.pdf', \Mpdf\Output\Destination::DOWNLOAD);
exit;