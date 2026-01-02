<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ('../../utils/mensajes_back.php'); # mensajes de error
require ("../../models/lecturas/inventario.php"); # mostrar tabla
require ("../../controllers/filtros/check_session.php"); # comprobar session.
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

?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión inventario</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../../styles/inventario.css">
</head>
<body>
    <div id="appContent" class="tabs-container">
        <div class="tabs-header">
            <div class="tabs-title">🔒 CRM + Inventario Profesional - Sistema Seguro</div>
            <div class="tabs-nav">
                <button class="tab-button">
                        <a href=clientes.php>👥 Clientes</a>
                    </button>
                    <button class="tab-button">
                        <a href=compras.php>📦 Compras</a>
                    </button>
                    <button class="tab-button active">
                        <a href=inventario.php>📊 Inventario</a>
                    </button>
                    <button class="tab-button">
                        <a href=ventas.php>💰 Ventas</a>
                    </button>
                    <button class="tab-button">
                        <a href=finanzas.php>💼 Finanzas</a>
                    </button>
                    <button class="tab-button">
                        <a href=indicadores.php>📈 Indicadores</a>
                    </button>
                    <button class="tab-button">
                        <a href=configuracion.php>⚙️ Configuración</a>
                    </button>
                    <button class="tab-button logout-btn">🚪 Salir</button>
            </div>
        </div>

        <div class="tabs-content">
            <!-- ================= PESTAÑA INVENTARIO ================= -->
            <div id="inventario" class="tab-pane active">
                <div class="tab-content">
                    <div class="card">
                        <div class="card-header">
                            <h2>📊 Inventario Actual</h2>
                        </div>
                        <?php error_mensaje_back(); ?>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <form method=POST action=../../controllers/inventario/reportes.php>
                                    <div class="export-buttons">
                                        <button class="export-btn" name=reporte value=excel>📊 Excel</button>
                                        <button class="export-btn" name=reporte value=pdf>📄 PDF</button>
                                        <button class="export-btn" name=reporte value=json>📁 JSON</button>
                                    </div>
                                </form>
                            </div>

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Marca</th>
                                            <th>Modelo</th>
                                            <th>Año</th>
                                            <th>Stock</th>
                                            <th id="precioCompraHeader">Precio Compra</th>
                                            <th>Precio Venta</th>
                                            <th>Total Vendido</th>
                                            <th>Ganancia</th>
                                            <th>Última Actualización</th>
                                            <th>Acciones</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaInventario">
                                        <?php inventario($conexion, $_SESSION['id_empresa']); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
