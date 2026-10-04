<?php
// =========================================================
// CoDevPro Technology
// Archivo: modal/modal_editar_asesor.php
// Módulo: Editar Asesor
// Sistema: Inmobiliario
// =========================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* =========================================================
   CSRF
========================================================= */

if (empty($_SESSION['csrf_asesores'])) {

    try {

        $_SESSION['csrf_asesores'] =
            bin2hex(random_bytes(32));
    } catch (Throwable $e) {

        $_SESSION['csrf_asesores'] =
            hash(
                'sha256',
                uniqid((string) mt_rand(), true)
            );
    }
}

$csrfAsesores =
    $_SESSION['csrf_asesores'];

?>

<!-- =====================================================
     MODAL EDITAR ASESOR
====================================================== -->

<div
    class="modal fade"
    id="modalEditar"
    tabindex="-1"
    aria-labelledby="modalEditarLabel"
    aria-hidden="true">

    <div
        class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content modal-asesor-content">

            <!-- =================================================
                 HEADER
            ================================================== -->

            <div class="modal-header modal-asesor-header">

                <div class="modal-title-wrapper">

                    <div class="modal-asesor-icon">

                        <i class="fa-solid fa-user-pen"></i>

                    </div>

                    <div>

                        <span class="modal-asesor-eyebrow">
                            GESTIÓN COMERCIAL
                        </span>

                        <h5
                            class="modal-title"
                            id="modalEditarLabel">

                            Editar asesor

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
                     ALERTA
                ================================================== -->

                <div
                    id="alertaAsesor"
                    class="alert d-none"
                    role="alert">
                </div>


                <!-- =================================================
                     FORMULARIO
                ================================================== -->

                <form
                    action="../controladores/editar_asesor.php"
                    method="POST"
                    enctype="multipart/form-data"
                    autocomplete="off"
                    novalidate>

                    <!-- =================================================
                         SEGURIDAD
                    ================================================== -->

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                                    $csrfAsesores,
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                    <input
                        type="hidden"
                        name="accion"
                        value="editar">

                    <input
                        type="hidden"
                        name="id_asesor"
                        id="edit-id"
                        value="">


                    <!-- =================================================
                         INFORMACIÓN PERSONAL
                    ================================================== -->

                    <div class="modal-form-section">

                        <div class="modal-form-section-title">

                            <span class="section-icon">

                                <i class="fa-solid fa-user"></i>

                            </span>

                            <div>

                                <strong>
                                    Información personal
                                </strong>

                                <small>
                                    Datos principales del asesor
                                </small>

                            </div>

                        </div>


                        <div class="row g-3">

                            <!-- =================================================
                                 NOMBRE
                            ================================================== -->

                            <div class="col-12 col-md-6">

                                <label
                                    for="edit-nombre"
                                    class="form-label">

                                    Nombre

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <div class="input-group asesor-input-group">

                                    <span class="input-group-text">

                                        <i class="fa-solid fa-user"></i>

                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="edit-nombre"
                                        name="nombre"
                                        maxlength="100"
                                        required
                                        placeholder="Ingrese el nombre">

                                </div>

                            </div>


                            <!-- =================================================
                                 APELLIDOS
                            ================================================== -->

                            <div class="col-12 col-md-6">

                                <label
                                    for="edit-apellidos"
                                    class="form-label">

                                    Apellidos

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <div class="input-group asesor-input-group">

                                    <span class="input-group-text">

                                        <i class="fa-solid fa-users"></i>

                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="edit-apellidos"
                                        name="apellidos"
                                        maxlength="150"
                                        required
                                        placeholder="Ingrese los apellidos">

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         CONTACTO
                    ================================================== -->

                    <div class="modal-form-section">

                        <div class="modal-form-section-title">

                            <span class="section-icon">

                                <i class="fa-solid fa-address-book"></i>

                            </span>

                            <div>

                                <strong>
                                    Información de contacto
                                </strong>

                                <small>
                                    Medios de comunicación del asesor
                                </small>

                            </div>

                        </div>


                        <div class="row g-3">

                            <!-- =================================================
                                 EMAIL
                            ================================================== -->

                            <div class="col-12 col-md-6">

                                <label
                                    for="edit-email"
                                    class="form-label">

                                    Correo electrónico

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <div class="input-group asesor-input-group">

                                    <span class="input-group-text">

                                        <i class="fa-solid fa-envelope"></i>

                                    </span>

                                    <input
                                        type="email"
                                        class="form-control"
                                        id="edit-email"
                                        name="email"
                                        maxlength="150"
                                        required
                                        placeholder="correo@ejemplo.com">

                                </div>

                            </div>


                            <!-- =================================================
                                 CELULAR
                            ================================================== -->

                            <div class="col-12 col-md-6">

                                <label
                                    for="edit-celular"
                                    class="form-label">

                                    Celular

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <div class="input-group asesor-input-group">

                                    <span class="input-group-text">

                                        <i class="fa-solid fa-phone"></i>

                                    </span>

                                    <input
                                        type="tel"
                                        class="form-control"
                                        id="edit-celular"
                                        name="celular"
                                        maxlength="20"
                                        required
                                        placeholder="999 999 999">

                                </div>

                                <div class="form-text">
                                    Puedes utilizar números, +, espacios,
                                    paréntesis y guiones.
                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         CARGO
                    ================================================== -->

                    <div class="modal-form-section">

                        <div class="modal-form-section-title">

                            <span class="section-icon">

                                <i class="fa-solid fa-briefcase"></i>

                            </span>

                            <div>

                                <strong>
                                    Información laboral
                                </strong>

                                <small>
                                    Cargo dentro del equipo comercial
                                </small>

                            </div>

                        </div>


                        <div class="row g-3">

                            <div class="col-12">

                                <label
                                    for="edit-cargo"
                                    class="form-label">

                                    Cargo

                                    <span class="text-danger">
                                        *
                                    </span>

                                </label>

                                <div class="input-group asesor-input-group">

                                    <span class="input-group-text">

                                        <i class="fa-solid fa-id-badge"></i>

                                    </span>

                                    <input
                                        type="text"
                                        class="form-control"
                                        id="edit-cargo"
                                        name="cargo"
                                        maxlength="100"
                                        required
                                        placeholder="Ej. Asesor inmobiliario">

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         IMAGEN
                    ================================================== -->

                    <div class="modal-form-section">

                        <div class="modal-form-section-title">

                            <span class="section-icon">

                                <i class="fa-solid fa-image"></i>

                            </span>

                            <div>

                                <strong>
                                    Foto del asesor
                                </strong>

                                <small>
                                    Selecciona una nueva imagen
                                    únicamente si deseas reemplazarla
                                </small>

                            </div>

                        </div>


                        <div class="asesor-image-upload">

                            <label
                                for="edit-imagen"
                                class="asesor-image-label">

                                <div class="asesor-image-upload-icon">

                                    <i class="fa-solid fa-cloud-arrow-up"></i>

                                </div>

                                <div class="asesor-image-upload-text">

                                    <strong>
                                        Seleccionar nueva imagen
                                    </strong>

                                    <span>
                                        JPG, JPEG, PNG o WEBP
                                    </span>

                                    <small>
                                        Tamaño máximo: 2.7 MB
                                    </small>

                                </div>

                            </label>


                            <input
                                type="file"
                                id="edit-imagen"
                                name="imagen"
                                class="d-none"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">


                            <!-- =================================================
                                 PREVIEW
                            ================================================== -->

                            <div
                                id="previewImagen"
                                class="asesor-image-preview d-none">

                                <div class="preview-image-wrapper">

                                    <img
                                        id="previewImg"
                                        src=""
                                        alt="Vista previa">

                                </div>


                                <div class="preview-image-info">

                                    <strong>
                                        Vista previa
                                    </strong>

                                    <div class="preview-data">

                                        <span>

                                            <i class="fa-solid fa-file-image"></i>

                                            <span id="imgNombre"></span>

                                        </span>

                                        <span>

                                            <i class="fa-solid fa-weight-hanging"></i>

                                            <span id="imgSize"></span>

                                        </span>

                                        <span>

                                            <i class="fa-solid fa-tag"></i>

                                            <span id="imgTipo"></span>

                                        </span>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    <!-- =================================================
                         NOTA
                    ================================================== -->

                    <div class="asesor-edit-note">

                        <div class="asesor-edit-note-icon">

                            <i class="fa-solid fa-circle-info"></i>

                        </div>

                        <div>

                            <strong>
                                Sobre la fotografía
                            </strong>

                            <p>
                                Si no seleccionas una nueva imagen,
                                se conservará la fotografía actual
                                del asesor.
                            </p>

                        </div>

                    </div>


                    <!-- =================================================
                         FOOTER
                    ================================================== -->

                    <div class="modal-footer modal-asesor-footer">

                        <button
                            type="button"
                            class="btn btn-light asesor-btn-cancel"
                            data-bs-dismiss="modal">

                            <i class="fa-solid fa-xmark me-2"></i>

                            Cancelar

                        </button>


                        <button
                            type="submit"
                            class="btn btn-success asesor-btn-save">

                            <i class="fa-solid fa-floppy-disk me-2"></i>

                            Guardar cambios

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>