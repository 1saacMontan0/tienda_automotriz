<!-- Login -->

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
                <form method=POST action=#>
                    <input type="text" id="username" name=usuario
                        class="login-input" placeholder="Usuario">
                    <input type="password" id="password" name=contra
                        class="login-input" placeholder="Contraseña">
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
    </body>
</html>