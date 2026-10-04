<?php
// ============================================================
// Inmobiliaria Iquitos
// Archivo: modal/modal_eliminar_testimonio.php
// Módulo: Eliminar Testimonio
// ============================================================
?>

<!-- ============================================================
     MODAL ELIMINAR TESTIMONIO
============================================================ -->

<div
    class="modal fade"
    id="modalEliminarTestimonio"
    tabindex="-1"
    aria-labelledby="modalEliminarTestimonioLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <!-- =================================================
                 CABECERA
            ================================================== -->

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="modalEliminarTestimonioLabel">

                    <i class="fa-solid fa-trash-can me-2"></i>

                    Eliminar testimonio

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar">
                </button>

            </div>


            <!-- =================================================
                 CUERPO
            ================================================== -->

            <div class="modal-body text-center">


                <div
                    class="eliminar-testimonio-icon">

                    <i class="fa-solid fa-trash-can"></i>

                </div>


                <h4
                    class="eliminar-testimonio-title">

                    ¿Eliminar este testimonio?

                </h4>


                <p
                    class="eliminar-testimonio-text">

                    Estás a punto de eliminar el testimonio de:

                </p>


                <div
                    id="eliminarTestimonioNombre"
                    class="eliminar-testimonio-cliente">

                    Cliente

                </div>


                <div
                    class="eliminar-testimonio-warning">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                    <span>
                        Esta acción no se puede deshacer.
                    </span>

                </div>


                <input
                    type="hidden"
                    id="eliminarTestimonioId"
                    value="">

            </div>


            <!-- =================================================
                 FOOTER
            ================================================== -->

            <div
                class="modal-footer justify-content-center">

                <button
                    type="button"
                    class="btn btn-secondary btn-admin"
                    data-bs-dismiss="modal">

                    <i class="fa-solid fa-xmark me-1"></i>

                    Cancelar

                </button>


                <button
                    type="button"
                    id="btnConfirmarEliminarTestimonio"
                    class="btn btn-danger btn-admin">

                    <i class="fa-solid fa-trash-can me-1"></i>

                    Eliminar testimonio

                </button>

            </div>

        </div>

    </div>

</div>


<!-- ============================================================
     CSS ESPECÍFICO
============================================================ -->

<style>
    #modalEliminarTestimonio .modal-content {
        overflow: hidden;

        border: none;
        border-radius: 16px;

        box-shadow:
            0 20px 60px rgba(15, 23, 42, 0.18);
    }

    #modalEliminarTestimonio .modal-header {
        padding: 17px 21px;

        background: #ffffff;

        border-bottom: 1px solid #e5e7eb;
    }

    #modalEliminarTestimonio .modal-title {
        color: #0f172a;

        font-size: 1rem;
        font-weight: 700;
    }

    #modalEliminarTestimonio .modal-title i {
        color: #dc2626;
    }

    #modalEliminarTestimonio .modal-body {
        padding: 30px 24px 25px;
    }

    .eliminar-testimonio-icon {
        width: 68px;
        height: 68px;

        display: flex;
        align-items: center;
        justify-content: center;

        margin: 0 auto 18px;

        border-radius: 18px;

        color: #dc2626;

        background: #fef2f2;

        border: 1px solid #fee2e2;

        font-size: 1.55rem;
    }

    .eliminar-testimonio-title {
        margin: 0 0 8px;

        color: #0f172a;

        font-size: 1.15rem;
        font-weight: 750;
    }

    .eliminar-testimonio-text {
        margin: 0 0 7px;

        color: #64748b;

        font-size: 0.84rem;
    }

    .eliminar-testimonio-cliente {
        display: inline-flex;
        align-items: center;
        justify-content: center;

        max-width: 100%;

        padding: 8px 13px;

        margin-bottom: 18px;

        color: #334155;

        background: #f8fafc;

        border: 1px solid #e2e8f0;

        border-radius: 8px;

        font-size: 0.84rem;
        font-weight: 700;
    }

    .eliminar-testimonio-warning {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;

        padding: 10px 13px;

        color: #9a3412;

        background: #fff7ed;

        border: 1px solid #fed7aa;

        border-radius: 9px;

        font-size: 0.77rem;
        font-weight: 600;
    }

    .eliminar-testimonio-warning i {
        color: #ea580c;
    }

    #modalEliminarTestimonio .modal-footer {
        padding: 14px 21px;

        background: #f8fafc;

        border-top: 1px solid #e5e7eb;
    }

    @media (max-width: 575.98px) {

        #modalEliminarTestimonio .modal-dialog {
            margin: 10px;
        }

        #modalEliminarTestimonio .modal-body {
            padding: 25px 17px 20px;
        }

        #modalEliminarTestimonio .modal-footer {
            padding: 12px 17px;

            flex-direction: column-reverse;
        }

        #modalEliminarTestimonio .modal-footer .btn {
            width: 100%;
        }

    }
