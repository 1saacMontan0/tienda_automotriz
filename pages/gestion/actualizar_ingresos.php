<?php

# check session.
require ("../../vendor/autoload.php");
require ("../../.config/.conexion.php");
require ("../../controllers/filtros/check_session.php");
require ("../../models/lecturas/finanzas.php");
$redirec = "../../index.php"; 

session_start();

# conexion a la base de datos
$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);

# verificar session
if (isset($_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'])) {
    check_session($conexion, $_SESSION['usuario'], $_SESSION['id_empresa'], $_SESSION['rol'], $redirec);
} else {
    $_SESSION['errores'][] = 'usuario no autorizado';
    header("Location: $redirec");
    exit;
}

# verifica id
$redirec = "../../pages/gestion/finanzas.php"; 
if (empty($_POST['id'])) {
    $_SESSION['errores'][] = 'No se seleccionaron registros';
    header("Location: $redirec");
    exit;
}

# CONSULTA PREPARADA PDO
$sql = "SELECT descripcion, categoria, monto FROM ingresos WHERE id_ingreso = :id LIMIT 1";
$query = $conexion->prepare($sql);
$query->execute(['id' => $_POST['id']]);
$ingreso = $query->fetch(PDO::FETCH_ASSOC);

# verificar si existen valores de retorno
if ($ingreso) {
    $descripcion = $ingreso['descripcion'];
    $categoria   = $ingreso['categoria'];
    $monto       = $ingreso['monto'];
} else {
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
        /* MANTENGO TUS ESTILOS EXACTAMENTE IGUAL */
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
            padding: 0; 
        }

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
        
        .tabs-content { padding: 20px; }
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
                <button class="tab-button active"><a href="finanzas.php">💼 Finanzas</a></button>
                <button class="tab-button"><a href="indicadores.php">📈 Indicadores</a></button>
                <button class="tab-button"><a href="configuracion.php">⚙️ Configuración</a></button>
                <button class="tab-button logout-btn">🚪 Salir</button>
            </div>
        </div>

        <div class="tabs-content">
            <div id="finanzas" class="tab-pane active">
                <div class="card">
                    <div class="card-header">
                        <h2 style="margin:0; color: white;">💼 Actualizar ingresos</h2>
                    </div>
                    <div class="card-body">
                        <div class="finance-tabs">
                            <button id="btn-ingresos" class="finance-tab active" onclick="showFinanceTab('ingresos')">💰 Ingresos</button>
                        </div>
                        
                        <div id="ingresos" class="finance-pane active">
                            <form method="POST" action="../../controllers/finanzas/actualizar_ingresos.php">
                                <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
                                <div class="form-grid">
                                    <div>
                                        <label>Descripción *</label>
                                        <input id="ingDescripcion" class="form-control" name="descripcion" 
                                               value="<?php echo $descripcion; ?>" placeholder="Ej: Venta de repuestos">
                                    </div>
                                    <div>
                                        <label>Monto *</label>
                                        <input id="ingMonto" class="form-control" name="monto" type="number" step="0.01" 
                                               value="<?php echo $monto; ?>" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label>Categoría *</label>
                                        <select name="categoria" id="ingCategoria" class="form-select">
                                            <option value="<?php echo $categoria; ?>"><?php echo $categoria; ?></option>
                                            <option value="ventas">Ventas</option>
                                            <option value="servicios">Servicios</option>
                                            <option value="inversiones">Inversiones</option>
                                            <option value="otros">Otros</option>
                                        </select>
                                    </div>
                                    <div style="display: flex; align-items: end;">
                                        <button type="submit" class="btn btn-success">💰 Actualizar</button>
                                    </div>
                                </div>
                            </form>
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