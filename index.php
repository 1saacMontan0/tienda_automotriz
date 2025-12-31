<!-- Login -->

<?php 
    # mensajes de erorr backend.
    require ('utils/mensajes_back.php');
    session_start();
?>

<!DOCTYPE html>
<html>
    <head>
        <title>login</title>
        <meta charset=uft-8>
        <link rel=stylesheet href=styles/styles.css>
    </head>
    <body>
        <div id="loginScreen" class="login-container">
            <div class="login-box">
                <h2 class="login-title">🔒 CRM + Inventario</h2>
                <div class="user-type-buttons">
                    <button class="user-type-btn active" onclick="selectUserType('admin')">👑 Administrador</button>
                    <button class="user-type-btn" onclick="selectUserType('user')">👤 Usuario</button>
                </div>
                <form method=POST action=controllers/acceso/login.php>
                    <input type="text" id="usuario" name=usuario
                        class="login-input" placeholder="Usuario">
                    <input type="password" id="contra" name=contra
                        class="login-input" placeholder="Contraseña">
                    <?php error_mensaje_back(); ?>
                    <button class="login-btn" id=enviar>Iniciar Sesión</button>
                </form>
            </div>
        </div>
        <script>
            function selectUserType(type) {
                userType = type;
                const buttons = document.querySelectorAll('.user-type-btn');
                buttons.forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
            }
        </script>
        <script type=module>
            import { validaciones_front } from "/automotriz/controllers/filtros/validaciones_front.js";

            validaciones_front("usuario","usuario", "no_especial","No se aceptan caracteres especiales");
            validaciones_front("contra","contra", "contraseña","Se requieren 8 caracteres minimo");
        </script>
    </body>
</html>