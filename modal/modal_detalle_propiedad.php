<?php
//======================================================
// CoDevPro Technology
// Archivo: modal/modal_detalle_propiedad.php
// Módulo: Propiedades
// Sistema: Inmobiliario
//======================================================
?>

<!--======================================================
    MODAL DETALLE DE PROPIEDAD
=======================================================-->

<div
    class="modal fade"
    id="modalDetallePropiedad"
    tabindex="-1"
    aria-labelledby="modalDetallePropiedadLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content property-detail-modal">


            <!--==================================================
                CABECERA
            ===================================================-->

            <div class="modal-header property-detail-header">

                <div class="property-detail-header-info">

                    <div class="property-detail-header-icon">

                        <i class="bi bi-house-door-fill"></i>

                    </div>

                    <div>

                        <div class="property-detail-header-label">
                            DETALLE DE PROPIEDAD
                        </div>

                        <small
                            id="detalleCodigo"
                            class="property-detail-code">

                            Código: --

                        </small>

                    </div>

                </div>


                <button
                    type="button"
                    class="property-detail-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">

                    <i class="bi bi-x-lg"></i>

                </button>

            </div>


            <!--==================================================
                CUERPO
            ===================================================-->

            <div class="modal-body p-0">

                <div class="row g-0">


                    <!--================================================
                        GALERÍA
                    =================================================-->

                    <div class="col-12 col-lg-7">

                        <div class="property-detail-gallery">


                            <!--========================================
                                IMAGEN PRINCIPAL
                            =========================================-->

                            <div class="property-detail-image-container">


                                <!-- SUPERIOR IZQUIERDO -->

                                <div class="property-detail-gallery-badge">

                                    <i class="bi bi-images"></i>

                                    <span>
                                        Galería
                                    </span>

                                </div>


                                <!-- CONTADOR -->

                                <div
                                    id="detalleImagenContador"
                                    class="property-detail-image-counter d-none">

                                    <i class="bi bi-images"></i>

                                    <span id="detalleImagenNumero">
                                        1
                                    </span>

                                    <span class="property-detail-counter-separator">
                                        /
                                    </span>

                                    <span id="detalleImagenTotal">
                                        1
                                    </span>

                                </div>


                                <!-- BOTÓN ANTERIOR -->

                                <button
                                    type="button"
                                    id="detalleImagenAnterior"
                                    class="property-detail-gallery-control property-detail-gallery-prev d-none"
                                    aria-label="Imagen anterior">

                                    <i class="bi bi-chevron-left"></i>

                                </button>


                                <!-- IMAGEN -->

                                <img
                                    id="detalleImagen"
                                    src=""
                                    alt="Imagen de la propiedad"
                                    class="property-detail-image">


                                <!-- BOTÓN SIGUIENTE -->

                                <button
                                    type="button"
                                    id="detalleImagenSiguiente"
                                    class="property-detail-gallery-control property-detail-gallery-next d-none"
                                    aria-label="Imagen siguiente">

                                    <i class="bi bi-chevron-right"></i>

                                </button>


                                <!-- SIN IMAGEN -->

                                <div
                                    id="detalleSinImagen"
                                    class="property-detail-no-image d-none">

                                    <div class="property-detail-no-image-icon">

                                        <i class="bi bi-image"></i>

                                    </div>

                                    <strong>
                                        Sin imágenes
                                    </strong>

                                    <span>
                                        Esta propiedad no tiene imágenes disponibles.
                                    </span>

                                </div>


                                <!-- PRECIO -->

                                <div class="property-detail-image-price">

                                    <span>
                                        Precio de venta
                                    </span>

                                    <strong id="detallePrecio">
                                        S/. 0.00
                                    </strong>

                                </div>

                            </div>


                            <!--========================================
                                MINIATURAS
                            =========================================-->

                            <div class="property-detail-thumbnails-wrapper">

                                <div
                                    id="detalleMiniaturas"
                                    class="property-detail-thumbnails d-none"
                                    aria-label="Imágenes de la propiedad">
                                </div>

                            </div>


                            <!--========================================
                                INDICADOR
                            =========================================-->

                            <div class="property-detail-gallery-hint">

                                <i class="bi bi-hand-index-thumb"></i>

                                <span>
                                    Explora las imágenes de la propiedad
                                </span>

                            </div>

                        </div>

                    </div>


                    <!--================================================
                        INFORMACIÓN
                    =================================================-->

                    <div class="col-12 col-lg-5">

                        <div class="property-detail-content">


                            <!--========================================
                                CATEGORÍA
                            =========================================-->

                            <div class="property-detail-category-wrapper">

                                <span
                                    id="detalleCategoria"
                                    class="property-detail-category">

                                    <i class="bi bi-tag-fill"></i>

                                    Propiedad

                                </span>

                            </div>


                            <!--========================================
                                NOMBRE
                            =========================================-->

                            <h3
                                id="detalleNombre"
                                class="property-detail-title">

                                Propiedad

                            </h3>


                            <!--========================================
                                UBICACIÓN
                            =========================================-->

                            <div class="property-detail-location-card">

                                <div class="property-detail-info-icon">

                                    <i class="bi bi-geo-alt-fill"></i>

                                </div>

                                <div class="property-detail-info-text">

                                    <span>
                                        Ubicación
                                    </span>

                                    <strong id="detalleUbicacion">
                                        No disponible
                                    </strong>

                                </div>

                            </div>


                            <!--========================================
                                CARACTERÍSTICAS
                            =========================================-->

                            <div class="property-detail-section-title">

                                <span>
                                    Características
                                </span>

                                <div></div>

                            </div>


                            <div class="property-detail-features">


                                <!-- ÁREA -->

                                <div class="property-detail-feature">

                                    <div class="property-detail-feature-icon">

                                        <i class="bi bi-arrows-fullscreen"></i>

                                    </div>

                                    <div class="property-detail-feature-info">

                                        <span>
                                            Área
                                        </span>

                                        <strong id="detalleArea">
                                            No disponible
                                        </strong>

                                    </div>

                                </div>


                                <!-- TIPO -->

                                <div class="property-detail-feature">

                                    <div class="property-detail-feature-icon">

                                        <i class="bi bi-building"></i>

                                    </div>

                                    <div class="property-detail-feature-info">

                                        <span>
                                            Tipo
                                        </span>

                                        <strong id="detalleTipo">
                                            Propiedad
                                        </strong>

                                    </div>

                                </div>

                            </div>


                            <!--========================================
                                PRECIO
                            =========================================-->

                            <div class="property-detail-price-box">

                                <div class="property-detail-current-price">

                                    <span>
                                        Precio de venta
                                    </span>

                                    <strong id="detallePrecioGrande">
                                        S/. 0.00
                                    </strong>

                                </div>


                                <div
                                    id="detallePrecioAnteriorBox"
                                    class="property-detail-old-price d-none">

                                    <span>
                                        Precio anterior
                                    </span>

                                    <del id="detallePrecioAnterior">
                                        S/. 0.00
                                    </del>

                                </div>

                            </div>


                            <!--========================================
                                SEPARADOR
                            =========================================-->

                            <div class="property-detail-divider"></div>


                            <!--========================================
                                CONTACTO
                            =========================================-->

                            <div class="property-detail-contact-title">

                                <div class="property-detail-contact-icon">

                                    <i class="bi bi-chat-dots-fill"></i>

                                </div>

                                <div>

                                    <strong>
                                        ¿Te interesa esta propiedad?
                                    </strong>

                                    <span>
                                        Comunícate con nosotros para recibir más información.
                                    </span>

                                </div>

                            </div>


                            <!--========================================
                                BOTONES
                            =========================================-->

                            <div class="property-detail-actions">


                                <!-- WHATSAPP -->

                                <a
                                    href="#"
                                    id="detalleWhatsapp"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="btn property-detail-whatsapp">

                                    <i class="bi bi-whatsapp"></i>

                                    <span>
                                        Solicitar información
                                    </span>

                                    <i class="bi bi-arrow-up-right ms-auto"></i>

                                </a>


                                <!-- CERRAR -->

                                <button
                                    type="button"
                                    class="btn property-detail-close-button"
                                    data-bs-dismiss="modal">

                                    <i class="bi bi-x-lg"></i>

                                    Cerrar

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

