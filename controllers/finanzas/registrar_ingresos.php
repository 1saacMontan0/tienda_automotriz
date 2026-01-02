<?php

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
$redirec = "../../index.php"; # donde se enviara al usuario si algo falla.

session_start();

# conexion a la base de datos
$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

// Ruta de redirección al finalizar
$redirec_ingresos = "../../pages/gestion/finanzas.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // 1. Captura de datos desde el formulario (names: descripcion, monto, categoria)
        // Usamos filter_input o saneamiento básico para mayor seguridad
        $descripcion = trim($_POST['descripcion']);
        $monto       = (float)$_POST['monto'];
        $categoria   = $_POST['categoria'];

        // Validación básica
        if (empty($descripcion) || $monto <= 0) {
            throw new Exception("Descripción o monto no válidos.");
        }

        // 2. REGISTRAR EL INGRESO
        // La tabla tiene: id_ingreso, descripcion, categoria, monto, fecha, hora
        $sql_insert = "INSERT INTO ingresos (descripcion, categoria, monto, fecha, hora) 
                       VALUES (?, ?, ?, CURDATE(), CURTIME())";

        $stmt = $conexion->prepare($sql_insert);
        
        // Ejecutamos pasando los 3 valores que vienen del formulario
        $stmt->execute([
            $descripcion, 
            $categoria, 
            $monto
        ]);

        // Redirección con éxito
        header("Location: $redirec_ingresos?res=success");
        exit;

    } catch (Exception $e) {
        // Manejo de errores
        echo "Error al registrar el ingreso: " . $e->getMessage();
        exit;
    }
}
?>