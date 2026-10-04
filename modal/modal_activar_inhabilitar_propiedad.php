<?php

/**
 * =========================================================
 * CoDevPro Technology
 * Archivo: modal/modal_activar_inhabilitar_propiedad.php
 *
 * Función:
 * - Confirmar activación de una propiedad
 * - Confirmar inhabilitación de una propiedad
 *
 * Estados:
 * - 0 = ACTIVO
 * - 1 = INHABILITADO
 * =========================================================
 */
?>

<div
    class="modal fade"
    id="modalCambiarEstado"
    tabindex="-1"
    aria-labelledby="modalCambiarEstadoLabel"
    aria-hidden="true">


    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg">

            <!-- =====================================================
             HEADER
        ====================================================== -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="modalCambiarEstadoLabel">

                    <i
                        id="iconoModalEstado"
                        class="fa-solid fa-toggle-on me-2">
                    </i>

                    Cambiar estado de propiedad

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>


            <!-- =====================================================
             BODY
        ====================================================== -->

            <div class="modal-body">

                <div
                    id="alertaCambioEstado"
                    class="alert d-none"
                    role="alert">
                </div>


                <div class="text-center mb-4">

                    <div
                        id="iconoEstadoConfirmacion"
                        class="mb-3"
                        style="font-size: 4rem;">

                        <i class="fa-solid fa-toggle-on"></i>

                    </div>

                    <h5
                        id="tituloCambioEstado"
                        class="fw-bold mb-2">

                        Cambiar estado

                    </h5>

                    <p class="text-muted mb-0">

                        ¿Estás seguro de que deseas cambiar el estado
                        de la propiedad?

                    </p>

                </div>


                <!-- Nombre de propiedad -->

                <div class="bg-light rounded-3 p-3 mb-3">

                    <div class="small text-muted mb-1">
                        Propiedad
                    </div>

                    <strong
                        id="cambiar-estado-nombre"
                        class="d-block text-break">

                        —

                    </strong>

                </div>


                <!-- Estado actual / nuevo -->

                <div class="row g-3">

                    <div class="col-6">

                        <div class="border rounded-3 p-3 h-100">

                            <div class="small text-muted mb-1">
                                Estado actual
                            </div>

                            <span
                                id="estadoActualTexto"
                                class="badge bg-success-subtle text-success border border-success-subtle">

                                ACTIVO

                            </span>

                        </div>

                    </div>


                    <div class="col-6">

                        <div class="border rounded-3 p-3 h-100">

                            <div class="small text-muted mb-1">
                                Nuevo estado
                            </div>

                            <span
                                id="nuevoEstadoTexto"
                                class="badge bg-danger-subtle text-danger border border-danger-subtle">

                                INHABILITADO

                            </span>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                 FORMULARIO
            ================================================== -->

                <form
                    id="formCambiarEstadoPropiedad"
                    method="POST"
                    action="../controladores/procesar_lista_propiedades.php"
                    class="d-none">

                    <input
                        type="hidden"
                        name="cambiar_estado_propiedad"
                        value="1">

                    <input
                        type="hidden"
                        name="id_propiedad"
                        id="cambiar-estado-id"
                        value="">

                    <input
                        type="hidden"
                        name="nuevo_estado"
                        id="cambiar-estado-nuevo"
                        value="">

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= htmlspecialchars(
                                    $_SESSION['csrf_propiedades'] ?? '',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>">

                </form>

            </div>


            <!-- =====================================================
             FOOTER
        ====================================================== -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light border"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-xmark me-1"></i>

                    Cancelar

                </button>


                <button
                    type="button"
                    id="btnConfirmarCambioEstado"
                    class="btn btn-success">

                    <i
                        id="iconoBtnCambioEstado"
                        class="fa-solid fa-toggle-on me-1">
                    </i>

                    Activar propiedad

                </button>

            </div>

        </div>

    </div>
</div>