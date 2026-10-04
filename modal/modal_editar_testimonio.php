<?php
// ============================================================
// Inmobiliaria Iquitos
// Archivo: modal/modal_editar_testimonio.php
// Módulo: Editar Testimonio
// ============================================================
?>

<!-- ============================================================
     MODAL EDITAR TESTIMONIO
============================================================ -->

<div
    class="modal fade"
    id="modalEditarTestimonio"
    tabindex="-1"
    aria-labelledby="modalEditarTestimonioLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <!-- =================================================
                 CABECERA
            ================================================== -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="modalEditarTestimonioLabel"
                >

                    <i class="fa-solid fa-pen-to-square me-2"></i>

                    Editar testimonio

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"
                ></button>

            </div>


            <!-- =================================================
                 FORMULARIO
            ================================================== -->

            <form
                id="formEditarTestimonio"
                enctype="multipart/form-data"
            >

                <div class="modal-body">

                    <!-- =================================================
                         ID DEL TESTIMONIO
                    ================================================== -->

                    <input
                        type="hidden"
                        id="editarTestimonioId"
                        name="id_testimonio"
                        value=""
                    >


                    <!-- =================================================
                         LOADING
                    ================================================== -->

                    <div
                        id="editarTestimonioLoading"
                        class="text-center py-5"
                    >

                        <div
                            class="spinner-border text-success"
                            role="status"
                        >

                            <span class="visually-hidden">
                                Cargando...
                            </span>

                        </div>


                        <p class="mt-3 mb-0 text-muted">
                            Cargando información...
                        </p>

                    </div>


                    <!-- =================================================
                         CONTENIDO
                    ================================================== -->

                    <div
                        id="editarTestimonioContenido"
                        class="d-none"
                    >

                        <div class="row g-3">


                            <!-- =================================================
                                 NOMBRE
                            ================================================== -->

                            <div class="col-12 col-md-6">

                                <label
                                    for="editarNombre"
                                    class="form-label fw-semibold"
                                >

                                    Nombres

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="fa-solid fa-user"></i>

                                    </span>


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="editarNombre"
                                        name="nombre"
                                        maxlength="100"
                                        autocomplete="off"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- =================================================
                                 APELLIDOS
                            ================================================== -->

                            <div class="col-12 col-md-6">

                                <label
                                    for="editarApellidos"
                                    class="form-label fw-semibold"
                                >

                                    Apellidos

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <div class="input-group">

                                    <span class="input-group-text">

                                        <i class="fa-solid fa-user-tag"></i>

                                    </span>


                                    <input
                                        type="text"
                                        class="form-control"
                                        id="editarApellidos"
                                        name="apellidos"
                                        maxlength="150"
                                        autocomplete="off"
                                        required
                                    >

                                </div>

                            </div>


                            <!-- =================================================
                                 COMENTARIO
                            ================================================== -->

                            <div class="col-12">

                                <label
                                    for="editarComentario"
                                    class="form-label fw-semibold"
                                >

                                    Comentario

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>


                                <textarea
                                    class="form-control"
                                    id="editarComentario"
                                    name="comentario"
                                    rows="6"
                                    maxlength="1000"
                                    required
                                ></textarea>


                                <div class="form-text">

                                    Escribe la experiencia u opinión
                                    del cliente.

                                </div>

                            </div>


                            <!-- =================================================
                                 IMAGEN ACTUAL
                            ================================================== -->

                            <div class="col-12">

                                <div
                                    class="editar-media-section"
                                >

                                    <div
                                        class="editar-media-title"
                                    >

                                        <i
                                            class="fa-solid fa-image me-1"
                                        ></i>

                                        Imagen del testimonio

                                    </div>


                                    <!-- IMAGEN ACTUAL -->

                                    <div
                                        id="editarImagenActualContainer"
                                        class="d-none mt-3"
                                    >

                                        <div
                                            class="editar-imagen-preview"
                                        >

                                            <img
                                                id="editarImagenActual"
                                                src=""
                                                alt="Imagen actual del testimonio"
                                            >

                                        </div>

                                    </div>


                                    <!-- REEMPLAZAR IMAGEN -->

                                    <label
                                        for="editarImagen"
                                        class="form-label mt-3"
                                    >

                                        Reemplazar imagen

                                    </label>


                                    <input
                                        type="file"
                                        class="form-control"
                                        id="editarImagen"
                                        name="imagen"
                                        accept="image/jpeg,image/png,image/webp"
                                    >


                                    <div class="form-text">

                                        JPG, PNG o WEBP.
                                        Máximo 5 MB.

                                    </div>

                                </div>

                            </div>


                            <!-- =================================================
                                 VIDEO
                            ================================================== -->

                            <div class="col-12">

                                <div
                                    class="editar-media-section"
                                >

                                    <div
                                        class="editar-media-title"
                                    >

                                        <i
                                            class="fa-solid fa-video me-1"
                                        ></i>

                                        Video del testimonio

                                    </div>


                                    <!-- =================================================
                                         VIDEO ACTUAL
                                    ================================================== -->

                                    <div
                                        id="editarVideoActualContainer"
                                        class="d-none mt-3"
                                    >

                                        <div
                                            class="editar-video-current-label"
                                        >

                                            <i class="fa-solid fa-circle-play me-1"></i>

                                            Video actual

                                        </div>


                                        <video
                                            id="editarVideoActual"
                                            class="editar-video-preview"
                                            controls
                                            preload="metadata"
                                            playsinline
                                        >

                                            Tu navegador no soporta
                                            la reproducción de videos.

                                        </video>


                                        <!-- =================================================
                                             ELIMINAR VIDEO
                                        ================================================== -->

                                        <div
                                            class="form-check editar-eliminar-video mt-3"
                                        >

                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                id="editarEliminarVideo"
                                                name="eliminar_video"
                                                value="1"
                                            >


                                            <label
                                                class="form-check-label"
                                                for="editarEliminarVideo"
                                            >

                                                <i
                                                    class="fa-solid fa-trash-can me-1"
                                                ></i>

                                                Eliminar video actual

                                            </label>

                                        </div>

                                    </div>


                                    <!-- =================================================
                                         REEMPLAZAR VIDEO
                                    ================================================== -->

                                    <label
                                        for="editarVideo"
                                        class="form-label mt-3"
                                    >

                                        Reemplazar video

                                    </label>


                                    <input
                                        type="file"
                                        class="form-control"
                                        id="editarVideo"
                                        name="video"
                                        accept="video/mp4,video/webm,video/ogg"
                                    >


                                    <div class="form-text">

                                        MP4, WEBM u OGG.
                                        Máximo 100 MB.

                                    </div>

                                </div>

                            </div>


                        </div>

                    </div>

                </div>


                <!-- =================================================
                     FOOTER
                ================================================== -->

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary btn-admin"
                        data-bs-dismiss="modal"
                    >

                        <i class="fa-solid fa-xmark me-1"></i>

                        Cancelar

                    </button>


                    <button
                        type="submit"
                        id="btnGuardarTestimonio"
                        class="btn btn-success btn-admin"
                    >

                        <i class="fa-solid fa-floppy-disk me-1"></i>

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- ============================================================
     CSS ESPECÍFICO DEL MODAL
