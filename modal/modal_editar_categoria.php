<?php
/* ============================================================
   CoDevPro Technology
   Archivo: modal/modal_editar_categoria.php
   Módulo: Categorías
============================================================ */
?>

<!-- =========================================================
     MODAL EDITAR CATEGORÍA
========================================================= -->

<div
    class="modal fade"
    id="modalEditarCategoria"
    tabindex="-1"
    aria-labelledby="modalEditarCategoriaLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content category-form-modal">

            <!-- HEADER -->

            <div class="modal-header category-modal-header">

                <div class="category-modal-title">

                    <div class="category-modal-icon orange">

                        <i class="fa-solid fa-pen-to-square"></i>

                    </div>

                    <div>

                        <h5 id="modalEditarCategoriaLabel">
                            Editar categoría
                        </h5>

                        <p>
                            Actualiza la información de la categoría.
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
                id="formEditarCategoria"
                novalidate>

                <div class="modal-body category-modal-body">

                    <input
                        type="hidden"
                        name="accion"
                        value="editar">

                    <input
                        type="hidden"
                        name="id_categorias"
                        id="editarIdCategoria"
                        value="">


                    <div class="category-form-group">

                        <label for="editarNombreCategoria">

                            Nombre de la categoría

                            <span>*</span>

                        </label>

                        <div class="category-input-wrapper">

                            <i class="fa-solid fa-tag"></i>

                            <input
                                type="text"
                                class="form-control category-input"
                                id="editarNombreCategoria"
                                name="nombre"
                                maxlength="100"
                                placeholder="Nombre de la categoría"
                                autocomplete="off"
                                required>

                        </div>

                        <div class="category-input-help">

                            <span>
                                Puedes modificar el nombre de la categoría.
                            </span>

                            <span id="contadorEditarCategoria">
                                0/100
                            </span>

                        </div>

                        <div
                            class="invalid-feedback"
                            id="errorEditarNombreCategoria">
                            Ingresa el nombre de la categoría.
                        </div>

                    </div>


                    <div class="category-modal-notice">

                        <i class="fa-solid fa-shield-halved"></i>

                        <span>
                            Los cambios se aplicarán únicamente a tu categoría.
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
                        class="btn btn-category-save btn-edit-category"
                        id="btnEditarCategoria">

                        <i class="fa-solid fa-floppy-disk"></i>

                        Guardar cambios

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

