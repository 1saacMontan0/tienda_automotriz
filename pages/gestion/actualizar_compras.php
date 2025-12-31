<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../models/lecturas/compras.php"); # registros de compras
require ('../../utils/mensajes_back.php'); # mensajes de error
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

$redirec = "../../pages/gestion/compras.php"; # donde se enviara al usuario si algo falla.
if (empty($_SESSION['id_compra'])) {
    header ("Location: $redirec");
    exit;
}

# obtiene un registro segun el id
$registro = compra_individual($conexion, $_SESSION['id_compra']);
# verificar si existen valores de retorno
if (isset($registro)) {
    // Accedemos a la primera fila [0]
    $compra = $registro[0];
} else {
    header("Location: $redirec");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión compras</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="../../styles/compras.css">
</head>
<body>

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

        <div id="compras" class="tab-pane active">
            <div class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>📦 Actualizar compras</h2>
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

                    <form method=POST action="../../controllers/compras/actualizar_compra.php">
                        <div class="form-grid">
                            <input id="descripcion" name=id_compra type=hidden
                                    value='<?php echo $_SESSION['id_compra']; ?>'>
                            <div>
                                <label for="descripcion">Descripción *</label>
                                <input id="descripcion" name=descripcion class="form-control"
                                    value=<?php echo $compra['descripcion']?> placeholder="Ej: Compra de repuestos">
                            </div>
                            <div>
                                <label for="cantidad">Cantidad *</label>
                                <input id="cantidad" class="form-control" type="number" name=cantidad
                                    value=<?php echo $compra['cantidad']?> min="1" placeholder="Ej: 5" required>
                            </div>
                            <div>
                                <label for="precio_compra">Precio Compra *</label>
                                <input id="precio_compra" class="form-control" type="number" name=precio_compra
                                    value=<?php echo $compra['precio_compra']?> min="0" step="0.01" placeholder="Ej: 125.50" required>
                            </div>
                            <div style="display: flex; align-items: end;">
                                <button id=enviar class="btn btn-success">
                                    <span>➕</span> Registrar Compra
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php error_mensaje_back(); ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script type=module>
        import { validaciones_front } from "/automotriz/controllers/filtros/validaciones_front.js";

        validaciones_front("descripcion","descripcion",'no_especial','No se aceptan caracteres especiales');
        validaciones_front("producto","producto","solo_letras","Solo se admiten letras");
        validaciones_front("marca","marca","solo_letras","Solo se admiten letras");
        validaciones_front("modelo","modelo","no_especial","No se admiten caracteres especiales");
        //validaciones_front("compatible_desde","compatible_desde","solo_numeros","Unicamente se admiten numeros");
        //validaciones_front("compatible_hasta","compatible_hasta","solo_numeros","Unicamente se admiten numeros");
        //validaciones_front("cantidad","cantidad","solo_numeros","Unicamente se admiten numeros");
        //validaciones_front("precio_compra","precio_compra","solo_numeros","Unicamente se admiten numeros");
        //validaciones_front("precio_venta","precio_venta","solo_numeros","Unicamente se admiten numeros");
    </script>
</body>
</html>

<?php
    unset($_SESSION['id_compra']);
?>