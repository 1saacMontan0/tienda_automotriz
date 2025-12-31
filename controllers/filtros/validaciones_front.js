
/* Validaciones front

La siguiente funcion muestra mensajes de errores cuando el valor escrito en el input 
o textarea no es valido para una correccion antes de enviar los datos al backend.

Debes importar la funcion en el html de la siguiente manera:

<script type=module>
    import { validaciones_front } from "/automotriz/controllers/filtros/validaciones_front.js";
</script>

Nota: Al ser una ruta absoluta unicamente debes copiar y pegar el codigo de importacion
      y te funcionara en cualquier parte del proyecto.

Lo unico que cambiaran seran los argumentos que se le pase a la funcion.
1 - El primer argumento corresponde al Id del input al que verificaras su dato.
2 - El segundo corresponde al Id del elemento hermano del futuro mensaje a mostrar. La razon de este argumento
    unicamente es para ubicar debajo el mensaje de error, por lo general sera el input que se validara.
3 - El tipo de validacion que esperas hacer, es decir si quieres verificar si el formato de un correo es correcto,
    que el nombre del usuario unicamente se haya escrito con letras, etc.
4 - Es el mensaje que quieres mostrar, la frase que se le mostrara al usuario cuando el dato no sea valido.

Esta funcion hace las siguientes validacions.
* Correos electronicos: verifica que el formato del correo sea correcto.
*/

export function validaciones_front(inputId, hermanoId, tipo_validacion, mensaje) {

    const input = document.getElementById(inputId);
    const hermano = document.getElementById(hermanoId);
    const boton = document.getElementById('enviar');

    // Creamos el <p> pero NO lo insertamos aún
    let mensajeError = document.createElement("p");
    mensajeError.id = 'mensaje_error';
    mensajeError.style.color = "red";
    mensajeError.style.margin = "0 0 1rem 0";
    mensajeError.style.textAlign = "left";

    input.addEventListener('change', function () {

        let error = false;
        const valor = input.value.trim(); 
        
        const formato_correo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const solo_letras = /^[A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s]+$/;
        const solo_numeros = /^[0-9]+([.,][0-9]+)*$/;
        const no_especial = /^[a-zA-Z0-9#, ]+$/; // no caracteres especiales

        switch (tipo_validacion) {
            case 'correo':
                if (!formato_correo.test(valor) || valor === "") {
                    mensajeError.textContent = mensaje;
                    if (!mensajeError.isConnected) {
                        hermano.insertAdjacentElement("afterend", mensajeError);
                    }
                    error = true;
                } else {
                    if (mensajeError.isConnected) {
                        mensajeError.remove();
                    }
                }
                break;

            case 'contraseña':
                if (valor.length < 8) {
                    mensajeError.textContent = mensaje;
                    if (!mensajeError.isConnected) {
                        hermano.insertAdjacentElement("afterend", mensajeError);
                    }
                    error = true;
                } else {
                    if (mensajeError.isConnected) {
                        mensajeError.remove();
                    }
                }
                break;

            case 'solo_letras':
                if (!solo_letras.test(valor) || valor === "") {
                    mensajeError.textContent = mensaje;
                    if (!mensajeError.isConnected) {
                        hermano.insertAdjacentElement("afterend", mensajeError);
                    }
                    error = true;
                } else {
                    if (mensajeError.isConnected) {
                        mensajeError.remove();
                    }
                }
                break;
            
            case 'no_especial':
                if (!no_especial.test(valor) || valor === "") {
                    mensajeError.textContent = mensaje;
                    if (!mensajeError.isConnected) {
                        hermano.insertAdjacentElement("afterend", mensajeError);
                    }
                    error = true;
                } else {
                    if (mensajeError.isConnected) {
                        mensajeError.remove();
                    }
                }
                break;

            case 'solo_numeros':
                if (!solo_numeros.test(valor)) {
                    mensajeError.textContent = mensaje;
                    if (!mensajeError.isConnected) {
                        hermano.insertAdjacentElement("afterend", mensajeError);
                    }
                    error = true;
                } else {
                    if (mensajeError.isConnected) {
                        mensajeError.remove();
                    }
                }
                break;

            case 'telefono':
                if (!solo_numeros.test(valor) || valor.length !== 8) {
                    mensajeError.textContent = mensaje;
                    if (!mensajeError.isConnected) {
                        hermano.insertAdjacentElement("afterend", mensajeError);
                    }
                    error = true;
                } else {
                    if (mensajeError.isConnected) {
                        mensajeError.remove();
                    }
                }
                break;

            case 'nit':
                if (!solo_numeros.test(valor) || valor.length !== 14) {
                    mensajeError.textContent = mensaje;
                    if (!mensajeError.isConnected) {
                        hermano.insertAdjacentElement("afterend", mensajeError);
                    }
                    error = true;
                } else {
                    if (mensajeError.isConnected) {
                        mensajeError.remove();
                    }
                }
                break;

            default:
                mensajeError.textContent = 'El dato ingresado no es válido.';
                if (!mensajeError.isConnected) {
                    hermano.insertAdjacentElement("afterend", mensajeError);
                }
                error = true;
                break;
        }

        // elimina el mensaje del back en caso que existan
        let errorBack = document.getElementById("error-back");
        if (errorBack) { errorBack.remove(); }
        boton.disabled = error;
        
        // etiliza el boton si el dato no es valido
        if ( boton.disabled === true) {
            boton.style.background = "red";
        }
        else {
            boton.style.background = "#1659a8";
        }
    });
}