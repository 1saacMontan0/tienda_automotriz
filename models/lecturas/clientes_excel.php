
<?php

# Funcion que genera los registros para reportes de clientes.
function celda_clientes_excel($conexion, $id_empresa, $sheet) {
    // 1. Consulta con JOIN para traer el total real de ventas
    $sentencia = "SELECT c.id_cliente, c.nombre, c.correo, c.nit, c.telefono, c.fecha, c.hora, c.direccion,
                  IFNULL(SUM(v.total), 0) as total_ventas
                  FROM clientes c
                  LEFT JOIN ventas v ON c.id_cliente = v.id_cliente
                  WHERE c.id_empresa = ?
                  GROUP BY c.id_cliente";
    
    $consulta = $conexion->prepare($sentencia);
    $consulta->execute([$id_empresa]);
    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

    $filaNum = 3; // Punto de inicio

    if ($usuarios) {
        foreach ($usuarios as $fila) {
            $sheet->setCellValue('B' . $filaNum, $fila['nombre']);
            $sheet->setCellValue('C' . $filaNum, $fila['telefono']);
            $sheet->setCellValue('D' . $filaNum, $fila['correo']);
            $sheet->setCellValue('E' . $filaNum, $fila['direccion']);
            $sheet->setCellValue('F' . $filaNum, ''); 
            $sheet->setCellValue('G' . $filaNum, $fila['nit']);
            $sheet->setCellValue('H' . $filaNum, $fila['total_ventas']); 
            $sheet->setCellValue('I' . $filaNum, $fila['fecha'] . ' ' . $fila['hora']);
            
            $filaNum++;
        }

        // --- FILA DE TOTALES ---
        // $filaNum ya tiene el valor de la siguiente fila vacía
        
        // Colocamos el texto "TOTAL" en la columna G
        $sheet->setCellValue('G' . $filaNum, 'TOTAL:');
        
        // Colocamos la SUMA en la columna H (Desde H3 hasta la fila anterior al total)
        $filaFinalDatos = $filaNum - 1;
        $sheet->setCellValue('H' . $filaNum, '=SUM(H3:H' . $filaFinalDatos . ')');

        // Aplicamos un estilo de negrita al total para que resalte
        $estiloTotal = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT]
        ];
        $sheet->getStyle('G' . $filaNum . ':H' . $filaNum)->applyFromArray($estiloTotal);

    } else {
        $sheet->setCellValue('B3', 'No se encontraron clientes para esta empresa.');
        $sheet->mergeCells('B3:I3');
    }

    $consulta = null;
}

?>