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
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <div></div>
                                <div class="export-buttons">
                                    <button class="export-btn" onclick="exportInventarioExcel()">📊 Excel</button>
                                    <button class="export-btn" onclick="exportInventarioPDF()">📄 PDF</button>
                                    <button class="export-btn" onclick="exportInventarioJSON()">📁 JSON</button>
                                </div>
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
                                        </tr>
                                    </thead>
                                    <tbody id="tablaInventario">
                                        <tr>
                                            <td colspan="11" class="text-center">No hay productos en inventario</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div> <!-- cierre .table-responsive -->
                        </div> <!-- cierre .card-body -->
                    </div> <!-- cierre .card -->
                </div> <!-- cierre .tab-content -->
            </div> <!-- cierre #inventario -->
        </div> <!-- cierre .tabs-content -->
    </div> <!-- cierre #appContent -->
</body>
</html>
