<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/lecturas/empresa.php"); # conexion a la base de datos.
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

# obtiene un registro segun el id
$registro = optener_empresa($conexion, $_SESSION['id_empresa']);
# verificar si existen valores de retorno
if (isset($registro)) {
    // Accedemos a la primera fila [0]
    $empresa = $registro[0];
} else {
    header("Location: ../../pages/gestion/clientes.php");
    exit;
}

?>

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
                        <form method=POST action=../../controllers/configuracion/actualizar.php>
                        <div class="form-grid">
                            
                                <div>
                                    <label for="configNombreEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Nombre de la Empresa *</label>
                                    <input id="configNombreEmpresa" class="form-control" name=nombre
                                        value=<?php echo $empresa['nombre']; ?> placeholder="Ej: Mi Taller SV" required>
                                </div>
                                <div>
                                    <label for="configTelefonoEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Teléfono de la Empresa</label>
                                    <input id="configTelefonoEmpresa" class="form-control" name=telefono
                                        value=<?php echo $empresa['telefono']; ?> placeholder="Ej: 2222-3333">
                                </div>
                                <div>
                                    <label for="configCorreoEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Correo de la Empresa</label>
                                    <input id="configCorreoEmpresa" class="form-control" type="email" name=correo
                                        value=<?php echo $empresa['correo']; ?> placeholder="Ej: info@mitaller.com">
                                </div>
                                <div>
                                    <label for="configDireccionEmpresa" style="display: block; margin-bottom: 5px; font-weight: 600;">Dirección de la Empresa</label>
                                    <input id="configDireccionEmpresa" class="form-control" name=direccion
                                        value=<?php echo $empresa['direccion']; ?> placeholder="Ej: San Salvador">
                                </div>
                                <div>
                                    <label for="configNIT" style="display: block; margin-bottom: 5px; font-weight: 600;">NIT de la Empresa</label>
                                    <input id="configNIT" class="form-control" name=nit 
                                        value=<?php echo $empresa['nit']; ?> placeholder="Ej: 0614-280789-103-4">
                                </div>
                                <div>
                                    <label for="configNRC" style="display: block; margin-bottom: 5px; font-weight: 600;">NRC de la Empresa</label>
                                    <input id="configNRC" class="form-control" name=nrc
                                        value=<?php echo $empresa['nrc']; ?> placeholder="Ej: 123456-7">
                                </div>
                                <div>
                                    <label for="configGiro" style="display: block; margin-bottom: 5px; font-weight: 600;">Giro de la Empresa</label>
                                    <input id="configGiro" class="form-control" name=giro
                                        value=<?php echo $empresa['giro']; ?> placeholder="Ej: Venta de repuestos automotrices">
                                </div>
                                <div style="display: flex; align-items: end;">
                                    <button class="btn btn-success" onclick="guardarConfiguracion()">
                                        <span>💾</span> Guardar Configuración
                                    </button>
                                </div>
                            </form>
                        </div>
                        </form>
                        <div class="mt-4">
                            <h3>📋 Información Actual de la Empresa</h3>
                            <div id="infoEmpresaActual" style="background: rgba(26, 95, 180, 0.05); border-radius: 12px; padding: 1.5rem; margin-top: 1rem;">
                                <ul>
                                    <?php
                                        foreach ($empresa as $campo => $valor) {
                                            $label = ucfirst(str_replace('_', ' ', $campo));
                                            echo "<li><strong>$label:</strong> $valor</li>";
                                        }
                                    ?>
                                </ul>
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