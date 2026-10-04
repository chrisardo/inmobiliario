<?php
// =========================================================
// CoDevPro Technology
// Archivo: modal/modal_ver_asesor.php
// Módulo: Lista de Asesores
// Sistema: Inmobiliario
// =========================================================
?>

<!-- =========================================================
     MODAL VER DETALLE DEL ASESOR
========================================================== -->

<div
    class="modal fade"
    id="modalVerAsesor"
    tabindex="-1"
    aria-labelledby="modalVerAsesorLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content modal-asesor-detalle">


            <!-- =================================================
                 HEADER
            ================================================== -->

            <div class="modal-header modal-asesor-detalle-header">

                <div class="modal-title-wrapper">

                    <div class="modal-title-icon">

                        <i class="fa-solid fa-user-tie"></i>

                    </div>

                    <div>

                        <h5
                            class="modal-title-eyebrow"
                            id="modalVerAsesorLabel">
                            Detalle del asesor


                        </h5>

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


                <!-- =================================================
                     PERFIL
                ================================================== -->

                <div class="asesor-detalle-profile">


                    <!-- FOTO -->

                    <div
                        class="asesor-detalle-avatar-wrapper"
                        id="detalleAvatarWrapper">

                        <div
                            class="asesor-detalle-avatar asesor-avatar-placeholder"
                            id="detalleAvatarPlaceholder">

                            AS

                        </div>

                        <img
                            src=""
                            alt="Foto del asesor"
                            class="asesor-detalle-avatar asesor-detalle-avatar-img d-none"
                            id="detalleImagen">

                    </div>


                    <!-- NOMBRE -->

                    <div class="asesor-detalle-profile-info">

                        <span class="asesor-detalle-label">
                            ASESOR
                        </span>

                        <h3 id="detalleNombre">
                            Sin nombre
                        </h3>
                    </div>


                    <!-- ESTADO -->

                    <div
                        class="asesor-detalle-status"
                        id="detalleEstado">

                        <span class="status-dot"></span>

                        <span id="detalleEstadoTexto">
                            Activo
                        </span>

                    </div>

                </div>


                <!-- =================================================
                     INFORMACIÓN PERSONAL Y CONTACTO
                ================================================== -->

                <div class="asesor-detalle-section">

                    <div class="asesor-detalle-section-title">

                        <i class="fa-solid fa-address-card"></i>

                        Información del asesor

                    </div>


                    <div class="row g-3">


                        <!-- NOMBRE -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-item">

                                <span class="asesor-detalle-item-label">

                                    <i class="fa-solid fa-user"></i>

                                    Nombre

                                </span>

                                <strong id="detalleNombreCampo">
                                    —
                                </strong>

                            </div>

                        </div>


                        <!-- APELLIDOS -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-item">

                                <span class="asesor-detalle-item-label">

                                    <i class="fa-solid fa-users"></i>

                                    Apellidos

                                </span>

                                <strong id="detalleApellidos">
                                    —
                                </strong>

                            </div>

                        </div>


                        <!-- CARGO -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-item">

                                <span class="asesor-detalle-item-label">

                                    <i class="fa-solid fa-briefcase"></i>

                                    Cargo

                                </span>

                                <strong id="detalleCargo">
                                    —
                                </strong>

                            </div>

                        </div>


                        <!-- ID USUARIO -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-item">

                                <span class="asesor-detalle-item-label">

                                    <i class="fa-solid fa-user-shield"></i>

                                    Usuario propietario

                                </span>

                                <strong id="detalleIdUser">
                                    —
                                </strong>

                            </div>

                        </div>

                    </div>
                    <div class="row g-3">


                        <!-- EMAIL -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-item">

                                <span class="asesor-detalle-item-label">

                                    <i class="fa-solid fa-envelope"></i>

                                    Correo electrónico

                                </span>

                                <strong id="detalleEmail">
                                    —
                                </strong>

                            </div>

                        </div>


                        <!-- CELULAR -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-item">

                                <span class="asesor-detalle-item-label">

                                    <i class="fa-solid fa-phone"></i>

                                    Celular

                                </span>

                                <strong id="detalleCelular">
                                    —
                                </strong>

                            </div>

                        </div>

                    </div>
                </div>

                <!-- =================================================
                     FECHAS
                ================================================== -->

                <div class="asesor-detalle-section">

                    <div class="asesor-detalle-section-title">

                        <i class="fa-solid fa-calendar-days"></i>

                        Información del registro

                    </div>


                    <div class="row g-3">


                        <!-- FECHA REGISTRO -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-date-card">

                                <div class="asesor-detalle-date-icon">

                                    <i class="fa-solid fa-calendar-plus"></i>

                                </div>

                                <div>

                                    <span>
                                        Fecha de registro
                                    </span>

                                    <strong id="detalleFechaRegistro">
                                        —
                                    </strong>

                                </div>

                            </div>

                        </div>


                        <!-- FECHA ACTUALIZACIÓN -->

                        <div class="col-12 col-md-6">

                            <div class="asesor-detalle-date-card">

                                <div class="asesor-detalle-date-icon">

                                    <i class="fa-solid fa-calendar-check"></i>

                                </div>

                                <div>

                                    <span>
                                        Última actualización
                                    </span>

                                    <strong id="detalleFechaActualizacion">
                                        —
                                    </strong>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
     FOOTER
================================================== -->

            <div class="modal-footer modal-asesor-detalle-footer">
                <!-- ENVIAR WHATSAPP -->

                <a
                    href="#"
                    id="detalleWhatsapp"
                    class="btn btn-success"
                    target="_blank"
                    rel="noopener noreferrer"
                    aria-label="Enviar mensaje por WhatsApp">

                    <i class="fa-brands fa-whatsapp me-2"></i>

                    Enviar WhatsApp

                </a>


                <!-- CERRAR -->

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-xmark me-2"></i>

                    Cerrar

                </button>

            </div>


        </div>

    </div>

</div>