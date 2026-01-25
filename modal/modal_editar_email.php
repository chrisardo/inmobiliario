<!--moda de editar perfi.-->
<div class="modal fade" id="editarEmailModal" tabindex="-1" aria-labelledby="editarEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditarEmail">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="editarEmailModalLabel">Editar Email</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="usId" value="<?= $usuario['id_user'] ?>">
                    <div class="mb-3">
                        <label for="emailActual" class="form-label">Email actual:</label>
                        <span
                            id="emailActual"
                            class="email-text"
                            name="emailActual"
                            data-email="<?= htmlspecialchars($usuario['email']) ?>">
                            ••••••••••••
                        </span>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-success flex-shrink-0"
                            onclick="toggleEmail2('emailActual', 'emailIcon')"
                            title="Mostrar / Ocultar email">
                            <i id="emailIcon" class="fa-solid fa-eye"></i>
                        </button>

                        <input type="hidden" class="form-control" id="emailActual" name="emailActual"    value="<?= htmlspecialchars($usuario['email']) ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="emailNuevo" class="form-label">Email Nuevo:</label>
                        <input type="email" class="form-control" id="emailNuevo" name="emailNuevo" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Guardar cambios</button>
                </div>
                <div id="emailMessages"></div>
            </form>
        </div>
    </div>
</div>