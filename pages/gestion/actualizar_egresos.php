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

# verifica id
$redirec = "../../pages/gestion/finanzas.php"; 
if (empty($_POST['id'])) {
    $_SESSION['errores'][] = 'No se seleccionaron registros';
    header("Location: $redirec");
    exit;
}

# CONSULTA PREPARADA PDO
$sql = "SELECT descripcion, categoria, monto FROM egresos WHERE id_egreso = :id LIMIT 1";
$query = $conexion->prepare($sql);
$query->execute(['id' => $_POST['id']]);
$egreso = $query->fetch(PDO::FETCH_ASSOC);

# verificar si existen valores de retorno
if ($egreso) {
    $descripcion = $egreso['descripcion'];
    $categoria   = $egreso['categoria'];
    $monto       = $egreso['monto'];
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
        .finance-tab[onclick*="egresos"].active { background: var(--danger-color); }


        .finance-pane {
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
                            <button id="btn-egresos" class="finance-tab active" onclick="showFinanceTab('egresos')">📉 Egresos</button>
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
                        
                        <div id="egresos" class="finance-pane">
                            <form method=POST action=../../controllers/finanzas/actualizar_egresos.php>
                                <div class="form-grid">
                                    <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
                                    <div>
                                        <label>Descripción *</label>
                                        <input id="ingDescripcion" class="form-control"
                                            value="<?php echo $descripcion; ?>" name=descripcion placeholder="Ej: Venta de repuestos">
                                    </div>
                                    <div>
                                        <label>Monto *</label>
                                        <input id="ingMonto" class="form-control"
                                            value="<?php echo $monto; ?>" name=monto type="number" placeholder="0.00">
                                    </div>
                                    <div>
                                        <label>Categoría *</label>
                                        <select name=categoria id="ingCategoria" class="form-select">
                                            <option value="<?php echo $categoria; ?>"><?php echo $categoria; ?></option>
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
                            
                        </div>
                    </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    
</body>
</html>