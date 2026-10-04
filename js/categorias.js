/* ============================================================
CoDevPro Technology
Archivo: js/categorias.js
Módulo: Categorías
Sistema: Panel Administrativo
============================================================ */

"use strict";

document.addEventListener("DOMContentLoaded", () => {

    /* ========================================================
       ELEMENTOS
    ======================================================== */

    const modalRegistrar =
        document.getElementById("modalRegistrarCategoria");

    const modalEditar =
        document.getElementById("modalEditarCategoria");

    const formRegistrar =
        document.getElementById("formRegistrarCategoria");

    const formEditar =
        document.getElementById("formEditarCategoria");

    const inputRegistrar =
        document.getElementById("nombreCategoria");

    const inputEditar =
        document.getElementById("editarNombreCategoria");

    const idEditar =
        document.getElementById("editarIdCategoria");

    const contadorRegistrar =
        document.getElementById("contadorCategoria");

    const contadorEditar =
        document.getElementById("contadorEditarCategoria");

    const btnRegistrar =
        document.getElementById("btnRegistrarCategoria");

    const btnEditar =
        document.getElementById("btnEditarCategoria");

    const inputEliminar =
        document.getElementById("idCategoriaEliminar");

    const nombreEliminar =
        document.getElementById("nombreCategoriaEliminar");

    const formEliminar =
        document.getElementById("formEliminarCategoria");

    /* ========================================================
       BUSCADOR
    ======================================================== */

    const formBusqueda =
        document.querySelector(".category-search");

    const buscador =
        document.querySelector(".category-search input");

    const botonLimpiarBusqueda =
        document.querySelector(".category-search .search-clear");

    /* ========================================================
       CONSTANTES
    ======================================================== */

    const MAX_LARGO = 100;

    /*
     * Tiempo de espera antes de ejecutar la búsqueda.
     *
     * Esto evita realizar una petición por cada tecla.
     */
    const TIEMPO_BUSQUEDA = 400;

    let temporizadorBusqueda = null;

    /*
     * Guarda el último término enviado.
     *
     * Evita recargar la página innecesariamente.
     */
    let ultimaBusqueda =
        buscador
            ? buscador.value.trim()
            : "";

    /* ========================================================
       CONTADOR DE CARACTERES
    ======================================================== */

    function actualizarContador(input, contador) {

        if (!input || !contador) {
            return;
        }

        const cantidad = input.value.length;

        contador.textContent =
            `${cantidad}/${MAX_LARGO}`;
    }

    /* ========================================================
       REGISTRAR - CONTADOR
    ======================================================== */

    if (inputRegistrar) {

        inputRegistrar.addEventListener("input", () => {

            actualizarContador(
                inputRegistrar,
                contadorRegistrar
            );

        });

    }

    /* ========================================================
       EDITAR - CONTADOR
    ======================================================== */

    if (inputEditar) {

        inputEditar.addEventListener("input", () => {

            actualizarContador(
                inputEditar,
                contadorEditar
            );

        });

    }

    /* ========================================================
       ========================================================
       BÚSQUEDA AUTOMÁTICA DE CATEGORÍAS
       ========================================================
    */

    if (buscador) {

        buscador.addEventListener("input", () => {

            /*
             * Cancelar búsqueda anterior.
             */
            clearTimeout(temporizadorBusqueda);

            /*
             * Obtener valor actual.
             */
            const texto =
                buscador.value.trim();

            /*
             * Esperar unos milisegundos antes de buscar.
             */
            temporizadorBusqueda =
                setTimeout(() => {

                    /*
                     * Si el texto no cambió,
                     * no hacemos nada.
                     */
                    if (texto === ultimaBusqueda) {
                        return;
                    }

                    ultimaBusqueda = texto;

                    /*
                     * Si está vacío:
                     * mostrar nuevamente todas
                     * las categorías.
                     */
                    if (texto === "") {

                        window.location.href =
                            "adm_categorias.php";

                        return;
                    }

                    /*
                     * Crear URL de búsqueda.
                     *
                     * Siempre regresamos a la página 1
                     * cuando cambia el criterio.
                     */
                    const url =
                        "adm_categorias.php" +
                        "?pagina=1" +
                        "&buscar=" +
                        encodeURIComponent(texto);

                    window.location.href = url;

                }, TIEMPO_BUSQUEDA);

        });

    }

    /* ========================================================
       SUBMIT MANUAL DEL BUSCADOR
       ======================================================== */

    if (formBusqueda && buscador) {

        formBusqueda.addEventListener("submit", (event) => {

            event.preventDefault();

            clearTimeout(temporizadorBusqueda);

            const texto =
                buscador.value.trim();

            /*
             * Si está vacío, mostrar todas.
             */
            if (texto === "") {

                window.location.href =
                    "adm_categorias.php";

                return;
            }

            /*
             * Ejecutar búsqueda inmediatamente.
             */
            ultimaBusqueda = texto;

            window.location.href =
                "adm_categorias.php" +
                "?pagina=1" +
                "&buscar=" +
                encodeURIComponent(texto);

        });

    }

    /* ========================================================
       LIMPIAR BÚSQUEDA
    ======================================================== */

    if (botonLimpiarBusqueda) {

        botonLimpiarBusqueda.addEventListener("click", (event) => {

            event.preventDefault();

            clearTimeout(temporizadorBusqueda);

            /*
             * Volver al listado completo.
             */
            window.location.href =
                "adm_categorias.php";

        });

    }

    /* ========================================================
       FOCUS AUTOMÁTICO - REGISTRAR
    ======================================================== */

    if (modalRegistrar) {

        modalRegistrar.addEventListener(
            "shown.bs.modal",
            () => {

                if (inputRegistrar) {

                    setTimeout(() => {

                        inputRegistrar.focus();

                    }, 150);

                }

            }
        );

    }

    /* ========================================================
       LIMPIAR MODAL REGISTRAR
    ======================================================== */

    if (modalRegistrar) {

        modalRegistrar.addEventListener(
            "hidden.bs.modal",
            () => {

                if (formRegistrar) {

                    formRegistrar.reset();

                    formRegistrar.classList.remove(
                        "was-validated"
                    );

                }

                if (contadorRegistrar) {

                    contadorRegistrar.textContent =
                        "0/100";

                }

                if (btnRegistrar) {

                    btnRegistrar.disabled = false;

                    btnRegistrar.innerHTML =
                        '<i class="fa-solid fa-plus"></i> Crear categoría';

                }

            }
        );

    }

    /* ========================================================
       PREPARAR MODAL EDITAR
    ======================================================== */

    document
        .querySelectorAll(".category-action.edit")
        .forEach((button) => {

            button.addEventListener("click", () => {

                const id =
                    button.dataset.id || "";

                const nombre =
                    button.dataset.name || "";

                if (idEditar) {

                    idEditar.value = id;

                }

                if (inputEditar) {

                    inputEditar.value =
                        nombre;

                }

                actualizarContador(
                    inputEditar,
                    contadorEditar
                );

                if (formEditar) {

                    formEditar.classList.remove(
                        "was-validated"
                    );

                }

            });

        });

    /* ========================================================
       FOCUS MODAL EDITAR
    ======================================================== */

    if (modalEditar) {

        modalEditar.addEventListener(
            "shown.bs.modal",
            () => {

                if (inputEditar) {

                    setTimeout(() => {

                        inputEditar.focus();

                        inputEditar.select();

                    }, 150);

                }

            }
        );

    }

    /* ========================================================
       LIMPIAR MODAL EDITAR
    ======================================================== */

    if (modalEditar) {

        modalEditar.addEventListener(
            "hidden.bs.modal",
            () => {

                if (formEditar) {

                    formEditar.classList.remove(
                        "was-validated"
                    );

                }

            }
        );

    }

    /* ========================================================
       PREPARAR ELIMINACIÓN
    ======================================================== */

    document
        .querySelectorAll(".category-action.delete")
        .forEach((button) => {

            button.addEventListener("click", () => {

                const id =
                    button.dataset.id || "";

                const nombre =
                    button.dataset.name ||
                    "esta categoría";

                if (inputEliminar) {

                    inputEliminar.value = id;

                }

                if (nombreEliminar) {

                    nombreEliminar.textContent =
                        `"${nombre}"`;

                }

            });

        });

    /* ========================================================
       VALIDAR NOMBRE
    ======================================================== */

    function validarNombre(input) {

        if (!input) {
            return false;
        }

        const valor =
            input.value.trim();

        if (valor === "") {

            input.classList.add(
                "is-invalid"
            );

            return false;

        }

        if (valor.length < 2) {

            input.classList.add(
                "is-invalid"
            );

            return false;

        }

        if (valor.length > MAX_LARGO) {

            input.classList.add(
                "is-invalid"
            );

            return false;

        }

        input.classList.remove(
            "is-invalid"
        );

        input.value = valor;

        return true;

    }

    /* ========================================================
       REGISTRAR CATEGORÍA
    ======================================================== */

    if (formRegistrar) {

        formRegistrar.addEventListener(
            "submit",
            (event) => {

                const valido =
                    validarNombre(inputRegistrar);

                if (!valido) {

                    event.preventDefault();

                    if (inputRegistrar) {
                        inputRegistrar.focus();
                    }

                    return;

                }

                if (btnRegistrar) {

                    btnRegistrar.disabled = true;

                    btnRegistrar.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

                }

            }
        );

    }

    /* ========================================================
       EDITAR CATEGORÍA
    ======================================================== */

    if (formEditar) {

        formEditar.addEventListener(
            "submit",
            (event) => {

                const valido =
                    validarNombre(inputEditar);

                const id =
                    idEditar
                        ? idEditar.value.trim()
                        : "";

                if (!valido || id === "") {

                    event.preventDefault();

                    if (!valido && inputEditar) {
                        inputEditar.focus();
                    }

                    return;

                }

                if (btnEditar) {

                    btnEditar.disabled = true;

                    btnEditar.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin"></i> Guardando...';

                }

            }
        );

    }

    /* ========================================================
       EVITAR DOBLE SUBMIT
    ======================================================== */

    let enviando = false;

    if (formRegistrar) {

        formRegistrar.addEventListener(
            "submit",
            (event) => {

                if (enviando) {

                    event.preventDefault();

                    return;

                }

                if (
                    inputRegistrar &&
                    inputRegistrar.value.trim() !== ""
                ) {

                    enviando = true;

                }

            }
        );

    }

    /* ========================================================
       ANIMACIÓN DE FILAS
    ======================================================== */

    const filas =
        document.querySelectorAll(
            ".categories-table tbody tr"
        );

    filas.forEach((fila, index) => {

        fila.style.opacity = "0";

        fila.style.transform =
            "translateY(5px)";

        setTimeout(() => {

            fila.style.transition =
                "opacity .25s ease, transform .25s ease";

            fila.style.opacity = "1";

            fila.style.transform =
                "translateY(0)";

        }, 40 + index * 35);

    });

    /* ========================================================
       AUTOFOCUS DEL BUSCADOR CON CTRL + K
    ======================================================== */

    document.addEventListener(
        "keydown",
        (event) => {

            if (
                (event.ctrlKey || event.metaKey) &&
                event.key.toLowerCase() === "k"
            ) {

                event.preventDefault();

                if (buscador) {

                    buscador.focus();

                    buscador.select();

                }

            }

        }
    );

    /* ========================================================
       CONFIRMACIÓN VISUAL DE ELIMINACIÓN
    ======================================================== */

    if (formEliminar) {

        formEliminar.addEventListener(
            "submit",
            () => {

                const button =
                    formEliminar.querySelector(
                        'button[type="submit"]'
                    );

                if (button) {

                    button.disabled = true;

                    button.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin me-1"></i> Eliminando...';

                }

            }
        );

    }

});
