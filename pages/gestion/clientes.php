
<!DOCTYPE html>
<html>
    <head>
        <title>Gestion de clientes</title>
        <meta charset=utf-8>
        <link rel=stylesheet href=../../styles/clientes.css>
    </head>
    <body>

        <!-- ================= SISTEMA DE PESTAÑAS ================= -->
        <div id="appContent" class="tabs-container">
            <div class="tabs-header">
                <div class="tabs-title">🔒 CRM + Inventario Profesional - Sistema Seguro</div>
                <div class="tabs-nav">
                    <button class="tab-button active">
                        <a href=clientes.php>👥 Clientes</a>
                    </button>
                    <button class="tab-button">
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

            <div class="tabs-content">
                <!-- ================= PESTAÑA CLIENTES ================= -->
                <div id="clientes" class="tab-pane active">
                    <div class="tab-content">
                        <div class="card">
                            <div class="card-header">
                                <h2>👥 Gestión de Clientes</h2>
                            </div>
                            <div class="card-body">
                                <!-- Formulario de clientes -->
                                <form method=POST action=#>
                                    <div class="form-grid">
                                        <div>
                                            <label for="cNombre">Nombre completo *</label>
                                            <input id="cNombre" class="form-control" placeholder="Ej: Juan Pérez" required>
                                            <div class="validation-message" id="cNombreError"></div>
                                        </div>
                                        <div>
                                            <label for="cTelefono">Teléfono *</label>
                                            <input id="cTelefono" class="form-control" placeholder="Ej: 7123-4567" required>
                                            <div class="validation-message" id="cTelefonoError"></div>
                                        </div>
                                        <div>
                                            <label for="cCorreo">Correo electrónico *</label>
                                            <input id="cCorreo" class="form-control" type="email" placeholder="Ej: cliente@correo.com" required>
                                            <div class="validation-message" id="cCorreoError"></div>
                                        </div>
                                        <div>
                                            <label for="cDireccion">Dirección</label>
                                            <input id="cDireccion" class="form-control" placeholder="Ej: San Salvador">
                                        </div>
                                        <div>
                                            <label for="cRegistroIVA">Registro IVA</label>
                                            <input id="cRegistroIVA" class="form-control" placeholder="Opcional">
                                        </div>
                                        <div>
                                            <label for="cRegistroNIT">Registro NIT</label>
                                            <input id="cRegistroNIT" class="form-control" placeholder="Opcional">
                                        </div>
                                    </div>
                                </form>
                                <div class="d-flex justify-content-between mb-3">
                                    <button class="btn btn-success" onclick="guardarCliente()">
                                        <span>💾</span> Guardar Cliente
                                    </button>
                                    <div class="export-buttons">
                                        <button class="export-btn">📊 Excel</button>
                                        <button class="export-btn">📄 PDF</button>
                                        <button class="export-btn">📁 JSON</button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Teléfono</th>
                                                <th>Correo</th>
                                                <th>Dirección</th>
                                                <th>Total Comprado</th>
                                                <th>Fecha Registro</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tablaClientes">
                                            <tr>
                                                <td colspan="7" class="text-center">No hay clientes registrados</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Historial de compras del cliente -->
                                <div class="client-details-container" id="clientDetailsContainer" style="display: none;">
                                    <h3>📋 Historial de Compras del Cliente</h3>
                                    <div class="table-responsive client-purchases-table">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Producto</th>
                                                    <th>Marca/Modelo</th>
                                                    <th>Año</th>
                                                    <th>Cantidad</th>
                                                    <th>Precio Unitario</th>
                                                    <th>Total</th>
                                                    <th>Fecha</th>
                                                    <th>Tipo Factura</th>
                                                </tr>
                                            </thead>
                                            <tbody id="clientPurchasesTable"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= PESTAÑA COMPRAS ================= -->
                <div id="compras" class="tab-pane">
                    <!-- contenido de compras -->
                </div>

                <!-- ================= PESTAÑA INVENTARIO ================= -->
                <div id="inventario" class="tab-pane">
                    <!-- contenido de inventario -->
                </div>

                <!-- ================= PESTAÑA VENTAS ================= -->
                <div id="ventas" class="tab-pane">
                    <!-- contenido de ventas -->
                </div>

                <!-- ================= PESTAÑA FINANZAS ================= -->
                <div id="finanzas" class="tab-pane">
                    <!-- contenido de finanzas -->
                </div>
            </div>
        </div>
    </body>
</html>