============================================================ -->

<style>

#modalEditarTestimonio .modal-content {

    border: none;

    border-radius: 16px;

    overflow: hidden;

    box-shadow:
        0 20px 60px rgba(15, 23, 42, 0.18);
}


/* ============================================================
   HEADER
============================================================ */

#modalEditarTestimonio .modal-header {

    padding: 17px 21px;

    background: #ffffff;

    border-bottom: 1px solid #e5e7eb;
}


#modalEditarTestimonio .modal-title {

    color: #0f172a;

    font-size: 1rem;

    font-weight: 700;
}


#modalEditarTestimonio .modal-title i {

    color: #d97706;
}


/* ============================================================
   BODY
============================================================ */

#modalEditarTestimonio .modal-body {

    padding: 24px;
}


/* ============================================================
   FOOTER
============================================================ */

#modalEditarTestimonio .modal-footer {

    padding: 14px 21px;

    background: #f8fafc;

    border-top: 1px solid #e5e7eb;
}


/* ============================================================
   FORMULARIOS
============================================================ */

#modalEditarTestimonio .form-label {

    color: #334155;

    font-size: 0.82rem;
}


#modalEditarTestimonio .form-control,
#modalEditarTestimonio .input-group-text {

    border-color: #dbe2e8;
}


#modalEditarTestimonio .form-control {

    min-height: 42px;

    font-size: 0.85rem;
}


