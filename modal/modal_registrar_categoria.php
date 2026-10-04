<?php
/* ============================================================
   CoDevPro Technology
   Archivo: modal/modal_registrar_categoria.php
   Módulo: Categorías
============================================================ */
?>

<!-- =========================================================
     MODAL REGISTRAR CATEGORÍA
========================================================= -->

<div
    class="modal fade"
    id="modalRegistrarCategoria"
    tabindex="-1"
    aria-labelledby="modalRegistrarCategoriaLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content category-form-modal">

            <!-- HEADER -->

            <div class="modal-header category-modal-header">

                <div class="category-modal-title">

                    <div class="category-modal-icon green">

                        <i class="fa-solid fa-layer-group"></i>

                    </div>

                    <div>

                        <h5 id="modalRegistrarCategoriaLabel">
                            Nueva categoría
                        </h5>

                        <p>
                            Crea una categoría para organizar tus propiedades.
                        </p>

                    </div>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Cerrar"></button>

            </div>


            <!-- FORMULARIO -->

            <form
                method="POST"
                action="../controladores/procesar_categorias.php"
                id="formRegistrarCategoria"
                novalidate>

                <div class="modal-body category-modal-body">

                    <input
                        type="hidden"
                        name="accion"
                        value="registrar">

                    <div class="category-form-group">

                        <label for="nombreCategoria">

                            Nombre de la categoría

                            <span>*</span>

                        </label>

                        <div class="category-input-wrapper">

                            <i class="fa-solid fa-tag"></i>

                            <input
                                type="text"
                                class="form-control category-input"
                                id="nombreCategoria"
                                name="nombre"
                                maxlength="100"
                                placeholder="Ej. Casa, Departamento, Terreno..."
                                autocomplete="off"
                                required>

                        </div>

                        <div class="category-input-help">

                            <span>
                                Escribe un nombre claro y fácil de identificar.
                            </span>

                            <span id="contadorCategoria">
                                0/100
                            </span>

                        </div>

                        <div
                            class="invalid-feedback"
                            id="errorNombreCategoria">
                            Ingresa el nombre de la categoría.
                        </div>

                    </div>


                    <div class="category-modal-notice">

                        <i class="fa-solid fa-circle-info"></i>

                        <span>
                            La categoría quedará disponible únicamente para
                            tus propiedades.
                        </span>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer category-modal-footer">

                    <button
                        type="button"
                        class="btn btn-category-cancel"
                        data-bs-dismiss="modal">

                        Cancelar

                    </button>

                    <button
                        type="submit"
                        class="btn btn-category-save"
                        id="btnRegistrarCategoria">

                        <i class="fa-solid fa-plus"></i>

                        Crear categoría

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>