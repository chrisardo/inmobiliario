<?php
// ============================================================
// Inmobiliaria Iquitos
// Archivo: modal/modal_ver_testimonio.php
// Módulo: Visualizar Testimonio
// ============================================================
?>

<!-- ============================================================
     MODAL VER TESTIMONIO
============================================================ -->

<div
    class="modal fade"
    id="modalVerTestimonio"
    tabindex="-1"
    aria-labelledby="modalVerTestimonioLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered ">

        <div class="modal-content modal-testimonio-view">

            <!-- =================================================
                 CABECERA
            ================================================== -->

            <div class="modal-header modal-testimonio-header">

                <div class="modal-header-info">

                    <div class="modal-header-icon">
                        <i class="fa-solid fa-comments"></i>
                    </div>

                    <div>

                        <span class="modal-header-label">
                            EXPERIENCIA DEL CLIENTE
                        </span>

                        <h5
                            class="modal-title"
                            id="modalVerTestimonioLabel">

                            Testimonio del cliente

                        </h5>

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-close modal-testimonio-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>


            <!-- =================================================
                 CUERPO
            ================================================== -->

            <div class="modal-body modal-testimonio-body">


                <!-- =================================================
                     LOADING
                ================================================== -->

                <div
                    id="modalTestimonioLoading"
                    class="modal-testimonio-loading">

                    <div class="modal-loading-icon">

                        <div
                            class="spinner-border"
                            role="status">

                            <span class="visually-hidden">
                                Cargando...
                            </span>

                        </div>

                    </div>

                    <h6>
                        Cargando testimonio
                    </h6>

                    <p>
                        Estamos obteniendo la información del cliente...
                    </p>

                </div>


                <!-- =================================================
                     ERROR
                ================================================== -->

                <div
                    id="modalTestimonioError"
                    class="modal-testimonio-error d-none">

                    <div class="modal-error-icon">

                        <i class="fa-solid fa-triangle-exclamation"></i>

                    </div>

                    <h5>
                        No se pudo cargar el testimonio
                    </h5>

                    <p>
                        Ocurrió un problema al obtener la información.
                        Intenta nuevamente.
                    </p>

                    <button
                        type="button"
                        class="btn btn-light btn-admin"
                        data-bs-dismiss="modal">

                        <i class="fa-solid fa-xmark me-1"></i>

                        Cerrar

                    </button>

                </div>


                <!-- =================================================
                     CONTENIDO
                ================================================== -->

                <div
                    id="modalTestimonioContenido"
                    class="d-none">


                    <div class="testimonial-view-grid">


                        <!-- =================================================
                             COLUMNA IZQUIERDA
                        ================================================== -->

                        <div class="testimonial-view-left">


                            <!-- =================================================
                                 IMAGEN
                            ================================================== -->

                            <div
                                id="modalTestimonioImagenContainer"
                                class="modal-testimonio-imagen-container d-none">

                                <div class="testimonial-image-wrapper">

                                    <img
                                        id="modalTestimonioImagen"
                                        src=""
                                        alt="Imagen del testimonio">

                                    <div class="testimonial-image-overlay">

                                        <span>
                                            <i class="fa-solid fa-user-check"></i>
                                            Cliente
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <!-- =================================================
                                 PERFIL SIN IMAGEN / IDENTIFICACIÓN
                            ================================================== -->

                            <div class="testimonial-profile-card">

                                <div class="testimonial-profile-icon">

                                    <i class="fa-solid fa-user"></i>

                                </div>

                                <div class="testimonial-profile-info">

                                    <span>
                                        TESTIMONIO DE
                                    </span>

                                    <h3
                                        id="modalTestimonioNombre"
                                        class="modal-testimonio-nombre">
                                        —
                                    </h3>

                                    <div
                                        id="modalTestimonioFecha"
                                        class="modal-testimonio-fecha">

                                        <i class="fa-regular fa-calendar"></i>

                                        <span>
                                            —
                                        </span>

                                    </div>

                                </div>

                            </div>


                        </div>


                        <!-- =================================================
                             COLUMNA DERECHA
                        ================================================== -->

                        <div class="testimonial-view-right">


                            <!-- =================================================
                                 ENCABEZADO COMENTARIO
                            ================================================== -->

                            <div class="testimonial-section-heading">

                                <div class="testimonial-section-icon">

                                    <i class="fa-solid fa-quote-left"></i>

                                </div>

                                <div>

                                    <span>
                                        OPINIÓN DEL CLIENTE
                                    </span>

                                    <h6>
                                        Lo que nos cuenta
                                    </h6>

                                </div>

                            </div>


                            <!-- =================================================
                                 COMENTARIO
                            ================================================== -->

                            <div
                                id="modalTestimonioComentario"
                                class="modal-testimonio-comentario">

                                —

                            </div>


                            <!-- =================================================
                                 VIDEO
                            ================================================== -->

                            <div
                                id="modalTestimonioVideoContainer"
                                class="modal-testimonio-video-container d-none">

                                <div class="testimonial-section-heading">

                                    <div class="testimonial-section-icon video-icon">

                                        <i class="fa-solid fa-play"></i>

                                    </div>

                                    <div>

                                        <span>
                                            CONTENIDO MULTIMEDIA
                                        </span>

                                        <h6>
                                            Video del testimonio
                                        </h6>

                                    </div>

                                </div>


                                <div class="testimonial-video-wrapper">

                                    <video
                                        id="modalTestimonioVideo"
                                        class="modal-testimonio-video"
                                        controls
                                        preload="metadata">

                                        Tu navegador no soporta la reproducción
                                        de videos.

                                    </video>

                                </div>

                            </div>


                        </div>


                    </div>


                    <!-- =================================================
                         PIE INTERNO
                    ================================================== -->

                    <div class="testimonial-view-note">

                        <div class="testimonial-view-note-icon">

                            <i class="fa-solid fa-shield-heart"></i>

                        </div>

                        <div>

                            <strong>
                                Experiencia de nuestros clientes
                            </strong>

                            <span>
                                Los testimonios nos ayudan a seguir mejorando
                                nuestro servicio inmobiliario.
                            </span>

                        </div>

                    </div>


                </div>

            </div>


            <!-- =================================================
                 PIE
            ================================================== -->

            <div class="modal-footer modal-testimonio-footer">

                <div class="modal-footer-status">

                    <span class="status-dot"></span>

                    <span>
                        Testimonio registrado
                    </span>

                </div>

                <button
                    type="button"
                    class="btn btn-secondary btn-admin modal-testimonio-btn-close"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-xmark me-1"></i>

                    Cerrar

                </button>

            </div>

        </div>

    </div>