#modalEditarTestimonio textarea.form-control {

    min-height: 140px;

    resize: vertical;
}


#modalEditarTestimonio .input-group-text {

    color: #64748b;

    background: #f8fafc;
}


/* ============================================================
   SECCIÓN DE MEDIOS
============================================================ */

.editar-media-section {

    padding: 16px;

    background: #f8fafc;

    border: 1px solid #e5e7eb;

    border-radius: 11px;
}


.editar-media-title {

    color: #334155;

    font-size: 0.84rem;

    font-weight: 700;
}


.editar-media-title i {

    color: #16a34a;
}


/* ============================================================
   IMAGEN
============================================================ */

.editar-imagen-preview {

    width: 100%;

    max-width: 300px;

    height: 180px;

    overflow: hidden;

    border-radius: 10px;

    background: #ffffff;

    border: 1px solid #e5e7eb;
}


.editar-imagen-preview img {

    width: 100%;

    height: 100%;

    display: block;

    object-fit: contain;
}


/* ============================================================
   VIDEO
============================================================ */

.editar-video-current-label {

    margin-bottom: 8px;

    color: #64748b;

    font-size: 0.78rem;

    font-weight: 600;
}


.editar-video-current-label i {

    color: #16a34a;
}


.editar-video-preview {

    width: 100%;

    max-width: 500px;

    max-height: 280px;

    display: block;

    background: #000000;

    border-radius: 10px;

    border: 1px solid #e2e8f0;
}


/* ============================================================
   CHECKBOX ELIMINAR
============================================================ */

.editar-eliminar-video {

    padding: 10px 12px;

    background: #fff7ed;

    border: 1px solid #fed7aa;

    border-radius: 8px;
}


.editar-eliminar-video .form-check-input {

    cursor: pointer;
}


.editar-eliminar-video .form-check-label {

    color: #9a3412;

    font-size: 0.8rem;

    font-weight: 600;

    cursor: pointer;
}


.editar-eliminar-video .form-check-input:checked {

    background-color: #dc2626;

    border-color: #dc2626;
}


/* ============================================================
   TEXTO AYUDA
============================================================ */

#modalEditarTestimonio .form-text {

    color: #64748b;

    font-size: 0.72rem;

    line-height: 1.5;
}


/* ============================================================
   RESPONSIVE
============================================================ */

@media (max-width: 575.98px) {

    #modalEditarTestimonio .modal-dialog {

        margin: 10px;
    }


    #modalEditarTestimonio .modal-body {

        padding: 16px;
    }


    #modalEditarTestimonio .modal-header {

        padding: 15px 17px;
    }


    #modalEditarTestimonio .modal-footer {

        padding: 12px 17px;
    }


    .editar-video-preview {

        max-height: 220px;
    }

}

</style>


<!-- ============================================================
     JS DEL MODAL
============================================================ -->

<script>

