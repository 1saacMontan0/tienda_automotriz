<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración del Sistema</title>
    <link rel="stylesheet" href="../../styles/finanzas.css">
    <style>
        /* CSS INTEGRADO ESPECÍFICO */
        :root {
            --primary-color: #1a5fb4;
            --secondary-color: #26a269;
            --bg-color: #f6f5f4;
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

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        .btn-success {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .mt-4 { margin-top: 2rem; }
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
                <button class="tab-button">
                    <a href="finanzas.php">💼 Finanzas</a>
                </button>
                <button class="tab-button">
                    <a href="indicadores.php">📈 Indicadores</a>
                </button>
                <button class="tab-button active">
                    <a href="configuracion.php">⚙️ Configuración</a>
                </button>
                <button class="tab-button logout-btn">🚪 Salir</button>
            </div>
        </div>

        <div class="tabs-content">
            <div id="configuracion" class="tab-pane active">
                <div class="card">
                    <div class="card-header" style="background: var(--primary-color); padding: 10px 20px; border-radius: 8px 8px 0 0;">
                        <h2 style="margin:0; color: white;">⚙️ Configuración del Sistema</h2>
                    </div>
                    <div class="card-body" style="background: white; padding: 25px; border: 1px solid #ddd; border-top: none;">
                        
                        <div class="form-grid">
                            <div>
                                <label for="configNombreEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre de la Empresa *</label>
                                <input id="configNombreEmpresa" class="form-control" placeholder="Ej: Mi Taller SV" required>
                            </div>
                            <div>
                                <label for="configTelefonoEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Teléfono de la Empresa</label>
                                <input id="configTelefonoEmpresa" class="form-control" placeholder="Ej: 2222-3333">
                            </div>
                            <div>
                                <label for="configCorreoEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Correo de la Empresa</label>
                                <input id="configCorreoEmpresa" class="form-control" type="email" placeholder="Ej: info@mitaller.com">
                            </div>
                            <div>
                                <label for="configDireccionEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Dirección de la Empresa</label>
                                <input id="configDireccionEmpresa" class="form-control" placeholder="Ej: San Salvador">
                            </div>
                            <div>
                                <label for="configNIT" style="display: block; margin-bottom: 5px; font-weight: 600;">NIT de la Empresa</label>
                                <input id="configNIT" class="form-control" placeholder="Ej: 0614-280789-103-4">
                            </div>
                            <div>
                                <label for="configNRC" style="display: block; margin-bottom: 5px; font-weight: 600;">NRC de la Empresa</label>
                                <input id="configNRC" class="form-control" placeholder="Ej: 123456-7">
                            </div>
                            <div>
                                <label for="configGiro" style="display: block; margin-bottom: 5px; font-weight: 600;">Giro de la Empresa</label>
                                <input id="configGiro" class="form-control" placeholder="Ej: Venta de repuestos automotrices">
                            </div>
                            <div style="display: flex; align-items: end;">
                                <button class="btn btn-success" onclick="guardarConfiguracion()">
                                    <span>💾</span> Guardar Configuración
                                </button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <h3>📋 Información Actual de la Empresa</h3>
                            <div id="infoEmpresaActual" style="background: rgba(26, 95, 180, 0.05); border-radius: 12px; padding: 1.5rem; margin-top: 1rem;">
                                <p>Cargando información...</p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function guardarConfiguracion() {
            alert('Configuración guardada correctamente');
            // Aquí iría la lógica para enviar los datos al servidor
        }
    </script>
</body>
</html>