</div>


<!-- ============================================================
     JS DEL MODAL
============================================================ -->

<script>
    window.verTestimonio = async function(idTestimonio) {

        const modalElement =
            document.getElementById('modalVerTestimonio');

        if (!modalElement) {
            console.error(
                'No se encontró #modalVerTestimonio'
            );
            return;
        }

        const modal =
            bootstrap.Modal.getOrCreateInstance(
                modalElement
            );

        const loading =
            document.getElementById(
                'modalTestimonioLoading'
            );

        const error =
            document.getElementById(
                'modalTestimonioError'
            );

        const contenido =
            document.getElementById(
                'modalTestimonioContenido'
            );

        const imagenContainer =
            document.getElementById(
                'modalTestimonioImagenContainer'
            );

        const imagen =
            document.getElementById(
                'modalTestimonioImagen'
            );

        const nombre =
            document.getElementById(
                'modalTestimonioNombre'
            );

        const fecha =
            document.getElementById(
                'modalTestimonioFecha'
            );

        const comentario =
            document.getElementById(
                'modalTestimonioComentario'
            );

        const videoContainer =
            document.getElementById(
                'modalTestimonioVideoContainer'
            );

        const video =
            document.getElementById(
                'modalTestimonioVideo'
            );


        /* ----------------------------------------------------------
           ESTADO INICIAL
        ---------------------------------------------------------- */

        loading.classList.remove('d-none');
        error.classList.add('d-none');
        contenido.classList.add('d-none');

        imagenContainer.classList.add('d-none');
        videoContainer.classList.add('d-none');

        imagen.removeAttribute('src');
        video.removeAttribute('src');

        nombre.textContent = '—';

        fecha.querySelector('span').textContent = '—';

        comentario.textContent = '—';


        /* ----------------------------------------------------------
           MOSTRAR MODAL
        ---------------------------------------------------------- */

        modal.show();


        /* ----------------------------------------------------------
           PETICIÓN AJAX
        ---------------------------------------------------------- */

        try {

            const respuesta = await fetch(
                '../controladores/ajax_obtener_testimonio.php?id=' +
                encodeURIComponent(idTestimonio), {
                    method: 'GET',
                    credentials: 'same-origin',
                    cache: 'no-store',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }
            );


            if (!respuesta.ok) {

                throw new Error(
                    'Error HTTP: ' + respuesta.status
                );

            }


            const datos =
                await respuesta.json();


            if (!datos.success) {

                throw new Error(
                    datos.message ||
                    'No fue posible obtener el testimonio.'
                );

            }


            /* ------------------------------------------------------
               NOMBRE
            ------------------------------------------------------ */

            nombre.textContent =
                datos.testimonio.nombreCompleto ||
                'Cliente';


            /* ------------------------------------------------------
               FECHA
            ------------------------------------------------------ */

            fecha.querySelector('span').textContent =
                datos.testimonio.fecha ||
                'Fecha no disponible';


            /* ------------------------------------------------------
               COMENTARIO
            ------------------------------------------------------ */

            comentario.textContent =
                datos.testimonio.comentario ||
                'El cliente no registró un comentario.';


            /* ------------------------------------------------------
               IMAGEN
            ------------------------------------------------------ */

            if (
                datos.testimonio.imagen &&
                datos.testimonio.imagen.trim() !== ''
            ) {

                imagen.src =
                    datos.testimonio.imagen;

                imagen.alt =
                    'Imagen del testimonio de ' +
                    (
                        datos.testimonio.nombreCompleto ||
                        'cliente'
                    );

                imagenContainer.classList.remove(
                    'd-none'
                );

            }


            /* ------------------------------------------------------
               VIDEO
            ------------------------------------------------------ */

            /* ------------------------------------------------------
   VIDEO
------------------------------------------------------ */

            if (
                datos.testimonio.video &&
                datos.testimonio.video.trim() !== ''
            ) {

                const rutaVideo =
                    datos.testimonio.video.trim();

                console.log(
                    'URL del video:',
                    rutaVideo
                );

                video.pause();

                video.removeAttribute('src');

                video.load();

                video.src = rutaVideo;

                video.load();

                videoContainer.classList.remove(
                    'd-none'
                );

            } else {

                videoContainer.classList.add(
                    'd-none'
                );

                video.removeAttribute('src');

                video.load();
            }


            /* ------------------------------------------------------
               MOSTRAR CONTENIDO
            ------------------------------------------------------ */

            loading.classList.add('d-none');

            contenido.classList.remove('d-none');


        } catch (e) {

            console.error(
                'Error al cargar testimonio:',
                e
            );

            loading.classList.add('d-none');

            contenido.classList.add('d-none');

            error.classList.remove('d-none');

        }

    };


    /* ============================================================
       LIMPIAR VIDEO AL CERRAR
    ============================================================ */

    document.addEventListener(
        'hidden.bs.modal',
        function(event) {

            if (
                event.target.id !==
                'modalVerTestimonio'
            ) {
                return;
            }

            const video =
                document.getElementById(
                    'modalTestimonioVideo'
                );

            if (video) {

                video.pause();

                video.removeAttribute('src');

                video.load();

            }

        }
    );
</script>