<?php

# check session.
require ("../../vendor/autoload.php");   # librerias composer y variables de entorno.
require ("../../.config/.conexion.php"); # conexion a la base de datos.
require ("../../controllers/filtros/check_session.php"); # comprobar session.
require ("../../models/lecturas/clientes.php"); # comprobar session.
require ("../../models/lecturas/inventario.php"); # comprobar session.
require ("../../models/lecturas/ventas.php"); # comprobar session.
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

# Informacion de los clientes.
$registro = nombre_clientes($conexion, $_SESSION['id_empresa']);
if (!isset($registro)) {
    header("Location: ../../pages/gestion/clientes.php");
    exit;
}

# Informacion de los productos del inventario.
$registro_producto = productos($conexion, $_SESSION['id_empresa']);
if (!isset($registro_producto)) {
    header("Location: ../../pages/gestion/clientes.php");
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
                <button class="tab-button"><a href="clientes.php">👥 Clientes</a></button>
                <button class="tab-button"><a href="compras.php">📦 Compras</a></button>
                <button class="tab-button"><a href="inventario.php">📊 Inventario</a></button>
                <button class="tab-button active"><a href="ventas.php">💰 Ventas</a></button>
                <button class="tab-button"><a href="finanzas.php">💼 Finanzas</a></button>
                <button class="tab-button"><a href="indicadores.php">📈 Indicadores</a></button>
                <button class="tab-button"><a href="configuracion.php">⚙️ Configuración</a></button>
                <button class="tab-button logout-btn">🚪 Salir</button>
            </div>
        </div>

        <div class="tabs-content">
            <div id="ventas" class="tab-pane active">
                <div class="card">
                    <div class="card-header" style="color: white;">
                        <h2>💰 Ventas Detalladas</h2>
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
                    <form method=POST action=../../controllers/ventas/actualizar.php>
                        <div class="form-grid">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Cliente *</label>
                                <select id="vCliente" class="form-select" name="cliente" required>
                                    <option value="">Seleccione cliente</option>
                                    <?php
                                        foreach ($registro as $c) {
                                            echo "<option value='" . $c['nombre'] . "' data-nit='" . $c['nit'] . "'>" . $c['nombre'] . "</option>";
                                        }
                                    ?>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Producto *</label>
                                <select id="vSelectProducto" name="producto" class="form-select" required>
                                    <option value="">Seleccione un producto</option>
                                    <?php
                                        foreach ($registro_producto as $prod) {
                                            $rango_anos = $prod['compatible_desde'] . " - " . $prod['compatible_hasta'];
                                            echo "<option value='" . $prod['nombre'] . "' 
                                                    data-marca='" . $prod['marca'] . "' 
                                                    data-modelo='" . $prod['modelo'] . "' 
                                                    data-ano='" . $rango_anos . "' 
                                                    data-precio='" . $prod['precio_venta'] . "'
                                                    data-stock='" . $prod['unidades'] . "'>" 
                                                    . $prod['nombre'] . 
                                                 "</option>";
                                        }
                                    ?>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Marca *</label>
                                <input id="vMarca" name=marca placeholder="Se autocompleta" required readonly style="background-color: #f9f9f9;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Modelo *</label>
                                <input id="vModelo" name=modelo placeholder="Se autocompleta" required readonly style="background-color: #f9f9f9;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Año *</label>
                                <input id="vAno" type="text" name=compatible placeholder="Se autocompleta" required readonly style="background-color: #f9f9f9;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Cantidad *</label>
                                <input id="vCantidad" name=cantidad type="number" min="1" placeholder="Ej: 2" required oninput="validarYCalcular()">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Precio Venta *</label>
                                <input id="vPrecioVenta" name=precio_venta type="number" min="0" step="0.01" placeholder="0.00" required readonly style="background-color: #f9f9f9;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Tipo de Factura *</label>
                                <select id="vTipoFactura" name=factura class="form-select" required onchange="validarYCalcular()">
                                    <option value="credito">Crédito Fiscal (IVA 13%)</option>
                                    <option value="consumidor">Factura Consumidor Final</option>
                                    <option value="comprobante">Comprobante de Pago</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Registro IVA</label>
                                <input id="vRegistroIVA" name=iva placeholder="Opcional">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 5px; font-weight: 600;">Registro NIT</label>
                                <input id="vRegistroNIT" name=nit readonly placeholder="Se autocompleta" style="background-color: #f9f9f9;">
                            </div>
                        </div>
                        <div style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Observaciones</label>
                            <input id="vObs" name=observaciones placeholder="Notas sobre la venta...">
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
                        
                        <div id="clientHistoryContainer" class="brand-model-section" style="display: none;">
                            <h4>📋 Historial de Compras</h4>
                            <div id="clientHistoryList"></div>
                        </div>
                        
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                            <div style="display: flex; gap: 10px;">
                                <button class="btn btn-success" onclick="registrarVenta()">💳 Registrar Venta</button>
                            </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // 1. Lógica para Cliente (NIT)
        document.getElementById('vCliente').onchange = function() {
            var selected = this.options[this.selectedIndex];
            var nit = selected.getAttribute('data-nit');
            document.getElementById('vRegistroNIT').value = nit || '';
        };

        // 2. Lógica para seleccionar Producto
        document.getElementById('vSelectProducto').onchange = function() {
            var selected = this.options[this.selectedIndex];
            var inputCant = document.getElementById('vCantidad');
            
            if (selected.value !== "") {
                document.getElementById('vMarca').value = selected.getAttribute('data-marca');
                document.getElementById('vModelo').value = selected.getAttribute('data-modelo');
                document.getElementById('vAno').value = selected.getAttribute('data-ano');
                document.getElementById('vPrecioVenta').value = selected.getAttribute('data-precio');
                
                var stock = selected.getAttribute('data-stock');
                inputCant.max = stock;
                inputCant.placeholder = "Max: " + stock;
            } else {
                document.getElementById('vMarca').value = "";
                document.getElementById('vModelo').value = "";
                document.getElementById('vAno').value = "";
                document.getElementById('vPrecioVenta').value = "";
                inputCant.placeholder = "Ej: 2";
                inputCant.removeAttribute('max');
            }
            validarYCalcular();
        };

        // 3. Validación de Stock y disparo de Cálculo
        function validarYCalcular() {
            const inputCant = document.getElementById('vCantidad');
            const maxStock = parseInt(inputCant.max);
            
            if (maxStock && parseInt(inputCant.value) > maxStock) {
                alert("Atención: No puedes vender más de " + maxStock + " unidades.");
                inputCant.value = maxStock;
            }
            
            calcularTotal();
        }

        // 4. Lógica de Cálculo de Total e IVA
        // 4. Lógica de Cálculo de Total e IVA
        function calcularTotal() {
            const cantidad = parseFloat(document.getElementById('vCantidad').value) || 0;
            const precio = parseFloat(document.getElementById('vPrecioVenta').value) || 0;
            const tipoFactura = document.getElementById('vTipoFactura').value;
            
            const subtotal = cantidad * precio;
            let totalFinal = subtotal;

            const displayTotal = document.getElementById('vTotalCalculado');
            const containerIVA = document.getElementById('vIVACalculado');
            const displayIVA = document.getElementById('vIVAValor');

            if (tipoFactura === "credito") {
                // Cálculo de IVA (13%) sobre el subtotal
                const iva = subtotal * 0.13;
                totalFinal = subtotal + iva;
                
                displayIVA.innerText = "$" + iva.toFixed(2);
                containerIVA.style.display = "block";
            } else {
                // Consumidor final o Comprobante (sin desglose)
                containerIVA.style.display = "none";
            }

            displayTotal.innerText = "$" + totalFinal.toFixed(2);
        }
    </script>
</body>
</html>