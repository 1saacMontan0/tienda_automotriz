<?php

require ("../../vendor/autoload.php");
require ("../../.config/.conexion.php");
require ("../../controllers/filtros/check_session.php");
require ("../../models/inserciones/clientes.php");
$redirec = "../../index.php";

session_start();

$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'])) {
    check_session($conexion, $_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'], $redirec);
} else {
    header("Location: $redirec");
    exit;
}

$redirec_ventas = "../../pages/gestion/ventas.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_venta'])) {
    try {
        $id_venta = (int)$_POST['id_venta'];
        $cliente_nom  = $_POST['cliente'];
        $producto_nom = $_POST['producto'];
        $cantidad_nueva = (int)$_POST['cantidad'];
        $precio_unit  = (float)$_POST['precio_venta'];
        $tipo_fact    = $_POST['factura'];

        // 1. OBTENER LA CANTIDAD Y PRODUCTO ORIGINAL ANTES DE ACTUALIZAR
        // Esto es necesario para devolver las unidades al inventario antes de restar las nuevas
        $sql_old = "SELECT id_producto, cantidad FROM ventas WHERE id_venta = ?";
        $stmt_old = $conexion->prepare($sql_old);
        $stmt_old->execute([$id_venta]);
        $venta_anterior = $stmt_old->fetch(PDO::FETCH_ASSOC);

        if ($venta_anterior) {
            // 2. DEVOLVER EL STOCK ANTERIOR AL INVENTARIO
            $sql_restaurar = "UPDATE inventario SET unidades = unidades + ? WHERE id_producto = ?";
            $conexion->prepare($sql_restaurar)->execute([$venta_anterior['cantidad'], $venta_anterior['id_producto']]);
        }

        // 3. RECALCULAR TOTAL
        $subtotal = $cantidad_nueva * $precio_unit;
        $total_final = ($tipo_fact === "credito") ? ($subtotal * 1.13) : $subtotal;

        // 4. ACTUALIZAR EL REGISTRO DE VENTA
        $sql_update = "UPDATE ventas SET 
                        id_cliente = (SELECT id_cliente FROM clientes WHERE nombre = ? LIMIT 1),
                        id_producto = (SELECT id_producto FROM inventario WHERE nombre = ? LIMIT 1),
                        cantidad = ?, 
                        total = ?, 
                        tipo_factura = ?
                       WHERE id_venta = ?";

        $stmt_venta = $conexion->prepare($sql_update);
        $stmt_venta->execute([$cliente_nom, $producto_nom, $cantidad_nueva, $total_final, $tipo_fact, $id_venta]);

        // 5. RESTAR EL NUEVO STOCK
        // Usamos el nombre del producto y la empresa para identificarlo en el inventario
        $sql_update_stock = "UPDATE inventario 
                             SET unidades = unidades - ? 
                             WHERE nombre = ? AND id_empresa = ?";

        $stmt_stock = $conexion->prepare($sql_update_stock);
        $stmt_stock->execute([$cantidad_nueva, $producto_nom, $_SESSION['id_empresa']]);

        header("Location: $redirec_ventas?res=edit_success");
        exit;

    } catch (PDOException $e) {
        echo "Error en la actualización: " . $e->getMessage();
        exit;
    }
}

header("Location: $redirec_ventas");
exit;

?>