window.editarTestimonio = async function (idTestimonio) {

    const modalElement =
        document.getElementById(
            'modalEditarTestimonio'
        );


    const loading =
        document.getElementById(
            'editarTestimonioLoading'
        );


    const contenido =
        document.getElementById(
            'editarTestimonioContenido'
        );


    const formulario =
        document.getElementById(
            'formEditarTestimonio'
        );


    const imagenContainer =
        document.getElementById(
            'editarImagenActualContainer'
        );


    const imagen =
        document.getElementById(
            'editarImagenActual'
        );


    const videoContainer =
        document.getElementById(
            'editarVideoActualContainer'
        );


    const video =
        document.getElementById(
            'editarVideoActual'
        );


    const checkboxEliminarVideo =
        document.getElementById(
            'editarEliminarVideo'
        );


    const modal =
        bootstrap.Modal.getOrCreateInstance(
            modalElement
        );


    /* ========================================================
       RESET COMPLETO
    ======================================================== */

    loading.classList.remove(
        'd-none'
    );


    contenido.classList.add(
        'd-none'
    );


    formulario.reset();


    document.getElementById(
        'editarTestimonioId'
    ).value =
        idTestimonio;


    /* ========================================================
       LIMPIAR IMAGEN
    ======================================================== */

    imagenContainer.classList.add(
        'd-none'
    );


    imagen.removeAttribute(
        'src'
    );


    /* ========================================================
       LIMPIAR VIDEO
    ======================================================== */

    video.pause();

    video.removeAttribute(
        'src'
    );

    video.load();


    videoContainer.classList.add(
        'd-none'
    );


    /* ========================================================
       DESMARCAR ELIMINAR VIDEO
    ======================================================== */

    if (checkboxEliminarVideo) {

        checkboxEliminarVideo.checked =
            false;

    }


    /* ========================================================
       MOSTRAR MODAL
    ======================================================== */

    modal.show();


    /* ========================================================
       CARGAR DATOS AJAX
    ======================================================== */

    try {

        const respuesta =
            await fetch(
                '../controladores/ajax_obtener_testimonio.php?id=' +
                encodeURIComponent(
                    idTestimonio
                ),
                {
                    method: 'GET',

                    credentials:
                        'same-origin',

                    cache:
                        'no-store',

                    headers: {
                        'X-Requested-With':
                            'XMLHttpRequest'
                    }
                }
            );


        if (!respuesta.ok) {

            throw new Error(
                'Error HTTP: ' +
                respuesta.status
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


        const testimonio =
            datos.testimonio;


        /* ====================================================
           DATOS
        ==================================================== */

        document.getElementById(
            'editarNombre'
        ).value =
            testimonio.nombre || '';


        document.getElementById(
            'editarApellidos'
        ).value =
            testimonio.apellidos || '';


        document.getElementById(
            'editarComentario'
        ).value =
            testimonio.comentario || '';


        /* ====================================================
           IMAGEN
        ==================================================== */

        if (
            testimonio.imagen &&
            testimonio.imagen.trim() !== ''
        ) {

            imagen.src =
                testimonio.imagen;


            imagenContainer.classList.remove(
                'd-none'
            );

        }


        /* ====================================================
           VIDEO
        ==================================================== */

        if (
            testimonio.video &&
            testimonio.video.trim() !== ''
        ) {

            video.src =
                testimonio.video.trim();


            video.load();


            videoContainer.classList.remove(
                'd-none'
            );

        } else {

            videoContainer.classList.add(
                'd-none'
            );

        }


        /* ====================================================
           MOSTRAR CONTENIDO
        ==================================================== */

        loading.classList.add(
            'd-none'
        );


        contenido.classList.remove(
            'd-none'
        );


    } catch (error) {

        console.error(
            'Error al obtener testimonio:',
            error
        );


        modal.hide();


        Swal.fire({

            icon: 'error',

            title:
                'No se pudo cargar',

            text:
                error.message ||
                'No fue posible obtener la información del testimonio.',

            confirmButtonColor:
                '#16a34a'

        });

    }

};


/* ============================================================
   GUARDAR CAMBIOS
============================================================ */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const formulario =
            document.getElementById(
                'formEditarTestimonio'
            );


        if (!formulario) {

            return;

        }


        formulario.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                const boton =
                    document.getElementById(
                        'btnGuardarTestimonio'
                    );


                /* =================================================
                   VALIDACIÓN HTML
                ================================================== */

                if (
                    !formulario.checkValidity()
                ) {

                    formulario.reportValidity();

                    return;

                }


                /* =================================================
                   FORM DATA
                ================================================== */

                const formData =
                    new FormData(
                        formulario
                    );


                /* =================================================
                   DEBUG DEL CHECKBOX
                   Se puede quitar posteriormente.
                ================================================== */

                console.log(
                    'Eliminar video:',
                    formData.get(
                        'eliminar_video'
                    )
                );


                /* =================================================
                   DESHABILITAR BOTÓN
                ================================================== */

                boton.disabled =
                    true;


                boton.innerHTML = `
                    <span
                        class="spinner-border spinner-border-sm me-1"
                        aria-hidden="true">
                    </span>
                    Guardando...
                `;


                try {

                    const respuesta =
                        await fetch(
                            '../controladores/ajax_editar_testimonio.php',
                            {
                                method:
                                    'POST',

                                credentials:
                                    'same-origin',

                                body:
                                    formData,

                                headers: {
                                    'X-Requested-With':
                                        'XMLHttpRequest'
                                }
                            }
                        );


                    if (!respuesta.ok) {

                        throw new Error(
                            'Error HTTP: ' +
                            respuesta.status
                        );

                    }


                    const datos =
                        await respuesta.json();


                    if (!datos.success) {

                        throw new Error(
                            datos.message ||
                            'No fue posible actualizar el testimonio.'
                        );

                    }


                    /* =================================================
                       LIMPIAR VIDEO DEL MODAL
                    ================================================== */

                    const videoActual =
                        document.getElementById(
                            'editarVideoActual'
                        );


                    if (videoActual) {

                        videoActual.pause();

                        videoActual.removeAttribute(
                            'src'
                        );

                        videoActual.load();

                    }


                    /* =================================================
                       CERRAR MODAL
                    ================================================== */

                    const modal =
                        bootstrap.Modal.getInstance(
                            document.getElementById(
                                'modalEditarTestimonio'
                            )
                        );


                    if (modal) {

                        modal.hide();

                    }


                    /* =================================================
                       MENSAJE
                    ================================================== */

                    await Swal.fire({

                        icon:
                            'success',

                        title:
                            'Testimonio actualizado',

                        text:
                            'Los cambios se guardaron correctamente.',

                        confirmButtonColor:
                            '#16a34a',

                        timer:
                            2200,

                        timerProgressBar:
                            true

                    });


                    /* =================================================
                       RECARGAR LISTA
                    ================================================= */

                    if (
                        typeof window
                            .recargarListaTestimonios ===
                        'function'
                    ) {

                        window
                            .recargarListaTestimonios();

                    }

                } catch (error) {

                    console.error(
                        'Error al editar testimonio:',
                        error
                    );


                    Swal.fire({

                        icon:
                            'error',

                        title:
                            'No se pudo actualizar',

                        text:
                            error.message ||
                            'Ocurrió un error al guardar los cambios.',

                        confirmButtonColor:
                            '#16a34a'

                    });

                } finally {

                    boton.disabled =
                        false;


                    boton.innerHTML = `
                        <i class="fa-solid fa-floppy-disk me-1"></i>
                        Guardar cambios
                    `;

                }

            }
        );

    }
);


/* ============================================================
   LIMPIAR VIDEO AL CERRAR MODAL
============================================================ */

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const modalElement =
            document.getElementById(
                'modalEditarTestimonio'
            );


        if (!modalElement) {

            return;

        }


        modalElement.addEventListener(
            'hidden.bs.modal',
            function () {

                const video =
                    document.getElementById(
                        'editarVideoActual'
                    );


                const checkbox =
                    document.getElementById(
                        'editarEliminarVideo'
                    );


                if (video) {

                    video.pause();

                    video.removeAttribute(
                        'src'
                    );

                    video.load();

                }


                if (checkbox) {

                    checkbox.checked =
                        false;

                }

            }
        );

    }
);

</script>
