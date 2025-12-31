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

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Gestión inventario</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../styles/ventas.css">
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
                <button class="tab-button active">
                    <a href="ventas.php">💰 Ventas</a>
                </button>
                <button class="tab-button">
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
            
            <div id="ventas" class="tab-pane active">
                <div class="card">
                    <div class="card-header" style="color: white;"> <h2>💰 Ventas Detalladas</h2>
                    </div>
                    <div class="card-body">
                        
                        <div class="brand-model-section">
                            <h4>🎯 Marcas Disponibles: Kia, Hyundai, Honda, Mitsubishi (2017+)</h4>
                            <div class="brand-list">
                                <span class="brand-tag">Kia</span>
                                <span class="brand-tag">Hyundai</span>
                                <span class="brand-tag">Honda</span>
                                <span class="brand-tag">Mitsubishi</span>
                                <span class="brand-tag">2017+</span>
                            </div>
                        </div>
           
                        <div class="form-grid">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Cliente *</label>
                                <select id="vCliente" class="form-select" onchange="cargarHistorialCliente(); cargarDatosIVA_NIT();" required>
                                    <option value="">-- Seleccione Cliente --</option>
                                </select>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Producto *</label>
                                <input id="vProducto" list="productosInventarioList" placeholder="Seleccione del inventario" required onchange="cargarInfoProductoVenta()">
                                <datalist id="productosInventarioList"></datalist>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Marca *</label>
                                <input id="vMarca" placeholder="Se autocompleta" required readonly>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Modelo *</label>
                                <input id="vModelo" placeholder="Se autocompleta" required readonly>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Año *</label>
                                <input id="vAno" type="text" placeholder="Se autocompleta" required readonly>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Cantidad *</label>
                                <input id="vCantidad" type="number" min="1" placeholder="Ej: 2" required onchange="calcularTotal()">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Precio Venta *</label>
                                <input id="vPrecioVenta" type="number" min="0" step="0.01" placeholder="0.00" required readonly>
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Tipo de Factura *</label>
                                <select id="vTipoFactura" class="form-select" required onchange="calcularTotal()">
                                    <option value="credito">Crédito Fiscal (IVA 13%)</option>
                                    <option value="consumidor">Factura Consumidor Final</option>
                                    <option value="comprobante">Comprobante de Pago</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Registro IVA</label>
                                <input id="vRegistroIVA" placeholder="Opcional">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Registro NIT</label>
                                <input id="vRegistroNIT" placeholder="Opcional">
                            </div>
                        </div>

                        <div style="margin: 1rem 0; padding: 1.5rem; background: rgba(26, 95, 180, 0.05); border-radius: 12px; border-left: 4px solid var(--primary-color);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-weight: bold; color: var(--primary-dark); font-size: 1.1rem;">Total Calculado:</span>
                                <span id="vTotalCalculado" style="font-size: 1.5rem; font-weight: bold; color: var(--secondary-color);">$0.00</span>
                            </div>
                            <div id="vIVACalculado" style="font-size: 0.9rem; color: #666; margin-top: 0.5rem; display: none;">
                                <span>IVA (13%): </span><span id="vIVAValor">$0.00</span>
                            </div>
                        </div>
                       
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Observaciones</label>
                            <input id="vObs" placeholder="Notas sobre la venta...">
                        </div>
                       
                        <div id="clientHistoryContainer" class="brand-model-section" style="display: none;">
                            <h4>📋 Historial de Compras</h4>
                            <div id="clientHistoryList"></div>
                        </div>
                       
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-success" onclick="registrarVenta()">💳 Registrar Venta</button>
                                <button class="btn" style="background: var(--primary-color);" onclick="actualizarListaProductosVenta()">🔄 Actualizar</button>
                            </div>
                            <div class="export-buttons">
                                <button class="export-btn" onclick="exportVentasExcel()">📊 Excel</button>
                                <button class="export-btn" onclick="exportVentasPDF()">📄 PDF</button>
                                <button class="export-btn" onclick="exportVentasJSON()">📁 JSON</button>
                            </div>
                        </div>
                       
                        <div class="table-responsive" style="margin-top: 1.5rem;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Producto</th>
                                        <th>Marca/Modelo</th>
                                        <th>Año</th>
                                        <th>Cant.</th>
                                        <th>Total</th>
                                        <th>Ganancia</th>
                                        <th>Fecha</th>
                                        <th>Factura</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaVentas">
                                    <tr><td colspan="10" class="text-center">No hay ventas registradas</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div> </div> </div> </div> </div> </body>
</html>