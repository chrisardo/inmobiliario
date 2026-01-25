<!-- Modal Editar Perfil -->
<div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="../controladores/actualizar_perfil.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="editarPerfilModalLabel">Editar Perfil</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="usId" value="<?= $usuario['id_user'] ?>">
                    <!-- Imagen -->
                    <div class="mb-2">
                        <div class="card border-0 shadow-sm">
                            <!-- Input -->
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white">
                                    <i class="bi bi-image"></i>
                                </span>
                                <input
                                    type="file"
                                    name="imagen"
                                    id="edit-imagen"
                                    class="form-control"
                                    accept="image/png, image/jpeg">
                            </div>

                            <div class="form-text">
                                Formatos permitidos: JPG, PNG · Tamaño máximo: 1.8 MB
                            </div>
                            <div class="card-body p-2">
                                <!-- Vista previa -->
                                <div id="previewImagen" class="mt-0 d-none">
                                    <div class="row align-items-center g-3">

                                        <!-- Imagen -->
                                        <div class="col-auto">
                                            <div class="border rounded p-2 bg-light">
                                                <img
                                                    id="previewImg"
                                                    class="img-fluid rounded"
                                                    style="width: 70px; height: 60px; object-fit: cover;">
                                            </div>
                                        </div>

                                        <!-- Detalles -->
                                        <div class="col">
                                            <ul class="list-group list-group-flush small">
                                                <!--<li class="list-group-item px-0">
                                                        <i class="bi bi-file-earmark-text text-success me-2"></i>
                                                        <strong>Nombre:</strong>
                                                        <span id="imgNombre"></span>
                                                    </li>-->
                                                <li class="list-group-item px-0">
                                                    <i class="bi bi-aspect-ratio text-info me-2"></i>
                                                    <strong>Tipo:</strong>
                                                    <span id="imgTipo"></span>
                                                </li>
                                                <li class="list-group-item px-0">
                                                    <i class="bi bi-hdd text-warning me-2"></i>
                                                    <strong>Tamaño:</strong>
                                                    <span id="imgSize"></span>
                                                </li>
                                            </ul>
                                        </div>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nombreEmpresa" class="form-label">Nombre Empresa</label>
                        <input type="text" class="form-control" id="nombreEmpresa" name="nombreEmpresa" value="<?= htmlspecialchars($usuario['nombreEmpresa']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="number" min="0" class="form-control" id="celular" name="celular" value="<?= htmlspecialchars($usuario['celular']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?= htmlspecialchars($usuario['direccion']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Breve descripcion acerca de la empresa</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"><?= htmlspecialchars($usuario['descripcion_acerca']) ?></textarea>
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