</style>


<!-- ============================================================
     JS DEL MODAL
============================================================ -->

<script>
    window.confirmarEliminarTestimonio =
        function(idTestimonio, nombreCliente) {

            const modalElement =
                document.getElementById(
                    'modalEliminarTestimonio'
                );


            const input =
                document.getElementById(
                    'eliminarTestimonioId'
                );


            const nombre =
                document.getElementById(
                    'eliminarTestimonioNombre'
                );


            if (!modalElement) {

                console.error(
                    'No se encontró #modalEliminarTestimonio'
                );

                return;

            }


            input.value =
                idTestimonio;


            nombre.textContent =
                nombreCliente ||
                'este cliente';


            const modal =
                bootstrap.Modal.getOrCreateInstance(
                    modalElement
                );


            modal.show();

        };


    /* ============================================================
       CONFIRMAR ELIMINACIÓN
    ============================================================ */

    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const boton =
                document.getElementById(
                    'btnConfirmarEliminarTestimonio'
                );


            if (!boton) {
                return;
            }


            boton.addEventListener(
                'click',
                async function() {

                    const id =
                        document.getElementById(
                            'eliminarTestimonioId'
                        ).value;


                    if (!id) {

                        Swal.fire({

                            icon: 'error',

                            title: 'Testimonio no válido',

                            text: 'No se pudo identificar el testimonio.',

                            confirmButtonColor: '#16a34a'

                        });

                        return;

                    }


                    const modal =
                        bootstrap.Modal.getInstance(
                            document.getElementById(
                                'modalEliminarTestimonio'
                            )
                        );


                    modal?.hide();


                    const confirmacion =
                        await Swal.fire({

                            icon: 'warning',

                            title: '¿Confirmar eliminación?',

                            text: 'El testimonio será eliminado permanentemente.',

                            showCancelButton: true,

                            confirmButtonText: 'Sí, eliminar',

                            cancelButtonText: 'Cancelar',

                            confirmButtonColor: '#dc2626',

                            cancelButtonColor: '#64748b',

                            reverseButtons: true

                        });


                    if (!confirmacion.isConfirmed) {

                        return;

                    }


                    /* --------------------------------------------------
                       ESTADO BOTÓN
                    -------------------------------------------------- */

                    boton.disabled = true;


                    try {

                        const formData =
                            new FormData();


                        formData.append(
                            'id_testimonio',
                            id
                        );


                        const respuesta =
                            await fetch(
                                '../controladores/ajax_eliminar_testimonio.php', {
                                    method: 'POST',
                                    credentials: 'same-origin',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                }
                            );


                        if (!respuesta.ok) {

                            throw new Error(
                                'Error HTTP: ' +
                                respuesta.status
                            );

                        }


                        const datos =
                            await respuesta.json();


                        if (!datos.success) {

                            throw new Error(
                                datos.message ||
                                'No fue posible eliminar el testimonio.'
                            );

                        }


                        await Swal.fire({

                            icon: 'success',

                            title: 'Testimonio eliminado',

                            text: 'El testimonio se eliminó correctamente.',

                            confirmButtonColor: '#16a34a',

                            timer: 2200,

                            timerProgressBar: true

                        });


                        /* ------------------------------------------------
                           RECARGAR LISTA Y KPI
                        ------------------------------------------------ */

                        if (
                            typeof window
                            .recargarListaTestimonios ===
                            'function'
                        ) {

                            window
                                .recargarListaTestimonios();

                        }


                    } catch (error) {

                        console.error(
                            'Error al eliminar testimonio:',
                            error
                        );


                        Swal.fire({

                            icon: 'error',

                            title: 'No se pudo eliminar',

                            text: error.message ||
                                'Ocurrió un error al eliminar el testimonio.',

                            confirmButtonColor: '#16a34a'

                        });

                    } finally {

                        boton.disabled = false;

                    }

                }
            );

        }
    );
</script>