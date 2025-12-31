
<?php

# Funcion que genera los registros para reportes de clientes.
function compras_excel($conexion, $id_empresa, $sheet) {
    // 1. Consulta combinada de inventario y compras
    $sentencia = "SELECT id_producto AS id, 'inventario' AS tipo, nombre, descripcion,
                        compatible_desde, compatible_hasta, unidades, precio_compra, fecha, hora, marca, modelo,
                        precio_venta
                  FROM inventario
                  WHERE id_empresa = ?
                  UNION ALL
                  SELECT id_compra AS id, 'servicio' AS tipo, '-' AS nombre,
                        descripcion, '-' AS compatible_desde, '-' AS compatible_hasta, cantidad AS unidades,
                        precio_compra, fecha, hora, '-' AS marca, '-' AS modelo, '-' AS precio_venta
                  FROM compras
                  WHERE id_empresa = ?
                  ORDER BY fecha DESC, hora DESC";

    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa, $id_empresa]);
    $items = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $filaNum = 3; // Punto de inicio

    if ($items) {
        foreach ($items as $fila) {
            $sheet->setCellValue('B' . $filaNum, $fila['tipo']);
            $sheet->setCellValue('C' . $filaNum, $fila['nombre']);
            $sheet->setCellValue('D' . $filaNum, $fila['descripcion']);
            $sheet->setCellValue('E' . $filaNum, $fila['compatible_desde']);
            $sheet->setCellValue('F' . $filaNum, $fila['compatible_hasta']);
            $sheet->setCellValue('G' . $filaNum, $fila['unidades']);
            $sheet->setCellValue('H' . $filaNum, $fila['precio_compra']);
            $sheet->setCellValue('I' . $filaNum, $fila['precio_venta']);
            $sheet->setCellValue('J' . $filaNum, $fila['marca']);
            $sheet->setCellValue('K' . $filaNum, $fila['modelo']);
            $sheet->setCellValue('L' . $filaNum, $fila['fecha'] . ' ' . $fila['hora']);
            
            $filaNum++;
        }

        // --- FILA DE TOTALES ---
        // $filaNum ya apunta a la siguiente fila vacía
        $sheet->setCellValue('G' . $filaNum, 'TOTAL:');

        // SUMA de unidades (columna G, desde G3 hasta la fila final de datos)
        $filaFinalDatos = $filaNum - 1;
        $sheet->setCellValue('H' . $filaNum, '=SUM(H3:H' . $filaFinalDatos . ')');

        // Estilo en negrita para resaltar el total
        $estiloTotal = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]
        ];
        $sheet->getStyle('G' . $filaNum . ':H' . $filaNum)->applyFromArray($estiloTotal);

    } else {
        $sheet->setCellValue('B3', 'No se encontraron registros de inventario o servicios para esta empresa.');
        $sheet->mergeCells('B3:L3');
    }

    $consulta = null;
}


?>