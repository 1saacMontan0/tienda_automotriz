<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
require ("../../models/lecturas/finanzas.php");
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
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Finanzas</title>
    <link rel="stylesheet" href="../../styles/finanzas.css">
    <style>
        /* CSS INTEGRADO ESPECÍFICO PARA FINANZAS */
        :root {
            --primary-color: #1a5fb4;
            --secondary-color: #26a269;
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
            padding: 0; /* Quitamos el padding para que el nav ocupe todo el ancho */
        }

        /* Estilos de las pestañas internas de finanzas */
        .finance-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .finance-tab {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            background: #e5e5e5;
            color: #555;
        }

        .finance-tab.active {
            color: white;
        }

        /* Colores específicos para botones activos internos */
        .finance-tab[onclick*="ingresos"].active { background: var(--secondary-color); }
        .finance-tab[onclick*="egresos"].active { background: var(--danger-color); }
        .finance-tab[onclick*="resumen"].active { background: var(--primary-color); }

        .finance-pane {
            display: none;
            animation: fadeIn 0.4s ease;
        }

        .finance-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }

        .financial-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .summary-card {
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border-left: 5px solid;
        }

        .summary-card.income { background: #eafaf1; border-color: var(--secondary-color); }
        .summary-card.expense { background: #fdf2f2; border-color: var(--danger-color); }
        .summary-card.profit { background: #eef4fb; border-color: var(--primary-color); }

        .summary-value { font-size: 1.5rem; font-weight: bold; margin: 10px 0; }
        
        /* Ajuste para que el contenido no pegue al nav */
        .tabs-content { padding: 20px; }
    </style>
</head>
<body>

    <div id="appContent" class="tabs-container">
        <div class="tabs-header">
            <div class="tabs-title">🔒 CRM + Inventario Profesional - Sistema Seguro</div>
            <div class="tabs-nav">
                <button class="tab-button">
                    <a href="clientes.php">👥 Clientes</a>
                </button>
                <button class="tab-button">
                    <a href="compras.php">📦 Compras</a>
                </button>
                <button class="tab-button">
                    <a href="inventario.php">📊 Inventario</a>
                </button>
                <button class="tab-button">
                    <a href="ventas.php">💰 Ventas</a>
                </button>
                <button class="tab-button active">
                    <a href="finanzas.php">💼 Finanzas</a>
                </button>
                <button class="tab-button">
                    <a href="indicadores.php">📈 Indicadores</a>
                </button>
                <button class="tab-button">
                    <a href="configuracion.php">⚙️ Configuración</a>
                </button>
                <button class="tab-button logout-btn">🚪 Salir</button>
            </div>
        </div>

        <div class="tabs-content">
            <div id="finanzas" class="tab-pane active">
                <div class="card">
                    <div class="card-header">
                        <h2 style="margin:0; color: white;">💼 Ingresos y Egresos</h2>
                    </div>
                    <div class="card-body">
                        
                        <div class="finance-tabs">
                            <button id="btn-ingresos" class="finance-tab active" onclick="showFinanceTab('ingresos')">💰 Ingresos</button>
                            <button id="btn-egresos" class="finance-tab" onclick="showFinanceTab('egresos')">📉 Egresos</button>
                            <button id="btn-resumen" class="finance-tab" onclick="showFinanceTab('resumen')">📊 Resumen</button>
                        </div>
                        
                        <div class="month-selector" style="margin-bottom: 20px;">
                            <label for="monthSelect" style="font-weight: 600;">Periodo:</label>
                            <select id="monthSelect" class="form-select" style="width: auto; display: inline-block;">
                                <option value="01">Enero</option>
                                <option value="02">Febrero</option>
                                <option value="03">Marzo</option>
                            </select>
                            <select id="yearSelect" class="form-select" style="width: auto; display: inline-block;">
                                <option value="2025">2025</option>
                                <option value="2024">2024</option>
                            </select>
                        </div>
                        
                        <div id="ingresos" class="finance-pane active">
                            <form method=POST action=../../controllers/finanzas/registrar_ingresos.php>
                                <div class="form-grid">
                                    <div>
                                        <label>Descripción *</label>
                                        <input id="ingDescripcion" class="form-control"
                                            name=descripcion placeholder="Ej: Venta de repuestos">
                                    </div>
                                    <div>
                                        <label>Monto *</label>
                                        <input id="ingMonto" class="form-control"
                                            name=monto type="number" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label>Categoría *</label>
                                        <select name=categoria id="ingCategoria" class="form-select">
                                            <option value="ventas">Ventas</option>
                                            <option value="servicios">Servicios</option>
                                            <option value="inversiones">Inversiones</option>
                                            <option value="otros">Otros</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; align-items: end;">
                                        <button class="btn btn-success" onclick="alert('Ingreso registrado')">💰 Registrar</button>
                                    </div>
                                </div>
                            </form>
                            
                            <table class="table" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Fecha</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Descripción</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Categoria</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Monto</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Acciones</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaIngresos">
                                    <?php ingresos($conexion); ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="egresos" class="finance-pane">
                            <form method=POST action=../../controllers/finanzas/registrar_egresos.php>
                                <div class="form-grid">
                                    <div>
                                        <label>Descripción *</label>
                                        <input id="ingDescripcion" class="form-control"
                                            name=descripcion placeholder="Ej: Venta de repuestos">
                                    </div>
                                    <div>
                                        <label>Monto *</label>
                                        <input id="ingMonto" class="form-control"
                                            name=monto type="number" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label>Categoría *</label>
                                        <select name=categoria id="ingCategoria" class="form-select">
                                            <option value="compras">Compras</option>
                                            <option value="servicios">Servicios</option>
                                            <option value="nominas">Nominas</option>
                                            <option value="alquiler">Alquiler</option>
                                            <option value="impuestos">Impuestos</option>
                                            <option value="mantenimiento">Mantenimiento</option>
                                            <option value="otros">Otros</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; align-items: end;">
                                        <button class="btn btn-success" onclick="alert('Ingreso registrado')">💰 Registrar</button>
                                    </div>
                                </div>
                            </form>
                            <table class="table" style="width: 100%; margin-top: 20px; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Fecha</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Descripción</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Categoria</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Monto</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;">Acciones</th>
                                        <th style="text-align: left; padding: 12px; border-bottom: 1px solid #eee;"></th>
                                    </tr>
                                </thead>
                                <tbody id="tablaIngresos">
                                    <?php egresos($conexion) ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div id="resumen" class="finance-pane">
                        <div class="financial-summary">
                            <?php
                            try {
                                // 1. Intentamos obtener los totales de las nuevas tablas
                                // Usamos subconsultas para obtener ambos totales en una sola fila de forma segura
                                $sentencia = "SELECT 
                                                (SELECT SUM(monto) FROM ingresos) AS total_ingresos,
                                                (SELECT SUM(monto) FROM egresos) AS total_egresos";

                                $consulta = $conexion->prepare($sentencia);
                                $consulta->execute();
                                $datos = $consulta->fetch(PDO::FETCH_ASSOC);

                                $ingresos = $datos['total_ingresos'] ?? 0;
                                $egresos  = $datos['total_egresos'] ?? 0;
                            } catch (Exception $e) {
                                // Si las tablas no existen o fallan, ponemos 0 para que el JS no se rompa
                                $ingresos = 0;
                                $egresos  = 0;
                            }

                            $utilidad = $ingresos - $egresos;
                            ?>

                            <div class="summary-card income">
                                <h3>Ingresos</h3>
                                <div class="summary-value" style="color: var(--secondary-color)">
                                    $<?php echo number_format($ingresos, 2); ?>
                                </div>
                            </div>

                            <div class="summary-card expense">
                                <h3>Egresos</h3>
                                <div class="summary-value" style="color: var(--danger-color)">
                                    $<?php echo number_format($egresos, 2); ?>
                                </div>
                            </div>

                            <div class="summary-card profit">
                                <h3>Utilidad</h3>
                                <div class="summary-value" style="color: var(--primary-color)">
                                    $<?php echo number_format($utilidad, 2); ?>
                                </div>
                            </div>
                        </div>
                        
                        <form method="POST" action="../../controllers/finanzas/reportes.php">
                            <div class="export-buttons" style="margin-top: 20px; display: flex; gap: 10px;">
                                <button class="export-btn" name="reporte" value="excel" style="padding: 10px 20px; cursor: pointer;">📊 Excel</button>
                                <button class="export-btn" name="reporte" value="pdf" style="padding: 10px 20px; cursor: pointer;">📄 PDF</button>
                                <button class="export-btn" name="reporte" value="json" style="padding: 10px 20px; cursor: pointer;">📁 JSON</button>
                            </div>
                        </form>
                    </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showFinanceTab(tabId) {
            const panes = document.querySelectorAll('.finance-pane');
            panes.forEach(pane => pane.classList.remove('active'));

            const tabs = document.querySelectorAll('.finance-tab');
            tabs.forEach(tab => tab.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            document.getElementById('btn-' + tabId).classList.add('active');
        }
    </script>
</body>
</html>