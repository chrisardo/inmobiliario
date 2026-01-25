<!-- Modal de confirmación de eliminar -->
<div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</a>
            </div>
        </div>
    </div>
</div>
<!-- Modal Ver Detalles del Mensaje -->
<div class="modal fade" id="modalVerDetalles" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">

            <!-- Header -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title d-flex align-items-center gap-2">
                    <i class="fas fa-envelope-open-text"></i>
                    Detalle del Mensaje
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Datos del Cliente -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        <i class="fas fa-user me-2 text-success"></i>
                        Datos del Cliente
                    </div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <i class="fas fa-id-card text-success me-2"></i>
                            <strong>Nombre:</strong>
                            <div id="verNombre" class="text-muted"></div>
                        </div>

                        <div class="col-md-6">
                            <i class="fas fa-id-card-clip text-success me-2"></i>
                            <strong>Apellidos:</strong>
                            <div id="verApellidos" class="text-muted"></div>
                        </div>

                        <div class="col-md-6">
                            <i class="fas fa-envelope text-success me-2"></i>
                            <strong>Email:</strong>
                            <div id="verEmail" class="text-muted"></div>
                        </div>

                        <div class="col-md-6">
                            <i class="fas fa-phone text-success me-2"></i>
                            <strong>Celular:</strong>
                            <div id="verCelular" class="text-muted"></div>
                        </div>

                    </div>
                </div>

                <!-- Datos del Mensaje -->
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-light fw-bold">
                        <i class="fas fa-message me-2 text-success"></i>
                        Información del Mensaje
                    </div>

                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <i class="fas fa-house text-success me-2"></i>
                            <strong>Propiedad:</strong>
                            <div id="verPropiedad" class="text-muted"></div>
                        </div>

                        <div class="col-md-6">
                            <i class="fas fa-calendar text-success me-2"></i>
                            <strong>Fecha:</strong>
                            <div id="verFecha" class="text-muted"></div>
                        </div>
                    </div>
                </div>

                <!-- Mensaje -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white fw-bold">
                        <i class="fas fa-comment-dots me-2"></i>
                        Mensaje del Cliente
                    </div>
                    <div class="card-body bg-light rounded">
                        <p id="verMensaje" class="mb-0" style="white-space: pre-line;"></p>
                    </div>
                </div>

            </div>

            <!-- Footer -->
            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cerrar
                </button>
                <a
                    href="#"
                    target="_blank"
                    id="btnWhatsapp"
                    class="btn btn-success">
                    <i class="fab fa-whatsapp me-1"></i>
                    Responder por WhatsApp
                </a>
            </div>

        </div>
    </div>
</div>