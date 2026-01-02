<?php

function ventas_excel($conexion, $id_empresa, $sheet) {
    // 1. Consulta con todos los campos solicitados
    $sentencia = "SELECT 
            c.nombre AS cliente_nombre,
            i.nombre AS producto_nombre, 
            i.marca, 
            i.modelo, 
            i.compatible_desde, 
            i.compatible_hasta,
            v.cantidad AS unidades, 
            v.total AS total_vendido,
            (v.total - (i.precio_compra * v.cantidad)) AS ganancias,
            v.tipo_factura,
            v.fecha, 
            v.hora
        FROM ventas v
        INNER JOIN inventario i ON v.id_producto = i.id_producto
        INNER JOIN clientes c ON v.id_cliente = c.id_cliente
        WHERE i.id_empresa = ?
        ORDER BY v.fecha DESC, v.hora DESC";

    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    $items = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $filaNum = 3; 

    if ($items) {
        foreach ($items as $fila) {
            // 2. Mapeo siguiendo tu lista de datos solicitada
            $sheet->setCellValue('B' . $filaNum, $fila['cliente_nombre']);
            $sheet->setCellValue('C' . $filaNum, $fila['producto_nombre']);
            $sheet->setCellValue('D' . $filaNum, $fila['marca']);
            $sheet->setCellValue('E' . $filaNum, $fila['modelo']);
            $sheet->setCellValue('F' . $filaNum, $fila['compatible_desde']);
            $sheet->setCellValue('G' . $filaNum, $fila['compatible_hasta']);
            $sheet->setCellValue('H' . $filaNum, $fila['unidades']);
            $sheet->setCellValue('I' . $filaNum, $fila['total_vendido']);
            $sheet->setCellValue('J' . $filaNum, $fila['ganancias']);
            $sheet->setCellValue('K' . $filaNum, $fila['tipo_factura']);
            $sheet->setCellValue('L' . $filaNum, $fila['fecha'] . ' ' . $fila['hora']);
            
            $filaNum++;
        }

        // --- FILA DE TOTALES ---
        $sheet->setCellValue('G' . $filaNum, 'TOTAL:');
        $filaFinalDatos = $filaNum - 1;

        // Sumatoria de Unidades(H), Total(I) y Ganancias(J)
        $sheet->setCellValue('H' . $filaNum, '=SUM(H3:H' . $filaFinalDatos . ')');
        $sheet->setCellValue('I' . $filaNum, '=SUM(I3:I' . $filaFinalDatos . ')');
        $sheet->setCellValue('J' . $filaNum, '=SUM(J3:J' . $filaFinalDatos . ')');

        $estiloTotal = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]
        ];
        // Aplicar estilo al rango de totales
        $sheet->getStyle('G' . $filaNum . ':J' . $filaNum)->applyFromArray($estiloTotal);

    } else {
        $sheet->setCellValue('B3', 'No se encontraron registros de ventas para esta empresa.');
        $sheet->mergeCells('B3:L3');
    }

    $consulta = null;
}

?>