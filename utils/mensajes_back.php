
<?php

//muestra mensajes de error del backend.

# cuando el sistema detecta un error, por ejemplo un usuario que no  este
# registrado se guarda un mensaje en el arreglo $_SESSION['errores'],  al 
# Al llamar esta funcion se generara un espacio donde se guarden muestren
# los mensajes de error del backend.

function error_mensaje_back() {
    if (isset($_SESSION['errores']) && count($_SESSION['errores']) > 0) {
        echo "<div
            id='error-back'
            style='
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
            padding: 15px;
            margin: 10px 0;
            border-radius: 10px;
        '>";
            foreach ($_SESSION['errores'] as $error) {
                echo "<p id='error-back'
                    style='
                    text-align: left;
                    margin: 4px 0'>"
                    . htmlspecialchars($error). 
                "</p>";
            }
        echo "</div>";
    }
    unset($_SESSION['errores']);
}


?>