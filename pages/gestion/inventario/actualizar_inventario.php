<?php

# check session.
require ("../../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../../models/lecturas/compras.php"); # mostrar tabla
require ('../../../utils/mensajes_back.php'); # mensajes de error
require ("../../../controllers/filtros/check_session.php"); # comprobar session.
$redirec = "../../../index.php"; # donde se enviara al usuario si algo falla.

session_start();

$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);

$redirec = "../../pages/gestion/inventario.php"; # donde se enviara al usuario si algo falla.

if (empty($_POST['id'])) {
    $_SESSION['errores'][] = 'No se han seleccionado registros';
    header ("Location: $redirec");
    exit;
}

$conexion = conexion($_ENV['HOST'], $_ENV['USER'], $_ENV['SECRET'], $_ENV['DB']);

# obtiene un registro segun el id
$registro = inventario_individual($conexion, $_POST['id']);
# verificar si existen valores de retorno
if (isset($registro)) {
    // Accedemos a la primera fila [0]
    $inventario = $registro[0];
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
    <link rel="stylesheet" href="../../../styles/compras.css">
</head>
<body>

    <div id="appContent" class="tabs-container">
        <div class="tabs-header">
            <div class="tabs-title">🔒 CRM + Inventario Profesional - Sistema Seguro</div>
            <div class="tabs-nav">
                <button class="tab-button">
                        <a href=../clientes.php>👥 Clientes</a>
                    </button>
                    <button class="tab-button">
                        <a href=../compras.php>📦 Compras</a>
                    </button>
                    <button class="tab-button active">
                        <a href=../inventario.php>📊 Inventario</a>
                    </button>
                    <button class="tab-button">
                        <a href=../ventas.php>💰 Ventas</a>
                    </button>
                    <button class="tab-button">
                        <a href=../finanzas.php>💼 Finanzas</a>
                    </button>
                    <button class="tab-button">
                        <a href=../indicadores.php>📈 Indicadores</a>
                    </button>
                    <button class="tab-button">
                        <a href=../configuracion.php>⚙️ Configuración</a>
                    </button>
                    <button class="tab-button logout-btn">🚪 Salir</button>
            </div>
        </div>

        <div id="compras" class="tab-pane active">
            <div class="tab-content">
                <div class="card">
                    <div class="card-header">
                        <h2>📦 Actualizar inventario</h2>
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

                    <form method=POST action="../../../controllers/inventario/actualizar_inventario.php">
                        <div class="form-grid">
                            <input type=hidden name=id_inventario value=<?php echo $_POST['id']?>>
                            <div>
                                <label for="descripcion">Descripción *</label>
                                <input id="descripcion" name=descripcion class="form-control"
                                    value='<?php echo $inventario['descripcion']; ?>' placeholder="Ej: Compra de repuestos">
                            </div>
                            <div id="compProductoContainer">
                                <label for="producto">Producto *</label>
                                <input id="producto" class="input-with-datalist" list="productosList"
                                    value=<?php echo $inventario['nombre']?> name=producto
                                    placeholder="Ej: Bumper delantero" required>
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
                                <label for="marca">Marca *</label>
                                <input id="marca" class="input-with-datalist" list="marcasList"
                                    value=<?php echo $inventario['marca']?> name=marca
                                    placeholder="Ej: Kia" required>
                                <datalist id="marcasList">
                                    <option value="Kia">
                                    <option value="Hyundai">
                                    <option value="Honda">
                                    <option value="Mitsubishi">
                                </datalist>
                            </div>
                            <div id="compModeloContainer">
                                <label for="modelo">Modelo *</label>
                                <input id="modelo" class="input-with-datalist" list="modelosList"
                                    value=<?php echo $inventario['modelo']?> name=modelo
                                    placeholder="Ej: Sportage" required>
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
                                    <label for="compatible_desde">Año desde *</label>
                                    <input id="compatible_desde" class="form-control" name=compatible_desde
                                        value=<?php echo $inventario['compatible_desde']?> type="number"
                                        min="1900" max="2050" placeholder="Ej: 2017" required>
                                </div>
                                <div>
                                    <label for="compatible_hasta">Año hasta *</label>
                                    <input id="compatible_hasta" class="form-control" name=compatible_hasta
                                        value=<?php echo $inventario['compatible_hasta']?> type="number"
                                        min="1900" max="2050" placeholder="Ej: 2022" required>
                                </div>
                            </div>
                            <div>
                                <label for="cantidad">Cantidad *</label>
                                <input id="cantidad" class="form-control" type="number" name=cantidad
                                    value=<?php echo $inventario['unidades']?> min="1" placeholder="Ej: 5" required>
                            </div>
                            <div>
                                <label for="precio_compra">Precio Compra *</label>
                                <input id="precio_compra" class="form-control" type="number" name=precio_compra
                                    value=<?php echo $inventario['precio_compra']?> min="0" step="0.01"
                                    placeholder="Ej: 125.50" required>
                            </div>
                            <div id="compPrecioVentaContainer">
                                <label for="precio_venta">Precio Venta *</label>
                                <input id="precio_venta" class="form-control" type="number" name=precio_venta
                                    value=<?php echo $inventario['precio_venta']?> min="0" step="0.01"
                                    placeholder="Ej: 200.00" required>
                            </div>
                            <div style="display: flex; align-items: end;">
                                <button id=enviar class="btn btn-success">
                                    <span>➕</span> Registrar Compra
                                </button>
                            </div>
                        </div>
                    </form>
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
