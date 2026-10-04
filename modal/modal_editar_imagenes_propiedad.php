<?php

/**
 * =========================================================
 * CoDevPro Technology
 * Archivo: modal/modal_editar_imagenes_propiedad.php
 * Módulo: Propiedades
 * Función: Visualizar y editar imágenes de una propiedad
 * =========================================================
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/*
 * CSRF utilizado por el módulo de propiedades.
 */
$csrfImagenes = $_SESSION['csrf_propiedades'] ?? '';

?>

<!-- =========================================================
     MODAL PRINCIPAL - EDITAR IMÁGENES
========================================================= -->

<div
    class="modal fade"
    id="modalEditarImagenes"
    tabindex="-1"
    aria-labelledby="modalEditarImagenesLabel"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false">

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content border-0 shadow-lg">

            <!-- =================================================
                 HEADER
            ================================================== -->

            <div class="modal-header bg-white border-bottom">

                <div class="d-flex align-items-center gap-3">

                    <div
                        class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                        style="
                            width:46px;
                            height:46px;
                            background:rgba(25,135,84,.10);
                            color:#198754;
                        ">

                        <i class="fa-solid fa-images fs-5"></i>

                    </div>

                    <div>

                        <h5
                            class="modal-title fw-bold mb-0"
                            id="modalEditarImagenesLabel">

                            Imágenes de la propiedad

                        </h5>

                        <div class="small text-muted mt-1">

                            <span id="nombrePropiedadImagenes">
                                Propiedad
                            </span>

                            <span class="mx-1">•</span>

                            <span id="contadorImagenes">
                                0 imágenes
                            </span>

                        </div>

                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>


            <!-- =================================================
                 BODY
            ================================================== -->

            <div class="modal-body bg-light">

                <!-- =================================================
                     ALERTA GENERAL
                ================================================== -->

                <div
                    id="alertaImagenes"
                    class="alert d-none"
                    role="alert">
                </div>


                


                <!-- =================================================
                     DATOS OCULTOS
                ================================================== -->

                <input
                    type="hidden"
                    id="imagenes-id-propiedad"
                    name="id_propiedad"
                    value="">

                <input
                    type="hidden"
                    id="imagenes-csrf"
                    name="csrf_token"
                    value="<?= htmlspecialchars(
                        $csrfImagenes,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>">


                <!-- =================================================
                     ZONA AGREGAR IMÁGENES
                ================================================== -->

                <div
                    class="card border-0 shadow-sm mb-4"
                    style="border-radius:16px;">

                    <div class="card-body">

                        <div
                            class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">

                            <div>

                                <h6 class="fw-bold mb-1">

                                    <i class="fa-solid fa-cloud-arrow-up text-success me-2"></i>

                                    Agregar imágenes

                                </h6>

                                <p class="text-muted small mb-0">

                                    Puedes seleccionar una o varias imágenes.

                                    <br>

                                    JPG, JPEG, PNG o WEBP · Máximo 2.7 MB por imagen.

                                </p>

                            </div>


                            <div class="d-flex align-items-center gap-2 flex-wrap">

                                <!-- INPUT REAL -->

                                <input
                                    type="file"
                                    id="inputImagenesPropiedad"
                                    class="d-none"
                                    accept="image/jpeg,image/png,image/webp"
                                    multiple>


                                <!-- BOTÓN SELECCIONAR -->

                                <button
                                    type="button"
                                    id="btnSeleccionarImagenes"
                                    class="btn btn-outline-success">

                                    <i class="fa-solid fa-plus me-1"></i>

                                    Seleccionar imágenes

                                </button>


                                <!-- BOTÓN SUBIR -->

                                <button
                                    type="button"
                                    id="btnSubirImagenes"
                                    class="btn btn-success">

                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i>

                                    Agregar imagen

                                </button>

                            </div>

                        </div>


                        <!-- =================================================
                             ARCHIVOS SELECCIONADOS
                        ================================================== -->

                        <div
                            id="archivosSeleccionados"
                            class="mt-3 d-none">

                            <div
                                class="small fw-semibold text-muted mb-2">

                                Archivos seleccionados:

                            </div>

                            <div
                                id="listaArchivosSeleccionados"
                                class="d-flex flex-wrap gap-2">
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     CARGANDO IMÁGENES
                ================================================== -->

                <div
                    id="cargandoImagenes"
                    class="text-center py-5 d-none">

                    <div
                        class="spinner-border text-success mb-3"
                        role="status">

                        <span class="visually-hidden">
                            Cargando...
                        </span>

                    </div>

                    <div class="fw-semibold">
                        Cargando imágenes...
                    </div>

                    <div class="small text-muted">
                        Espere un momento.
                    </div>

                </div>


                <!-- =================================================
                     GALERÍA
                ================================================== -->

                <div
                    id="galeriaImagenesPropiedad"
                    class="row g-4">
                </div>


                <!-- =================================================
                     SIN IMÁGENES
                ================================================== -->

                <div
                    id="sinImagenesPropiedad"
                    class="card border-0 shadow-sm d-none"
                    style="border-radius:18px;">

                    <div class="card-body text-center py-5">

                        <div
                            class="mx-auto mb-3 rounded-circle d-flex align-items-center justify-content-center"
                            style="
                                width:80px;
                                height:80px;
                                background:rgba(108,117,125,.10);
                                color:#6c757d;
                            ">

                            <i class="fa-regular fa-images fs-2"></i>

                        </div>

                        <h5 class="fw-bold mb-2">

                            Esta propiedad no tiene imágenes

                        </h5>

                        <p class="text-muted mb-4">

                            Agrega imágenes para mostrar la propiedad
                            en tu sistema.

                        </p>

                        <button
                            type="button"
                            id="btnAgregarImagenVacio"
                            class="btn btn-success">

                            <i class="fa-solid fa-plus me-1"></i>

                            Agregar imagen

                        </button>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div class="modal-footer bg-white border-top">

                <div class="me-auto small text-muted">

                    <i class="fa-solid fa-circle-info me-1"></i>

                    La primera imagen puede utilizarse como imagen
                    principal.

                </div>

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-xmark me-1"></i>

                    Cerrar

                </button>

            </div>

        </div>
        
    </div>

