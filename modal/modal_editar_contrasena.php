<!--Todo esto es modal/modal_editar_contrasena.php-->
<div class="modal fade" id="editarContrasenaModal" tabindex="-1" aria-labelledby="editarContrasenaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditarContrasena">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="editarContrasenaModalLabel">Cambiar Contraseña</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="usId" value="<?= $usuario['id_user'] ?>">

                    <div class="mb-3">
                        <label for="contrasenaActual" class="form-label">Contraseña actual:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="contrasenaActual" name="contrasenaActual" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contrasenaNueva" class="form-label">Contraseña nueva:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="contrasenaNueva" name="contrasenaNueva" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword3">
                                👁️
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contrasenaConfirmar" class="form-label">Confirmar contraseña nueva:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="contrasenaConfirmar" name="contrasenaConfirmar" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword2">
                                👁️
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                </div>
                <div id="contrasenaMessages"></div>
            </form>

        </div>
    </div>
</div>