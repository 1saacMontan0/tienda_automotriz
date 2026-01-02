
<?php

# Aqui se generan reportes pdf, excel y json.

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/lecturas/empresa.php"); # informacion de empresas.
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

$redirec = "../../pages/gestion/inventario.php";  

if (empty($_POST['reporte'])) {
    $_SESSION['errores'][] = 'No decidio el tipo de reporte que desea generar';
    header("Location: $redirec");
    exit;
}

switch ($_POST['reporte']) {
    case 'pdf':
        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L', // 'L' para que quepan todas las columnas (Horizontal)
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

        // 2. Definición de estilos CSS para mPDF
        $stylesheet = "
            body { font-family: sans-serif; }
            .titulo { text-align:center; color:#1a5fb4; font-size:22pt; font-weight:bold; margin-bottom:0; }
            .subtitulo { text-align:center; color:#252525; font-size:14pt; margin-top:5px; }
            .contenedor-empresa { background:#f6f5f4; padding: 15px; border-radius: 10px; margin: 20px 0; }
            .contenedor-empresa ul { list-style-type:none; padding:0; }
            table { width:100%; border-collapse:collapse; margin-top:15px; }
            th { background:#1a5fb4; color:white; font-weight:bold; padding:8px; border:1px solid #ccc; text-align:left; }
            td { padding:8px; border:1px solid #ccc; font-size:10pt; }
            .footer { text-align:center; color:#666666; font-size:10pt; margin-top:20px; }
        ";

        // 3. Construcción del HTML
        $html = "<h1 class='titulo'>".htmlspecialchars($empresa['nombre'])."</h1>";
        $html .= "<h2 class='titulo' style='font-size:18pt;'>Reporte de Inventario</h2>";
        $html .= "<p class='subtitulo'>Sistema CRM + Inventario</p>";
        $html .= "<p class='subtitulo' style='font-size:12pt;'>Generado: $fecha_generacion</p>";

        $html .= '<div class="contenedor-empresa">';
        $html .= '<h3>Información de la Empresa</h3>';
        $html .= '<ul>';
        foreach ($empresa as $campo => $valor) {
            $label = ucfirst(str_replace('_', ' ', $campo));
            $html .= "<li><strong>$label:</strong> $valor</li>";
        }
        $html .= '</ul></div>';

        $html .= '<table>';
        $html .= '<thead>';
        $html .= '  <tr>'; // Etiqueta tr obligatoria
        $html .= '    <th>Cliente</th>';
        $html .= '    <th>Producto</th>';
        $html .= '    <th>Marca / Modelo</th>';
        $html .= '    <th>Año</th>';
        $html .= '    <th>Total</th>';
        $html .= '    <th>Ganancia</th>';
        $html .= '    <th>Factura</th>';
        $html .= '    <th>Fecha</th>';
        $html .= '  </tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        $html .= generar_inventario($conexion, $_SESSION['id_empresa']);
        $html .= '</tbody>';
        $html .= '</table>';

        // Para el total real de clientes, cuenta el número de filas del reporte
        $html .= "<p class='footer'>&copy; ".date('Y')." ".htmlspecialchars($empresa['nombre'])." - Sistema CRM + Inventario</p>";

        // 4. Procesar y Descargar
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        
        // Limpiamos cualquier salida previa del buffer para evitar errores en el PDF
        if (ob_get_contents()) ob_end_clean();
        $mpdf->Output('Reporte_inventario.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        break;
    case 'excel':
        if (ob_get_level()) ob_end_clean();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Nombres de los encabezados adaptados a inventario + servicios
        $encabezados = [
            'Nombre', 'Marca', 'Modelo', 'Unidades', 'Compatible Desde', 'Compatible Hasta',
            'Precio Compra', 'Precio Venta', 'Total vendido', 'Ganancia', 'Registro'
        ];

        // 2. Insertar encabezados a partir de B2
        $sheet->fromArray([$encabezados], NULL, 'B2');

        // 3. Definir el estilo (Fondo #1a5fb4 y Letra Blanca)
        $estiloEncabezado = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // Letra Blanca
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1A5FB4'], // Fondo Azul
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];

        // 4. Aplicar el estilo al rango del encabezado (B2 hasta L2)
        $sheet->getStyle('B2:L2')->applyFromArray($estiloEncabezado);

        // 5. Llamar a la función adaptada
        inventario_excel($conexion, $_SESSION['id_empresa'], $sheet);

        // 6. Auto-ajustar el ancho de las columnas para que se lea bien
        foreach (range('B', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // 7. Headers para la descarga
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="reporte_inventario.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
        break;
    case 'json':
        if (ob_get_level()) ob_end_clean();

        // 2. Obtener los datos
        $datos = inventario_json($conexion, $_SESSION['id_empresa']);

        // 4. HEADERS PARA FORZAR DESCARGA
        header('Content-Type: application/json; charset=utf-8');
        // Fuerza al navegador a descargar el archivo con un nombre específico
        header('Content-Disposition: attachment; filename="reporte_inventario.json"');
        // Evita el almacenamiento en caché
        header('Cache-Control: max-age=0');

        // 5. Convertir a JSON y enviar al flujo de salida
        echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        // 6. Finalizar para que no se envíe nada más
        exit;
        break;
    default:
        $_SESSION['errores'][] = 'No decidio el tipo de reporte que desea generar';
        break;
}

header("Location: $redirec");
exit;

?>