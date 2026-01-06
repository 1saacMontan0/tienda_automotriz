<?php 

# actualiza los registros de ingresos.
require ("../../vendor/autoload.php");
require ("../../.config/.conexion.php");
require ("../../models/lecturas/empresa.php");
require ("../../controllers/filtros/check_session.php");

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$redirec = "../../index.php"; 

session_start();

$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);

if (isset($_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'])) {
    check_session($conexion, $_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'], $redirec);
} else {
    $_SESSION['errores'][] = 'usuario no autorizado';
    header("Location: $redirec");
    exit;
}

$redirec = "../../pages/gestion/finanzas.php#egresos";  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    
    $id_ingreso  = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $categoria   = $_POST['categoria'];
    $monto       = $_POST['monto'];

    try {
        # CORRECCIÓN: Tabla 'ingresos' y columna 'id_ingreso'
        $sql = "UPDATE egresos 
                SET descripcion = :descripcion, 
                    categoria = :categoria, 
                    monto = :monto 
                WHERE id_egreso = :id";
        
        $stmt = $conexion->prepare($sql);
        
        $resultado = $stmt->execute([
            ':descripcion' => $descripcion,
            ':categoria'   => $categoria,
            ':monto'       => $monto,
            ':id'          => $id_ingreso
        ]);

        if ($resultado && $stmt->rowCount() > 0) {
            $_SESSION['mensaje'] = "Registro actualizado correctamente";
        } else {
            # Si rowCount es 0, es porque los datos eran idénticos a los ya existentes
            $_SESSION['mensaje'] = "No hubo cambios que guardar";
        }

    } catch (PDOException $e) {
        $_SESSION['errores'][] = "Error en la base de datos: " . $e->getMessage();
    }
} else {
    $_SESSION['errores'][] = "Datos insuficientes para la actualización";
}

header("Location: $redirec");
exit;
?>