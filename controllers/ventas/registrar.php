<?php

require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
require ("../../models/inserciones/clientes.php"); # genera la tabla de clientes
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

$redirec_ventas = "../../pages/gestion/ventas.php"; 

// ... (Tus requires y validación de sesión se mantienen igual)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Datos del formulario
        $cliente_nom  = $_POST['cliente'];
        $producto_nom = $_POST['producto'];
        $cantidad     = (int)$_POST['cantidad'];
        $precio_unit  = (float)$_POST['precio_venta'];
        $tipo_fact    = $_POST['factura'];

        // Recálculo del total
        $subtotal = $cantidad * $precio_unit;
        $total_final = ($tipo_fact === "credito") ? ($subtotal * 1.13) : $subtotal;

        // 1. REGISTRAR LA VENTA
        $sql_insert = "INSERT INTO ventas (id_cliente, id_producto, cantidad, total, fecha, hora, tipo_factura) 
                       VALUES (
                           (SELECT id_cliente FROM clientes WHERE nombre = ? LIMIT 1),
                           (SELECT id_producto FROM inventario WHERE nombre = ? LIMIT 1),
                           ?, ?, CURDATE(), CURTIME(), ?
                       )";

        $stmt_venta = $conexion->prepare($sql_insert);
        $stmt_venta->execute([$cliente_nom, $producto_nom, $cantidad, $total_final, $tipo_fact]);

        // 2. RESTAR EL STOCK EN INVENTARIO
        // Restamos la cantidad enviada de la columna 'unidades' filtrando por el nombre
        $sql_update_stock = "UPDATE inventario 
                             SET unidades = unidades - ? 
                             WHERE nombre = ? AND id_empresa = ?";

        $stmt_stock = $conexion->prepare($sql_update_stock);
        
        // Ejecutamos pasando la cantidad, el nombre del producto y la empresa de la sesión
        $stmt_stock->execute([
            $cantidad, 
            $producto_nom, 
            $_SESSION['id_empresa']
        ]);

        header("Location: $redirec_ventas?res=success");
        exit;

    } catch (PDOException $e) {
        echo "Error en la transacción: " . $e->getMessage();
        exit;
    }
}


?>