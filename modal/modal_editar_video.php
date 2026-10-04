<!-- Modal Editar Video -->
<div class="modal fade" id="editarVideoModal" tabindex="-1" aria-labelledby="editarVideoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="../controladores/actualizar_video.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="editarVideoModalLabel">Editar Video</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="usId" value="<?= $usuario['id_user'] ?>">
                    <!-- Video -->
                    <div class="mb-2">
                        <div class="card border-0 shadow-sm">
                            <!-- Input -->
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white">
                                    <i class="bi bi-video"></i>
                                </span>
                                <input type="file"
                                    name="video"
                                    id="edit-video"
                                    class="form-control"
                                    accept="video/mp4">
                            </div>

                            <div class="form-text">
                                Formatos permitidos: MP3, MP4 · Tamaño máximo: 5.8 MB
                            </div>
                            <div class="card-body p-2">
                                <!-- Vista previa -->
                                <div id="previewImagen" class="mt-2 d-none">
                                    <div class="row align-items-center g-3">

                                        <!-- Video -->
                                        <div class="col-auto">
                                            <video id="previewVideo"
                                                controls
                                                style="width: 120px; height: 80px; object-fit: cover;"
                                                class="rounded border"></video>
                                        </div>

                                        <!-- Detalles -->
                                        <div class="col">
                                            <ul class="list-group list-group-flush small">
                                                <li class="list-group-item px-0">
                                                    <strong>Nombre:</strong>
                                                    <span id="videoNombre"></span>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <strong>Tipo:</strong>
                                                    <span id="videoTipo"></span>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <strong>Tamaño:</strong>
                                                    <span id="videoSize"></span>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <strong>Duración:</strong>
                                                    <span id="videoDuracion"></span>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                </div>
            </form>
            <!--mostrar div mensaje-->
            <div id="alertPerfil" class="alert d-none" role="alert"></div>
        </div>
    </div>
</div>