<?php 

# actualiza los registros de ingresos.
# Aqui se generan reportes pdf, excel y json.

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/lecturas/empresa.php"); # informacion de empresas.
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

# 2. Validar que la petición sea POST y contenga el ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    
    // Captura de datos del formulario
    $id_ingreso  = $_POST['id'];
    $descripcion = $_POST['descripcion'];
    $categoria   = $_POST['categoria'];
    $monto       = $_POST['monto'];

    try {
        # 3. Consulta de actualización preparada
        $sql = "UPDATE ingresos 
                SET descripcion = :descripcion, 
                    categoria = :categoria, 
                    monto = :monto 
                WHERE id_ingreso = :id";
        
        $stmt = $conexion->prepare($sql);
        
        # 4. Ejecución con mapeo de valores
        $resultado = $stmt->execute([
            ':descripcion' => $descripcion,
            ':categoria'   => $categoria,
            ':monto'       => $monto,
            ':id'          => $id_ingreso
        ]);

        if ($resultado) {
            $_SESSION['mensaje'] = "Registro actualizado correctamente";
        } else {
            $_SESSION['errores'][] = "No se pudo actualizar el registro";
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