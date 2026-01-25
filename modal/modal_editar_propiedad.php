    <?php
    if (!empty($mensaje)) {
        echo "<script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('modalEditar'));
            modal.show();
            const alert = document.getElementById('alertaPropiedad');
            alert.classList.remove('d-none');
            alert.classList.add('alert-danger');
            alert.innerHTML = " . json_encode($mensaje) . ";
        });
    </script>";
    }
    ?>

    <!-- Toda esta parte es modal/modal_editar_propiedad.php -->
    <div class="modal fade" id="modalEditar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-success">
                        <h5 class="modal-title text-white">Editar Propiedad</h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="alertaPropiedad" class="alert d-none mt-3"></div>

                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_propiedad" id="edit-id">
                        <!--Imagen-->
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
                        <!--Nombre y documento-->
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="form-label">Nombre</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="bi bi-house-fill"></i>
                                    </span>
                                    <input type="text" name="nombre" id="edit-nombre" class="form-control" required>
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label">Codigo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="bi bi-upc-scan"></i>
                                    </span>
                                    <input type="text" name="codigo" id="edit-codigo" class="form-control" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="form-label">Tamaño del area</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="bi bi-arrows-fullscreen"></i>
                                    </span>
                                    <input
                                        type="number"
                                        step="any"
                                        min="0"
                                        name="tamano_area"
                                        id="edit-tamano-area-metros"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label">Precio a vender</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="bi bi-currency-dollar"></i>
                                    </span>
                                    <input type="number" step="any" min="0" name="precio" id="edit-precio" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- opciones de rubro + opciones de departamento -->
                        <div class="row g-2 mb-3">
                            <div class="col">
                                <label class="form-label">Categoria</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white"> <i class="fas fa-th-large"></i></span>
                                    <!--<input type="text" name="rubro" class="form-control" id="edit-rubro">-->
                                    <!--poner un select mostrando el rubro seleccionado y luego mostrar los rubros de la base de datos-->
                                    <select name="categoria" id="edit-categoria" class="form-select" required>
                                        <option value="">Seleccione categoría</option>
                                        <?php
                                        $resultadoCatego = $conexion->query("SELECT id_categoria, nombre, id_user FROM categoria where Eliminado = 0 AND id_user=" . intval($_SESSION['usId']) . " ");
                                        while ($cate = $resultadoCatego->fetch_assoc()) {
                                            echo '<option value="' . $cate['id_categoria'] . '">'
                                                . htmlspecialchars($cate['nombre']) .
                                                '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col">
                                <label class="form-label">Ubicación / Zona</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-success text-white">
                                        <i class="bi bi-geo-alt-fill"></i>
                                    </span>
                                    <input type="text" id="edit-ubicacion" name="ubicacion" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <!--<a type="submit" class="btn btn-primary" id="btnGuardarCambios">Guardar cambios</a>-->
                    </div>
                </form>
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-<?php echo $tipoAlerta; ?> mt-3">
                        <?php echo $mensaje; ?>
                    </div>
                <?php endif; ?>
                <div id="mensajeActualizacion" class="mt-2"></div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminar -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas eliminar este propiedad?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="#" class="btn btn-danger" id="btnConfirmarEliminar">Eliminar</a>
                </div>
            </div>
        </div>
    </div>