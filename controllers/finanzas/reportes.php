
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

# resumen de ingresos:
$sql = "SELECT 
        SUM(v.total_v) AS total_ventas, 
        SUM(i.total_i) AS total_ingresos,
        (SUM(v.total_v) + SUM(i.total_i)) AS gran_total
    FROM 
        (SELECT IFNULL(SUM(total), 0) AS total_v FROM ventas) AS v,
        (SELECT IFNULL(SUM(monto), 0) AS total_i FROM ingresos) AS i";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$resumen_ingresos = $stmt->fetch(PDO::FETCH_ASSOC); # resultado.
$stmt->closeCursor();

# resumen egresos:
$sql = "SELECT 
        SUM(e.total_e) AS total_egresos, 
        SUM(c.total_c) AS total_compras,
        (SUM(e.total_e) + SUM(c.total_c)) AS gran_total
    FROM 
        (SELECT IFNULL(SUM(monto), 0) AS total_e FROM egresos) AS e,
        (SELECT IFNULL(SUM(cantidad * precio_compra), 0) AS total_c FROM compras) AS c";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$resumen_egresos = $stmt->fetch(PDO::FETCH_ASSOC); # resultado.
$stmt->closeCursor();

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
        $html .= "<h2 class='titulo' style='font-size:18pt;'>Resumen financiero</h2>";
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
        $html .= '<div class=contenedor-empresa><ul>';
        $html .= '<li><b>Ingresos totales: </b>$'.$resumen_ingresos['gran_total'].'</li>';
        $html .= '<li><b>Egresos totales: </b>$'.$resumen_egresos['gran_total'].'</li>';
        $html .= '<li><b>Utilidad neta: </b>$'.$resumen_ingresos['gran_total'] - $resumen_egresos['gran_total'].'</li>';
        $html .= '</ul></div>';

        $html .= '<h3>Ingresos</h3>';
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '  <tr>'; // Etiqueta tr obligatoria
        $html .= '    <th>Descripcion</th>';
        $html .= '    <th>Categoria</th>';
        $html .= '    <th>Monto</th>';
        $html .= '    <th>Fecha</th>';
        $html .= '  </tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        # Consulta para obtener los registros detallados
        $sql_detalle = "SELECT tipo_factura AS descripcion, 'ventas' AS categoria, total AS monto, fecha, hora 
            FROM ventas
            UNION ALL
            SELECT descripcion, categoria, monto, fecha, hora 
            FROM ingresos
            ORDER BY fecha DESC, hora DESC";
        $stmt_det = $conexion->prepare($sql_detalle);
        $stmt_det->execute();
        while ($row = $stmt_det->fetch(PDO::FETCH_ASSOC)) {
            $html .= '  <tr>';
            $html .= '    <td>' . htmlspecialchars($row['descripcion']) . '</td>';
            $html .= '    <td>' . htmlspecialchars($row['categoria']) . '</td>';
            $html .= '    <td>' . number_format($row['monto'], 2) . '</td>';
            $html .= '    <td>' . $row['fecha'] . '</td>';
            $html .= '  </tr>';
        }
        $stmt_det->closeCursor();
        $html .= '</tbody>';
        $html .= '</table>';

        $html .= '<h3>Egresos</h3>';
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '  <tr>';
        $html .= '    <th>Descripcion</th>';
        $html .= '    <th>Categoria</th>';
        $html .= '    <th>Monto</th>';
        $html .= '    <th>Fecha</th>';
        $html .= '  </tr>';
        $html .= '</thead>';
        $html .= '<tbody>';
        # Consulta para obtener los registros detallados de egresos y compras
        $sql_detalle = "SELECT descripcion, categoria, monto, fecha, hora 
                        FROM egresos
                        UNION ALL
                        SELECT descripcion, 'compras' AS categoria, (cantidad * precio_compra) AS monto, fecha, hora 
                        FROM compras
                        ORDER BY fecha DESC, hora DESC";
        $stmt_det = $conexion->prepare($sql_detalle);
        $stmt_det->execute();
        while ($row = $stmt_det->fetch(PDO::FETCH_ASSOC)) {
            $html .= '  <tr>';
            $html .= '    <td>' . htmlspecialchars($row['descripcion']) . '</td>';
            $html .= '    <td>' . htmlspecialchars($row['categoria']) . '</td>';
            $html .= '    <td>' . number_format($row['monto'], 2) . '</td>';
            $html .= '    <td>' . $row['fecha'] . '</td>';
            $html .= '  </tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';

        $stmt_det->closeCursor();

        $html .= "<p class='footer'>&copy; ".date('Y')." ".htmlspecialchars($empresa['nombre'])." - Sistema CRM + Inventario</p>";

        // 4. Procesar y Descargar
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
        
        // Limpiamos cualquier salida previa del buffer para evitar errores en el PDF
        if (ob_get_contents()) ob_end_clean();
        $mpdf->Output('resumen_financiero.pdf', \Mpdf\Output\Destination::DOWNLOAD);
        break;
    case 'excel':
        if (ob_get_level()) ob_end_clean();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Resumen Financiero');

        // --- ESTILOS ---
        $estiloEncabezado = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A5FB4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $estiloResumen = [
            'font' => ['bold' => true],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $filaActual = 2; // Iniciamos en la fila 2 según tu requerimiento

        // --- 1. TABLA DE RESUMEN ---
        $utilidad = $resumen_ingresos['gran_total'] - $resumen_egresos['gran_total'];
        
        $sheet->setCellValue("B$filaActual", "RESUMEN GENERAL");
        $sheet->getStyle("B$filaActual:C$filaActual")->applyFromArray($estiloEncabezado);
        $filaActual++;

        $sheet->setCellValue("B$filaActual", "Ingresos Totales:");
        $sheet->setCellValue("C$filaActual", $resumen_ingresos['gran_total']);
        $filaActual++;

        $sheet->setCellValue("B$filaActual", "Egresos Totales:");
        $sheet->setCellValue("C$filaActual", $resumen_egresos['gran_total']);
        $filaActual++;

        $sheet->setCellValue("B$filaActual", "Utilidad Neta:");
        $sheet->setCellValue("C$filaActual", $utilidad);
        
        // Formato moneda para el resumen
        $sheet->getStyle("C".($filaActual-2).":C$filaActual")->getNumberFormat()->setFormatCode('$#,##0.00');
        $sheet->getStyle("B".($filaActual-3).":C$filaActual")->applyFromArray($estiloResumen);

        $filaActual += 2; // Espacio de una fila (casilla de por medio)

        // --- 2. TABLA DE INGRESOS ---
        $sheet->setCellValue("B$filaActual", "DETALLE DE INGRESOS");
        $sheet->getStyle("B$filaActual:E$filaActual")->applyFromArray($estiloEncabezado);
        $filaActual++;

        $encabezadosIng = ['Descripción', 'Categoría', 'Monto', 'Fecha'];
        $sheet->fromArray([$encabezadosIng], NULL, "B$filaActual");
        $sheet->getStyle("B$filaActual:E$filaActual")->applyFromArray($estiloEncabezado);
        
        $inicioIngresos = $filaActual + 1;
        $sql_ing = "SELECT tipo_factura AS descripcion, 'ventas' AS categoria, total AS monto, fecha 
                    FROM ventas UNION ALL 
                    SELECT descripcion, categoria, monto, fecha FROM ingresos 
                    ORDER BY fecha DESC";
        $stmt = $conexion->prepare($sql_ing);
        $stmt->execute();
        $filaActual++;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sheet->setCellValue("B$filaActual", $row['descripcion']);
            $sheet->setCellValue("C$filaActual", $row['categoria']);
            $sheet->setCellValue("D$filaActual", $row['monto']);
            $sheet->setCellValue("E$filaActual", $row['fecha']);
            $filaActual++;
        }
        $sheet->getStyle("D$inicioIngresos:D$filaActual")->getNumberFormat()->setFormatCode('$#,##0.00');

        $filaActual += 2; // Espacio de una fila (casilla de por medio)

        // --- 3. TABLA DE EGRESOS ---
        $sheet->setCellValue("B$filaActual", "DETALLE DE EGRESOS");
        $sheet->getStyle("B$filaActual:E$filaActual")->applyFromArray($estiloEncabezado);
        $filaActual++;

        $encabezadosEgr = ['Descripción', 'Categoría', 'Monto', 'Fecha'];
        $sheet->fromArray([$encabezadosEgr], NULL, "B$filaActual");
        $sheet->getStyle("B$filaActual:E$filaActual")->applyFromArray($estiloEncabezado);
        
        $inicioEgresos = $filaActual + 1;
        $sql_egr = "SELECT descripcion, categoria, monto, fecha FROM egresos
                    UNION ALL
                    SELECT descripcion, 'compras' AS categoria, (cantidad * precio_compra) AS monto, fecha 
                    FROM compras ORDER BY fecha DESC";
        $stmt = $conexion->prepare($sql_egr);
        $stmt->execute();
        $filaActual++;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sheet->setCellValue("B$filaActual", $row['descripcion']);
            $sheet->setCellValue("C$filaActual", $row['categoria']);
            $sheet->setCellValue("D$filaActual", $row['monto']);
            $sheet->setCellValue("E$filaActual", $row['fecha']);
            $filaActual++;
        }
        $sheet->getStyle("D$inicioEgresos:D$filaActual")->getNumberFormat()->setFormatCode('$#,##0.00');

        // --- AJUSTES FINALES ---
        foreach (range('B', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Resumen_Financiero_Completo.xlsx"');
        header('Cache-Control: max-age=0');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    case 'json':
        if (ob_get_level()) ob_end_clean();

        // 1. Preparar el array contenedor
        $datos_reporte = [
            'empresa' => [],
            'resumen' => [],
            'ingresos' => [],
            'egresos' => []
        ];

        // 2. Obtener información de la empresa
        $registro_emp = optener_empresa($conexion, $_SESSION['id_empresa']);
        $datos_reporte['empresa'] = $registro_emp[0] ?? [];

        // 3. Calcular Resumen General
        $total_ingresos = (float)$resumen_ingresos['gran_total'];
        $total_egresos = (float)$resumen_egresos['gran_total'];
        $datos_reporte['resumen'] = [
            'total_ingresos' => $total_ingresos,
            'total_egresos'  => $total_egresos,
            'utilidad_neta'  => $total_ingresos - $total_egresos,
            'moneda'         => 'USD', // O la que utilices
            'fecha_reporte'  => date('Y-m-d H:i:s')
        ];

        // 4. Obtener Detalle de Ingresos
        $sql_ing = "SELECT tipo_factura AS descripcion, 'ventas' AS categoria, total AS monto, fecha, hora 
                    FROM ventas UNION ALL 
                    SELECT descripcion, categoria, monto, fecha, hora FROM ingresos 
                    ORDER BY fecha DESC, hora DESC";
        $stmt_ing = $conexion->prepare($sql_ing);
        $stmt_ing->execute();
        $datos_reporte['ingresos'] = $stmt_ing->fetchAll(PDO::FETCH_ASSOC);

        // 5. Obtener Detalle de Egresos
        $sql_egr = "SELECT descripcion, categoria, monto, fecha, hora FROM egresos
                    UNION ALL
                    SELECT descripcion, 'compras' AS categoria, (cantidad * precio_compra) AS monto, fecha, hora 
                    FROM compras ORDER BY fecha DESC, hora DESC";
        $stmt_egr = $conexion->prepare($sql_egr);
        $stmt_egr->execute();
        $datos_reporte['egresos'] = $stmt_egr->fetchAll(PDO::FETCH_ASSOC);

        // 6. Configurar Headers para descarga
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="resumen_financiero_' . date('Ymd') . '.json"');
        header('Cache-Control: max-age=0');

        // 7. Salida JSON limpia
        echo json_encode($datos_reporte, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);

        exit;
        break;
    default:
        $_SESSION['errores'][] = 'No decidio el tipo de reporte que desea generar';
        break;
}

header("Location: $redirec");
exit;

?>