</div>


<!-- =========================================================
     MODAL CAMBIAR IMAGEN
========================================================= -->

<div
    class="modal fade"
    id="modalCambiarImagen"
    tabindex="-1"
    aria-labelledby="modalCambiarImagenLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            <!-- =================================================
                 HEADER
            ================================================== -->

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title fw-bold"
                        id="modalCambiarImagenLabel">

                        Cambiar imagen

                    </h5>

                    <div class="small text-muted">

                        Reemplazarás únicamente esta imagen.

                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>


            <!-- =================================================
                 BODY
            ================================================== -->

            <div class="modal-body">

                <!-- ID DE LA IMAGEN DE LA TABLA imagenes -->

                <input
                    type="hidden"
                    id="cambiar-id-imagen"
                    name="id_imagen"
                    value="">


                <div class="text-center mb-3">

                    <img
                        id="vistaPreviaCambiarImagen"
                        src=""
                        alt="Vista previa de la imagen"
                        class="img-fluid rounded-3 border"
                        style="
                            max-height:260px;
                            width:100%;
                            object-fit:cover;
                        ">

                </div>


                <input
                    type="file"
                    id="inputCambiarImagen"
                    class="form-control"
                    accept="image/jpeg,image/png,image/webp">


                <div class="form-text">

                    JPG, JPEG, PNG o WEBP.
                    Tamaño máximo: 2.7 MB.

                </div>


                <div
                    id="alertaCambiarImagen"
                    class="alert d-none mt-3 mb-0"
                    role="alert">
                </div>

            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    Cancelar

                </button>

                <button
                    type="button"
                    id="btnConfirmarCambiarImagen"
                    class="btn btn-success">

                    <i class="fa-solid fa-repeat me-1"></i>

                    Cambiar imagen

                </button>

            </div>

        </div>

    </div>

</div>
