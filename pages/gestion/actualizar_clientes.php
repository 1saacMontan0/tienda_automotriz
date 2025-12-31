
<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
require ("../../models/lecturas/clientes.php"); # genera la tabla de clientes
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
$registro = cliente_individual($conexion, $_POST['id']);
# verificar si existen valores de retorno
if (isset($registro)) {
    // Accedemos a la primera fila [0]
    $cliente = $registro[0];
} else {
    header("Location: ../../pages/gestion/clientes.php");
    exit;
}

# verifica si has seleccionado el id de algun usuario.
$redirec = "../../pages/gestion/clientes.php"; # donde se enviara al usuario si algo falla.
if (empty($_POST['id'])) {
    $_SESSION['errores'][] = 'Debes seleccionar un cliente';
    header("Location: $redirec");
    exit;
}


?>

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
                                <h2>👥 Actualizar información de Clientes</h2>
                            </div>
                            <div class="card-body">
                                <!-- Formulario de clientes -->
                                <form method=POST action=../../controllers/clientes/actualizar.php>
                                    <div class="form-grid">
                                        <input name=id type=hidden value="<?php echo $_POST['id'] ?>">
                                        <div>
                                            <label for="nombre">Nombre completo *</label>
                                            <input id="nombre" class="form-control"
                                                name=nombre value="<?php echo $cliente['nombre']; ?>"
                                                placeholder="Ej: Juan Pérez" required>
                                            <div class="validation-message" id="cNombreError"></div>
                                        </div>
                                        <div>
                                            <label for="telefono">Teléfono *</label>
                                            <input id="telefono" class="form-control"
                                                name=telefono value="<?php echo $cliente['telefono']; ?>"
                                                placeholder="Ej: 7123-4567" required>
                                            <div class="validation-message" id="cTelefonoError"></div>
                                        </div>
                                        <div>
                                            <label for="correo">Correo electrónico *</label>
                                            <input id="correo" class="form-control" type="email"
                                                name=correo value="<?php echo $cliente['correo']; ?>"
                                                placeholder="Ej: cliente@correo.com" required>
                                            <div class="validation-message" id="cCorreoError"></div>
                                        </div>
                                        <div>
                                            <label for="direccion">Dirección</label>
                                            <input id="direccion" class="form-control"
                                                name=direccion value="<?php echo $cliente['direccion']; ?>"
                                                placeholder="Ej: San Salvador">
                                        </div>
                                        <div>
                                            <label for="registro_iva">Registro IVA</label>
                                            <input id="registro_iva" class="form-control"
                                                name=registro_iva
                                                placeholder="Opcional">
                                        </div>
                                        <div>
                                            <label for="nit">Registro NIT</label>
                                            <input id="nit" class="form-control"
                                                name=nit value="<?php echo $cliente['nit']; ?>"
                                                placeholder="Opcional">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <button class="btn btn-success" onclick="guardarCliente()">
                                            <span>💾</span> Actualizar registro
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type=module>
            import { validaciones_front } from "/automotriz/controllers/filtros/validaciones_front.js";

            validaciones_front("nombre","nombre", "no_especial","No se aceptan caracteres especiales");
            validaciones_front("telefono","telefono", "telefono","Unicamente se aceptan numeros de has 8 digitos");
            validaciones_front("correo","correo", "correo","El formato de correo es incorrecto");
            validaciones_front("direccion","direccion", "no_especial","No se aceptan caracteres especiales");
            validaciones_front("nit","nit", "nit","Solo se aceptan numeros de hasta 14 digitos");
        </script>
    </body>
</html>