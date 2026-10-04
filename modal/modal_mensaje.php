<!-- =========================================================
     MODAL VER DETALLES DEL MENSAJE
========================================================= -->

<div class="modal fade"
    id="modalVerDetalles"
    tabindex="-1"
    aria-labelledby="modalVerDetallesLabel"
    aria-hidden="true">


    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content message-modal">

            <!-- =================================================
             HEADER
        ================================================== -->

            <div class="modal-header message-modal-header">

                <div class="message-modal-title">

                    <div class="message-modal-icon">
                        <i class="fa-solid fa-envelope-open-text"></i>
                    </div>

                    <div>
                        <span class="message-modal-eyebrow">
                            GESTIÓN DE MENSAJES
                        </span>

                        <h5 class="modal-title" id="modalVerDetallesLabel">
                            Detalle del Mensaje
                        </h5>
                    </div>

                </div>

                <button type="button"
                    class="btn-close btn-close-white"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>


            <!-- =================================================
             BODY
        ================================================== -->

            <div class="modal-body message-modal-body">


                <!-- =================================================
                 DATOS DEL CLIENTE
            ================================================== -->

                <section class="message-detail-card">

                    <div class="message-detail-header">

                        <div class="message-section-icon client">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>
                            <span class="message-section-label">
                                INFORMACIÓN
                            </span>

                            <h6>
                                Datos del Cliente
                            </h6>
                        </div>

                    </div>


                    <div class="message-detail-grid">

                        <div class="message-detail-item">

                            <span class="message-detail-label">
                                <i class="fa-solid fa-user"></i>
                                Nombre
                            </span>

                            <strong id="verNombre">
                                No especificado
                            </strong>

                        </div>


                        <div class="message-detail-item">

                            <span class="message-detail-label">
                                <i class="fa-solid fa-id-card"></i>
                                Apellidos
                            </span>

                            <strong id="verApellidos">
                                No especificados
                            </strong>

                        </div>


                        <div class="message-detail-item">

                            <span class="message-detail-label">
                                <i class="fa-solid fa-envelope"></i>
                                Correo electrónico
                            </span>

                            <strong id="verEmail">
                                No especificado
                            </strong>

                        </div>


                        <div class="message-detail-item">

                            <span class="message-detail-label">
                                <i class="fa-solid fa-phone"></i>
                                Celular
                            </span>

                            <strong id="verCelular">
                                No especificado
                            </strong>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                 INFORMACIÓN DEL MENSAJE
            ================================================== -->

                <section class="message-detail-card">

                    <div class="message-detail-header">

                        <div class="message-section-icon property">
                            <i class="fa-solid fa-message"></i>
                        </div>

                        <div>
                            <span class="message-section-label">
                                SEGUIMIENTO
                            </span>

                            <h6>
                                Información del Mensaje
                            </h6>
                        </div>

                    </div>


                    <div class="message-detail-grid">


                        <!-- PROPIEDAD -->

                        <div class="message-detail-item">

                            <span class="message-detail-label">
                                <i class="fa-solid fa-house"></i>
                                Propiedad
                            </span>

                            <strong id="verPropiedad">
                                No especificada
                            </strong>

                        </div>


                        <!-- FECHA RECIBIDO -->

                        <div class="message-detail-item">

                            <span class="message-detail-label">
                                <i class="fa-solid fa-calendar-plus"></i>
                                Fecha recibido
                            </span>

                            <strong id="verFecha">
                                No especificada
                            </strong>

                        </div>

                        <!-- FECHA LEÍDO -->

                        <div class="message-detail-item">

                            <span class="message-detail-label">
                                <i class="fa-solid fa-eye"></i>
                                Fecha de lectura
                            </span>

                            <strong id="verFechaLeido"
                                class="message-read-date">
                                No leído
                            </strong>

                        </div>

                    </div>

                </section>


                <!-- =================================================
                 MENSAJE DEL CLIENTE
            ================================================== -->

                <section class="message-content-card">

                    <div class="message-content-header">

                        <div class="message-content-icon">
                            <i class="fa-solid fa-comment-dots"></i>
                        </div>

                        <div>
                            <span>
                                MENSAJE RECIBIDO
                            </span>

                            <h6>
                                Mensaje del Cliente
                            </h6>
                        </div>

                    </div>


                    <div class="message-content-body">

                        <div class="message-quote-icon">
                            <i class="fa-solid fa-quote-left"></i>
                        </div>

                        <p id="verMensaje">
                            El cliente no escribió un mensaje.
                        </p>

                    </div>

                </section>

            </div>


            <!-- =================================================
             FOOTER
        ================================================== -->

            <div class="modal-footer message-modal-footer">

                <button type="button"
                    class="btn message-btn-close"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-xmark"></i>
                    Cerrar

                </button>


                <a href="#"
                    id="btnCorreo"
                    class="btn message-btn-email">

                    <i class="fa-solid fa-envelope"></i>
                    Correo

                </a>


                <a href="#"
                    target="_blank"
                    rel="noopener noreferrer"
                    id="btnWhatsapp"
                    class="btn message-btn-whatsapp">

                    <i class="fa-brands fa-whatsapp"></i>
                    WhatsApp

                </a>

            </div>

        </div>

    </div>
</div>