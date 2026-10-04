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
<!-- =========================================================
     MODAL EDITAR PROPIEDAD
========================================================= -->

<div
    class="modal fade"
    id="modalEditar"
    tabindex="-1"
    aria-labelledby="modalEditarLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <form
                method="POST"
                enctype="multipart/form-data">

                <!-- =================================================
                     HEADER
                ================================================== -->

                <div class="modal-header bg-success">

                    <h5
                        class="modal-title text-white"
                        id="modalEditarLabel">

                        <i class="fa-solid fa-pen-to-square me-2"></i>

                        Editar Propiedad

                    </h5>

                    <button
                        type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar">
                    </button>

                </div>


                <!-- =================================================
                     BODY
                ================================================== -->

                <div class="modal-body">

                    <!-- ALERTA -->

                    <div
                        id="alertaPropiedad"
                        class="alert d-none"
                        role="alert">
                    </div>


                    <!-- CAMPOS OCULTOS -->

                    <input
                        type="hidden"
                        name="accion"
                        value="editar">

                    <input
                        type="hidden"
                        name="id_propiedad"
                        id="edit-id">


                    <!-- =================================================
                         NOMBRE / CÓDIGO
                    ================================================== -->

                    <div class="row g-3 mb-3">

                        <!-- Nombre -->

                        <div class="col-md-8">

                            <label
                                for="edit-nombre"
                                class="form-label">

                                Nombre de la propiedad

                            </label>

                            <div class="input-group">

                                <span
                                    class="input-group-text bg-success text-white">

                                    <i class="bi bi-house-fill"></i>

                                </span>

                                <input
                                    type="text"
                                    name="nombre"
                                    id="edit-nombre"
                                    class="form-control"
                                    required>

                            </div>

                        </div>


                        <!-- Código -->

                        <div class="col-md-4">

                            <label
                                for="edit-codigo"
                                class="form-label">

                                Código

                            </label>

                            <div class="input-group">

                                <span
                                    class="input-group-text bg-success text-white">

                                    <i class="bi bi-upc-scan"></i>

                                </span>

                                <input
                                    type="text"
                                    name="codigo"
                                    id="edit-codigo"
                                    class="form-control"
                                    required>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         ÁREA / CATEGORÍA
                    ================================================== -->

                    <div class="row g-3 mb-3">

                        <!-- Área -->

                        <div class="col-md-6">

                            <label
                                for="edit-tamano-area-metros"
                                class="form-label">

                                Tamaño del área

                            </label>

                            <div class="input-group">

                                <span
                                    class="input-group-text bg-success text-white">

                                    <i class="bi bi-arrows-fullscreen"></i>

                                </span>

                                <input
                                    type="number"
                                    step="any"
                                    min="0"
                                    name="tamano_area"
                                    id="edit-tamano-area-metros"
                                    class="form-control"
                                    required>

                                <span class="input-group-text">
                                    m²
                                </span>

                            </div>

                        </div>


                        <!-- Categoría -->

                        <div class="col-md-6">

                            <label
                                for="edit-categoria"
                                class="form-label">

                                Categoría

                            </label>

                            <div class="input-group">

                                <span
                                    class="input-group-text bg-success text-white">

                                    <i class="fas fa-th-large"></i>

                                </span>

                                <select
                                    name="categoria"
                                    id="edit-categoria"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Seleccione categoría
                                    </option>

                                    <?php

                                    $resultadoCatego = $conexion->query("
                                        SELECT
                                            id_categoria,
                                            nombre
                                        FROM categoria
                                        WHERE Eliminado = 0
                                          AND id_user = " . intval($_SESSION['usId']) . "
                                        ORDER BY nombre ASC
                                    ");

                                    if ($resultadoCatego):

                                        while ($cate = $resultadoCatego->fetch_assoc()):

                                    ?>

                                        <option
                                            value="<?= (int) $cate['id_categoria'] ?>">

                                            <?= htmlspecialchars(
                                                $cate['nombre'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>

                                        </option>

                                    <?php

                                        endwhile;

                                    endif;

                                    ?>

                                </select>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         PRECIO ANTERIOR / PRECIO ACTUAL
                    ================================================== -->

                    <div class="row g-3 mb-3">

                        <!-- Precio anterior -->

                        <div class="col-md-6">

                            <label
                                for="edit-precio-anterior"
                                class="form-label">

                                Precio anterior

                            </label>

                            <div class="input-group">

                                <span
                                    class="input-group-text bg-secondary text-white">

                                    <i class="bi bi-tag"></i>

                                </span>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="precio_anterior"
                                    id="edit-precio-anterior"
                                    class="form-control"
                                    placeholder="0.00">

                            </div>

                            <small class="text-muted">
                                Precio anterior de la propiedad.
                            </small>

                        </div>


                        <!-- Precio actual -->

                        <div class="col-md-6">

                            <label
                                for="edit-precio"
                                class="form-label">

                                Precio de venta

                            </label>

                            <div class="input-group">

                                <span
                                    class="input-group-text bg-success text-white">

                                    <i class="bi bi-currency-dollar"></i>

                                </span>

                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="precio"
                                    id="edit-precio"
                                    class="form-control"
                                    required
                                    placeholder="0.00">

                            </div>

                            <small class="text-muted">
                                Precio actual de venta.
                            </small>

                        </div>

                    </div>


                    <!-- =================================================
                         UBICACIÓN
                    ================================================== -->

                    <div class="mb-3">

                        <label
                            for="edit-ubicacion"
                            class="form-label">

                            Ubicación / Zona

                        </label>

                        <div class="input-group">

                            <span
                                class="input-group-text bg-success text-white">

                                <i class="bi bi-geo-alt-fill"></i>

                            </span>

                            <input
                                type="text"
                                id="edit-ubicacion"
                                name="ubicacion"
                                class="form-control"
                                required>

                        </div>

                    </div>


                    <!-- =================================================
                         INFORMACIÓN DE FECHAS
                    ================================================== -->

                    <div class="card border-0 bg-light mt-4">

                        <div class="card-body py-3">

                            <div class="row g-3">

                                <!-- Fecha de registro -->

                                <div class="col-md-6">

                                    <div class="d-flex align-items-center">

                                        <div
                                            class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center me-3"
                                            style="width: 42px; height: 42px;">

                                            <i class="fa-solid fa-calendar-plus"></i>

                                        </div>

                                        <div>

                                            <small
                                                class="text-muted d-block">

                                                Fecha de registro

                                            </small>

                                            <strong
                                                id="edit-fecha-registro"
                                                class="text-dark">

                                                —

                                            </strong>

                                        </div>

                                    </div>

                                </div>


                                <!-- Fecha de actualización -->

                                <div class="col-md-6">

                                    <div class="d-flex align-items-center">

                                        <div
                                            class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center me-3"
                                            style="width: 42px; height: 42px;">

                                            <i class="fa-solid fa-calendar-check"></i>

                                        </div>

                                        <div>

                                            <small
                                                class="text-muted d-block">

                                                Última actualización

                                            </small>

                                            <strong
                                                id="edit-fecha-actualizacion"
                                                class="text-dark">

                                                —

                                            </strong>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

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

                        <i class="fa-solid fa-xmark me-1"></i>

                        Cancelar

                    </button>


                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="fa-solid fa-floppy-disk me-1"></i>

                        Guardar cambios

                    </button>

                </div>

            </form>


            <?php if (!empty($mensaje)): ?>

                <div
                    class="alert alert-<?= htmlspecialchars(
                        $tipoAlerta,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?> mx-3 mb-3">

                    <?= htmlspecialchars(
                        $mensaje,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>

                </div>

            <?php endif; ?>


            <div
                id="mensajeActualizacion"
                class="mt-2">
            </div>

        </div>

    </div>

</div>

    <!-- Modal de confirmación de eliminar -->
    <!-- =========================================================
     MODAL ELIMINAR PROPIEDAD
========================================================= -->

    <div
        class="modal fade"
        id="modalEliminar"
        tabindex="-1"
        aria-labelledby="modalEliminarLabel"
        aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">

                <form
                    method="POST"
                    action="../controladores/eliminar_propiedad.php"
                    id="formEliminarPropiedad">

                    <!-- HEADER -->

                    <div class="modal-header bg-danger text-white">

                        <h5
                            class="modal-title"
                            id="modalEliminarLabel">

                            <i class="fa-solid fa-triangle-exclamation me-2"></i>

                            Confirmar eliminación

                        </h5>

                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar">
                        </button>

                    </div>


                    <!-- BODY -->

                    <div class="modal-body">

                        <input
                            type="hidden"
                            name="accion"
                            value="eliminar">

                        <input
                            type="hidden"
                            name="id_propiedad"
                            id="eliminar-id"
                            value="">


                        <div class="text-center mb-3">

                            <div
                                class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-danger bg-opacity-10"
                                style="width: 70px; height: 70px;">

                                <i
                                    class="fa-solid fa-trash-can text-danger"
                                    style="font-size: 30px;">
                                </i>

                            </div>

                        </div>


                        <p class="text-center mb-2">

                            ¿Estás seguro de que deseas eliminar esta propiedad?

                        </p>


                        <div
                            class="alert alert-warning text-center mb-0">

                            <i class="fa-solid fa-building me-2"></i>

                            <strong id="eliminar-nombre">
                                Propiedad
                            </strong>

                            <br>

                            <small>
                                La propiedad dejará de aparecer en el listado.
                            </small>

                        </div>
                    </div>


                    <!-- FOOTER -->

                    <div class="modal-footer">

                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">

                            <i class="fa-solid fa-xmark me-1"></i>

                            Cancelar

                        </button>


                        <button
                            type="submit"
                            class="btn btn-danger"
                            id="btnConfirmarEliminar">

                            <i class="fa-solid fa-trash me-1"></i>

                            Eliminar propiedad

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>