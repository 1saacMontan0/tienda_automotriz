<!DOCTYPE html>
<html>
<head>
    <title>Gestión compras</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../../styles/compras.css">
</head>
<body>

    <!-- ================= SISTEMA DE PESTAÑAS ================= -->
    <div id="appContent" class="tabs-container">
        <div class="tabs-header">
            <div class="tabs-title">🔒 CRM + Inventario Profesional - Sistema Seguro</div>
            <div class="tabs-nav">
                <button class="tab-button">
                        <a href=clientes.php>👥 Clientes</a>
                    </button>
                    <button class="tab-button active">
                        <a href=compras.php>📦 Compras</a>
                    </button>
                    <button class="tab-button">
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

        <!-- ================= PESTAÑA COMPRAS ================= -->
        <div id="compras" class="tab-pane active">
            <div class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>📦 Registro de Compras</h2>
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
                                <label for="compTipo">Tipo de Compra *</label>
                                <select id="compTipo" class="form-select" onchange="toggleInventarioFields()" required>
                                    <option value="inventario">Para Inventario</option>
                                    <option value="servicio">Servicio/Otro Producto</option>
                                </select>
                            </div>
                            <div>
                                <label for="compDescripcion">Descripción *</label>
                                <input id="compDescripcion" class="form-control" placeholder="Ej: Compra de repuestos" required>
                            </div>
                            <div id="compProductoContainer">
                                <label for="compProducto">Producto *</label>
                                <input id="compProducto" class="input-with-datalist" list="productosList" placeholder="Ej: Bumper delantero" required>
                                <datalist id="productosList">
                                    <option value="Bumper delantero">
                                    <option value="Bumper trasero">
                                    <option value="Capó delantero">
                                    <option value="Capó trasero">
                                    <option value="Silvines">
                                    <option value="Puertas">
                                    <option value="Parabrisas">
                                    <option value="Faros delanteros">
                                    <option value="Luces traseras">
                                    <option value="Retrovisores">
                                </datalist>
                            </div>
                            <div id="compMarcaContainer">
                                <label for="compMarca">Marca *</label>
                                <input id="compMarca" class="input-with-datalist" list="marcasList" placeholder="Ej: Kia" required>
                                <datalist id="marcasList">
                                    <option value="Kia">
                                    <option value="Hyundai">
                                    <option value="Honda">
                                    <option value="Mitsubishi">
                                </datalist>
                            </div>
                            <div id="compModeloContainer">
                                <label for="compModelo">Modelo *</label>
                                <input id="compModelo" class="input-with-datalist" list="modelosList" placeholder="Ej: Sportage" required>
                                <datalist id="modelosList">
                                    <option value="Sportage">
                                    <option value="Sorento">
                                    <option value="Rio">
                                    <option value="Tucson">
                                    <option value="Santa Fe">
                                    <option value="Accord">
                                    <option value="Civic">
                                    <option value="CR-V">
                                    <option value="Outlander">
                                    <option value="Lancer">
                                </datalist>
                            </div>
                            <div id="compAnoContainer" class="year-range-container">
                                <div>
                                    <label for="compAnoDesde">Año desde *</label>
                                    <input id="compAnoDesde" class="form-control" type="number" min="1900" max="2050" placeholder="Ej: 2017" required>
                                </div>
                                <div>
                                    <label for="compAnoHasta">Año hasta *</label>
                                    <input id="compAnoHasta" class="form-control" type="number" min="1900" max="2050" placeholder="Ej: 2022" required>
                                </div>
                            </div>
                            <div>
                                <label for="compCantidad">Cantidad *</label>
                                <input id="compCantidad" class="form-control" type="number" min="1" placeholder="Ej: 5" required>
                            </div>
                            <div>
                                <label for="compPrecioCompra">Precio Compra *</label>
                                <input id="compPrecioCompra" class="form-control" type="number" min="0" step="0.01" placeholder="Ej: 125.50" required>
                            </div>
                            <div id="compPrecioVentaContainer">
                                <label for="compPrecioVenta">Precio Venta *</label>
                                <input id="compPrecioVenta" class="form-control" type="number" min="0" step="0.01" placeholder="Ej: 200.00" required>
                            </div>
                            <div style="display: flex; align-items: end;">
                                <button class="btn btn-success">
                                    <span>➕</span> Registrar Compra
                                </button>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <div></div>
                            <div class="export-buttons">
                                <button class="export-btn" onclick="exportComprasExcel()">📊 Excel</button>
                                <button class="export-btn" onclick="exportComprasPDF()">📄 PDF</button>
                                <button class="export-btn" onclick="exportComprasJSON()">📁 JSON</button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Producto</th>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Año</th>
                                        <th>Cantidad</th>
                                        <th>Precio Compra</th>
                                        <th>Precio Venta</th>
                                        <th>Fecha y Hora</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaCompras">
                                    <tr>
                                        <td colspan="11" class="text-center">No hay compras registradas</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
