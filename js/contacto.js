/**
 * ==========================================================
 * CoDevPro Technology
 * Archivo: js/contacto.js
 * Módulo: Formulario de contacto
 * Sistema: Inmobiliario
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', function () {

    const formulario = document.getElementById('formContacto');

    const respuesta = document.getElementById('respuestaContacto');

    const boton = document.getElementById('btnEnviarCotizacion');

    const textoBoton = document.getElementById('textoBtnEnviar');


    /*
     * Verificar que el formulario exista
     */

    if (!formulario) {
        return;
    }


    /*
     * ========================================================
     * ENVIAR FORMULARIO
     * ========================================================
     */

    formulario.addEventListener('submit', async function (event) {

        /*
         * Evita la recarga de la página
         */

        event.preventDefault();


        /*
         * Validación HTML5
         */

        if (!formulario.checkValidity()) {

            formulario.reportValidity();

            return;
        }


        /*
         * Ocultar mensaje anterior
         */

        respuesta.style.display = 'none';

        respuesta.innerHTML = '';


        /*
         * Deshabilitar botón
         */

        boton.disabled = true;


        /*
         * Cambiar contenido del botón
         */

        textoBoton.innerHTML = `
            <span
                class="spinner-border spinner-border-sm me-2"
                role="status"
                aria-hidden="true">
            </span>

            Enviando...
        `;


        /*
         * Obtener datos
         */

        const datos = new FormData(formulario);


        try {

            /*
             * =================================================
             * PETICIÓN AJAX
             * =================================================
             */

            const response = await fetch(
                formulario.action,
                {
                    method: 'POST',
                    body: datos,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );


            /*
             * Verificar respuesta HTTP
             */

            if (!response.ok) {

                throw new Error(
                    'Error HTTP: ' + response.status
                );
            }


            /*
             * Convertir respuesta a JSON
             */

            const resultado = await response.json();


            /*
             * =================================================
             * MOSTRAR RESPUESTA
             * =================================================
             */

            respuesta.style.display = 'block';


            if (resultado.success) {

                respuesta.className =
                    'alert alert-success alert-dismissible fade show';

                respuesta.innerHTML = `

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <strong>Solicitud registrada</strong>

                    <br>

                    <span>
                        ${resultado.message}
                    </span>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                    </button>

                `;


                /*
                 * Limpiar formulario
                 */

                formulario.reset();


                /*
                 * Volver a colocar el botón
                 * en su estado original
                 */

                boton.disabled = false;

                textoBoton.innerHTML = `
                    <i class="bi bi-send me-2"></i>
                    Enviar y cotizar
                `;


                /*
                 * Desplazar suavemente hacia
                 * el mensaje
                 */

                respuesta.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });


            } else {

                /*
                 * Error de validación o servidor
                 */

                respuesta.className =
                    'alert alert-danger alert-dismissible fade show';

                respuesta.innerHTML = `

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <strong>No se pudo enviar</strong>

                    <br>

                    <span>
                        ${resultado.message}
                    </span>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="alert"
                        aria-label="Cerrar">
                    </button>

                `;


                /*
                 * Reactivar botón
                 */

                boton.disabled = false;

                textoBoton.innerHTML = `
                    <i class="bi bi-send me-2"></i>
                    Enviar y cotizar
                `;
            }


        } catch (error) {

            console.error(
                'Error al enviar formulario:',
                error
            );


            /*
             * Mostrar error de conexión
             */

            respuesta.style.display = 'block';

            respuesta.className =
                'alert alert-danger alert-dismissible fade show';

            respuesta.innerHTML = `

                <i class="bi bi-wifi-off me-2"></i>

                <strong>Error de conexión</strong>

                <br>

                No se pudo procesar la solicitud.
                Inténtalo nuevamente.

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar">
                </button>

            `;


            /*
             * Reactivar botón
             */

            boton.disabled = false;

            textoBoton.innerHTML = `
                <i class="bi bi-send me-2"></i>
                Enviar y cotizar
            `;
        }

    });

});
