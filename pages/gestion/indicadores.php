<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
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

# --- CONSULTA TOTAL VENTAS ---
$sentencia = "SELECT SUM(total) AS total_ventas FROM ventas";
$consulta = $conexion->prepare($sentencia);
$consulta->execute();
$datos = $consulta->fetch(PDO::FETCH_ASSOC);
$totalVentas = $datos['total_ventas'] ?? 0;
$consulta->closeCursor();

# --- CONSULTA TOTAL VENTAS ---
$sentencia = "SELECT SUM(precio_compra) AS total_compras FROM compras";
$consulta = $conexion->prepare($sentencia);
$consulta->execute();
$datos = $consulta->fetch(PDO::FETCH_ASSOC);
$totalcompras = $datos['total_compras'] ?? 0;
$consulta->closeCursor();

$sentencia = " SELECT i.nombre AS nombre
    FROM ventas v
    JOIN inventario i ON v.id_producto = i.id_producto
    GROUP BY i.id_producto, i.nombre
    ORDER BY SUM(v.cantidad) DESC
    LIMIT 1";
$consulta = $conexion->prepare($sentencia);
$consulta->execute();
$datos = $consulta->fetch(PDO::FETCH_ASSOC);
$productoMasVendido = $datos['nombre'] ?? 0;
$consulta->closeCursor();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Indicadores</title>
    <link rel="stylesheet" href="../../styles/finanzas.css">
    <style>
        /* CSS INTEGRADO ESPECÍFICO */
        :root {
            --primary-color: #1a5fb4;
            --primary-dark: #1c71d8;
            --secondary-color: #26a269;
            --warning-color: #e5a50a;
            --danger-color: #c01c28;
            --bg-color: #f6f5f4;
            --card-bg: #ffffff;
            --text-main: #2e3436;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 0;
        }

        .tabs-content { padding: 20px; }

        .indicators-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 2rem;
        }

        .indicator-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
            border: 1px solid #eee;
        }

        .indicator-value {
            font-size: 1.8rem;
            font-weight: bold;
            margin: 10px 0;
        }

        .indicator-label {
            font-size: 0.8rem;
            color: #888;
        }

        .table-responsive { overflow-x: auto; margin-top: 1rem; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        
        .export-buttons { display: flex; gap: 10px; }
        .export-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            background: #eee;
        }
    </style>
</head>
<body>

    <div id="appContent" class="tabs-container">
        <div class="tabs-header">
            <div class="tabs-title">🔒 CRM + Inventario Profesional - Sistema Seguro</div>
            <div class="tabs-nav">
                <button class="tab-button"><a href="clientes.php">👥 Clientes</a></button>
                <button class="tab-button"><a href="compras.php">📦 Compras</a></button>
                <button class="tab-button"><a href="inventario.php">📊 Inventario</a></button>
                <button class="tab-button"><a href="ventas.php">💰 Ventas</a></button>
                <button class="tab-button"><a href="finanzas.php">💼 Finanzas</a></button>
                <button class="tab-button active"><a href="indicadores.php">📈 Indicadores</a></button>
                <button class="tab-button"><a href="configuracion.php">⚙️ Configuración</a></button>
                <button class="tab-button logout-btn">🚪 Salir</button>
            </div>
        </div>

        <div class="tabs-content">
            <div id="indicadores" class="tab-pane active">
                <div class="card">
                    <div class="card-header" style="background: var(--primary-color); padding: 10px 20px; border-radius: 8px 8px 0 0;">
                        <h2 style="margin:0; color: white;">📈 Indicadores y Gráficos</h2>
                    </div>
                    <div class="card-body" style="background: white; padding: 25px; border: 1px solid #ddd; border-top: none;">
                        
                        <div class="indicators-grid">
                            <div class="indicator-card">
                                <h3 style="font-size: 1rem; color: #444;">💰 Total Ventas</h3>
                                <div class="indicator-value" style="color: var(--primary-color);">
                                    $<?php echo number_format($totalVentas, 2); ?>
                                </div>
                                <div class="indicator-label">Acumulado</div>
                            </div>
                            <div class="indicator-card">
                                <h3 style="font-size: 1rem; color: #444;">📦 Total Compras</h3>
                                <div class="indicator-value" style="color: var(--warning-color);">
                                    $<?php echo number_format($totalcompras, 2); ?>
                                </div>
                                <div class="indicator-label">Acumulado</div>
                            </div>
                            <div class="indicator-card">
                                <h3 style="font-size: 1rem; color: #444;">📈 Utilidad Neta</h3>
                                <div class="indicator-value" style="color: var(--secondary-color);">
                                    $<?php echo $totalVentas - $totalcompras; ?>
                                </div>
                                <div class="indicator-label">Beneficio neto</div>
                            </div>
                            <div class="indicator-card">
                                <h3 style="font-size: 1rem; color: #444;">🏆 Producto Más Vendido</h3>
                                <div class="indicator-value" style="color: var(--primary-dark);">
                                    <?php echo $productoMasVendido; ?>
                                </div>
                                <div class="indicator-label">Top producto</div>
                            </div>
                        </div>

                        <div style="margin-top: 2rem;">
                            <h3 style="margin-bottom: 15px;">👑 Top 10 Clientes</h3>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Cliente</th>
                                            <th>Total Comprado</th>
                                            <th>N° Compras</th>
                                            <th>Última Compra</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tablaTopClientes">
                                        <?php
                                        try {
                                            // Consulta preparada con PDO
                                            $sql = "SELECT c.nombre,
                                                        SUM(v.total) AS total_comprado,
                                                        COUNT(v.id_venta) AS numero_compras,
                                                        MAX(v.fecha) AS ultima_compra
                                                    FROM ventas v
                                                    JOIN clientes c ON v.id_cliente = c.id_cliente
                                                    GROUP BY c.id_cliente, c.nombre
                                                    ORDER BY numero_compras DESC
                                                    LIMIT 10";

                                            $stmt = $conexion->prepare($sql);
                                            $stmt->execute();
                                            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            if ($clientes) {
                                                $contador = 1;
                                                foreach ($clientes as $row) {
                                                    echo "<tr>";
                                                    echo "<td>" . $contador++ . "</td>";
                                                    echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                                                    echo "<td>$" . number_format($row['total_comprado'], 2) . "</td>";
                                                    echo "<td>" . $row['numero_compras'] . "</td>";
                                                    echo "<td>" . $row['ultima_compra'] . "</td>";
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo '<tr><td colspan="5" style="text-align:center;">No hay datos de clientes</td></tr>';
                                            }
                                        } catch (PDOException $e) {
                                            echo '<tr><td colspan="5" style="text-align:center;">Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
                                        }
                                        ?>
                                        </tbody>
                                </table>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
                            <form method=POST action=../../controllers/indicadores/reportes.php>
                                <div class="export-buttons">
                                    <button class="export-btn" name=reporte value=excel>📊 Excel</button>
                                    <button class="export-btn" name=reporte value=pdf>📄 PDF</button>
                                    <button class="export-btn" name=reporte value=json>📁 JSON</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>